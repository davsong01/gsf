<?php

namespace App\Services;

use App\Models\Chapter;
use App\Models\Field;
use App\Models\StakeholderReportQuestion;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GrowthAnalysisService
{
    public function measures()
    {
        $questions = StakeholderReportQuestion::query()->isActive()->forModule('report')
            ->where('is_quantifiable', true)
            ->whereIn('type', ['number', 'select'])
            ->orderBy('section_id')->orderBy('order')->get()
            ->filter(function ($question) {
                if (in_array($question->slug, ['month', 'year', 'year_established', 'activity_problems', 'evangelism_no_reason'], true)) return false;
                if ($question->type === 'number') return true;
                $options = $question->options ?: [];
                if (! is_array($options) || count($options) !== 5) return false;
                $values = array_map(fn ($key, $option) => is_array($option) ? ($option['value'] ?? null) : $key, array_keys($options), $options);
                sort($values);
                return array_map('strval', $values) === ['1', '2', '3', '4', '5'];
            })->values()->map(fn ($question) => [
                'id' => (int) $question->id,
                'label' => $question->label,
                'kind' => $question->type === 'select' ? 'rating' : 'count',
                'section_id' => $question->section_id,
            ])->all();

        return array_merge([[
            'id' => 0,
            'label' => 'Overall growth index',
            'kind' => 'index',
            'section_id' => null,
        ]], $questions);
    }

    public function build(Request $request, array $scope, array $measures): array
    {
        $validated = $request->validate([
            'level' => 'nullable|in:chapter,zone,field',
            'period' => 'nullable|in:month,year',
            'measure_id' => 'nullable|integer',
            'from_month' => 'nullable|date_format:Y-m',
            'to_month' => 'nullable|date_format:Y-m',
            'field_id' => 'nullable|integer',
            'zone_id' => 'nullable|integer',
            'entity_ids' => 'nullable|array',
            'entity_ids.*' => 'integer',
            'focus_id' => 'nullable|integer',
        ]);

        $level = $validated['level'] ?? 'chapter';
        $period = $validated['period'] ?? 'month';
        $from = Carbon::createFromFormat('Y-m-d', ($validated['from_month'] ?? now()->startOfYear()->format('Y-m')).'-01')->startOfMonth();
        $to = Carbon::createFromFormat('Y-m-d', ($validated['to_month'] ?? now()->format('Y-m')).'-01')->startOfMonth();
        if ($to->lt($from) || $from->diffInMonths($to) > 119) {
            throw ValidationException::withMessages(['to_month' => 'Choose an end month after the start month, within a 10-year range.']);
        }
        $months = [];
        for ($month = $from->copy(); $month->lte($to); $month->addMonth()) $months[] = $month->format('Y-m');
        $periodKeys = $period === 'year'
            ? array_values(array_unique(array_map(fn ($month) => substr($month, 0, 4), $months)))
            : $months;

        $measure = collect($measures)->firstWhere('id', (int) ($validated['measure_id'] ?? ($measures[0]['id'] ?? 0)));
        if (! $measure) throw ValidationException::withMessages(['measure_id' => 'Choose an available growth measure.']);

        $allowedIds = collect($scope['chapterIds'] ?? [])->map(fn ($id) => (int) $id)->all();
        $chapters = Chapter::actualCampus()->whereIn('id', $allowedIds)
            ->when(! empty($validated['field_id']), fn ($query) => $query->where('field_id', $validated['field_id']))
            ->when(! empty($validated['zone_id']), fn ($query) => $query->where('zone_id', $validated['zone_id']))
            ->get(['id', 'name', 'field_id', 'zone_id']);
        $fields = Field::whereIn('id', $chapters->pluck('field_id')->unique())->pluck('name', 'id');
        $zones = Zone::whereIn('id', $chapters->pluck('zone_id')->unique())->pluck('name', 'id');
        $entities = $level === 'chapter'
            ? $chapters->map(fn ($chapter) => ['id' => (int) $chapter->id, 'name' => $chapter->name])->all()
            : ($level === 'zone'
                ? $zones->map(fn ($name, $id) => ['id' => (int) $id, 'name' => $name])->values()->all()
                : $fields->map(fn ($name, $id) => ['id' => (int) $id, 'name' => $name])->values()->all());
        usort($entities, fn ($a, $b) => strcasecmp($a['name'], $b['name']));
        $availableIds = array_column($entities, 'id');
        $selectedIds = array_key_exists('entity_ids', $validated)
            ? array_values(array_intersect($availableIds, array_map('intval', $validated['entity_ids'])))
            : $availableIds;
        $selectedEntities = array_values(array_filter($entities, fn ($entity) => in_array($entity['id'], $selectedIds, true)));
        $focusId = (int) ($validated['focus_id'] ?? ($selectedIds[0] ?? 0));
        if (! in_array($focusId, $selectedIds, true)) $focusId = $selectedIds[0] ?? 0;

        $chapterMap = $chapters->keyBy('id');
        $measureMap = collect($measures)->filter(fn ($item) => $item['kind'] !== 'index')->keyBy('id');
        $entityForChapter = fn ($chapter) => $level === 'chapter' ? (int) $chapter->id : ($level === 'zone' ? (int) $chapter->zone_id : (int) $chapter->field_id);
        $selectedChapterIds = $chapters->filter(fn ($chapter) => in_array($entityForChapter($chapter), $selectedIds, true))->pluck('id');
        $focusChapterIds = $chapters->filter(fn ($chapter) => $entityForChapter($chapter) === $focusId)->pluck('id');
        $rows = DB::table('stakeholder_report_answers as a')
            ->join('stakeholder_reports as r', 'r.id', '=', 'a.report_id')
            ->whereIn('r.chapter_id', $measure['kind'] === 'index' ? $chapters->pluck('id') : $selectedChapterIds)
            ->where('r.edit_mode', 0)
            ->where('r.school_not_in_session', 0)
            ->where(function ($query) use ($measure, $focusChapterIds, $measureMap) {
                if ($measure['kind'] === 'index') {
                    $query->whereIn('a.question_id', $measureMap->keys());
                } else {
                    $query->where('a.question_id', $measure['id'])
                        ->orWhere(fn ($q) => $q->whereIn('r.chapter_id', $focusChapterIds)->whereIn('a.question_id', $measureMap->keys()));
                }
            })
            ->where(function ($query) use ($from) {
                $query->where('r.year', '>', $from->year)->orWhere(fn ($q) => $q->where('r.year', $from->year)->where('r.month', '>=', $from->month));
            })
            ->where(function ($query) use ($to) {
                $query->where('r.year', '<', $to->year)->orWhere(fn ($q) => $q->where('r.year', $to->year)->where('r.month', '<=', $to->month));
            })
            ->orderByDesc('r.id')
            ->get(['r.id as report_id', 'r.chapter_id', 'r.year', 'r.month', 'a.question_id', 'a.answer_value', 'a.answer_quantity']);

        $seen = [];
        $buckets = [];
        $chapterValues = [];
        $maximumByQuestion = [];
        foreach ($rows as $row) {
            $month = sprintf('%04d-%02d', $row->year, $row->month);
            $key = $row->chapter_id.':'.$month.':'.$row->question_id;
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $item = $measureMap->get((int) $row->question_id);
            $raw = $item['kind'] === 'rating' ? ($row->answer_quantity ?? $row->answer_value) : $row->answer_value;
            if (! is_numeric($raw) || (float) $raw < 0) continue;
            $value = (float) $raw;
            if ($item['kind'] === 'rating' && ($value < 1 || $value > 5)) continue;
            if ($measure['kind'] === 'index') {
                $chapterValues[$row->chapter_id][$row->question_id][$month] = $value;
                if ($item['kind'] === 'count') {
                    $maximumByQuestion[$row->question_id] = max($maximumByQuestion[$row->question_id] ?? 0, $value);
                }
            }
            $chapter = $chapterMap->get($row->chapter_id);
            $entityId = $level === 'chapter' ? (int) $chapter->id : ($level === 'zone' ? (int) $chapter->zone_id : (int) $chapter->field_id);
            if (! in_array($entityId, $selectedIds, true)) continue;
            $buckets[$entityId][$row->question_id][$month]['sum'] = ($buckets[$entityId][$row->question_id][$month]['sum'] ?? 0) + $value;
            $buckets[$entityId][$row->question_id][$month]['count'] = ($buckets[$entityId][$row->question_id][$month]['count'] ?? 0) + 1;
        }

        if ($measure['kind'] === 'index') {
            foreach ($chapterValues as $chapterId => $answersByQuestion) {
                $chapter = $chapterMap->get($chapterId);
                $entityId = $entityForChapter($chapter);
                if (! in_array($entityId, $selectedIds, true)) continue;
                $scoresByMonth = [];
                foreach ($answersByQuestion as $questionId => $answersByMonth) {
                    $item = $measureMap->get((int) $questionId);
                    $maximum = $item['kind'] === 'rating' ? 5 : ($maximumByQuestion[$questionId] ?? 0);
                    foreach ($answersByMonth as $month => $value) {
                        $scoresByMonth[$month]['sum'] = ($scoresByMonth[$month]['sum'] ?? 0) + ($maximum > 0 ? 100 * $value / $maximum : 0);
                        $scoresByMonth[$month]['count'] = ($scoresByMonth[$month]['count'] ?? 0) + 1;
                    }
                }
                foreach ($scoresByMonth as $month => $scores) {
                    $campusIndex = $scores['sum'] / $scores['count'];
                    $buckets[$entityId][0][$month]['sum'] = ($buckets[$entityId][0][$month]['sum'] ?? 0) + $campusIndex;
                    $buckets[$entityId][0][$month]['count'] = ($buckets[$entityId][0][$month]['count'] ?? 0) + 1;
                }
            }
        }

        $series = [];
        foreach ($selectedEntities as $entity) {
            $entityBuckets = $buckets[$entity['id']][$measure['id']] ?? [];
            $monthlyValues = $this->values($entityBuckets, $months, $measure['kind'], $level);
            $monthlyContributors = array_map(fn ($month) => $entityBuckets[$month]['count'] ?? 0, $months);
            $yearly = $period === 'year' ? $this->yearlyValues($monthlyValues, $months, $periodKeys) : null;
            $values = $yearly['values'] ?? $monthlyValues;
            $series[] = ['id' => $entity['id'], 'name' => $entity['name'], 'values' => $values,
                'contributors' => $yearly['reported_months'] ?? $monthlyContributors] + $this->growth($values, $periodKeys);
        }
        $aspects = [];
        if ($focusId) {
            foreach ($measures as $item) {
                if ($item['kind'] === 'index') continue;
                $monthlyValues = $this->values($buckets[$focusId][$item['id']] ?? [], $months, $item['kind'], $level);
                $values = $period === 'year' ? $this->yearlyValues($monthlyValues, $months, $periodKeys)['values'] : $monthlyValues;
                $aspects[] = ['id' => $item['id'], 'name' => $item['label'], 'kind' => $item['kind']] + $this->growth($values, $periodKeys);
            }
        }
        return [
            'level' => $level, 'period' => $period, 'measure' => $measure, 'months' => $periodKeys,
            'aggregation' => ($measure['kind'] === 'index'
                ? 'Counts as a share of the highest value in the selected scope and period; ratings as a share of 5. Available question scores are equally averaged (0–100)'.($level === 'chapter' ? '' : '; then averaged across reporting in-session campuses')
                : ($level === 'chapter' ? 'Campus value' : 'Average per reporting in-session campus'))
                .($period === 'year' ? '; yearly points average valid monthly values' : ''),
            'entities' => $entities, 'selected_ids' => $selectedIds, 'series' => $series,
            'focus_id' => $focusId, 'focus_name' => collect($entities)->firstWhere('id', $focusId)['name'] ?? null,
            'aspects' => $aspects,
            'summary' => [
                'growing' => collect($series)->where('status', 'growing')->count(),
                'declining' => collect($series)->where('status', 'declining')->count(),
                'stable' => collect($series)->where('status', 'stable')->count(),
                'insufficient' => collect($series)->where('status', 'insufficient')->count(),
            ],
        ];
    }

    private function values(array $buckets, array $months, string $kind, string $level): array
    {
        return array_map(function ($month) use ($buckets, $kind, $level) {
            if (! isset($buckets[$month])) return null;
            $value = $kind === 'rating' || $level !== 'chapter' ? $buckets[$month]['sum'] / $buckets[$month]['count'] : $buckets[$month]['sum'];
            return round($value, $kind !== 'count' || $level !== 'chapter' ? 2 : 0);
        }, $months);
    }

    private function yearlyValues(array $monthlyValues, array $months, array $years): array
    {
        $values = [];
        $reportedMonths = [];
        foreach ($years as $year) {
            $reported = [];
            foreach ($months as $index => $month) {
                if (substr($month, 0, 4) === $year && $monthlyValues[$index] !== null) {
                    $reported[] = $monthlyValues[$index];
                }
            }
            $reportedMonths[] = count($reported);
            $values[] = $reported ? round(array_sum($reported) / count($reported), 2) : null;
        }
        return ['values' => $values, 'reported_months' => $reportedMonths];
    }

    private function growth(array $values, array $months): array
    {
        $reported = [];
        foreach ($values as $index => $value) {
            if ($value !== null) $reported[] = ['month' => $months[$index], 'value' => $value];
        }
        $latestPoint = $reported[count($reported) - 1] ?? null;
        $previousPoint = $reported[count($reported) - 2] ?? null;
        $previous = $previousPoint['value'] ?? null;
        $latest = $latestPoint['value'] ?? null;
        $change = $previous !== null && $latest !== null ? round($latest - $previous, 2) : null;
        return [
            'previous' => $previous, 'previous_month' => $previousPoint['month'] ?? null,
            'latest' => $latest, 'latest_month' => $latestPoint['month'] ?? null, 'change' => $change,
            'percent' => $change !== null && $previous > 0 ? round($change / $previous * 100, 1) : null,
            'reported_months' => count($reported),
            'status' => $change === null ? 'insufficient' : ($change > 0 ? 'growing' : ($change < 0 ? 'declining' : 'stable')),
        ];
    }
}

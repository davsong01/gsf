<?php

namespace App\Http\Controllers;

use App\Models\StakeholderCampaign;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\StakeholderDesignation;
use App\Models\StakeholderReportQuestion;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StakeholderCampaignController extends Controller
{
    public function index()
    {
        $campaigns = StakeholderCampaign::withCount(['questions', 'responses'])->latest()->paginate(25);

        return view('admin.stakeholder-campaigns.index', compact('campaigns'));
    }

    public function responses(StakeholderCampaign $campaign)
    {
        $this->authorizeCampaignManagement();

        $campaignQuestions = $campaign->questions()->get()->keyBy('slug');
        $responses = $campaign->responses()
            ->latest('submitted_at')
            ->latest('id')
            ->paginate(20);

        return view('admin.stakeholder-campaigns.responses', compact('campaign', 'campaignQuestions', 'responses'));
    }

    public function exportResponses(StakeholderCampaign $campaign)
    {
        $this->authorizeCampaignManagement();

        $questions = $campaign->questions()->get();
        $responses = $campaign->responses()->orderBy('submitted_at')->orderBy('id')->get();
        $filename = Str::slug($campaign->title ?: 'campaign') . '-responses.csv';
        $headers = array_merge(
            ['Submission ID', 'Submitted At', 'Field', 'Zone', 'Chapter', 'Designation'],
            $questions->map(fn ($question) => $this->safeCsvCell($question->label))->all()
        );

        return response()->streamDownload(function () use ($questions, $responses, $headers) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);

            foreach ($responses as $response) {
                $answers = $response->answers ?? [];
                $row = [
                    $response->id,
                    $response->submitted_at?->format('Y-m-d H:i:s') ?? $response->created_at?->format('Y-m-d H:i:s'),
                    $this->safeCsvCell($answers['__field'] ?? ''),
                    $this->safeCsvCell($answers['__zone'] ?? ''),
                    $this->safeCsvCell($answers['__chapter'] ?? ''),
                    $this->safeCsvCell($answers['__designation'] ?? ''),
                ];

                foreach ($questions as $question) {
                    $value = $answers[$question->slug] ?? null;
                    if (is_array($value)) {
                        $value = implode(', ', array_map(
                            fn ($item) => is_scalar($item) ? (string) $item : json_encode($item, JSON_UNESCAPED_UNICODE),
                            $value
                        ));
                    }
                    $row[] = $this->safeCsvCell($value);
                }

                fputcsv($output, $row);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    protected function authorizeCampaignManagement(): void
    {
        $permissions = auth()->user()->permissions ?? [];
        abort_unless(
            (isAdmin()['status'] ?? false)
                || in_array('campaigns.index', $permissions, true)
                || in_array('stakeholder.questions.index', $permissions, true),
            403
        );
    }

    protected function safeCsvCell($value): string
    {
        $value = is_scalar($value) ? (string) $value : '';

        return preg_match('/^[\s]*[=+\-@]/u', $value) ? "'" . $value : $value;
    }

    public function showPublic(string $slug)
    {
        $campaign = StakeholderCampaign::where('slug', $slug)->firstOrFail();
        $questions = $campaign->questions()->where('status', 1)->get();
        $fields = $campaign->enable_field_options ? Field::orderBy('name')->get(['id', 'name']) : collect();
        $zones = $campaign->enable_zone_options ? Zone::orderBy('name')->get(['id', 'name']) : collect();
        $chapters = $campaign->enable_chapter_option ? Chapter::actualCampus()->orderBy('name')->get(['id', 'name']) : collect();
        $designations = $campaign->enable_designation_options
            ? StakeholderDesignation::where('status', 'active')->orderBy('order')->orderBy('name')->get(['id', 'name'])
            : collect();
        $isOpen = $campaign->status === 'active'
            && now()->between($campaign->open_from, $campaign->open_until);
        $antiSpamNumbers = [random_int(1, 9), random_int(1, 9)];
        session()->put('campaign_antispam.' . $campaign->slug, array_sum($antiSpamNumbers));

        return view('campaigns.show', compact('campaign', 'questions', 'isOpen', 'antiSpamNumbers', 'fields', 'zones', 'chapters', 'designations'));
    }

    public function submitPublic(Request $request, string $slug)
    {
        $campaign = StakeholderCampaign::where('slug', $slug)->firstOrFail();
        abort_unless($campaign->status === 'active' && now()->between($campaign->open_from, $campaign->open_until), 404);
        $questions = $campaign->questions()->where('status', 1)->get();
        $expectedAntiSpamAnswer = session('campaign_antispam.' . $campaign->slug);
        $rules = [
            'answers' => ['required', 'array'],
            'anti_spam' => ['required', 'integer', Rule::in($expectedAntiSpamAnswer === null ? [] : [$expectedAntiSpamAnswer])],
        ];

        foreach ($questions as $question) {
            $key = 'answers.' . $question->slug;
            $required = (bool) $question->is_required ? 'required' : 'nullable';
            $typeRules = match ($question->type) {
                'number' => ['numeric'],
                'date' => ['date'],
                'file' => ['file', 'max:10240'],
                'checkbox' => ['array'],
                'select', 'radio', 'rating' => [Rule::in($this->questionOptionValues($question))],
                default => ['string', 'max:10000'],
            };
            $rules[$key] = array_merge([$required], $typeRules);

            if ($question->type === 'checkbox') {
                $rules[$key . '.*'] = [Rule::in($this->questionOptionValues($question))];
            }
        }

        $optionDefinitions = [
            'field_id' => ['enabled' => $campaign->enable_field_options, 'table' => 'fields', 'key' => '__field'],
            'zone_id' => ['enabled' => $campaign->enable_zone_options, 'table' => 'zones', 'key' => '__zone'],
            'chapter_id' => ['enabled' => $campaign->enable_chapter_option, 'table' => 'chapters', 'key' => '__chapter'],
            'designation_id' => ['enabled' => $campaign->enable_designation_options, 'table' => 'stakeholder_designations', 'key' => '__designation'],
        ];
        foreach ($optionDefinitions as $input => $definition) {
            if ($definition['enabled']) {
                $rules['campaign_options.' . $input] = ['nullable', 'integer', Rule::exists($definition['table'], 'id')];
            }
        }

        $validated = Validator::make($request->all(), $rules)->validate();
        session()->forget('campaign_antispam.' . $campaign->slug);
        $answers = [];
        foreach ($questions as $question) {
            $value = data_get($validated, 'answers.' . $question->slug);
            if ($question->type === 'file' && $request->hasFile('answers.' . $question->slug)) {
                $value = $request->file('answers.' . $question->slug)->store('campaign-responses');
            }
            $answers[$question->slug] = $value;
        }
        foreach ($optionDefinitions as $input => $definition) {
            if ($definition['enabled'] && $id = data_get($validated, 'campaign_options.' . $input)) {
                $answers[$definition['key']] = DB::table($definition['table'])->where('id', $id)->value('name');
            }
        }

        $campaign->responses()->create(['answers' => $answers, 'submitted_at' => now()]);

        return redirect()->route('campaigns.public.show', $campaign->slug)
            ->with('campaign_success', 'Your response has been submitted. Thank you.');
    }

    protected function questionOptionValues(StakeholderReportQuestion $question): array
    {
        return collect($question->options ?? [])->map(function ($option) {
            return is_array($option) ? ($option['value'] ?? $option['label'] ?? null) : $option;
        })->filter(fn ($value) => $value !== null)->values()->all();
    }

    public function create()
    {
        $campaign = new StakeholderCampaign();
        $questions = $this->campaignQuestions();

        return view('admin.stakeholder-campaigns.edit', compact('campaign', 'questions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCampaign($request);

        DB::transaction(function () use ($validated) {
            $campaign = StakeholderCampaign::create($validated['campaign']);
            $campaign->questions()->sync($this->orderedQuestionSyncData($validated['question_ids']));
        });

        return redirect()->route('campaigns.index')->with('message', 'Campaign created successfully.');
    }

    public function edit(StakeholderCampaign $campaign)
    {
        $campaign->load('questions');
        $questions = $this->campaignQuestions();

        return view('admin.stakeholder-campaigns.edit', compact('campaign', 'questions'));
    }

    public function update(Request $request, StakeholderCampaign $campaign)
    {
        $validated = $this->validateCampaign($request, $campaign);

        DB::transaction(function () use ($campaign, $validated) {
            $campaign->update($validated['campaign']);
            $campaign->questions()->sync($this->orderedQuestionSyncData($validated['question_ids']));
        });

        return redirect()->route('campaigns.index')->with('message', 'Campaign updated successfully.');
    }

    public function destroy(StakeholderCampaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('campaigns.index')->with('message', 'Campaign deleted successfully.');
    }

    protected function campaignQuestions()
    {
        return StakeholderReportQuestion::query()
            ->where('module_type', 'campagn')
            ->orderBy('order')
            ->orderBy('label')
            ->get();
    }

    protected function orderedQuestionSyncData(array $questionIds): array
    {
        $syncData = [];
        foreach (array_values(array_unique($questionIds)) as $position => $questionId) {
            $syncData[$questionId] = ['position' => $position];
        }

        return $syncData;
    }

    protected function validateCampaign(Request $request, ?StakeholderCampaign $campaign = null): array
    {
        $slug = trim((string) $request->input('slug'));
        if ($slug === '') {
            $slug = Str::slug((string) $request->input('title'));
        }

        $request->merge(['slug' => $slug]);

        $slugRule = Rule::unique('stakeholder_campaigns', 'slug');
        if ($campaign) {
            $slugRule->ignore($campaign->id);
        }

        $validated = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'description' => ['nullable', 'string'],
            'instructions' => ['required', 'string'],
            'open_from' => ['required', 'date'],
            'open_until' => ['required', 'date', 'after:open_from'],
            'event_at' => ['required', 'date'],
            'status' => ['required', 'in:draft,active,closed'],
            'enable_field_options' => ['sometimes', 'boolean'],
            'enable_zone_options' => ['sometimes', 'boolean'],
            'enable_chapter_option' => ['sometimes', 'boolean'],
            'enable_designation_options' => ['sometimes', 'boolean'],
            'question_ids' => ['required', 'array', 'min:1'],
            'question_ids.*' => [
                'integer',
                Rule::exists('stakeholder_report_questions', 'id')->where('module_type', 'campagn'),
            ],
        ])->validate();

        return [
            'campaign' => collect($validated)->only([
                'title', 'slug', 'description', 'instructions', 'open_from', 'open_until', 'event_at', 'status',
            ])->merge([
                'enable_field_options' => $request->boolean('enable_field_options'),
                'enable_zone_options' => $request->boolean('enable_zone_options'),
                'enable_chapter_option' => $request->boolean('enable_chapter_option'),
                'enable_designation_options' => $request->boolean('enable_designation_options'),
            ])->all(),
            'question_ids' => array_values(array_unique($validated['question_ids'])),
        ];
    }
}

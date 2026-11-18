<?php

namespace App\Http\Controllers;

use App\Models\StakeholderCampaign;
use App\Models\StakeholderReportQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StakeholderCampaignController extends Controller
{
    public function index()
    {
        $campaigns = StakeholderCampaign::withCount('questions')->latest()->paginate(25);

        return view('admin.stakeholder-campaigns.index', compact('campaigns'));
    }

    public function showPublic(string $slug)
    {
        $campaign = StakeholderCampaign::where('slug', $slug)->firstOrFail();
        $questions = $campaign->questions()->where('status', 1)->get();
        $isOpen = $campaign->status === 'active'
            && now()->between($campaign->open_from, $campaign->open_until);
        $antiSpamNumbers = [random_int(1, 9), random_int(1, 9)];
        session()->put('campaign_antispam.' . $campaign->slug, array_sum($antiSpamNumbers));

        return view('campaigns.show', compact('campaign', 'questions', 'isOpen', 'antiSpamNumbers'));
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
            'question_ids' => ['required', 'array', 'min:1'],
            'question_ids.*' => [
                'integer',
                Rule::exists('stakeholder_report_questions', 'id')->where('module_type', 'campagn'),
            ],
        ])->validate();

        return [
            'campaign' => collect($validated)->only([
                'title', 'slug', 'description', 'instructions', 'open_from', 'open_until', 'event_at', 'status',
            ])->all(),
            'question_ids' => array_values(array_unique($validated['question_ids'])),
        ];
    }
}

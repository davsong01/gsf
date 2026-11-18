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
            'status' => ['required', 'in:draft,active,closed'],
            'question_ids' => ['required', 'array', 'min:1'],
            'question_ids.*' => [
                'integer',
                Rule::exists('stakeholder_report_questions', 'id')->where('module_type', 'campagn'),
            ],
        ])->validate();

        return [
            'campaign' => collect($validated)->only([
                'title', 'slug', 'description', 'instructions', 'open_from', 'open_until', 'status',
            ])->all(),
            'question_ids' => array_values(array_unique($validated['question_ids'])),
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Stakeholder;
use App\Services\AppraisalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StakeholderAppraisalController extends Controller
{
    public function __construct(protected AppraisalService $appraisalService)
    {
    }

    public function index()
    {
        $user = Auth::guard('stakeholder')->user();
        $access = $this->appraisalService->dashboardAccess($user);

        return view('stakeholder.appraisal.index', [
            'user' => $user,
            'access' => $access,
            'summary' => $this->appraisalService->summary($user),
        ]);
    }

    public function my()
    {
        $user = Auth::guard('stakeholder')->user();

        if (! $this->appraisalService->canAccessMode($user, 'my_appraisal')) {
            return back()->with('error', 'You are not authorized to access your appraisal form.');
        }

        $appraisal = $this->appraisalService->selfAppraisalFor($user);
        $formPrefix = $this->appraisalService->appraisalFormPrefix($user);

        return view('stakeholder.appraisal.my', [
            'user' => $user,
            'access' => $this->appraisalService->dashboardAccess($user),
            'sections' => $this->appraisalService->structureForMode($user, 'my', false, $formPrefix),
            'summary' => $this->appraisalService->summary($user),
            'appraisal' => $appraisal,
            'answers' => $this->appraisalService->loadSelfAnswers($appraisal),
            'prefillData' => $this->appraisalService->selfAppraisalPrefillData($user),
            'instructionProfile' => appraisalInstructionProfile($this->appraisalService->appraisalFormPrefix($user)),
            'pageTitle' => 'Self Appraisal',
        ]);
    }

    public function evaluations()
    {
        $user = Auth::guard('stakeholder')->user();

        if (! $this->appraisalService->canAccessMode($user, 'evaluations')) {
            return back()->with('error', 'You are not authorized to view evaluations.');
        }

        $targets = $this->appraisalService->evaluationTargets($user)
            ->map(function (Stakeholder $target) {
                $target->setRelation('published_appraisal', $this->appraisalService->appraisalForEvaluation($target));

                return $target;
            })
            ->sortBy(function (Stakeholder $target) {
                $isFilled = (($target->published_appraisal?->self_status ?? 'draft') === 'published') ? 0 : 1;

                return sprintf('%d|%s', $isFilled, mb_strtolower($target->name ?? ''));
            })
            ->values();

        return view('stakeholder.appraisal.evaluations', [
            'user' => $user,
            'access' => $this->appraisalService->dashboardAccess($user),
            'summary' => $this->appraisalService->summary($user),
            'targets' => $targets,
            'pageTitle' => 'Evaluations',
        ]);
    }

    public function evaluate(Stakeholder $stakeholder)
    {
        $user = Auth::guard('stakeholder')->user();

        if (! $this->appraisalService->canAccessMode($user, 'evaluations')) {
            return back()->with('error', 'You are not authorized to evaluate officers.');
        }

        $targetIds = $this->appraisalService->evaluationTargets($user)->pluck('id')->all();

        if (! in_array($stakeholder->id, $targetIds, true)) {
            return back()->with('error', 'This officer is not available for your evaluation scope.');
        }

        $appraisal = $this->appraisalService->appraisalForEvaluation($stakeholder);
        $audience = $this->appraisalService->evaluatorAudience($user, $stakeholder);
        $evaluationLocked = ($appraisal?->evaluation_status ?? 'draft') === 'published';
        $formPrefix = $this->appraisalService->appraisalFormPrefix($stakeholder);

        return view('stakeholder.appraisal.evaluate', [
            'user' => $user,
            'target' => $stakeholder,
            'access' => $this->appraisalService->dashboardAccess($user),
            'summary' => $this->appraisalService->summary($user),
            'selfSections' => $this->appraisalService->structureForMode($stakeholder, 'my', false, $formPrefix),
            'evaluationSections' => $this->appraisalService->structureForMode($user, 'evaluations', false, $formPrefix),
            'appraisal' => $appraisal,
            'selfAnswers' => $appraisal ? $this->appraisalService->loadSelfAnswers($appraisal) : collect(),
            'evaluationAnswers' => $appraisal ? $this->appraisalService->loadAnswersForAudience($appraisal, $audience) : collect(),
            'audience' => $audience,
            'evaluationAuthorityLabel' => $this->appraisalService->evaluationAuthorityLabel($stakeholder),
            'instructionProfile' => appraisalInstructionProfile($this->appraisalService->appraisalFormPrefix($stakeholder)),
            'evaluationPrefillData' => $this->appraisalService->evaluationPrefillData($user, $stakeholder),
            'pageTitle' => 'Evaluate ' . $stakeholder->name,
            'selfEditable' => false,
            'canSubmitSelf' => false,
            'evaluationEditable' => (bool) $appraisal && ! $evaluationLocked,
            'canSubmitEvaluation' => (bool) $appraisal && ! $evaluationLocked,
            'appraisalMissing' => ! (bool) $appraisal,
            'evaluationLocked' => $evaluationLocked,
        ]);
    }

    public function saveMyAppraisal(Request $request)
    {
        $user = Auth::guard('stakeholder')->user();

        if (! $this->appraisalService->canAccessMode($user, 'my_appraisal')) {
            return back()->with('error', 'You are not authorized to save this appraisal.');
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,published',
            'answers' => 'nullable|array',
        ]);

        $appraisal = $this->appraisalService->selfAppraisalFor($user);

        if ($appraisal->self_status === 'published') {
            return back()->with('error', 'This appraisal has already been published and cannot be edited again.');
        }

        $validated['answers'] = $this->appraisalService->prepareSubmissionAnswers(
            $user,
            $request,
            'my',
            $validated['status'],
            $appraisal
        );

        $this->appraisalService->saveSelfAppraisal($user, $validated, $validated['status']);

        if ($validated['status'] === 'published') {
            return redirect()
                ->route('stakeholders.dashboard')
                ->with('message', 'Appraisal Completed, thank you!');
        }

        return redirect()
            ->route('stakeholders.appraisal.my')
            ->with('message', 'Your appraisal draft has been saved.');
    }

    public function saveEvaluation(Request $request, Stakeholder $stakeholder)
    {
        $user = Auth::guard('stakeholder')->user();

        if (! $this->appraisalService->canAccessMode($user, 'evaluations')) {
            return back()->with('error', 'You are not authorized to save evaluations.');
        }

        $targetIds = $this->appraisalService->evaluationTargets($user)->pluck('id')->all();

        if (! in_array($stakeholder->id, $targetIds, true)) {
            return back()->with('error', 'This officer is not available for your evaluation scope.');
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,published',
            'answers' => 'nullable|array',
        ]);

        $appraisal = $this->appraisalService->selfAppraisalFor($stakeholder);

        if (($appraisal->evaluation_status ?? 'draft') === 'published') {
            return back()->with('error', 'This evaluation has already been published and cannot be edited again.');
        }

        $validated['answers'] = $this->appraisalService->prepareSubmissionAnswers(
            $user,
            $request,
            'evaluations',
            $validated['status'],
            $appraisal,
            $stakeholder
        );

        $this->appraisalService->saveEvaluation($user, $stakeholder, $validated, $validated['status']);

        return redirect()
            ->route('stakeholders.appraisal.evaluations.show', $stakeholder)
            ->with('message', $validated['status'] === 'published' ? 'The evaluation has been published.' : 'The evaluation draft has been saved.');
    }

    public function appraisee()
    {
        return redirect()->route('stakeholders.appraisal.my');
    }

    public function appraiser()
    {
        return redirect()->route('stakeholders.appraisal.evaluations');
    }
}

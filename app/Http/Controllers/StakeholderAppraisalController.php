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

        return view('stakeholder.appraisal.my', [
            'user' => $user,
            'access' => $this->appraisalService->dashboardAccess($user),
            'sections' => $this->appraisalService->structureForMode($user, 'my'),
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

        $targets = $this->appraisalService->evaluationCandidates($user);

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

        if (! $appraisal) {
            return back()->with('error', 'This officer has not published a self appraisal yet.');
        }

        $audience = $this->appraisalService->evaluatorAudience($user, $stakeholder);

        return view('stakeholder.appraisal.evaluate', [
            'user' => $user,
            'target' => $stakeholder,
            'access' => $this->appraisalService->dashboardAccess($user),
            'summary' => $this->appraisalService->summary($user),
            'selfSections' => $this->appraisalService->structureForMode($stakeholder, 'my'),
            'evaluationSections' => $this->appraisalService->structureForMode($user, 'evaluations'),
            'appraisal' => $appraisal,
            'selfAnswers' => $this->appraisalService->loadSelfAnswers($appraisal),
            'evaluationAnswers' => $this->appraisalService->loadAnswersForAudience($appraisal, $audience),
            'audience' => $audience,
            'evaluationAuthorityLabel' => $this->appraisalService->evaluationAuthorityLabel($stakeholder),
            'instructionProfile' => appraisalInstructionProfile($this->appraisalService->appraisalFormPrefix($stakeholder)),
            'pageTitle' => 'Evaluate ' . $stakeholder->name,
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

        $validated['answers'] = $this->appraisalService->prepareSubmissionAnswers(
            $user,
            $request,
            'evaluations',
            $validated['status'],
            $this->appraisalService->appraisalForEvaluation($stakeholder),
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

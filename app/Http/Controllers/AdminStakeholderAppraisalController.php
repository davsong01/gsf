<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Stakeholder;
use App\Models\Zone;
use App\Services\AppraisalService;
use App\Services\ExcelService;
use Illuminate\Http\Request;
use Pdf;

class AdminStakeholderAppraisalController extends Controller
{
    public function __construct(protected AppraisalService $appraisalService)
    {
    }

    public function index(Request $request)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $query = Stakeholder::query()
            ->with(['role', 'designation', 'appraisal.evaluator'])
            ->whereHas('role', function ($query) {
                $query->where('slug', '!=', 'chapter-representative');
            })
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = trim($request->input('search'));

                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('field_id'), function ($builder) use ($request) {
                $builder->where('field_id', $request->input('field_id'));
            })
            ->when($request->filled('zone_id'), function ($builder) use ($request) {
                $builder->where('zone_id', $request->input('zone_id'));
            })
            ->when($request->filled('self_status'), function ($builder) use ($request) {
                $builder->whereHas('appraisal', function ($appraisalQuery) use ($request) {
                    $appraisalQuery->where('self_status', $request->input('self_status'));
                });
            })
            ->when($request->filled('evaluation_status'), function ($builder) use ($request) {
                $builder->whereHas('appraisal', function ($appraisalQuery) use ($request) {
                    $appraisalQuery->where('evaluation_status', $request->input('evaluation_status'));
                });
            })
            ->orderByRaw("CASE WHEN EXISTS (
                SELECT 1
                FROM stakeholder_appraisals sa
                WHERE sa.appraisee_id = stakeholders.id
                AND sa.self_status = 'published'
            ) THEN 0 ELSE 1 END")
            ->orderBy('name');

        $stakeholders = (clone $query)
            ->paginate(100)
            ->withQueryString();

        $filteredTotal = (clone $query)->count();
        $appraisedCount = (clone $query)
            ->whereHas('appraisal', function ($appraisalQuery) {
                $appraisalQuery->where('self_status', 'published');
            })
            ->count();

        $evaluatedCount = (clone $query)
            ->whereHas('appraisal', function ($appraisalQuery) {
                $appraisalQuery->where('evaluation_status', 'published');
            })
            ->count();

        return view('admin.stakeholder_appraisals.index', [
            'stakeholders' => $stakeholders,
            'appraisalService' => $this->appraisalService,
            'appraisedCount' => $appraisedCount,
            'evaluatedCount' => $evaluatedCount,
            'filteredTotal' => $filteredTotal,
            'fields' => Field::orderBy('name')->get(),
            'zones' => Zone::orderBy('name')->get(),
        ]);
    }

    public function pdf(Stakeholder $stakeholder)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $data = $this->appraisalService->appraisalPdfData($stakeholder, null, true);

        $pdf = Pdf::loadView('stakeholder.appraisal.pdf', [
            ...$data,
            'isAdmin' => true,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('appraisal-' . str()->slug($stakeholder->name) . '.pdf');
    }

    public function export(Request $request)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $stakeholders = $this->filteredQuery($request)
            ->with(['role', 'designation', 'field', 'zone', 'chapter', 'appraisal.evaluator', 'appraisal.answers'])
            ->get()
            ->sortBy(function (Stakeholder $stakeholder) {
                $appraisal = $stakeholder->appraisal;
                $isAppraised = ($appraisal?->self_status ?? 'draft') === 'published' ? 0 : 1;
                $isEvaluated = ($appraisal?->evaluation_status ?? 'draft') === 'published' ? 0 : 1;

                return sprintf('%d|%d|%s', $isAppraised, $isEvaluated, mb_strtolower($stakeholder->name ?? ''));
            })
            ->values();

        $sheets = $this->appraisalService->appraisalExportSheets($stakeholders);
        $fileName = 'stakeholder-appraisals-' . now()->format('Y-m-d_His') . '.xlsx';

        return ExcelService::downloadMultipleSheetsWithHeaders($sheets, $fileName);
    }

    public function editSelf(Stakeholder $stakeholder)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        return $this->editEvaluation($stakeholder);
    }

    public function updateSelf(Request $request, Stakeholder $stakeholder)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,published',
            'answers' => 'nullable|array',
        ]);

        $appraisal = $this->appraisalService->selfAppraisalFor($stakeholder);

        $validated['answers'] = $this->appraisalService->prepareSubmissionAnswers(
            $stakeholder,
            $request,
            'my',
            $validated['status'],
            $appraisal
        );

        $this->appraisalService->saveSelfAppraisal($stakeholder, $validated, $validated['status']);

        return redirect()
            ->route('stakeholderappraisals.self.edit', $stakeholder)
            ->with('message', 'Self appraisal updated successfully.');
    }

    public function editEvaluation(Stakeholder $stakeholder)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $appraisal = $this->appraisalService->selfAppraisalFor($stakeholder);
        $evaluator = $appraisal?->evaluator ?: $stakeholder;
        $audience = $appraisal
            ? $this->appraisalService->evaluatorAudience($evaluator, $stakeholder)
            : AppraisalService::AUDIENCE_EVALUATE;
        $formPrefix = $this->appraisalService->appraisalFormPrefix($stakeholder);

        return view('admin.stakeholder_appraisals.view', [
            'user' => $stakeholder,
            'target' => $stakeholder,
            'isAdmin' => true,
            'access' => $this->appraisalService->dashboardAccess($evaluator),
            'summary' => $this->appraisalService->summary($stakeholder),
            'selfSections' => $this->appraisalService->structureForMode($stakeholder, 'my', true, $formPrefix),
            'evaluationSections' => $this->appraisalService->structureForMode($evaluator, 'evaluations', true, $formPrefix),
            'appraisal' => $appraisal,
            'selfAnswers' => $this->appraisalService->loadSelfAnswers($appraisal),
            'evaluationAnswers' => $this->appraisalService->loadAnswersForAudience($appraisal, $audience),
            'audience' => $audience,
            'evaluationAuthorityLabel' => $this->appraisalService->evaluationAuthorityLabel($stakeholder),
            'instructionProfile' => appraisalInstructionProfile($this->appraisalService->appraisalFormPrefix($stakeholder)),
            'evaluationPrefillData' => $this->appraisalService->evaluationPrefillData($evaluator, $stakeholder),
            'pageTitle' => 'View Appraisal: ' . $stakeholder->name,
            'backUrl' => route('stakeholderappraisals.index'),
            'formModeLabel' => 'Admin Review',
            'selfEditable' => true,
            'selfFormAction' => route('stakeholderappraisals.self.update', $stakeholder),
            'canSubmitSelf' => true,
            'evaluationEditable' => true,
            'formAction' => route('stakeholderappraisals.evaluation.update', $stakeholder),
            'canSubmitEvaluation' => true,
        ]);
    }

    public function updateEvaluation(Request $request, Stakeholder $stakeholder)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,published',
            'answers' => 'nullable|array',
        ]);

        $appraisal = $this->appraisalService->selfAppraisalFor($stakeholder);

        $evaluator = $appraisal->evaluator ?: $stakeholder;

        $validated['answers'] = $this->appraisalService->prepareSubmissionAnswers(
            $evaluator,
            $request,
            'evaluations',
            $validated['status'],
            $appraisal,
            $stakeholder
        );

        $this->appraisalService->saveEvaluation($evaluator, $stakeholder, $validated, $validated['status']);

        return redirect()
            ->route('stakeholderappraisals.evaluation.edit', $stakeholder)
            ->with('message', 'Evaluation updated successfully.');
    }

    public function unlockSelf(Stakeholder $stakeholder)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $appraisal = $stakeholder->appraisal;

        if (! $appraisal || $appraisal->self_status !== 'published') {
            return back()->with('message', 'Self appraisal is already open.');
        }

        $this->appraisalService->unlockSelfAppraisal($appraisal);

        return back()->with([
            'message' => 'Self appraisal reopened successfully.',
            'appraisal_status_label' => 'Reopened',
            'appraisal_status_scope' => 'self',
            'appraisal_status_stakeholder_id' => $stakeholder->id,
        ]);
    }

    public function unlockEvaluation(Stakeholder $stakeholder)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $appraisal = $stakeholder->appraisal;

        if (! $appraisal || $appraisal->evaluation_status !== 'published') {
            return back()->with('message', 'Evaluation is already open.');
        }

        $this->appraisalService->unlockEvaluation($appraisal);

        return back()->with([
            'message' => 'Evaluation reopened successfully.',
            'appraisal_status_label' => 'Reopened',
            'appraisal_status_scope' => 'evaluation',
            'appraisal_status_stakeholder_id' => $stakeholder->id,
        ]);
    }

    public function remind(Stakeholder $stakeholder)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $queued = $this->appraisalService->queueAppraisalReminderEmails($stakeholder);

        if ($queued === 0) {
            return back()->with('message', 'No pending appraisal reminders for this stakeholder.');
        }

        return back()->with('message', "{$queued} reminder email(s) queued successfully.");
    }

    public function bulkRemind(Request $request)
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|in:remind',
            'stakeholders' => 'required|array|min:1',
            'stakeholders.*' => 'integer|exists:stakeholders,id',
        ]);

        $stakeholders = Stakeholder::query()
            ->with(['appraisal.evaluator', 'role', 'designation', 'field', 'zone', 'chapter'])
            ->whereIn('id', $validated['stakeholders'])
            ->get();

        $queued = 0;

        foreach ($stakeholders as $stakeholder) {
            $queued += $this->appraisalService->queueAppraisalReminderEmails($stakeholder, $stakeholder->appraisal);
        }

        return back()->with('message', "{$queued} reminder email(s) queued successfully.");
    }

    protected function filteredQuery(Request $request)
    {
        return Stakeholder::query()
            ->with(['role', 'designation', 'appraisal.evaluator'])
            ->whereHas('role', function ($query) {
                $query->where('slug', '!=', 'chapter-representative');
            })
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = trim($request->input('search'));

                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('field_id'), function ($builder) use ($request) {
                $builder->where('field_id', $request->input('field_id'));
            })
            ->when($request->filled('zone_id'), function ($builder) use ($request) {
                $builder->where('zone_id', $request->input('zone_id'));
            })
            ->when($request->filled('self_status'), function ($builder) use ($request) {
                $builder->whereHas('appraisal', function ($appraisalQuery) use ($request) {
                    $appraisalQuery->where('self_status', $request->input('self_status'));
                });
            })
            ->when($request->filled('evaluation_status'), function ($builder) use ($request) {
                $builder->whereHas('appraisal', function ($appraisalQuery) use ($request) {
                    $appraisalQuery->where('evaluation_status', $request->input('evaluation_status'));
                });
            })
            ->orderByRaw("CASE WHEN EXISTS (
                SELECT 1
                FROM stakeholder_appraisals sa
                WHERE sa.appraisee_id = stakeholders.id
                AND sa.self_status = 'published'
            ) THEN 0 ELSE 1 END")
            ->orderBy('name');
    }
}

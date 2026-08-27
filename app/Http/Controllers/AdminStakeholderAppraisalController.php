<?php

namespace App\Http\Controllers;

use App\Models\Stakeholder;
use App\Services\AppraisalService;
use Illuminate\Http\Request;

class AdminStakeholderAppraisalController extends Controller
{
    public function __construct(protected AppraisalService $appraisalService)
    {
    }

    public function index()
    {
        if ((auth()->user()?->role ?? null) != 1) {
            abort(403);
        }

        $stakeholders = Stakeholder::query()
            ->with(['role', 'designation', 'appraisal'])
            ->whereHas('role', function ($query) {
                $query->where('slug', '!=', 'chapter-representative');
            })
            ->orderBy('name')
            ->get();

        return view('admin.stakeholder_appraisals.index', [
            'stakeholders' => $stakeholders,
            'appraisalService' => $this->appraisalService,
        ]);
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

        return back()->with('message', 'Self appraisal reopened successfully.');
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

        return back()->with('message', 'Evaluation reopened successfully.');
    }
}

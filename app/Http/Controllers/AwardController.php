<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Chapter;
use App\Services\AwardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AwardController extends Controller
{

    // 2. Type-hint the service in the constructor
    public function __construct(protected AwardService $awardService)
    {
    }

    public function webhook(Request $request){
        Log::info('Raw Incoming Google Webhook Data:', $request->all());

        $this->awardService->storeFromGoogle($request->all());

        return response()->json([], 200);
    }
    /**
     * Display a listing of the resource.
     */

    public function goAwardEntries(Request $request){
        $user = auth()->user() ?? auth()->guard('stakeholder')->user();
    
        $isAdmin = (!empty($user->role) && $user->role == 1);
        $request->merge([
            'type' => 'go'
        ]);

        $entries = $this->awardService->index($request, $user, 'go', $isAdmin);
        $title = 'General Overseer (G.O.) Award Submissions';
        return view('admin.awards.index', array_merge(compact('isAdmin','entries', 'user', 'title')));
    }

    public function etfAwardEntries(Request $request){
        $adminDetails = isAdmin();
        $isAdmin = $adminDetails['status'];
        $user = $adminDetails['user'];

        $request->merge([
            'type' => 'etf'
        ]);

        $entries = $this->awardService->index($request, $user, 'etf', $isAdmin);
        $title = 'EducationTrust Fund (E.T.F.) Award Submissions';
        
        return view('admin.awards.index', array_merge(compact('isAdmin','entries', 'user', 'title')));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Award $award)
    {
        $adminDetails = isAdmin();
        $isAdmin = $adminDetails['status'];
        $user = $adminDetails['user'];
        
        $award->load('entries');
        $chapters = Chapter::select('id', 'name')->get();
        
        return view('admin.awards.show', compact('isAdmin', 'isAdmin', 'award', 'chapters', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Award $award)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Award $award)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Award $award)
    {
        //
    }
}

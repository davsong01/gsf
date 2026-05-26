<?php

namespace App\Http\Controllers;

use App\Models\Award;
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

        $entries = $this->awardService->index($request, $user, 'go', $isAdmin);
        
        return view('admin.awards.index', array_merge(compact('isAdmin','entries', 'user')));
    }

    public function etfAwardEntries(){

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
        //
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

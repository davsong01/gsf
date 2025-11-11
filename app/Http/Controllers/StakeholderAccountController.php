<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Reports;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Models\StakeholderReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StakeholderAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function dashboard()
    {
        dd('dashboard');
        $count = 1;
        // return redirect(route('stakeholder.login'));
        if (!auth::guard('stakeholder')->check()) return redirect(route('stakeholders.login'));
        $role = Auth::guard('stakeholder')->user()->role;

        if (in_array($role, ['Chapter President', 'Chapter Secretary', 'Chapter Financial Secretary'])) {
            $reports = StakeholderReport::whereChapterId(Auth::guard('stakeholder')->user()->chapter_id)->orderBy('created_at', 'desc')->get();
        } elseif ($role == 'Field Pastor') {
            $reports = StakeholderReport::whereFieldId(Auth::guard('stakeholder')->user()->field_id)->orderBy('created_at', 'desc')->get();
        } elseif ($role == 'Zonal Pastor') {
            $reports = StakeholderReport::whereZoneId(Auth::guard('stakeholder')->user()->zone_id)->orderBy('created_at', 'desc')->get();
        } elseif ($role == 'Secretariat') {
            $reports = StakeholderReport::orderBy('created_at', 'desc')->get();
        }

        if ($role == 'Financial Secretary') {
            return redirect(route('stakeholderpayment.index'));
        }

        return view('stakeholder.dashboard', compact('reports', 'count'));
    }

    public function index(Request $request)
    {
        $user = Auth::guard('stakeholder')->user();
        $role = $user->role;

        // Base queries
        $chapters = Chapter::query();
        $zones = Zone::query();
        $fields = Field::query();

        // Scope variables for filtering reports
        $chapterIds = collect();
        $zoneIds = collect();
        $fieldIds = collect();

        /** =====================
         * ROLE-BASED SCOPING
         * ===================== */
        if (in_array($role, ['Chapter President', 'Chapter Secretary', 'Chapter Financial Secretary'])) {
            $chapterIds = collect([$user->chapter_id]);
            $zoneIds = collect([$user->zone_id]);
            $fieldIds = collect([$user->field_id]);
        } elseif ($role === 'Zonal Pastor') {
            $zoneIds = collect([$user->zone_id]);

            // All chapters under this zone
            $chapterIds = Chapter::where('zone_id', $user->zone_id)->pluck('id');

            // Fields that contain this zone
            $fieldIds = Field::whereHas('zones', fn($q) => $q->where('id', $user->zone_id))
                ->pluck('id');
        } elseif ($role === 'Field Pastor') {
            $fieldIds = collect([$user->field_id]);

            // Zones under this field
            $zoneIds = Zone::where('field_id', $user->field_id)->pluck('id');

            // Chapters under all zones in this field
            $chapterIds = Chapter::whereIn('zone_id', $zoneIds)->pluck('id');
        }

        // Secretariat → full access (no scoping)
        elseif ($role === 'Secretariat') {
            $chapterIds = Chapter::pluck('id');
            $zoneIds = Zone::pluck('id');
            $fieldIds = Field::pluck('id');
        }

        /** =====================
         * FILTER REPORTS BY SCOPED IDS
         * ===================== */
        $reports = StakeholderReport::query()
            ->when($chapterIds->isNotEmpty(), fn($q) => $q->whereIn('chapter_id', $chapterIds))
            ->when($zoneIds->isNotEmpty(), fn($q) => $q->whereIn('zone_id', $zoneIds))
            ->when($fieldIds->isNotEmpty(), fn($q) => $q->whereIn('field_id', $fieldIds));
        
        /** =====================
         * DATE FILTERS
         * ===================== */
        if ($request->filled('from_date')) {
            $reports->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $reports->whereDate('created_at', '<=', $request->to_date);
        }

        /** =====================
         * MANUAL FILTERS
         * ===================== */
        if ($request->filled('chapter_filter')) {
            $reports->where('chapter_id', $request->chapter_filter);
        }

        if ($request->filled('zone_filter')) {
            $reports->where('zone_id', $request->zone_filter);
        }

        if ($request->filled('field_filter')) {
            $reports->where('field_id', $request->field_filter);
        }

        /** =====================
         * STATUS FILTERS
         * ===================== */
        if ($request->filled('status_filter')) {
            $statusMap = [
                'field_pending' => ['field_status', 0],
                'field_approved' => ['field_status', 1],
                'field_rejected' => ['field_status', 2],
                'zone_pending' => ['zone_status', 0],
                'zone_approved' => ['zone_status', 1],
                'zone_rejected' => ['zone_status', 2],
                'national_pending' => ['national_status', 0],
                'national_approved' => ['national_status', 1],
                'national_rejected' => ['national_status', 2],
            ];

            if (isset($statusMap[$request->status_filter])) {
                [$column, $value] = $statusMap[$request->status_filter];
                $reports->where($column, $value);
            }
        }

        /** =====================
         * EXECUTE QUERIES
         * ===================== */
        $reports = $reports->with(['chapter', 'zone', 'field'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $chapters = $chapters->whereIn('id', $chapterIds)->orderBy('name')->get();
        $zones = $zones->whereIn('id', $zoneIds)->orderBy('name')->get();
        $fields = $fields->whereIn('id', $fieldIds)->orderBy('name')->get();

        /** =====================
         * REDIRECT FOR FINANCIAL SECRETARY
         * ===================== */
        if ($role === 'Financial Secretary') {
            return redirect(route('stakeholderpayment.index'));
        }
        return view('stakeholder.index', compact('reports', 'chapters', 'fields', 'zones'));
    }

    public function profile()
    {
        return view('stakeholder.profile');
    }

    public function saveProfile(Request $request)
    {
        //Handle Password
        if ($request['password']) {
			$password = Hash::make($request['password']);
		} else {
			$password = Hash::make($request['12345@GSF2021']);
		}

        //Handle all signatures
        if($request->has('signature')){
            if (file_exists(base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->signature))
            unlink( base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->signature);
            $signaturefilename = date('d-M-Y-s') . '-' . pathinfo($request->signature->getClientOriginalName(), PATHINFO_FILENAME);
            $signatureextension = $request->signature->getClientOriginalExtension();
            Storage::disk('uploads')->putFileAs('signatures', new File($request->signature->path()), $signaturefilename.'.'.$signatureextension);
            
            $signature = $signaturefilename . '.' . $signatureextension;
        }else{
            $signature = Auth::guard('stakeholder')->user()->signature;
        }

        if($request->has('gen_sec_signature')){
            if (!is_null(Auth::guard('stakeholder')->user()->gen_sec_signature) && file_exists(base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->gen_sec_signature))
            unlink( base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->gen_sec_signature);
            $gen_sec_signaturefilename = date('d-M-Y-s') . '-' . pathinfo($request->gen_sec_signature->getClientOriginalName(), PATHINFO_FILENAME);
            $gen_sec_signatureextension = $request->gen_sec_signature->getClientOriginalExtension();
            Storage::disk('uploads')->putFileAs('signatures', new File($request->gen_sec_signature->path()), $gen_sec_signaturefilename.'.'.$gen_sec_signatureextension);
            
            $gen_sec_signature = $gen_sec_signaturefilename . '.' . $gen_sec_signatureextension;
        }else{
            $gen_sec_signature = Auth::guard('stakeholder')->user()->gen_sec_signature;
        }

        if($request->has('fin_sec_signature')){
            if (!is_null(Auth::guard('stakeholder')->user()->fin_sec_signature) && file_exists(base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->fin_sec_signature))
            unlink( base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->fin_sec_signature);
            $fin_sec_signaturefilename = date('d-M-Y-s') . '-' . pathinfo($request->fin_sec_signature->getClientOriginalName(), PATHINFO_FILENAME);
            $fin_sec_signatureextension = $request->fin_sec_signature->getClientOriginalExtension();
            Storage::disk('uploads')->putFileAs('signatures', new File($request->fin_sec_signature->path()), $fin_sec_signaturefilename.'.'.$fin_sec_signatureextension);
            
            $fin_sec_signature = $fin_sec_signaturefilename . '.' . $fin_sec_signatureextension;
        }else{
            $fin_sec_signature = Auth::guard('stakeholder')->user()->fin_sec_signature;
        }
       
        if($request->has('evang_sec_signature')){
            if (!is_null(Auth::guard('stakeholder')->user()->evang_sec_signature) && file_exists(base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->evang_sec_signature))
            unlink( base_path() . '/uploads/signatures' . '/' . Auth::guard('stakeholder')->user()->evang_sec_signature);

            $evang_sec_signaturefilename = date('d-M-Y-s') . '-' . pathinfo($request->evang_sec_signature->getClientOriginalName(), PATHINFO_FILENAME);
            $evang_sec_signatureextension = $request->evang_sec_signature->getClientOriginalExtension();
            Storage::disk('uploads')->putFileAs('signatures', new File($request->evang_sec_signature->path()), $evang_sec_signaturefilename.'.'.$evang_sec_signatureextension);
            
            $evang_sec_signature = $evang_sec_signaturefilename . '.' . $evang_sec_signatureextension;
            
        }else{
            $evang_sec_signature = Auth::guard('stakeholder')->user()->evang_sec_signature;
        }
        Auth::guard('stakeholder')->user()->name = $request->name;
        Auth::guard('stakeholder')->user()->gen_sec_signature = $gen_sec_signature;
        Auth::guard('stakeholder')->user()->password = $password;
        Auth::guard('stakeholder')->user()->signature = $signature;
        Auth::guard('stakeholder')->user()->fin_sec_signature = $fin_sec_signature;
        Auth::guard('stakeholder')->user()->evang_sec_signature = $evang_sec_signature;
        Auth::guard('stakeholder')->user()->phone = $request->phone;
        Auth::guard('stakeholder')->user()->email = $request->email;
        Auth::guard('stakeholder')->user()->day = $request->day;
        Auth::guard('stakeholder')->user()->month = $request->month;
        Auth::guard('stakeholder')->user()->year = $request->year;

        Auth::guard('stakeholder')->user()->save();
               
        return back()->with('message', 'Update Successful');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

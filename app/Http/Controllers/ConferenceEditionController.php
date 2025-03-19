<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Donation;
use App\Models\Material;
use Illuminate\Http\Request;
use App\Models\ConferenceEdition;

class ConferenceEditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->role == 1) {
            return view('conference_management.admin.editions.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            "status" => "required",
            "conference_theme" => "required",
            "registration_fee" => "required",
            "official_email" => "required",
            "new_alumni_registration_fee" => "required|numeric",
            "start_date" => "required",
            "alumni_registration_fee" => "required",
            "end_date" => "required",
            "close_registration" => "required",
            "random_hostel" => "required",
            "random_foodstand" => "required",
            "reg_prefix" => "required",
            "conference_overview" => "required",
            "PAYSTACK_SECRET_KEY" => "required",
            "PAYSTACK_PUBLIC_KEY" => "required",
            "MERCHANT_EMAIL" => "required",
        ]);
        
        // Check if existing active
        $active = ConferenceEdition::where('status','active')->count();
        
        if(isset($active) && $active > 0){
            $data['status'] = 'inactive';
        }

        ConferenceEdition::create($data);
        return redirect(route('conferenceeditions.index'))->with('message', 'Operation Successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */
    public function show(ConferenceEdition $conferenceEdition, $id)
    {
        if (auth()->user()->role == 1) {
            $edition = ConferenceEdition::with(['payments','donations'])->find($id);
            
            $registered_participants = $edition->payments->count();
            $pending_registration = $edition->payments->where('registration_status', 'Pending')->count();
            $total = $edition->payments->sum('amount_paid');
            $completed_registration = $edition->payments->where('registration_status', 'Complete')->count();
            $donations = Donation::where('conference_edition_id',$id)->sum('amount');
            $materials = Material::where('conference_edition_id',$id)->count();
            
            return view('conference_management.admin.index', compact('registered_participants', 'pending_registration', 'completed_registration', 'total', 'donations', 'materials', 'edition'));
        }
    }

    public function chart(ConferenceEdition $id)
    {
        if (auth()->user()->role == 1) {
            $data = User::join('payments', 'payments.user_id', '=', 'users.id')
                ->where('payments.purpose', 'conference')
                ->where('payments.registration_status', 'Complete')
                ->leftJoin('hostels', 'hostels.id', '=', 'payments.hostel_id')
                ->leftJoin('food', 'food.id', '=', 'payments.food_id')
                ->leftJoin('chapters', 'chapters.id', '=', 'users.chapter_id')
                ->where('payments.conference_edition_id', $id->id)
                ->where('users.role', '!=', 1)
                ->select(
                    'users.family_id',
                    'payments.transid',
                    'payments.registration_status',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'chapters.name as chapter',
                    'chapters.id as chapter_id',
                    'payments.created_at as registration_date',
                    'payments.amount_paid',
                    'payments.level',
                    'hostels.name as hostel',
                    'food.name as foodstand',
                    'payments.purpose',
                    'payments.location'
                )
                ->orderBy('users.created_at', 'desc')
                ->get();

            // Merge "Participant" and "Moderator" as "Participants"
            $data->transform(function ($item) {
                if (in_array(strtolower(trim($item->level)), ['participant', 'moderator'])) {
                    $item->level = 'Participants';
                }
                return $item;
            });

            // Get unique chapters
            $formattedLabels = [];
            $formattedData = [
                'label' => 'Participants',
                'data' => [],
                'backgroundColor' => 'rgba(54, 162, 235, 0.6)' // Static color for clarity
            ];

            foreach ($data->pluck('chapter')->filter()->unique()->values() as $chapter) {
                $count = $data->filter(function ($item) use ($chapter) {
                    return strtolower(trim($item->chapter)) === strtolower(trim($chapter)) &&
                        strtolower(trim($item->level)) === 'participants';
                })->count();

                // Append count to chapter label (e.g., "Lagos (50)")
                $formattedLabels[] = "{$chapter} - ({$count})";
                $formattedData['data'][] = $count;
            }

            return response()->json([
                'labels' => $formattedLabels,
                'datasets' => [$formattedData]
            ]);
        }
    }

    public function clone(ConferenceEdition $conferenceEdition, $id)
    {
        if (auth()->user()->role == 1) {
            $edition = ConferenceEdition::find($id);
            $new = $edition->replicate();
            $new->status = 'inactive';
            $new->conference_theme = $edition->conference_theme . '_copy';
            $new->save();
           
            return back()->with('message','Copy Succesfull');
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */
    public function edit(ConferenceEdition $conferenceEdition, $id)
    {
        if (auth()->user()->role == 1) {
            $edition = ConferenceEdition::find($id);
            return view('conference_management.admin.editions.edit', compact('edition'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConferenceEdition $conferenceEdition)
    {
        $edition = ConferenceEdition::find($request->id);
        
        if($request->has('ban')){
			$request['banner'] = $this->uploadImage($request->ban, 'frontend/img/site');
        }

        if ($request->has('logo')) {
			$request['conference_logo'] = $this->uploadImage($request->logo, 'frontend/img/site');
        }

        if ($request->has('favicon')) {
            $request['conference_favicon'] = $this->uploadImage($request->favicon, 'frontend/img/site');
        }
        
        $edition->update($request->except(['ban','logo','favicon']));
        return back()->with('message', 'Operation Successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $edition = ConferenceEdition::find($id);
        $edition->delete();
        return back()->with('message', 'Operation Successful');
    }
}

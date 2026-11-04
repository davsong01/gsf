<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Donation;
use App\Models\Material;
use App\Models\Ministry;
use Illuminate\Http\Request;
use App\Models\ConferenceFaq;
use App\Models\ConferencePlan;
use App\Models\PaymentProvider;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSpeaker;
use App\Services\DynamicImageGeneratorService;

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
            $paymentproviders = PaymentProvider::where('status', 'active')->get();
            $ministries = Ministry::where('status', 'active')->latest()->get();
            $faqs = ConferenceFaq::where('status', 1)->orderBy('display_order')->get();

            return view('conference_management.admin.editions.create', compact('paymentproviders', 'ministries', 'faqs'));
        }
    }

    public function edit(ConferenceEdition $conferenceEdition, $id)
    {
        if (auth()->user()->role == 1) {
            $edition = ConferenceEdition::find($id);
            $paymentproviders = PaymentProvider::where('status', 'active')->get();
            $ministries = Ministry::where('status', 'active')->latest()->get();
            $faqs = ConferenceFaq::where('status', 1)->orderBy('display_order')->get();
            $speakers = ConferenceSpeaker::where('status', 1)->latest()->get();

            return view('conference_management.admin.editions.edit', compact('edition', 'paymentproviders', 'ministries', 'faqs','speakers'));
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
        $request->validate([
            'ban' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $data = $request->except(['ban', 'banner', 'logo', 'favicon', 'conference_logo', 'conference_favicon']);
        if ($request->hasFile('ban')) {
            $data['banner'] = $this->uploadImage($request->file('ban'), 'frontend/img/site');
        }

        // Check if existing active
        $active = ConferenceEdition::where('status','active')->count();

        if(isset($active) && $active > 0){
            $data['status'] = 'inactive';
        }

        ConferenceEdition::create($data);
        return redirect(route('conferencemanagement.index'))->with('message', 'Operation Successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */
    public function show(ConferenceEdition $conferenceEdition, $id)
    {
        if (auth()->user()->role != 1) {
            abort(403, 'Conference edition management is restricted to admin users.');
        }

        $edition = ConferenceEdition::with(['transactions', 'donations'])
            ->findOrFail($id);
        
        $transactions = $edition->transactions
            ->unique(fn ($t) => $t->provider_reference ?? 'null_' . $t->id)
            ->values();

        $complete = $transactions->where('registration_status', 'Complete');
        $pending = $transactions->where('registration_status', 'Pending');

        // Moderators
        $moderators = $complete->where('registration_user_type', 'moderator');

        $registered_moderators_count = $moderators->count();
        $total_slots = $moderators->sum('slot');
        $slots_filled = $moderators->sum('slot_filled');
        $unallocated_slots = $total_slots - $slots_filled;

        // Registrations
        $pending_registration = $pending->count();
        $completed_registration = $complete->count();

        // Financials
        $total = $complete
            ->filter(fn ($t) => $t->isSystemPayment())
            ->where('status', 'Complete')
            // ->where('purpose', 'conference')
            ->sum('total_amount');

        $donations = $edition->donations->sum('amount');

        // Materials
        $materials = Material::where('conference_edition_id', $id)->count();

        $plans = ConferencePlan::with('registered')
            ->where('conference_edition_id', $edition->id)
            ->where('status', 1)
            ->get();

        return view('conference_management.admin.index', compact(
            'plans',
            'pending_registration',
            'completed_registration',
            'total',
            'donations',
            'materials',
            'edition',
            'registered_moderators_count',
            'total_slots',
            'unallocated_slots',
            'slots_filled'
        ));
    }

    public function chart(ConferenceEdition $id)
    {
        if (auth()->user()->role == 1) {
            $data = User::join('transactions', 'transactions.user_id', '=', 'users.id')
                ->where('transactions.purpose', 'conference')
                ->where('transactions.registration_status', 'Complete')
                ->leftJoin('hostels', 'hostels.id', '=', 'transactions.hostel_id')
                ->leftJoin('food', 'food.id', '=', 'transactions.food_id')
                ->leftJoin('chapters', 'chapters.id', '=', 'users.chapter_id')
                ->leftJoin('zones', 'zones.id', '=', 'chapters.zone_id')
                ->leftJoin('fields', 'fields.id', '=', 'chapters.field_id')
                ->where('transactions.conference_edition_id', $id->id)
                ->where('users.role', '!=', 1)
                ->select(
                    'users.family_id',
                    'transactions.transid',
                    'transactions.registration_status',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'chapters.name as chapter',
                    'chapters.id as chapter_id',
                    'zones.name as zone',
                    'fields.name as field',
                    'transactions.created_at as registration_date',
                    'transactions.amount_paid',
                    'transactions.level',
                    'hostels.name as hostel',
                    'food.name as foodstand',
                    'transactions.purpose',
                    'transactions.location'
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

            $buildBreakdown = function (string $property, string $label) use ($data): array {
                $groups = $data
                    ->filter(fn ($item) => strtolower(trim($item->level)) === 'participants')
                    ->groupBy(fn ($item) => trim((string) ($item->{$property} ?? '')) ?: 'Unassigned')
                    ->map(fn ($items) => $items->count())
                    ->sortDesc();

                return [
                    'labels' => $groups->keys()->values()->all(),
                    'datasets' => [[
                        'label' => $label,
                        'data' => $groups->values()->all(),
                    ]],
                ];
            };

            $breakdowns = [
                'chapters' => $buildBreakdown('chapter', 'Registrations by chapter'),
                'zones' => $buildBreakdown('zone', 'Registrations by zone'),
                'fields' => $buildBreakdown('field', 'Registrations by field'),
            ];

            return response()->json([
                'labels' => $breakdowns['chapters']['labels'],
                'datasets' => $breakdowns['chapters']['datasets'],
                'breakdowns' => $breakdowns,
            ]);
        }
    }

    public function clone(ConferenceEdition $conferenceEdition, $id)
    {
        abort_unless(
            auth()->check() && auth()->user()->role == 1 && auth()->user()->conference_role === 'superadmin',
            403
        );

        $edition = ConferenceEdition::findOrFail($id);
        $new = $edition->replicate();
        $new->status = 'inactive';
        $new->conference_theme = $edition->conference_theme . '_copy';
        $new->save();

        return back()->with('message', 'Conference edition cloned successfully.');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConferenceEdition  $conferenceEdition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConferenceEdition $conferenceEdition)
    {
        $request->validate([
            'ban' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $edition = ConferenceEdition::find($request->id);
        $data = $request->except(['ban', 'banner', 'logo', 'favicon', 'conference_logo', 'conference_favicon', 'template_text_type', 'template_text_type_face', 'template_font_size', 'template_left_offset', 'template_top_offset', 'template_color', 'template']);
        if ($request->hasFile('ban')) {
            $data['banner'] = $this->uploadImage($request->file('ban'), 'frontend/img/site');
        }

        $edition->update($data);

        if(!empty($request->template)){
            $generator = new DynamicImageGeneratorService();
            $generator->updateSettings($request, $edition);
        }
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
        abort_unless(
            auth()->check() && auth()->user()->role == 1 && auth()->user()->conference_role === 'superadmin',
            403
        );

        $edition = ConferenceEdition::findOrFail($id);
        $edition->delete();

        return back()->with('message', 'Conference edition deleted successfully.');
    }
}

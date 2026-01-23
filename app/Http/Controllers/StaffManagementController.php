<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\User;
use App\Models\Hostel;
use App\Models\Chapter;
use App\Models\Payment;
use App\Mail\WelcomeMail;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CriticalEmail;
use App\Models\ConferencePlan;
use App\Services\EmailService;
use App\Services\ExcelService;
use App\Services\PaymentService;
use App\Models\ConferenceEdition;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ConferenceUsersImport;
use App\Services\HostelAllocationService;
use Illuminate\Support\Facades\Validator;
use App\Models\TransactionAllocationField;
use App\Services\DynamicImageGeneratorService;
use App\Services\ServicePointAllocationService;
use App\Http\Controllers\CriticalEmailController;

class StaffManagementController extends Controller
{

	public function create(Request $request, $edition)
	{
		$edition = ConferenceEdition::find($edition);
		if (isset($edition) && $edition->status == 'active') {
			if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') {
				$staff = User::where('role', 1)->where('conference_role', 'admin')->get();
				return view('conference_management.admin.staff.create', compact('edition'));
			}
		} else {
			return back()->with('error', 'Conference Edition not active');
		}
	}

	public function edit($id, Request $request)
	{
		$edition = ConferenceEdition::find($request->edition);
		$user = User::find($id);

		if (isset($edition) && $edition->status == 'active') {
			if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') {
				$staff = User::where('role', 1)->where('conference_role', 'admin')->get();
				return view('conference_management.admin.staff.edit', compact('edition', 'user'));
			}
		} else {
			return back()->with('error', 'Conference Edition not active');
		}
	}

	public function store(Request $request)
	{

		$data = $this->validate($request, [
			"name" => "required",
			"email" => "required",
			"phone" => "required",
			"gender" => "required",
			"conference_role" => "required",
			"password" => "nullable"
		]);

		//Handle password
		if ($request['password']) {
			$password = $data['password'];
		} else {
			$password = $request['phone'];
		}

		$password = Hash::make($password);
		//Handle Passport Upload
		if ($request->has('avatar')) {
			$data['passport'] = $this->uploadImage($request->avatar, 'images/passports', 400, 400);
		} else {
			$data['passport'] = NULL;
		}

		$user = User::Create([
			"name" => $data['name'],
			"email" => $data['email'],
			"phone" => $data['phone'],
			"gender" => $data['gender'],
			"passport" => $data['passport'],
			"role" => 1,
			'slug' => Str::slug($data['name']),
			"conference_role" => $data['conference_role'],
			"password" => $password
		]);

		$user->update([
			'family_id' => PaymentService::generateStaffFamilyId($this->edition, $user),
		]);

		return redirect(route('conference.staff', ['edition' => $this->edition]))->with('message', 'Staff successfully created');
	}

	public function update(Request $request, $id)
	{

		$edition = ConferenceEdition::find($request->edition);
		$user = User::find($id);

		if (isset($edition) && $edition->status == 'active') {
			if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') {
				//Handle password
				if ($request['password']) {
					$password = $request['password'];
				} else {
					$password = $request['phone'];
				}

				$request['password'] = Hash::make($password);

				//Handle Passport Upload
				if ($request->has('avatar')) {
					$request['passport'] = $this->uploadImage($request->avatar, 'images/passports', 400, 400);
				} else {
					$request['passport'] = NULL;
				}

				$user->update($request->except(['edition', 'avatar']));
				return redirect(route('conference.staff', ['edition' => $this->edition]))->with('message', 'Staff successfully updated');
			}
		} else {
			return back()->with('error', 'Conference Edition not active');
		}
	}

	public function index($edition = '')
	{
		$count = 1;
		$edition = ConferenceEdition::find($edition);
		if (isset($edition) && $edition->status == 'active') {
			if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') {
				$staff = User::where('role', 1)->get();

				return view('conference_management.admin.staff.index', compact('staff', 'count', 'edition'));
			}
		} else {
			return back()->with('error', 'Conference Edition not active');
		}
	}

	public function destroy(Request $request, $id)
	{
		//stop from accidentally deleted self
		if (auth()->user()->id == $id) {
			return back()->with('error', 'You cannot delete yourself');
		}

		$user = User::where('id', $id)->first();
		//Delete Avatar

		$this->deleteImage($user->passport);
		$user->forceDelete();

		return back()->with('message', 'Staff has been deleted forever');
	}
}

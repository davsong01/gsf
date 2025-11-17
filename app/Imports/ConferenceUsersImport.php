<?php

namespace App\Imports;

use Auth;
use App\User;
use App\Models\Chapter;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Services\HostelAllocationService;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Services\ServicePointAllocationService;

class ConferenceUsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
	use Importable, SkipsFailures;

	private $data;
	private $setting;
	private $type;
	private $prefix;
	private $amount_paid;

	public function __construct($data, $payment=null)
	{
		$this->data = $data;
		$this->payment = $payment;
		$this->setting = $data['setting'] ?? activeConferenceEdition();
		
		switch ($this->data['import_level']) {
			case 'Participant':
				$this->type = 1;
				$details = app('App\Http\Controllers\Controller')->getExtras(1, $this->setting);
				break;
			case 'Choir':
				$this->type = 6;
				$details = app('App\Http\Controllers\Controller')->getExtras(6, $this->setting);
				break;
			case 'Moderator':
				$this->type = 2;
				$details = app('App\Http\Controllers\Controller')->getExtras(1, $this->setting);
				break;
			case 'Alumni':
				$this->type = 3;
				$details = app('App\Http\Controllers\Controller')->getExtras(1, $this->setting);
				break;
			case 'Nec':
				$this->type = 4;
				$details = app('App\Http\Controllers\Controller')->getExtras(1, $this->setting);
				break;
		}
		
		$this->prefix = $details['ledge'];
		$this->amount_paid = $this->setting->registration_fee;
	}

	public function model(array $row)
	{

		$name = trim($row['name']);
		$email = trim($row['email']);
		$phone = trim($row['phone']);
		$level = trim($this->data['import_level']);
		$password = Hash::make(trim($row['phone']));
		$gender = $row['gender'] ?? null;
		$registration_status = $row['registration_status'] ?? 'Pending';
		$slot = $row['slot'] ?? 1;
		$slot_filled = $row['slot_filled'] ?? 1;
		$amount_paid = $row['amount_paid'] ?? $this->amount_paid;
		$payment_type = $row['payment_type'] ?? 'Bulk Upload';
		$uploaded_by = $row['uploaded_by'] ?? Auth::user()->id;
		
		if(!$this->data['chapter_id'] && auth()->user()->isAdmin()){
			$chapter_id = !empty($row['chapter_id']) ? $row['chapter_id'] : $this->getChapterIdByChapterName(trim($row['chapter']));
		}else{
			$chapter_id = $this->data['chapter_id'];
		}


		$data = [
			'name'  => $name,
			'email' => $email,
			'phone' => $phone,
			'level' => $level,
			'type' => $this->type,
			'chapter_id' => $chapter_id ?? null,
			'chapter' => $chapter_id ?? null,
			'gender' => $gender,
			'registration_status' => $registration_status,
			'slot' => $slot,
			'slot_filled' => $slot_filled,
			'amount_paid' => $amount_paid,
			'payment_type' => $payment_type,
			'transid' => app('App\Http\Controllers\Controller')->generateTransactionId(),
			'uploaded_by' => $uploaded_by,
			'password' => $password,
			'conference_edition_id' => $this->setting->id
		];
		
		$user = app('App\Http\Controllers\Controller')->createUser($data);
		$payment = app('App\Http\Controllers\Controller')->createPayment($data, $user);

		$chapter = Chapter::with('field:id,name')->select('id', 'field_id')->where('id', $data['chapter_id'])->first();
		$data['field_id'] = $chapter->field->id ?? $data['field_id'] ?? null;
		$data['setting'] = $this->setting;


		$hostel_allocation = HostelAllocationService::assignHostel($data);
		$service_point = ServicePointAllocationService::assignFoodStand($data);

		$data['allocated_hostel_data'] = $hostel_allocation;
		$data['allocated_service_point_data'] = $service_point;

		if ($this->payment && $this->payment->level == 'Moderator' && !empty($payment)) {
			$this->payment->update(['slot_filled' => $this->payment->slot_filled + 1]);

			if ($this->payment->slot_filled > $this->payment->slot) {
				Validator::make($row, [
					'slot' => 'required'
				], [
					'slot.required' => "You cannot import more than " . $this->payment->slot . ' Participants. Check the excel file for extra rows',
				])->validate();
			}
		}

		$payment->update([
			'hostel_allocation_number' => $hostel_allocation['hostel_allocation_number'],
			'hostel_allocation_type' => $hostel_allocation['hostel_allocation_type'],
			'service_point_allocation_number' => $service_point['service_point_allocation_number'],
			'service_point_allocation_type' => $service_point['service_point_allocation_type'],
			'hostel_id' => $hostel_allocation['hostel_id'],
			'food_id' => $service_point['service_point_allocation_id']
		]);

		if(empty($user->family_id)){
			$user->update([
				'family_id' => app('App\Http\Controllers\Controller')->createFamilyId($user, $this->prefix),
			]);
		}

		if ($payment->hostel_id && $payment->food_id) {
			$payment->update([
				'registration_status' => 'Complete'
			]);
		}
	
		return $user;
	}

	public function rules(): array
	{
		return [
			'*.name' => 'required|min:3|max:200',
			'*.email' => 'required|email',
			'*.phone' => 'required',
			'*.conference_number' => 'nullable|unique:users,conference_number',
			'*.registration_status' => 'nullable|in:Pending,Complete',
			'*.gender' => $this->data['import_level'] === 'Participant' ? 'required|in:Male,Female' : 'nullable|in:Male,Female',
			'*.chapter' => 'nullable|exists:chapters,name',
		];
		
	}

	public function customValidationMessages()
	{
		$level = $this->data['import_level'];
		return [
			'*.name.required' => "One or more $level do not have a name.",
			'*.name.min' => "One or more $level name is too short (min 3).",
			'*.name.max' => "One or more $level name is too long (max 200).",
			'*.gender.required' => "One or more $level have no gender.",
			'*.gender.in' => "One or more $level gender is invalid.",
			'*.chapter.exists' => "One or more $level chapter does not exist.",
		];
	}

	public function getChapterIdByChapterName($name){
		$chapter = Chapter::where('name', $name)->first();
		return $chapter?->id;
	}
}

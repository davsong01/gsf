<?php

namespace App\Imports;

use App\Food;
use App\Hostel;
use Auth;
use App\User;
use App\Setting;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ConferenceUsersImport implements ToModel, WithHeadingRow
{
	use Importable;

	private $data = null;
	private $count = 0;

	public function __construct($data)
	{
		$this->data = $data;
		
	}

	public function model(array $row)
	{
		$validation_rule = [
			// Nullables
			'conference_number' => 'nullable|unique:users,conference_number',
			'registration_status' => 'nullable|in:Pending,Complete',
			'chapter' => 'nullable|exists:chapters,name',

			// Required
			'name' => 'required|min:3|max:200',
			'email' => 'required|unique:users,email',
			'phone' => 'required|unique:users,phone',
			'level' => $this->data['import_level'],
		];


		switch ($this->data['import_level']) {
			case 'Participant':
				$validation_rule['sex'] = 'required|in:Male,Female';
				$type = 1;
				$details = app('App\http\Controllers\Controller')->getExtras(1, $this->data['edition']);
				$prefix = $details['ledge'];
				$amount_paid = $this->data['edition']->registration_fee;
				break;

			case 'Choir':
				$type = 6;
				$details = app('App\http\Controllers\Controller')->getExtras(6, $this->data['edition']);
				$amount_paid = $this->data['edition']->registration_fee;
				$prefix = $details['ledge'];
				break;
			case 'Moderator':
				$type = 2;
				$details = app('App\http\Controllers\Controller')->getExtras(1, $this->data['edition']);
				$prefix = $details['ledge'];
				$amount_paid = $this->data['edition']->registration_fee;
				$chapter = null;
				break;
			case 'Alumni':
				$type = 3;
				$details = app('App\http\Controllers\Controller')->getExtras(1, $this->data['edition']);
				$prefix = $details['ledge'];
				$chapter = null;
				break;
			case 'Nec':
				$type = 4;
				$details = app('App\http\Controllers\Controller')->getExtras(1, $this->data['edition']);
				$prefix = $details['ledge'];
				$chapter = null;
				break;
		}
		$import_level = $this->data['import_level'];
		Validator::make(
			$row,
			$validation_rule,
			[
				'name.required' => "One or more $import_level do not have a name, please check the name field and try again",
				'name.min' => "One or more $import_level name is too short minimum is 3, please check the name field and try again",
				'name.max' => "One or more $import_level name is too long maximum is 200, please check the name field and try again",

				// 'conference_number.unique' => 'One or more conference number already exists, please check the conference number field and try again',
				'email.unique' => 'One or more email already exists, please check the email field and try again',
				'phone.unique' => 'One or more phone number already exists, please check the phone number field and try again',

				'sex.in' => 'One or more sex/gender is wrong, try Male/Female, please check the sex field and try again',
				'sex.required' => 'One or more sex/gender is wrong, try Male/Female, please check the sex field and try again',
				'chapter.exists' => 'One or more chapter is using a non existing chapter, please check the chapter field and try again',
				// 'registration_status.in' => 'One or more registration status is wrong, try Pending/Complete, please check the registration status field and try again',
			]
		)->validate();

		$name = trim($row['name']);
		$email = trim($row['email']);
		$phone = trim($row['phone']);
		$level = trim($this->data['import_level']);
		$password = Hash::make(trim($row['phone']));
		$sex = isset($row['sex']) ? $row['sex'] : null;

		$registration_status = isset($row['registration_status']) ? $row['registration_status'] : 'Pending';
		$slot = isset($row['slot']) ? $row['slot'] : 1;
		$slot_filled = isset($row['slot_filled']) ? $row['slot_filled'] : 1;
		$amount_paid = isset($row['amount_paid']) ? $row['amount_paid'] : 0;
		$payment_type = isset($row['payment_type']) ? $row['payment_type'] : 'Bulk Upload';
		$uploaded_by = isset($row['uploaded_by']) ? $row['uploaded_by'] : auth::user()->id;
				
		if(auth::user()->level == 'Moderator' ){
			auth::user()->update([
				'slot_filled' => auth::user()->slot_filled + 1,
			]);
			
			if(auth::user()->slot_filled > auth::user()->slot ){
				// Create fake Validation rule
				$validation_rule = [
			// Nullables
				'slot' => 'required',
				];

				Validator::make(
				$row,
				$validation_rule,
				[
				'slot.required' => "You cannot import more than ".auth::user()->slot. ' Participants. Check the excel file for extra rows',
				
			]
			)->validate();
			}

		}
		$data = [
			'name'  => $name,
			'email' => $email,
			'phone' => $phone,
			'level' => $level,
			'type' => $type,
			'chapter_id' => $chapter ?? null,
			'sex' => $sex,
			'registration_status' => $registration_status,
			'slot' => $slot,
			'slot_filled' => $slot_filled,
			'amount_paid' => $amount_paid,
			'payment_type' => $payment_type,
			'transid' => app('App\Http\Controllers\Controller')->generateTransactionId(),
			'uploaded_by' => $uploaded_by,
			'password' => $password,
			'conference_edition_id' => $this->data['edition']->id
		];
		
		// Create User
		$user = app('App\Http\Controllers\Controller')->createUser($data);
		$payment = app('App\Http\Controllers\Controller')->createPayment($data, $user);

		//take care of nullable fields
		$hostel_id = app('App\http\Controllers\Controller')->assignHostel($level, $sex, $this->data['edition']);
		$food_id = app('App\Http\Controllers\Controller')->assignFoodStand($level, $this->data['edition']);
		// dd($hostel_id);
		$payment->update([
			'hostel_id'=> $hostel_id->id,
			'food_id'=>$food_id->id
		]);
		
		$user->update([
			'family_id' => app('App\Http\Controllers\Controller')->createFamilyId($user, $prefix),
		]);
		if(isset($payment->hostel_id) && isset($payment->food_id)){
			$payment->update([
				'registration_status' => 'Complete'
			]);
		}
		// dd($payment);
		return $user;

	}

}

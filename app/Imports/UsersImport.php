<?php

namespace App\Imports;

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

class UsersImport implements ToModel, WithHeadingRow
{
	use Importable;

	private $import_level = null;
	private $count = 0;

	public function __construct(string $import_level)
	{
		$this->import_level = $import_level;
		
	}

	public function model(array $row)
	{
			
		$validation_rule = [
			// Nullables
			'conference_number' => 'nullable|unique:users,conference_number',
			'hostel_name' => 'nullable|exists:hostels,name',
			'food_name' => 'nullable|exists:food,name',
			'registration_status' => 'nullable|in:Pending,Complete',
			'chapter' => 'nullable|exists:chapters,name',

			// Required
			'name' => 'required|min:3|max:200',
			'email' => 'required|unique:users,email',
			'phone' => 'required|unique:users,phone',
			// 'type' => 'required',
			'level' => $this->import_level,
		];

		switch ($this->import_level) {
			case 'Participant':
				$validation_rule['sex'] = 'required|in:Male,Female';
				$type = 1;
				$prefix = 'AOP';
				$chapter = auth::user()->chapter;
				break;
			case 'Choir':
				$type = 5;
				$prefix = 'AOC';
				$chapter = null;
				break;
			case 'Moderator':
				$type = 2;
				$prefix = 'AOP';
				$chapter = null;
				break;
			case 'Alumni':
				$type = 3;
				$prefix = 'AOA';
				$chapter = null;
				break;
			case 'Nec':
				$type = 4;
				$prefix = 'AON';
				$chapter = null;
				break;
				default:
				$type = 1;
				$prefix = 'AOP';
				$chapter = null;
				break;
		}

		Validator::make(
			$row,
			$validation_rule,
			[
				'name.required' => "One or more $this->import_level do not have a name, please check the name field and try again",
				'name.min' => "One or more $this->import_level name is too short minimum is 3, please check the name field and try again",
				'name.max' => "One or more $this->import_level name is too long maximum is 200, please check the name field and try again",

				'conference_number.unique' => 'One or more conference number already exists, please check the conference number field and try again',

				'hostel_name.exists' => "One or more $this->import_level is using a non existing hostel, please check the hostel name field and try again",
				'food_name.exists' => "One or more $this->import_level is using a non existing food stand, please check the food name field and try again",

				'email.unique' => 'One or more email already exists, please check the email field and try again',
				'phone.unique' => 'One or more phone number already exists, please check the phone number field and try again',

				'sex.in' => 'One or more sex/gender is wrong, try Male/Female, please check the sex field and try again',
				'sex.required' => 'One or more sex/gender is wrong, try Male/Female, please check the sex field and try again',
				'chapter.exists' => 'One or more chapter is using a non existing chapter, please check the chapter field and try again',
				'registration_status.in' => 'One or more registration status is wrong, try Pending/Complete, please check the registration status field and try again',
			]
		)->validate();

		$name = trim($row['name']);
		$email = trim($row['email']);
		$phone = trim($row['phone']);
		$level = trim($this->import_level);
		$password = Hash::make(trim($row['phone']));

		//take care of nullable fields
		$conference_number = isset($row['conference_number']) ? trim($row['conference_number']) : null;
		$hostel_id = isset($row['hostel_name']) ?
			DB::table('hostels')->whereName(trim($row['hostel_name']))->pluck('id')->first()
			: null;
		$food_id = isset($row['food_name']) ?
			DB::table('food')->whereName(trim($row['food_name']))->pluck('id')->first()
			: null;
		$chapter = isset($row['chapter']) ?
			DB::table('chapters')->whereName(trim($row['chapter']))->pluck('id')->first()
			: null;
		$sex = isset($row['sex']) ? $row['sex'] : null;
		$registration_status = isset($row['registration_status']) ? $row['registration_status'] : 'Pending';
		$slot = isset($row['slot']) ? $row['slot'] : 1;
		$slot_filled = isset($row['slot_filled']) ? $row['slot_filled'] : 1;
		$amount_paid = isset($row['amount_paid']) ? $row['amount_paid'] : 0;
		$payment_type = isset($row['payment_type']) ? $row['payment_type'] : null;
		$transid = isset($row['transid']) ? $row['transid'] : null;
		$uploaded_by = isset($row['uploaded_by']) ? $row['uploaded_by'] : auth::user()->id;
	

		if(auth::user()->level == 'Moderator' || auth::user()->level == 'Admin' ){
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
		
		
		$user = User::create([
			'name'  => $name,
			'email' => $email,
			'phone' => $phone,
			'level' => $level,
			'type' => $type,
			'food_id' => $food_id,
			'hostel_id' => $hostel_id,
			'chapter' => $chapter,
			'sex' => $sex,
			'registration_status' => $registration_status,
			'slot' => $slot,
			'slot_filled' => $slot_filled,
			'amount_paid' => Setting::select('registration_fee')->first()->value('registration_fee'),
			'payment_type' => $payment_type,
			'transid' => $transid,
			'uploaded_by' => $uploaded_by,
			'password' => $password
		]);

		$user->update([
			'conference_number' => 'GSF-'.$prefix.'-'.$user->id,
		]);

		return $user;

	}

}

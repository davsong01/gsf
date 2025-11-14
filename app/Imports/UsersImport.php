<?php

namespace App\Imports;

use Auth;
use App\User;
use App\Setting;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport extends Controller implements ToModel, WithHeadingRow
{
	use Importable;

	private $import_level;
	private $count = 0;

	public function __construct($data)
	{
		$this->data = $data;
	}

	public function model(array $row)
	{
		$validation_rule = [
			'name' => 'required',
			'email' => 'required|unique:users,email',
			'phone' => 'required|unique:users,phone',
			'chapter_id' => $this->data['chapter_id'],
			'role' => 'required',
			'status' => $this->data['type'],
			'gender' => 'required',
			'course_duration' => 'nullable',
			'portfolio_session' => 'nullable',
			'matric_year' => 'nullable',
			'graduation_year' => 'nullable',
			'dob' => 'nullable',
			'course' => 'required',
			'program' => 'required',
			'course_duration' => 'nullable',
			'facebook' => 'nullable',
			'twitter' => 'nullable'
		];

		Validator::make(
			$row,
			$validation_rule,
			[
				'name.required' => "One or more rows do not have a name, please check the name field and try again",
				'email.unique' => 'One or more email already exists, please check the email field and try again',
				'phone.required' => "One or more rows do not have a phone number, please check the phone field and try again",
				'phone.unique' => "One or more rows have duplicate phone numbers or phone number already exists in the system, please check the phone field and try again",
				'role.required' => "One or more rows do not have a role, please check the role field and try again",
				'sex.required' => 'One or more sex row is incorrect, try Male/Female, please check the sex field and try again',
				'course.required' => 'One or more rows do not have a course of study, please check the course field and try again',
				'program.required' => 'One or more rows do not have a program, please check the program field and try again',
			]
		)->validate();
			
		$upload = [
			'slug' => Str::slug(trim($row['name'])),
			'name' => trim($row['name']),
			'chapter_id' => $this->data['chapter_id'],
			'email' => trim($row['email']),
			'phone' => trim($row['phone']),
			'gender' => trim($row['gender']),
			'role' => $this->getRole(trim($row['role'])),
			'status' => $this->data['type'],
			'portfolio_session' => trim($row['portfolio_session']) ?? NULL,
			'matric_year' => trim($row['matric_year']) ?? NULL,
			'graduation_year' => trim($row['graduation_year']) ?? NULL,
			'dob' => trim($row['dob']) ?? NULL,
			'course' => trim($row['course']),
			'program' => trim($row['program']),
			'course_duration' => trim($row['course_duration']),
			'facebook' => trim($row['facebook']),
			'twitter'	=> trim($row['twitter']),
			'password' => Hash::make(trim($row['phone'])),
		];
		
		$user = User::create($upload);
		$this->createFamilyId($user);

		return $user;

	}

	private function getRole($role){
		$portfolios = $this->getCommunityPortfolios();
		return array_search($role, $portfolios);
	}

}

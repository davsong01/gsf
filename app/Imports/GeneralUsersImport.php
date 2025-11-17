<?php

namespace App\Imports;

use Auth;
use App\User;
use App\Setting;
use App\Models\TempMember;
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

class GeneralUsersImport extends Controller implements ToModel, WithHeadingRow
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
		// $validation_rule = [
		// 	'name' => 'required',
		// 	'email' => 'required',
		// 	'phone' => 'required',
		// 	'gender' => 'required',
		// 	'year_of_matriculation' => 'nullable',
		// 	'year_of_graduation' => 'nullable',
		// 	'course' => 'nullable',
		// 	'program' => 'nullable',
		// ];

		// Validator::make(
		// 	$row,
		// 	$validation_rule,
		// 	[
		// 		'name.required' => "One or more rows do not have a name, please check the name field and try again",
		// 		'email.required' => 'One or more rows do not have an email, please check the email field and try again',
		// 		'phone.required' => "One or more rows do not have a phone number, please check the phone field and try again",
		// 		'gender.required' => 'One or more row does not have gender defined, try Male/Female, please check the sex field and try again',
		// 		'course.required' => 'One or more rows do not have a course of study, please check the course field and try again',
		// 	]
		// )->validate();

		$upload = [
			'name' => trim($row['name']),
			'chapter' => $this->data['chapter']->id,
			'email' => trim($row['email']),
			'phone' => trim($row['phone']),
			'gender' => trim($row['gender']),
			'matriculation_year' => trim($row['year_of_matriculation']),
			'graduation_year' => trim($row['year_of_graduation']),
			'course' => trim($row['course']),
			'program' => trim($row['program']),
			'marital_status' => trim($row['marital_status']),
			'date_of_birth' => trim($row['date_of_birth']),
		];
		
		if(!empty($upload['name'])){
			$user = TempMember::updateOrCreate($upload);
			return $user;
		}
	
	}

	private function getRole($role){
		$portfolios = $this->getCommunityPortfolios();
		return array_search($role, $portfolios);
	}

}

<?php

namespace App\Imports;

use App\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsersImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
			Validator::make($row,
			[
				'name'=> 'required|min:3|max:200',
				'email' => 'required|unique:users,email',
				'employee_id'  => 'required|unique:users,employee_id',
				'designation' => 'nullable',
				'department' => 'nullable',
                'position' => 'nullable',
                'unit' => 'nullable',
				'company' => 'required|exists:companies,title',
				'highest_qualification' => 'nullable',
				'professional_cert' => 'nullable',
				'technical_trainings' => 'nullable',
				'non_technical_trainings' => 'nullable',
				'role' => 'required|exists:roles,title',
				'password' => 'nullable'
			],
			[
				'name.required' => 'One or more Employees do not have a name, please check the name field and try again',

				'employee_id.required' => 'One or more Employees do not have an ID, please check the ID field and try again',
				'employee_id.unique' => 'One or more Employees ID already exists, please check the ID field and try again',

				'email.unique' => 'One or more Employees already exists, please check the email field and try again',
				'email.required' => 'One or more Employees do not have an email, please check the email field and try again',

				'company.required' => 'One or more Employees do not have a company ID, please check the company ID field and try again',
				'company.exists' => 'One or more Employees do not have an existing company name, please check the company field and try again',

				'role.required' => 'One or more Employees do not have a role ID, please check the role ID field and try again',
				'role.exists' => 'One or more Employees is using a non existing role, please check the role field and try again'
			]
			)->validate();

				$name = trim($row['name']);
				$email = trim($row['email']); 
				$employee_id = trim($row['employee_id']);
				$role = trim($row['role']);
				$password = Hash::make(trim($row['employee_id']));

        //take care of nullable fields
				$designation_id = isset($row['designation'])? $this->getDesignationId(trim($row['designation'])): null;
				$department_id = isset($row['department'])? $this->getDepartmentId(trim($row['department'])): null;
				$position_id = isset($row['position'])? $this->getPositionId(trim($row['position'])): null;
                $company_id = isset($row['company'])? $this->getCompanyId(trim($row['company'])): null;
                $unit_id = isset($row['unit'])? $this->getPositionId(trim($row['unit'])): null;
				$highest_qualification = isset($row['highest_qualification'])? trim($row['highest_qualification']): 0;
				$nontechnical_trainings = isset($row['nontechnical_trainings'])? trim($row['nontechnical_trainings']): NULL;
				$technical_trainings = isset($row['technical_trainings'])? trim($row['technical_trainings']): NULL;
				$professional_cert = isset($row['professional_cert'])? trim($row['professional_cert']): 0;

        //Create new user
        return new User([
            'name'     => $name,
            'email'    => $email,
            'employee_id'    => $employee_id,
            'designation_id' => $designation_id,
            'department_id' => $department_id,
            'position_id' => $position_id,
            'company_id' => $company_id,
            'highest_qualification' => $highest_qualification,
            'nontechnical_trainings' => $nontechnical_trainings,
            'technical_trainings' => $technical_trainings,
            'professional_cert' => $professional_cert,
            'role_id' =>  $this->getRoleId($role),
            'password' => $password,
        ]);
    }

    private function getDesignationId($designation){
        $designationId = DB::table('designations')->where('title', $designation)->value('id');
        
        return $designationId;
    }

    private function getRoleId($role){
        $roleId = DB::table('roles')->where('title', $role)->value('id');
        
        return $roleId;
    }


    private function getDepartmentId($department){
        $departmentId = DB::table('departments')->where('title', $department)->value('id');
        // if(!$departmentId){
        //     dd('One or more department has not been added to the department Record, add all departments in department record first and try again');
        // }
        return $departmentId;
    }

    private function getPositionId($position){
        $positionId = DB::table('positions')->where('title', $position)->value('id');
        return $positionId;
    }

    private function getCompanyId($company){
        $companyId = DB::table('companies')->where('title', $company)->value('id');
        if(!$companyId){
            dd('One or more company has not been added to the company Record, add all companies in company record first and try again');
        }
        return $companyId;
    }

   
}
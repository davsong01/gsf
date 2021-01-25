<?php

namespace App\Imports;

use App\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DepartmentsImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
			Validator::make($row,
			[
				'company'=> 'required|exists:companies,title',
				'title' => 'required|unique:roles,title',
			],
			[
				'company.required' => 'One or more Companies do not have a name, please check the company field and try again',
				'company.exists' => 'One or more Companies do not exists in the database, please check the company field and try again',

				'title.required' => 'One or more Departments do not have a name, please check the department field and try again',
				'title.exists' => 'One or more Departments already exists, please check the department field and try again'
			]
			)->validate();

        //get company ID
        $company_id = $this->getCompanyId(trim($row['company']));

        return new Department([
            'title' => trim($row['title']),
            'company_id' => $company_id,
        ]);
    }

    private function getCompanyId($company){
        $companyId = DB::table('companies')->where('title', $company)->value('id');
        if(!$companyId){
            dd('One or more company(s) in the uploaded file has not been added to the company Record, add all companies into the company record first and try again');
        }
        return $companyId;
    }
}

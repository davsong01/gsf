<?php

namespace App\Imports;

use App\Designation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DesignationsImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
			Validator::make($row,
			[
				'company'=> 'required|exists:companies,title',
				// 'title' => 'required|unique:designations,title',
			],
			[
				'company.required' => 'One or more Designations do not have a company, please check the company column and try again',
				'company.exists' => 'One or more Designations company name does not exists, please check the company column and try again',

				'title.required' => 'One or more Designations do not have a name, please check the department field and try again',
				'title.unique' => 'One or more Designations already exists, please check the department field and try again'
			]
			)->validate();

        //get company ID
        $company_id = $this->getCompanyId(trim($row['company']));

        return new Designation([
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

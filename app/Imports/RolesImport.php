<?php

namespace App\Imports;

use App\Role;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class RolesImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
			Validator::make($row,
			[
                'company'=> 'required|exists:companies,title',
                'title'=> [
					'required',
					'min:2',
					'max:200',
					Rule::unique('roles', 'title')->where(function ($query) use ($row){
						if(($company = $this->getCompanyByName($row['company'])) && ($title = $this->getRoleByName($row['title'])) ){
						
							if ($query->where('title', $row['title'])->where('company_id', $company->id)->get()->count() > 0){
								throw ValidationException::withMessages(['title' => 'Atleast one Role exist with the same title and company in the database']);
							}
						}
					})
				],
				
			],
			[
				'company.required' => 'One or more Companies do not have a name, please check the company field and try again',
				'company.exists' => 'One or more Companies do not exists in the database, please check the company field and try again',

				'title.required' => 'One or more Roles do not have a name, please check the role field and try again',
				'title.unique' => 'One or more Roles already exists with the same name, please check the role field and try again'
			]
			)->validate();

        //get company ID
        $company_id = $this->getCompanyId(trim($row['company']));

        return new Role([
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

    private function getCompanyByName(string $name) {
			return DB::table('companies')->where('title', $name)->first();
        }
        

    private function getRoleByName(string $name) {
        return DB::table('roles')->where('title', $name)->first();
    }
}

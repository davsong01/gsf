<?php

namespace App\Imports;

use App\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class CategoriesImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
			Validator::make($row,
			[
					'title'=> [
					'required',
					'min:2',
					'max:200',
					Rule::unique('categories', 'title')->where(function ($query) use ($row){
						if(($company = $this->getCompanyByName($row['company']))){
							$count = DB::table('categories')->where('title', $row['title'])->count();
							
							if ($count > 1){
								
								throw ValidationException::withMessages(['title' => 'At least one category exist with the same title and company in the database']);
							}
						}
					})
				],
				'company' => [
					'required',
					'exists:companies,title'
				],
				// 'role' => [
				// 	'required',
				// 	'exists:roles,title'
				// ]
			],
			[
				'company.required' => 'One or more Companies do not have a name, please check the company field and try again',
				'company.exists' => 'One or more Companies do not exists in the database, please check the company field and try again',

				'title.required' => 'One or more Categories do not have a name, please check the title field and try again',
				'title.unique' => 'One or more Categories already exists with the same name, please check the title field and try again',

				'role.required' => 'One or more Roles do not have a name, please check the role field and try again',
				'role.exists' => 'One or more Roles do not exists, please check the role field and try again',

				'default.required' => 'The default column is missing, please check the file and try again' 
			]
		)->validate();

        //get company ID
        $company_id = $this->getCompanyId(trim($row['company']));

        //get role ID
        $role_id = $this->getRoleId(trim($row['role']));
        
        
        return new Category([
            'title' => trim($row['title']),
            'company_id' => $company_id,
            'role_id' => $role_id,
            'makeDefault' => trim($row['default']),
        ]);
    }

    private function getCompanyId($company){
        $companyId = DB::table('companies')->where('title', $company)->value('id');
        if(!$companyId){
            dd('One or more company(s) in the uploaded file has not been added to the company Record, add all companies into the company record first and try again');
        }
        return $companyId;
    }

    private function getRoleId($role){
        $roleId = DB::table('roles')->where('title', $role)->value('id');
        if(!$roleId){
            dd('One or more role(s) in the uploaded file has not been added to the Role Record, add all roles into the role record first and try again');
        }
        return $roleId;
		}

		private function getCompanyByName(string $name) {
			return DB::table('companies')->where('title', $name)->first();
		}

		private function getRoleByName(string $name) {
			return DB::table('categories')->where('title', $name)->first();
		}
}

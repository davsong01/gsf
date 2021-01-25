<?php

namespace App\Imports;

use App\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionsImport implements ToModel,  WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
			Validator::make($row,
			[
				'company'=> 'required|exists:companies,title',
				'category'=> 'required|exists:categories,title',
				'question' => 'required|string',
			],
			[
				'company.required' => 'One or more Companies do not have a name, please check the company field and try again',
				'company.exists' => 'One or more Companies do not exists in the database, please check the company field and try again',

				'category.required' => 'One or more Categories do not have a name, please check the category field and try again',
				'category.exists' => 'One or more Categories do not exist in the database, please check the category field and try again',

				'question.required' => 'One or more Questions is empty, please check the question field and try again',
			]
			)->validate();

    //get company ID
    $company_id = $this->getCompanyId(trim($row['company']));

     //get category ID
    $category_id = $this->getCategoryId(trim($row['category']));

        return new Question([
            'question' => trim($row['question']),
            'company_id' => $company_id,
            'category_id' => $category_id,
        ]);
    }

    private function getCompanyId($company){
        $companyId = DB::table('companies')->where('title', $company)->value('id');
        if(!$companyId){
            dd('One or more company(s) in the uploaded file has not been added to the company Record, add all companies into the company record first and try again');
        }
        return $companyId;
    }

    private function getCategoryId($category){
        $categoryId = DB::table('categories')->where('title', $category)->value('id');
        if(!$categoryId){
            dd('One or more category(s) in the uploaded file has not been added to the category Record, add all categories into the category record first and try again');
        }
        return $categoryId;
    }
}

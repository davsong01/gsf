<?php

namespace App\Imports;

use App\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\DB;

class UnitsImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        if(!isset($row['company']) || $row['company'] == null){
            dd('One or more units do not have a valid company, please check the company column and try again');
        }
       
        if(!isset($row['title']) || $row['title'] == null){
            dd('One or more units do not have a name, please check the name column and try again');
        }

        //get company ID
        $company_id = $this->getCompanyId(trim($row['company']));

        return new Unit([
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

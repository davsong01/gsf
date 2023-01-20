<?php

namespace App\Imports;

use App\Allocation;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ImportAllocations implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
			Validator::make($row,
			[
				'appraiser'=> [
					'required',
					'email',
					Rule::exists('users', 'email')->where(function($query) use ($row){
						if($user = $this->getUserByEmail($row['appraisee'])){
							$query->where('company_id', $user->company_id)->get()->count() > 0;
						}
					}),
					Rule::unique('allocations', 'appraiser_id')->where(function ($query) use ($row){
						if($user = $this->getUserByEmail($row['appraisee'])){
							if ($query->where('appraiser_id', $this->getUserByEmail($row['appraiser'])->id)->where('appraisee_id', $user->id)->get()->count() > 0){
								throw ValidationException::withMessages(['appraiser' => 'Atleast one appraiser already matched the same appraisee in the database']);
							}
						}
					})
				],
				'appraisee' => [
					'required',
					'email',
					Rule::exists('users', 'email')->where(function($query) use ($row){
						if($user = $this->getUserByEmail($row['appraiser'])){
							$query->where('company_id', $user->company_id)->get()->count() > 0;
						}
					}),
					Rule::unique('allocations', 'appraisee_id')->using(function ($query) use ($row){
						if($user = $this->getUserByEmail($row['appraiser'])){
							if ($query->where('appraisee_id', $this->getUserByEmail($row['appraisee'])->id)->where('appraiser_id', $user->id)->get()->count() > 0){
								throw ValidationException::withMessages(['appraisee' => 'Atleast one appraisee already matched the same appraiser in the database']);
							}
						}
					})
				]
			],
			[
				'appraiser.required' => 'One or more Appraisers do not have a name, please check the appraiser field and try again',
				'appraiser.exists' => 'Atleast one appraiser do not exist in database or appraiser and appraisee company\'s name do not match.',

				'appraisee.required' => 'One or more Appraisees do not have a name, please check the appraisee field and try again',
				'appraisee.exists' => 'Atleast one appraisee do not exist in database or appraiser and appraisee company\'s name do not match.',
			]
			)->validate();

        return new Allocation([
            'appraiser_id' => $this->getAppraiserId(trim($row['appraiser'])), 
            'appraisee_id' => $this->getAppraiseeId(trim($row['appraisee'])),
            'company_id' => $this->getAppraiseeCompany(trim($row['appraisee'])),
        ]);
  
    }
    
    private function getAppraiserId($appraiser){
        $appraiserId = DB::table('users')->where('email', $appraiser)->value('id');
        if(!$appraiserId){
            dd('One or more appraiser has not been added to the User Record, add all appraisers as users first and try again');
        }
        return $appraiserId;
    }

    private function getAppraiseeId($appraisee){
        $appraiseeId = DB::table('users')->where('email', $appraisee)->value('id');
        if(!$appraiseeId){
            dd('One or more appraisee has not been added to the User Record, add all appraisees as users first and try again');
        }
        return $appraiseeId;
    }

    private function getAppraiseeCompany($appraisee){
        $companyId = DB::table('users')->where('email', $appraisee)->value('company_id');
        if(!$companyId){
            dd('One or more companies has not been added to the User Record, add all companys and try again');
        }
        return $companyId;
		}
		
		private function getUserByEmail(string $email) {
			return DB::table('users')->where('email', $email)->first();
		}
}
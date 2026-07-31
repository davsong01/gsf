<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AwardShortlistStageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException('Only admin users can manage shortlist stages.');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Safely extract the ID from the 'shortlist' route parameter string or instance
        $stage = $this->route('shortlist');
        
        $stageId = is_object($stage) ? $stage->id : $stage;

        return [
            'title' => [
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'nullable',
                'string',
                'max:255'
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                // Keeps uniqueness intact but ignores this record during an update route execution
                Rule::unique('award_shortlist_stages', 'slug')->ignore($stageId),
            ],
            'award_type' => [
                'nullable',
                'in:go,etf,both'
            ],
            'stage_engine' => [
                'required',
                'in:manual,system'
            ],
            'approval_match' => [
                'nullable',
                'in:any,all,at_least,exactly'
            ],
            'approval_count' => [
                'nullable',
                'integer',
                'min:1',
                'max:3'
            ],
            'report_metric_months' => [
                'nullable',
                'integer',
                'min:1',
                'max:60'
            ],
            'report_approval_match' => [
                'nullable',
                'in:any,all,at_least,exactly'
            ],
            'report_approval_count' => [
                'nullable',
                'integer',
                'min:1',
                'max:3'
            ],
            'position' => [
                'required',
                'integer',
                'min:1'
            ],
            'active' => [
                'nullable',
                'in:0,1,true,false'
            ],
            'mark_as_final' => [
                'nullable',
                'in:0,1,true,false'
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'mark_as_final' => 'pipeline finality flag',
            'position'      => 'sort order position',
            'award_type'    => 'award type',
            'stage_engine'  => 'stage engine',
            'description'   => 'short description',
        ];
    }
}

<?php

namespace App\Http\Requests;

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
            'slug' => [
                'nullable',
                'string',
                'max:255',
                // Keeps uniqueness intact but ignores this record during an update route execution
                Rule::unique('award_shortlist_stages', 'slug')->ignore($stageId),
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
        ];
    }
}

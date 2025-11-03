<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PaymentProviderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $providerId = $this->route('id') ?? $this->route('paymentprovider') ?? null;
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_providers', 'name')->ignore($providerId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('payment_providers', 'slug')->ignore($providerId),
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'base_url' => 'nullable|url',
            'provider_charge' => 'nullable|numeric|min:0',
            'customer_pays_provider_charge' => 'boolean',
            'enable_sub_account' => 'boolean',
            'api_key' => 'nullable|string|max:500',
            'secret_key' => 'nullable|string|max:500',
            'public_key' => 'nullable|string|max:500',
            "sub_account_code" => "nullable|string",
            "sub_account_fee_percentage" => "nullable|numeric",
            'channels' => 'nullable|array',
            'channels.*' => 'in:card,bank,ussd,qr,mobile_money,bank_transfer,CARD,account_transfer,apple_pay',
        ];
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PaystackService
{
    public static function verify($transaction)
    {
        $setting = $transaction->paymentprovider;

        $status = false;
        $message = null;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $setting->base_url . "transaction/verify/" . $transaction->transid,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $setting->secret_key,
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        try {
            //code...
            if ($err) {
                Log::info("cURL Error #:" . $err);
            } else {
                $response = json_decode($response);
                
                if (!empty($response) && $response->data->status == 'success' && (($response->data->amount / 100) == $transaction->total_amount)) {
                    $status = true;
                    $message = $response;
                    $provider_reference = $response->data->reference;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return [
            'status' => $status,
            'message' => $message,
            'provider_reference' => $provider_reference ?? null
        ];
    }


    // public function queryPaystack($request, $setting, $callback = null)
    // {
    //     $url = "https://api.paystack.co/transaction/initialize";
    //     // Convert amount using payment mode exchange rate
    //     $metadata = isset($request['metadata']) ? json_decode($request['metadata'], true) : [];
    //     $metadata["custom_fields"] = [
    //         [
    //             "display_name" => "First Name",
    //             "variable_name" => "first_name",
    //             "value" => $request['name']
    //         ],
    //         [
    //             "display_name" => "Phone Number",
    //             "variable_name" => "phone_number",
    //             "value" => $request['phone']
    //         ]
    //     ];
    //     $fields = [
    //         'first_name' => $request['name'],
    //         'last_name' => $request['name'],
    //         'phone' => $request['phone'],
    //         'email' => $request['email'],
    //         'amount' => $request['amount'],
    //         'reference' =>  $request['transid'],
    //         'callback_url' => $callback ?? url('/') . '/payment/callback',
    //         'currency' => $request['currency'],
    //         'channels' => ["card", "bank", "bank_transfer"],
    //         // 'channels' => ["card", "bank", "apple_pay", "ussd", "qr", "mobile_money", "bank_transfer", "eft"],
    //         'metadata' => $metadata,
    //     ];

    //     // dd($fields);
    //     $fields_string = http_build_query($fields);
    //     // dd($fields, $fields_string, $request);
    //     //open connection
    //     $ch = curl_init();
    //     //set the url, number of POST vars, POST data
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         "Authorization: Bearer " . $setting->PAYSTACK_SECRET_KEY,
    //         "Cache-Control: no-cache",
    //     ));

    //     //So that curl_exec returns the contents of the cURL; rather than echoing it
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     //execute post

    //     $result = curl_exec($ch);
    //     $result = json_decode($result);

    //     try {
    //         return $result->data->authorization_url;
    //     } catch (\Exception $th) {
    //         \Log::error('Payment Gateway Error: ' . $th->getMessage());
    //         return [
    //             'error' => $result->message
    //         ];
    //     }
    // }

    // public function paystackGetCustomerIdByEmail(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'edition_id' => 'required|integer'
    //     ]);

    //     try {
    //         $setting = activeConferenceEdition();

    //         $paystackSecretKey = $setting->PAYSTACK_SECRET_KEY;

    //         // Step 1: Get customer ID using email
    //         $customerResponse = Http::withToken($paystackSecretKey)
    //             ->get("https://api.paystack.co/customer/{$request->email}");

    //         if (!$customerResponse->ok() || !$customerResponse->json('data.id')) {
    //             return response()->json(['success' => false, 'message' => 'Customer not found.']);
    //         }

    //         $customerId = $customerResponse->json('data.id');

    //         // Step 2: Get transactions for this customer ID
    //         $transactionsResponse = Http::withToken($paystackSecretKey)
    //             ->get("https://api.paystack.co/transaction?customer={$customerId}");

    //         if (!$transactionsResponse->ok()) {
    //             return response()->json(['success' => false, 'message' => 'Could not fetch transactions.']);
    //         }

    //         $transactions = $transactionsResponse->json('data');

    //         $filtered = collect($transactions)->filter(function ($tx) use ($request) {
    //             return isset($tx['metadata']['conference_edition_id']) &&
    //                 $tx['metadata']['conference_edition_id'] == $request->edition_id;
    //         })->map(function ($tx) {
    //             $editionId = $tx['metadata']['conference_edition_id'];
    //             $edition = ConferenceEdition::find($tx['metadata']['conference_edition_id']);
    //             $tx['conference_edition'] = $edition ? $edition->conference_theme : 'Unknown Edition';
    //             return $tx;
    //         })->values();

    //         return response()->json([
    //             'success' => true,
    //             'transactions' => $filtered
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    //     }
    // }
}

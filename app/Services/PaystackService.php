<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PaystackService
{
    public static function verify($transaction)
    {
        $setting = $transaction->paymentprovider;

        $status = false;
        $querySuccess = false;
        $message = null;
        $verificationMessage = null;
        $expectedAmountKobo = (int) round((float) $transaction->total_amount * 100);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => rtrim($setting->base_url, '/') . "/transaction/verify/" . rawurlencode($transaction->transid),
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
        $httpStatus = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        try {
            if ($err) {
                $verificationMessage = 'Paystack could not be reached: ' . $err;
                Log::error('Paystack verification request failed at the transport layer.', [
                    'transaction_id' => $transaction->id,
                    'transaction_reference' => $transaction->transid,
                    'payment_provider_id' => $setting->id ?? null,
                    'http_status' => $httpStatus,
                    'curl_error' => $err,
                ]);
            } else {
                $decodedResponse = json_decode($response);
                $gatewayMessage = $decodedResponse->message ?? null;
                $querySuccess = $httpStatus >= 200 && $httpStatus < 300 && ($decodedResponse->status ?? false) === true;
                $gatewayStatus = $decodedResponse->data->status ?? null;
                $actualAmountKobo = isset($decodedResponse->data->amount)
                    ? (int) $decodedResponse->data->amount
                    : null;
                $gatewayReference = $decodedResponse->data->reference ?? null;
                $verificationMessage = $gatewayMessage ?: 'Paystack returned a response, but the transaction could not be verified.';
                $message = $decodedResponse ?? $response;

                if ($httpStatus >= 200 && $httpStatus < 300
                    && $gatewayStatus === 'success'
                    && $actualAmountKobo === $expectedAmountKobo
                    && $gatewayReference === $transaction->transid) {
                    $status = true;
                    $verificationMessage = 'Payment status, reference, and amount were verified.';
                    $provider_reference = $gatewayReference;

                    Log::info('Paystack payment verified successfully.', [
                        'transaction_id' => $transaction->id,
                        'transaction_reference' => $transaction->transid,
                        'payment_provider_id' => $setting->id ?? null,
                        'amount_kobo' => $actualAmountKobo,
                        'http_status' => $httpStatus,
                    ]);
                } else {
                    Log::warning('Paystack payment verification did not match the expected transaction.', [
                        'transaction_id' => $transaction->id,
                        'transaction_reference' => $transaction->transid,
                        'payment_provider_id' => $setting->id ?? null,
                        'gateway_status' => $gatewayStatus,
                        'gateway_message' => $gatewayMessage,
                        'gateway_reference' => $gatewayReference,
                        'expected_amount_kobo' => $expectedAmountKobo,
                        'actual_amount_kobo' => $actualAmountKobo,
                        'http_status' => $httpStatus,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            $verificationMessage = 'An unexpected error occurred while verifying payment.';
            Log::error('Unexpected exception while verifying Paystack payment.', [
                'transaction_id' => $transaction->id,
                'transaction_reference' => $transaction->transid,
                'payment_provider_id' => $setting->id ?? null,
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
            ]);
        }

        if ($message === null && $verificationMessage !== null) {
            $message = ['message' => $verificationMessage];
        }

        return [
            'status' => $status,
            'query_success' => $querySuccess,
            'verification_message' => $verificationMessage,
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

<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MonnifyService {
    public static function getAccessToken($setting)
    {
        try {
            //code...
            $authUrl = rtrim($setting->base_url, '/') . '/api/v1/auth/login';
            $credentials = base64_encode($setting->api_key . ':' . $setting->secret_key);
            $headers = [
                "Authorization" => "Basic {$credentials}",
            ];
    
            $response = Http::withHeaders($headers)->post($authUrl);
            
            if ($response->successful() && isset($response['responseBody']['accessToken'])) {
                return $response['responseBody']['accessToken'];
            }
        } catch (\Throwable $th) {

        }

        return null;
    }


    public static function verify($transaction)
    {
        $setting = $transaction->paymentprovider;

        try {
            $credentials = base64_encode($setting->api_key . ':' . $setting->secret_key);
            $authResponse = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
            ])->post(rtrim($setting->base_url, '/') . '/api/v1/auth/login');
            $authData = $authResponse->json();
            $token = data_get($authData, 'responseBody.accessToken');

            if (!$token) {
                return [
                    'status' => false,
                    'query_success' => false,
                    'verification_message' => 'Monnify authentication failed.',
                    'message' => $authData ?? ['message' => 'Monnify authentication failed.'],
                ];
            }

            $url = rtrim($setting->base_url, '/') . '/api/v1/merchant/transactions/query?paymentReference=' . urlencode($transaction->transid);
            $response = Http::withToken($token)->get($url);
            $data = $response->json();
            $querySuccess = $response->successful()
                && isset($data['responseCode'])
                && (int) $data['responseCode'] === 0;
            $paymentStatus = data_get($data, 'responseBody.paymentStatus');
            $paidAmount = data_get($data, 'responseBody.amount');
            $verified = $querySuccess
                && $paymentStatus === 'PAID'
                && $paidAmount !== null
                && (float) $paidAmount <= (float) $transaction->total_amount;

            return [
                'status' => $verified,
                'query_success' => $querySuccess,
                'verification_message' => $verified
                    ? 'Payment status and amount were verified.'
                    : ($data['responseMessage'] ?? 'Monnify did not confirm this payment.'),
                'message' => $data ?? $response->body(),
                'provider_reference' => data_get($data, 'responseBody.transactionReference'),
            ];

        } catch (\Throwable $e) {
            \Log::error('Monnify transaction query failed.', [
                'transaction_id' => $transaction->id,
                'transaction_reference' => $transaction->transid,
                'payment_provider_id' => $setting->id ?? null,
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'query_success' => false,
                'verification_message' => 'An error occurred while querying Monnify.',
                'message' => ['message' => 'An error occurred while querying Monnify.'],
            ];
        }
    }
}

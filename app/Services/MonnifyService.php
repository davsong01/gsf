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
        $status = false;
        $message = null;

        try {
            // Step 1: Get Access Token
            $token = self::getAccessToken($setting);
            
            if (!$token) {
                return [
                    'status' => false,
                    'message' => 'Failed to authenticate with Monnify',
                ];
            }

            $url = rtrim($setting->base_url, '/') . '/api/v1/merchant/transactions/query?paymentReference=' . urlencode($transaction->transid);
            $response = Http::withToken($token)->get($url);

            $data = $response->json();

            if (
                isset($data['responseCode'], $data['responseBody'], $data['responseMessage']) &&
                $data['responseCode'] == 0 &&
                $data['responseBody']['paymentStatus'] === 'PAID' &&
                $data['responseMessage'] == 'success' &&
                ($data['responseBody']['amount']) <= $transaction->total_amount
            ) {
                return [
                    'status' => true,
                    'message' => $data,
                    'provider_reference' => $data['responseBody']['transactionReference'] ?? null,
                ];
            }

            return [
                'status' => false,
                'message' => $response->json('message') ?? 'Payment verification failed',
                'gateway_response' => $data,
            ];

        } catch (\Throwable $th) {
            \Log::error("Monnify Verify Exception: " . $th->getMessage());
        }
    }
}
<?php
namespace App\Services;
class WebhookAnalyzerService {
    public static function analyze($payment) {
        $setting = activeConferenceEdition();
        $provider = $payment->provider;
        $status = false;
        switch ($provider) {
            case 'paystack':
                $analysis = app('App\Http\Controllers\PaymentController')->verify($payment->reference, $setting);
                if (isset($analysis) && $analysis->status === 'success') {
                    $status = true;
                }
                
                break;
            case 'ravepay':
                return Self::verifyRavepayWebhook($setting);
                
            case 'monnify':
                return Self::verifyMonnifyWebhook($setting);
                break;

            default:
                return [
                    'status' => 'failed',
                    'error' => 'Unknown payment provider'
                ];

        }

        return $status;
    }
    
    public static function verifyRavepayWebhook($setting){
        $hashkey = $provider_payment_settings['ravepay']['hash_key'] ?? null;
        $signature = request()->header('verif-hash');
        if(empty($hashkey)){
            return [
                'status' => 'failed',
                'error' => 'Hash key not set for ravepay'
            ]; 
        }
        if(empty($signature)){
            return [
                'status' => 'failed',
                'error' => 'Verification signature not present'
            ]; 
        }
        if( $signature !== $hashkey ){
            return [
                'status' => 'failed',
                'error' => 'Verification signature does not match'
            ]; 
        }
        
        if ($signature === $hashkey) {
            return [
                'status' => 'successful',
            ]; 
        }
    }
    
    public static function verifyPaystackWebhook($setting){
        $secretkey = $setting->PAYSTACK_SECRET_KEY;

        $encodedData = file_get_contents('php://input');
        $hashkey = Self::computeSHA512TransactionHash($encodedData, $secretkey);
        $signature = request()->header('x-paystack-signature');
        
        $allowedEvents = ['charge.success'];
        $event = request()->event;

        if(!in_array($event, $allowedEvents)){
            return [
                'status' => 'failed',
                'error' => 'Not a loggable event'
            ];
        }
        
        if(empty($hashkey)){
            return [
                'status' => 'failed',
                'error' => 'Hash key not set for paystack'
            ]; 
        }
        if(empty($signature)){
            return [
                'status' => 'failed',
                'error' => 'Verification signature not present'
            ]; 
        }
        if( $signature !== $hashkey ){
            return [
                'status' => 'failed',
                'error' => 'Verification signature does not match'
            ]; 
        }
        
        if ($signature === $hashkey) {
            return [
                'status' => 'successful',
            ]; 
        }
    }

    public static function verifyMonnifyWebhook($provider_payment_settings, $domainSalt = null){
        $secretkey = $provider_payment_settings['monnify']['secret_key'] ?? null;
        $secret_key = !empty($secretkey) ? decryptKey($secretkey, $domainSalt) : null;
        
        $encodedData = file_get_contents('php://input');
        $hashkey = Self::computeSHA512TransactionHash($encodedData,  $secret_key);
        $signature = request()->header('monnify-signature');
        
        // \Log::info(['monnify' => [
        //     'signature' => $signature,
        //     'raw' => $encodedData,
        //     'hash_key' => $hashkey
        // ]]);
        
        if(empty($secret_key)){
            return [
                'status' => 'failed',
                'error' => 'Secret key not set for monnify'
            ]; 
        }
        
        if(empty($signature)){
            return [
                'status' => 'failed',
                'error' => 'Verification signature not present'
            ]; 
        }
        
        if( $signature !== $hashkey ){
            return [
                'status' => 'failed',
                'error' => 'Verification signature does not match'
            ]; 
        }
        
        if ($signature === $hashkey) {
            return [
                'status' => 'successful',
            ]; 
        }
    }

    public static function computeSHA512TransactionHash($stringifiedData, $clientSecret) {
        $computedHash = hash_hmac('sha512', $stringifiedData, $clientSecret);

        return $computedHash;
    }
}
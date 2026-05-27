<?php

namespace App\Services;

use App\Models\Otp;
use App\Enums\OtpTypeEnum;
use App\Models\CriticalEmail;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class OtpService
{
    /* =========================
        PUBLIC METHODS
    ========================== */

   public static function getOrCreateValidOtp(Model $user, OtpTypeEnum $type, bool $isResent = false, int $length = 6, int $expiryMinutes = 15): array {

    $query = self::otpQuery($user)
        ->where('type', $type->value)
        ->latest();

    $otp = $query->first();

    // If resend is requested → always generate fresh OTP
    if ($isResent) {
        if ($otp) {
            $otp->delete();
        }

        $data = self::createAndSendOtp($user, $type, $length, $expiryMinutes);

        self::sendOtp($user, $type->value, $data);

        return [
            'status' => 'resent',
            'otp' => $data['otp'],
        ];
    }

    // If valid OTP exists → reuse it
    if ($otp && !$otp->expires_at->isPast()) {
        return [
            'status' => 'existing',
            'otp' => $otp,
        ];
    }

    // Expired OTP → delete
    if ($otp) {
        $otp->delete();
    }

    // Create new OTP
    $data = self::createAndSendOtp($user, $type, $length, $expiryMinutes);

    self::sendOtp($user, $type->value, $data);

    return [
        'status' => 'created',
        'otp' => $data['otp'],
    ];
}


    public static function verifyOtp(Model $user, string $otp, string $type = 'forgot-password'): array
    {
        $record = Otp::where('userable_id', $user->id)
            ->where('userable_type', get_class($user))
            ->where('type', $type)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();

        if (! $record) {
            return [
                'success' => false,
                'errors' => ['otp' => ['Invalid OTP provided.']],
            ];
        }

        if ($record->expires_at->isPast()) {
            $record->delete();
            return [
                'success' => false,
                'errors' => ['otp' => ['OTP has expired. Please request a new one.']],
            ];
        }


        $record->delete();

        $expiryMinutes = 15;

        session()->put('otpx___', [
            'value' => true,
            'expires_at' => now()->addMinutes($expiryMinutes)
        ]);

        session()->put('user_idz', [
            'value' => $user->id, // store just the id
            'expires_at' => now()->addMinutes($expiryMinutes)
        ]);

        if($type == OtpTypeEnum::FORGOT_PASSWORD->value){
            return [
                'success' => true,
                'redirect_url' => route('stakeholders.reset-password', ['user' => $user->id, 'type' => $type]),
                'message' => 'Email verified successfully!',
            ];
        }

        return [
            'success' => true,
            'message' => 'OTP verified successfully!',
        ];
    }


    /* =========================
        INTERNAL HELPERS
    ========================== */

    private static function createAndSendOtp(
        Model $user,
        OtpTypeEnum $type,
        int $length,
        int $expiryMinutes
    ): Array {

        $otpCode = self::generateOtpCode($length);

        $otp = $user->otps()->create([
            'otp' => $otpCode,
            'type' => $type->value,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);

        $message = self::buildMessage($user, $otpCode, $type);

        return [
            'otp' => $otp,
            'type' => $type->value,
            'message' => $message
        ];
    }


    private static function otpQuery(Model $user)
    {
        return Otp::where('userable_id', $user->id)
            ->where('userable_type', get_class($user));
    }


    private static function generateOtpCode(int $length): string
    {
        return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }


    private static function buildMessage(Model $user, string $otp, OtpTypeEnum $type): array
    {
        $appName = config('app.name');

        return match ($type) {
            // OtpTypeEnum::SIGNUP_VERIFICATION => [
            //     'subject' => 'Verify Your Email',
            //     'preamble' => 'Please verify your email address using the OTP below.',
            //     'content' => "
            //         Hi {$user->name},<br><br>
            //         Welcome to {$appName}!<br><br>
            //         Your verification code is:<br><br>
            //         <strong style='font-size:32px;letter-spacing:8px'>{$otp}</strong><br><br>
            //         This code expires in 15 minutes.<br><br>
            //         {$appName} Team
            //     "
            // ],

            // OtpTypeEnum::AUTHENTICATION_OTP => [
            //     'subject' => 'Login OTP',
            //     'preamble' => 'A login attempt was made using your account.',
            //     'content' => "Your OTP is: <strong>{$otp}</strong>"
            // ],

            OtpTypeEnum::FORGOT_PASSWORD => [
                'subject' => 'Reset Your Password',
                'preamble' => 'You requested a password reset.',
                'content' => "
                    Hi {$user->name},<br><br>
                    Use the OTP below to reset your password:<br><br>
                    <strong style='font-size:32px;letter-spacing:8px'>{$otp}</strong><br><br>
                    This code expires in 15 minutes.<br><br>
                    {$appName} Team
                "
            ],

            default => [
                'subject' => 'OTP Code',
                'preamble' => '',
                'content' => "Your OTP code is: <strong>{$otp}</strong>"
            ]
        };
    }


    public static function sendOtp($user, $type, $data)
    {
        $emailsToQueue = [
            'recipient' => $user->email,
            'subject' => $data['message']['subject'] ?? '',
            'content' => $data['message']['content'] ?? '',
            'type'       => 'otp',
            'priority'       => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $log = EmailService::logEmail($emailsToQueue);

        return $log;
    }
}

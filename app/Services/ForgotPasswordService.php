<?php

namespace App\Services;

use App\Models\Otp;
use App\Enums\OtpTypeEnum;
use App\Models\CriticalEmail;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class ForgotPasswordService
{
    /* =========================
        PUBLIC METHODS
    ========================== */

    public static function getOrCreateValidOtp(
        Model $user,
        OtpTypeEnum $type,
        int $length = 6,
        int $expiryMinutes = 15
    ) {
        $otp = self::otpQuery($user)
            ->where('type', $type->value)
            ->latest()
            ->first();

        if ($otp && !$otp->expires_at->isPast()) {
            return $otp;
        }

        if ($otp) {
            $otp->delete();
        }

        $data = self::createAndSendOtp($user, $type, $length, $expiryMinutes);

        self::sendOtp(
            $user,
            $type->value,
            $data
        );

        return $otp;
    }


    public function verifyOtp(Model $user, string $otp, string $type = 'forgot-password'): bool
    {
        $record = Otp::where('userable_id', $user->id)
            ->where('userable_type', get_class($user))
            ->where('type', $type)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();

        if (! $record) {
            return false;
        }

        $record->delete();
        return true;
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

        $user->otps()->create([
            'otp' => $otpCode,
            'type' => $type->value,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);

        $message = self::buildMessage($user, $otpCode, $type);

        return [
            'otp' => $otpCode,
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
        $emailsToQueue[] = [
            'type' => $type,
            'recipient' => $user->email,
            'subject' => $data['message']['subject'] ?? '',
            'content' => $data['message']['content'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $log = EmailService::logEmail([
            'type'       => 'report_email',
            'recipients' => $emailsToQueue,
        ]);

        return $log;
    }
}

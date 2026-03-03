<?php

namespace App\Services;

use App\Mail\BookingStatusMail;
use App\Mail\LoginActivityCheckMail;
use App\Mail\MagicLoginLinkMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class BrevoMailer
{
    private const API_ENDPOINT = 'https://api.brevo.com/v3/smtp/email';

    public function sendMailable(Mailable $mailable, string $toEmail, ?string $toName = null): bool
    {
        try {
            $subject = method_exists($mailable, 'envelope')
                ? ($mailable->envelope()->subject ?? Config::get('app.name', 'The Pearl Manila'))
                : Config::get('app.name', 'The Pearl Manila');

            $html = $mailable->render();

            return $this->sendRaw(
                toEmail: $toEmail,
                toName: $toName,
                subject: (string) $subject,
                html: $html,
                text: null,
            );
        } catch (\Throwable $exception) {
            Log::error('Brevo render failed.', [
                'email' => $toEmail,
                'error' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    public function sendVerificationEmail(User $user): bool
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $subject = 'Verify Email Address - The Pearl Manila';
        $html = view('emails.verify-email', [
            'user' => $user,
            'verificationUrl' => $verificationUrl,
        ])->render();

        return $this->sendRaw(
            toEmail: $user->email,
            toName: $user->name,
            subject: $subject,
            html: $html,
            text: "Please verify your email address: {$verificationUrl}",
        );
    }

    private function sendRaw(string $toEmail, ?string $toName, string $subject, string $html, ?string $text): bool
    {
        $apiKey = Config::get('services.brevo.key');
        if (! $apiKey) {
            Log::error('Brevo API key missing. Email not sent.', [
                'email' => $toEmail,
                'subject' => $subject,
            ]);
            return false;
        }

        $senderAddress = (string) Config::get('mail.from.address', 'no-reply@thepearlmanila.com');
        $senderName = (string) Config::get('mail.from.name', 'The Pearl Manila');

        $payload = [
            'sender' => [
                'name' => $senderName,
                'email' => $senderAddress,
            ],
            'to' => [
                [
                    'email' => $toEmail,
                    'name' => $toName ?: $toEmail,
                ],
            ],
            'subject' => $subject,
            'htmlContent' => $html,
        ];

        if ($text) {
            $payload['textContent'] = $text;
        }

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->timeout(20)->post(self::API_ENDPOINT, $payload);

        if (! $response->successful()) {
            Log::error('Brevo send failed.', [
                'email' => $toEmail,
                'subject' => $subject,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        return true;
    }
}

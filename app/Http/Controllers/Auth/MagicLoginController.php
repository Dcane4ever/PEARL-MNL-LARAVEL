<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginActivityCheckMail;
use App\Mail\MagicLoginLinkMail;
use App\Models\MagicLoginToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MagicLoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $genericMessage = 'If this email is registered as a customer account, we sent a secure login link.';

        $user = User::where('email', $data['email'])->first();
        if (! $user || $user->is_admin) {
            return back()->with('status', $genericMessage);
        }

        MagicLoginToken::where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $plainToken = Str::random(96);
        $token = MagicLoginToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'requested_ip' => $request->ip(),
            'requested_user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            'expires_at' => now()->addMinutes(15),
        ]);

        $magicLink = route('magic-login.consume', ['token' => $plainToken]);

        try {
            Mail::to($user->email)->send(new MagicLoginLinkMail(
                user: $user,
                magicLink: $magicLink,
                expiresAt: $token->expires_at
            ));
        } catch (\Throwable $exception) {
            Log::error('Magic login email send failed.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            return back()->withErrors([
                'email' => 'We could not send the login email right now. Please try again in a moment.',
            ]);
        }

        if (app()->environment('local')) {
            Log::info('Magic login link generated (local debug).', [
                'user_id' => $user->id,
                'email' => $user->email,
                'magic_link' => $magicLink,
                'expires_at' => optional($token->expires_at)->toDateTimeString(),
            ]);
        }

        return back()->with('status', $genericMessage);
    }

    public function consume(Request $request, string $token): RedirectResponse
    {
        $tokenHash = hash('sha256', $token);

        $magicToken = MagicLoginToken::with('user')
            ->where('token_hash', $tokenHash)
            ->whereNull('used_at')
            ->where('expires_at', '>=', now())
            ->first();

        if (! $magicToken || ! $magicToken->user || $magicToken->user->is_admin) {
            return redirect()->route('login')
                ->withErrors(['email' => 'This login link is invalid or expired. Please request a new one.']);
        }

        $magicToken->update([
            'used_at' => now(),
            'consumed_ip' => $request->ip(),
            'consumed_user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
        ]);

        Auth::login($magicToken->user);
        $request->session()->regenerate();

        $yesUrl = URL::temporarySignedRoute(
            'magic-login.activity',
            now()->addHours(24),
            ['loginToken' => $magicToken->id, 'action' => 'yes']
        );
        $noUrl = URL::temporarySignedRoute(
            'magic-login.activity',
            now()->addHours(24),
            ['loginToken' => $magicToken->id, 'action' => 'no']
        );

        Mail::to($magicToken->user->email)->send(new LoginActivityCheckMail(
            user: $magicToken->user,
            yesUrl: $yesUrl,
            noUrl: $noUrl,
            ipAddress: (string) $request->ip(),
            userAgent: (string) $request->userAgent(),
            loggedInAt: now()
        ));

        return redirect()->route('rooms.booking')
            ->with('status', 'You are now signed in. Welcome back!');
    }

    public function verifyDevice(Request $request, MagicLoginToken $loginToken, string $action): RedirectResponse
    {
        if (! in_array($action, ['yes', 'no'], true)) {
            abort(404);
        }

        $user = $loginToken->user;
        if (! $user) {
            return redirect()->route('login');
        }

        if ($action === 'yes') {
            return redirect()->route('login')
                ->with('status', 'Thanks for confirming this login activity.');
        }

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        MagicLoginToken::where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        if (Auth::check() && Auth::id() === $user->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login')
            ->with('status', 'We secured your account and signed out all active sessions. Please log in again.');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $minAge = 16;
        $minBirthdate = now()->subYears($minAge)->toDateString();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date', 'before_or_equal:'.$minBirthdate],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'birthdate.before_or_equal' => "You must be at least {$minAge} years old to register.",
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'birthdate' => $validated['birthdate'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('verification.notice', absolute: false))
            ->with('status', 'Registration successful. Please verify your email before booking.');
    }
}

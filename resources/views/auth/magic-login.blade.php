<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-4 text-sm text-gray-600">
        Enter your customer email and we will send a one-time secure login link.
    </div>

    <form method="POST" action="{{ route('magic-login.send') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               href="{{ route('login') }}">
                Back to password login
            </a>

            <x-primary-button>
                Send login link
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

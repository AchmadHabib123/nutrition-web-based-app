<x-guest-layout>

    <x-auth-session-status class="mb-4 text-cyan-300" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-200"/>

            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                class="block mt-1 w-full 
                bg-white/10 border border-white/20 placeholder-gray-300
                rounded-lg
                focus:ring-cyan-400 focus:border-cyan-400"
            />

            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-gray-200"/>

            <x-text-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="block mt-1 w-full
                bg-white/10 border border-white/20 placeholder-gray-300
                rounded-lg
                focus:ring-cyan-400 focus:border-cyan-400"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
        </div>

        <!-- Remember -->
        <div class="flex items-center">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                class="rounded border-white/30 bg-white/10 text-cyan-400 focus:ring-cyan-400"
            >

            <label for="remember_me" class="ml-2 text-sm text-gray-300">
                {{ __('Remember me') }}
            </label>
        </div>

        <!-- Button -->
        <div class="flex justify-end">
            <x-primary-button
                class="px-6 py-2
                bg-cyan-500 hover:bg-cyan-600
                text-white font-semibold
                rounded-lg shadow-lg
                transition duration-200">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

    </form>

</x-guest-layout>
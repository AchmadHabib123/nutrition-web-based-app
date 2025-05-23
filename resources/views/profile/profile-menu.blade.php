<div class="flex items-center">
    <!-- Authentication Dropdown -->
    @auth
        <div class="ml-3 relative" x-data="{ open: false }">
            <div>
                <button @click="open = ! open" type="button" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                    <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                </button>
            </div>

            <!-- Dropdown Panel -->
            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-36 rounded-md shadow-lg z-50">
                <div class="py-1 bg-white rounded-md shadow-xs" role="bahan_makanan aria-orientation="vertical" aria-labelledby="user-bahan_makanan>
                    {{-- <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        {{ __('Pengaturan Profil') }}
                    </a> --}}
                    <form method="GET" action="{{ route('profile.edit') }}">
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Pengaturan Profil') }}
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>

        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
        @endif
    @endauth
</div>
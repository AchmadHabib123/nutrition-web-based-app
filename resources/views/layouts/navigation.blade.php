<!-- resources/views/layouts/navigation.blade.php -->
<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-600" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('admin.logistics.index') }}" :active="request()->routeIs('admin.bahan_makanans')">
                        {{ __('Bahan Makanan') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('admin.logistics.index') }}" :active="request()->routeIs('admin.bahan_makanans')">
                        {{ __('Operasional') }}
                    </x-nav-link>                       
                </div>
            </div>
        </div>
    </div>
</nav>

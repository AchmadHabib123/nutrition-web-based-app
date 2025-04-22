<!-- resources/views/components/navigation-menu.blade.php -->

<nav class="space-y-1">
    <!-- Existing Navigation Links -->

    <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>

    <x-nav-link href="{{ route('admin.patients.index') }}" :active="request()->routeIs('admin.patients.*')">
        {{ __('Pasien') }}
    </x-nav-link>

    <x-nav-link href="{{ route('admin.bahan_makanans.index') }}" :active="request()->routeIs('admin.bahan_makanans.*')">
        {{ __('Menu') }}
    </x-nav-link>

    <!-- Tambahkan link lainnya jika diperlukan -->
</nav>

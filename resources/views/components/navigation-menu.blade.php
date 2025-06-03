<!-- resources/views/components/navigation-menu.blade.php -->

<nav class="space-y-1">
    <!-- Existing Navigation Links -->

    <x-nav-link href="{{ route('ahli-gizi.dashboard') }}" :active="request()->routeIs('ahli-gizi.dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>

    <x-nav-link href="{{ route('ahli-gizi.patients.index') }}" :active="request()->routeIs('ahli-gizi.patients.*')">
        {{ __('Pasien') }}
    </x-nav-link>

    <x-nav-link href="{{ route('ahli-gizi.logistics.index') }}" :active="request()->routeIs('ahli-gizi.bahan_makanans.*')">
        {{ __('Menu') }}
    </x-nav-link>

    <!-- Tambahkan link lainnya jika diperlukan -->
</nav>

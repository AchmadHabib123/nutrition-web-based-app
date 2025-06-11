<!-- Sidebar Navigation -->
<aside class="w-48 bg-green-100 text-gray-700 flex flex-col py-6 pl-7 gap-3 overflow-visible">
    <div class="shrink-0 flex items-center">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-10 w-auto fill-current text-gray-600" />
        </a>
    </div>
    @if(Auth::check())
    {{-- Jika user adalah Ahli Gizi --}}
    @if(Auth::user()->role === 'ahli-gizi')
        <x-nav-link href="{{ route('ahli-gizi.dashboard') }}" :active="request()->routeIs('ahli-gizi.dashboard')">
            Dashboard
        </x-nav-link>
        
        <x-nav-link href="{{ route('ahli-gizi.logistics.index') }}" :active="request()->routeIs('ahli-gizi.logistics.*')">
            Bahan Makanan
        </x-nav-link>

        <x-nav-link href="{{ route('ahli-gizi.patients.index') }}" :active="request()->routeIs('ahli-gizi.patients.*')">
            Daftar Pasien
        </x-nav-link>
        <x-nav-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.*')">
            Profile
        </x-nav-link>
        {{-- <x-nav-link href="{{ route('ahli-gizi.operasionals.index') }}" :active="request()->routeIs('ahli-gizi.operasionals.*')">
            Operasional
        </x-nav-link> --}}
    {{-- Jika user adalah Tenaga Gizi --}}
    @elseif(Auth::user()->role === 'tenaga-gizi')
        {{-- Tambahkan tautan untuk Tenaga Gizi di sini --}}
        <x-nav-link href="{{ route('tenaga-gizi.dashboard') }}" :active="request()->routeIs('tenaga-gizi.dashboard')">
            Dashboard
        </x-nav-link>
        <x-nav-link href="{{ route('tenaga-gizi.patients.index') }}" :active="request()->routeIs('tenaga-gizi.patients.*')">
            Data Pasien
        </x-nav-link>
        {{-- Contoh link lain untuk Tenaga Gizi --}}
        {{-- <x-nav-link href="{{ route('tenaga-gizi.jadwal-konsultasi') }}" :active="request()->routeIs('tenaga-gizi.jadwal-konsultasi')">
            Jadwal Konsultasi
        </x-nav-link> --}}
    @else
        {{-- Tautan default atau untuk peran lain jika ada --}}
        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
            Dashboard Umum
        </x-nav-link>
    @endif
    @else
        {{-- Jika user belum login, mungkin tampilkan link login atau kosongkan --}}
        <x-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
            Login
        </x-nav-link>
    @endif
</aside>

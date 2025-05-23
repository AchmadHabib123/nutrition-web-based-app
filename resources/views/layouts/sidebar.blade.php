<!-- Sidebar Navigation -->
<aside class="w-48 bg-green-100 text-gray-700 flex flex-col py-6 pl-7 gap-3 overflow-visible">
    <div class="shrink-0 flex items-center">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-10 w-auto fill-current text-gray-600" />
        </a>
    </div>
    <nav class="flex flex-col gap-3 space-y-1">
        <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
            Dashboard
        </x-nav-link>
        
        <x-nav-link href="{{ route('admin.logistics.index') }}" :active="request()->routeIs('admin.logistics.*')">
            Bahan Makanan
        </x-nav-link>
        <x-nav-link href="{{ route('admin.logistics.index') }}" :active="request()->routeIs('admin.operasionals.*')">
            Operasional
        </x-nav-link>
    </nav>
</aside>

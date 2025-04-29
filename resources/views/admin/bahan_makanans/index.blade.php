<!-- resources/views/admin/bahan_makanans/index.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bahan Makanan') }}
            </h2>
        </div>
        <div class="flex gap-6 justify-center">
            <!-- Search & Filter UI -->
            <div class="relative w-full flex items-center gap-2">
                <div class="flex items-center w-full border border-gray-300 rounded-full px-4 py-2 shadow-sm focus-within:ring-2 focus-within:ring-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.41-1.41l5.38 5.38a1 1 0 01-1.42 1.42l-5.37-5.38zM14 8a6 6 0 11-12 0 6 6 0 0112 0z" clip-rule="evenodd" />
                    </svg>
                    <form action="{{ route('admin.bahan_makanans.index') }}" method="GET" class="w-full flex">
                        <input type="text" name="search" placeholder="Search" value="{{ request('search') }}"
                            class="w-full outline-none bg-transparent border-none focus:outline-none focus:ring-0 py-0" />
                    </form>
                    @if(request('search'))
                        <a href="{{ route('admin.bahan_makanans.index') }}" class="ml-2 text-gray-500 hover:text-red-500">
                            &#10005;
                        </a>
                    @endif
                </div>
                <!-- Filter Toggle Button -->
                <button id="filterToggle" type="button" class="text-gray-600 hover:text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V18a1 1 0 01-1.447.894l-4-2A1 1 0 018 16v-2.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                </button>
                <button onclick="window.location='{{ route('admin.bahan_makanans.create') }}'" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Tambah
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                    </svg>
                </button>
            </div>
            <!-- Floating Filter Panel -->
            <div id="filterPanel" class="absolute top-20 left-0 bg-white shadow-xl border rounded-lg p-5 w-full md:w-96 z-50 hidden">
                <form id="filterForm" action="{{ route('admin.bahan_makanans.index') }}" method="GET">
                    <!-- Hidden input to maintain search value -->
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    <!-- Kategori -->
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700 mb-1">Kategori Bahan</label>
                        <select name="kategori" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriOptions as $kategori)
                                <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                    {{ $kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Updated At -->
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700 mb-1">Tanggal Update</label>
                        <select id="dateFilter" name="updated" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Kapan saja</option>
                            <option value="today" {{ request('updated') == 'today' ? 'selected' : '' }}>Hari ini</option>
                            <option value="last7" {{ request('updated') == 'last7' ? 'selected' : '' }}>7 Hari Terakhir</option>
                            <option value="last30" {{ request('updated') == 'last30' ? 'selected' : '' }}>30 Hari Terakhir</option>
                            <option value="custom" {{ request('updated') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>

                    <!-- Custom Range -->
                    <div id="customDateRange" class="mb-4 hidden">
                        <label class="block text-sm text-gray-700 mb-1">Dari:</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="w-full border-gray-300 rounded-md mb-2">
                        <label class="block text-sm text-gray-700 mb-1">Sampai:</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="w-full border-gray-300 rounded-md">
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-all">
                            Apply Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-slot>
    <div class="py-7">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-7">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($bahanMakanans as $bahanMakanan)
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden relative p-4">
                        <a href="{{ route('admin.bahan_makanans.show', $bahanMakanan->id) }}" class="block">
                            <div class="flex gap-4">
                                @if($bahanMakanan->gambar)
                                    @if (Str::startsWith($bahanMakanan->gambar, ['http://', 'https://']))
                                        {{-- Jika gambar adalah URL eksternal --}}
                                        <img src="{{ $bahanMakanan->gambar }}" alt="{{ $bahanMakanan->nama }}" class="w-16 h-16 rounded-xl">
                                    @else
                                        {{-- Jika gambar adalah path lokal di storage --}}
                                        <img src="{{ asset('storage/' . $bahanMakanan->gambar) }}" alt="{{ $bahanMakanan->nama }}" class="h-16 object-cover rounded-xl">
                                    @endif
                                @else
                                    {{-- Gambar default jika tidak ada --}}
                                    <img src="https://via.placeholder.com/150" alt="Default Image" class="h-16 object-cover rounded-xl">
                                @endif
                                <div class="">
                                    <h3 class="text-base font-bold">{{ $bahanMakanan->nama }}</h3>
                                    <div class="text-gray-500 text-sm">
                                        <span>
                                            {{
                                                ($bahanMakanan->karbohidrat * 4) + 
                                                ($bahanMakanan->protein * 4) + 
                                                ($bahanMakanan->total_lemak * 9)
                                            }} Kalori . 100g
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-0">
                                <div class="grid grid-cols-3 gap-1 mt-4">
                                    <div class="flex">
                                        <div class="flex h-5/5 bg-slate-300 items-end rounded-full">
                                            <div class="bg-green-500 w-3 rounded-full" style="height: {{ $bahanMakanan->protein}}%;"></div>
                                        </div>
                                        <div class="flex flex-col ml-2 text-gray-700">
                                            <span class="font-semibold text-green-500 text-sm">Protein:</span> 
                                            <span>{{ $bahanMakanan->protein }}g</span>
                                        </div>
                                    </div>
                            
                                    <div class="flex">
                                        <div class="flex h-[100%] bg-slate-300 rounded-full items-end">
                                            <div class="bg-yellow-500 w-3 rounded-full" style="height: {{ $bahanMakanan->karbohidrat}}%;"></div>
                                        </div>
                                        <div class="flex flex-col ml-2 text-gray-700">
                                            <span class="font-semibold text-yellow-500 text-sm">Karbohidrat:</span>
                                            <span>{{ $bahanMakanan->karbohidrat }}g</span>
                                        </div>
                                    </div>
                            
                                    <div class="flex">
                                        <div class="flex h-[100%] bg-slate-300 rounded-full items-end">
                                            <div class="bg-purple-500 w-3 rounded-full" style="height: {{ $bahanMakanan->total_lemak}}%;"></div>
                                        </div>
                                        <div class="flex flex-col ml-2 text-gray-700">
                                            <span class="font-semibold text-purple-500 text-sm">Lemak:</span>
                                            <span>{{ $bahanMakanan->total_lemak }}g</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-500 text-sm mt-2">Tipe Pasien: {{ $bahanMakanan->tipe_pasien }}</p>
                        </a>
                        <div class="absolute top-6 right-6">
                            <button 
                                class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded inline-flex items-center" 
                                onclick="confirmEdit('{{ route('admin.bahan_makanans.edit', $bahanMakanan->id) }}')"
                            >
                                <svg class="fill-current h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M17.414 2.586a2 2 0 010 2.828l-2 2-2.828-2.828 2-2a2 2 0 012.828 0zm-3.828 3.828L5 15l-2.5.5.5-2.5L13.586 4l2.828 2.828z" />
                                </svg>
                            </button>
                        </div>                        
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $bahanMakanans->links() }}
            </div>
        </div>
    </div>
    <script>
        const filterToggle = document.getElementById('filterToggle');
        const filterPanel = document.getElementById('filterPanel');
        const dateFilter = document.getElementById('dateFilter');
        const customDateRange = document.getElementById('customDateRange');

        // Toggle panel visibility
        filterToggle.addEventListener('click', () => {
            filterPanel.classList.toggle('hidden');
        });

        // Show custom range fields if selected
        dateFilter.addEventListener('change', function () {
            if (this.value === 'custom') {
                customDateRange.classList.remove('hidden');
            } else {
                customDateRange.classList.add('hidden');
            }
        });

        // Trigger initial visibility based on selected option
        window.addEventListener('DOMContentLoaded', () => {
            if (dateFilter.value === 'custom') {
                customDateRange.classList.remove('hidden');
            }
        });
        
        function confirmEdit(url) {
            const confirmation = confirm("Apakah anda ingin mengedit menu ini?");
            if (confirmation) {
                window.location.href = url;
            }
        }
    </script>
    
</x-app-layout>

<!-- resources/views/admin/menus/index.blade.php -->

{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menu Makanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notifikasi Success -->
            @if(session('success'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Menu</h3>
                    <a href="{{ route('admin.menus.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Tambah Menu
                    </a>
                </div>

                @foreach($menus as $tipe => $menuItems)
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">{{ $tipe }}</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b">Nama Makanan</th>
                                        <th class="py-2 px-4 border-b">Kalori (kcal)</th>
                                        <th class="py-2 px-4 border-b">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($menuItems as $menu)
                                        <tr>
                                            <td class="py-2 px-4 border-b">{{ $menu->nama_makanan }}</td>
                                            <td class="py-2 px-4 border-b">{{ $menu->kalori }}</td>
                                            <td class="py-2 px-4 border-b flex space-x-2">
                                                <!-- Edit -->
                                                <a href="{{ route('admin.menus.edit', $menu->id) }}" class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                                    <!-- Icon Edit -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m6 0l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                </a>

                                                <!-- Delete -->
                                                <form method="POST" action="{{ route('admin.menus.destroy', $menu->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                                        <!-- Icon Delete -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">Tidak ada menu untuk tipe {{ $tipe }}.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout> --}}
{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menu Makanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($menus as $menu)
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        @if($menu->gambar)
                            <img src="{{ asset('storage/' . $menu->gambar) }}" alt="{{ $menu->nama }}" class="w-full h-40 object-cover">
                        @endif
                        <div class="p-6">
                            <h3 class="text-lg font-bold">{{ $menu->nama }}</h3>
                            <p class="text-gray-700 mt-2">{{ $menu->definisi_makanan }}</p>
                            <p class="text-gray-500 text-sm mt-2">Tipe Pasien: {{ $menu->tipe_pasien }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout> --}}

<x-app-layout>
    {{-- <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin.menus.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Tambah Masakan
        </a>
    </div> --}}

    {{-- <div class="py-7">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="nama" class="block text-gray-700">Nama Makanan</label>
                        <input type="text" name="nama" id="nama" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="gambar" class="block text-gray-700">Gambar</label>
                        <input type="file" name="gambar" id="gambar" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="">
                            <label for="protein" class="block text-gray-700">Protein (gram)</label>
                            <input type="number" name="protein" id="protein" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>
    
                        <div class="">
                            <label for="karbohidrat" class="block text-gray-700">Karbohidrat (gram)</label>
                            <input type="number" name="karbohidrat" id="karbohidrat" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>
    
                        <div class="">
                            <label for="total_lemak" class="block text-gray-700">Total Lemak (gram)</label>
                            <input type="number" name="total_lemak" id="total_lemak" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="tipe_pasien" class="block text-gray-700">Tipe Pasien</label>
                        <select name="tipe_pasien" id="tipe_pasien" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                            <option value="VVIP">VVIP</option>
                            <option value="VIP">VIP</option>
                            <option value="Normal">Normal</option>
                        </select>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bahan Makanan') }}
            </h2>
            <a href="{{ route('admin.menus.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Tambah Bahan Makanan
            </a>
        </div>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($menus as $menu)
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden relative p-6">
                        <div class="flex gap-4">
                            @if($menu->gambar)
                                <img src="{{ asset('storage/' . $menu->gambar) }}" alt="{{ $menu->nama }}" class="h-16 object-cover rounded-xl">
                            @endif
                            <div class="">
                                <h3 class="text-lg font-bold">{{ $menu->nama }}</h3>
                                <div class="text-gray-500">
                                    <span>
                                        {{
                                            ($menu->karbohidrat * 4) + 
                                            ($menu->protein * 4) + 
                                            ($menu->total_lemak * 9)
                                        }} Kalori . 100g
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="p-0">
                            <div class="flex">
                                {{-- <div class="flex space-x-4 mt-4">
                                    <div class="flex flex-col items-center w-16">
                                        <p class="text-gray-700">
                                            <span class="font-semibold">Protein:</span>
                                            <span class="text-green-500">{{ $menu->protein }}g</span>
                                        </p>
                                        <div class="bg-green-500 w-10" style="height: {{ $menu->protein * 2 }}px;"></div>
                                    </div>
                            
                                    <div class="flex flex-col items-center w-16">
                                        <p class="text-gray-700">
                                            <span class="font-semibold">Karbohidrat:</span>
                                            <span class="text-yellow-500">{{ $menu->karbohidrat }}g</span>
                                        </p>
                                        <div class="bg-yellow-500 w-10" style="height: {{ $menu->karbohidrat * 2 }}px;"></div>
                                    </div>
                            
                                    <div class="flex flex-row-reverse items-center w-16">
                                        <p class="text-gray-700">
                                            <span class="font-semibold">Lemak:</span>
                                            <span class="text-purple-500">{{ $menu->total_lemak }}g</span>
                                        </p>
                                        <div class="bg-purple-500 w-10 flex flex-col" style="height: {{ $menu->total_lemak * 2 }}px;"></div>
                                    </div>
                                </div> --}}
                                <div class="grid grid-cols-3 gap-1 mt-4">
                                    <div class="flex">
                                        <!-- Bar Protein (Vertikal) -->
                                        <div class="flex h-5/5 bg-slate-300 items-end rounded-full">
                                            <div class="bg-green-500 w-3 rounded-full" style="height: {{ $menu->protein}}%;"></div>
                                        </div>
                                        <!-- Teks Protein -->
                                        <div class="flex flex-col ml-2 text-gray-700">
                                            <span class="font-semibold text-green-500">Protein:</span> 
                                            <span>{{ $menu->protein }}g</span>
                                        </div>
                                    </div>
                            
                                    <div class="flex">
                                        <!-- Bar Karbohidrat (Vertikal) -->
                                        <div class="flex h-[100%] bg-slate-300 rounded-full items-end">
                                            <div class="bg-yellow-500 w-3 rounded-full" style="height: {{ $menu->karbohidrat}}%;"></div>
                                        </div>
                                        <!-- Teks Karbohidrat -->
                                        <div class="flex flex-col ml-2 text-gray-700">
                                            <span class="font-semibold text-yellow-500">Karbohidrat:</span>
                                            <span>{{ $menu->karbohidrat }}g</span>
                                        </div>
                                    </div>
                            
                                    <div class="flex">
                                        <!-- Bar Lemak (Vertikal) -->
                                        <div class="flex h-[100%] bg-slate-300 rounded-full items-end">
                                            <div class="bg-purple-500 w-3 rounded-full" style="height: {{ $menu->total_lemak}}%;"></div>
                                        </div>
                                        <!-- Teks Lemak -->
                                        <div class="flex flex-col ml-2 text-gray-700">
                                            <span class="font-semibold text-purple-500">Lemak:</span>
                                            <span>{{ $menu->total_lemak }}g</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-500 text-sm mt-2">Tipe Pasien: {{ $menu->tipe_pasien }}</p>
                        </div>
                        {{-- <div class="px-4 pb-4 relative">
                            <div class="dropdown inline-block relative">
                                <button class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded inline-flex items-center">
                                    <span class="mr-1">Menu</span>
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                    </svg>
                                </button>
                                <ul class="dropdown-menu absolute text-gray-700 pt-1">
                                    <li class="">
                                        <a href="{{ route('admin.menus.edit', $menu->id) }}" 
                                           class="rounded-t bg-gray-100 hover:bg-gray-200 py-2 px-4 block whitespace-no-wrap">Edit</a>
                                    </li>
                                </ul>
                            </div>
                        </div> --}}
                        <div class="absolute top-6 right-6">
                            <button 
                                class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded inline-flex items-center" 
                                onclick="confirmEdit('{{ route('admin.menus.edit', $menu->id) }}')"
                            >
                                <svg class="fill-current h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M17.414 2.586a2 2 0 010 2.828l-2 2-2.828-2.828 2-2a2 2 0 012.828 0zm-3.828 3.828L5 15l-2.5.5.5-2.5L13.586 4l2.828 2.828z" />
                                </svg>
                            </button>
                        </div>                        
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('admin.menus.update', $menu) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="nama" class="block text-gray-700">Nama Makanan</label>
                        <input type="text" name="nama" id="nama" value="{{ $menu->nama }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="gambar" class="block text-gray-700">Gambar</label>
                        <input type="file" name="gambar" id="gambar" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="protein" class="block text-gray-700">Protein (gram)</label>
                        <input type="number" name="protein" id="protein" value="{{ $menu->protein }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="karbohidrat" class="block text-gray-700">Karbohidrat (gram)</label>
                        <input type="number" name="karbohidrat" id="karbohidrat" value="{{ $menu->karbohidrat }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="total_lemak" class="block text-gray-700">Total Lemak (gram)</label>
                        <input type="number" name="total_lemak" id="total_lemak" value="{{ $menu->total_lemak }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="tipe_pasien" class="block text-gray-700">Tipe Pasien</label>
                        <select name="tipe_pasien" id="tipe_pasien" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                            <option value="VVIP" {{ $menu->tipe_pasien == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                            <option value="VIP" {{ $menu->tipe_pasien == 'VIP' ? 'selected' : '' }}>VIP</option>
                            <option value="Normal" {{ $menu->tipe_pasien == 'Normal' ? 'selected' : '' }}>Normal</option>
                        </select>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
    <script>
        function confirmEdit(url) {
            const confirmation = confirm("Apakah anda ingin mengedit menu ini?");
            if (confirmation) {
                window.location.href = url;
            }
        }
    </script>
    
</x-app-layout>

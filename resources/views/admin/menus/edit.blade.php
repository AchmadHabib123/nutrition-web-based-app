<!-- resources/views/admin/menus/edit.blade.php -->

{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Menu Makanan') }}
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
                <form method="POST" action="{{ route('admin.menus.update', $menu->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Tipe Pasien -->
                    <div>
                        <x-input-label for="tipe" :value="__('Tipe Pasien')" />
                        <select id="tipe" name="tipe" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option value="">-- Pilih Tipe Pasien --</option>
                            @foreach($tipe_options as $tipe)
                                <option value="{{ $tipe }}" {{ old('tipe', $menu->tipe) == $tipe ? 'selected' : '' }}>{{ $tipe }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tipe')" class="mt-2" />
                    </div>

                    <!-- Nama Makanan -->
                    <div class="mt-4">
                        <x-input-label for="nama_makanan" :value="__('Nama Makanan')" />
                        <x-text-input id="nama_makanan" class="block mt-1 w-full" type="text" name="nama_makanan" value="{{ old('nama_makanan', $menu->nama_makanan) }}" required />
                        <x-input-error :messages="$errors->get('nama_makanan')" class="mt-2" />
                    </div>

                    <!-- Kalori -->
                    <div class="mt-4">
                        <x-input-label for="kalori" :value="__('Kalori (kcal)')" />
                        <x-text-input id="kalori" class="block mt-1 w-full" type="number" name="kalori" value="{{ old('kalori', $menu->kalori) }}" required min="0" />
                        <x-input-error :messages="$errors->get('kalori')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Tautan Kembali -->
                <div class="mt-4">
                    <a href="{{ route('admin.menus.index') }}" class="text-indigo-600 hover:text-indigo-900">
                        &larr; Kembali ke Daftar Menu
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Menu Makanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form id="editForm" action="{{ route('admin.menus.update', $menu) }}" method="POST" enctype="multipart/form-data">
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

                    <div class="grid grid-cols-3 gap-3">
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
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="kategori_bahan_masakan" class="block text-gray-700">Kategori Bahan Makanan/Minuman</label>
                            <select name="kategori_bahan_masakan" id="kategori_bahan_masakan" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="makanan_pokok" {{ $menu->kategori_bahan_masakan == 'makanan_pokok' ? 'selected' : '' }}>Makanan Pokok</option>
                                <option value="lauk" {{ $menu->kategori_bahan_masakan == 'lauk' ? 'selected' : '' }}>Lauk Pauk</option>
                                <option value="sayur" {{ $menu->kategori_bahan_masakan == 'sayur' ? 'selected' : '' }}>Sayuran</option>
                                <option value="buah" {{ $menu->kategori_bahan_masakan == 'buah' ? 'selected' : '' }}>Buah</option>
                                <option value="susu" {{ $menu->kategori_bahan_masakan == 'susu' ? 'selected' : '' }}>Susu</option>
                            </select>
                        </div>
    
                        <div class="mb-4">
                            <label for="tipe_pasien" class="block text-gray-700">Tipe Pasien</label>
                            <select name="tipe_pasien" id="tipe_pasien" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="VVIP" {{ $menu->tipe_pasien == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                                <option value="VIP" {{ $menu->tipe_pasien == 'VIP' ? 'selected' : '' }}>VIP</option>
                                <option value="Normal" {{ $menu->tipe_pasien == 'Normal' ? 'selected' : '' }}>Normal</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="stok" class="block text-gray-700">Stok</label>
                        <input type="number" name="stok" id="stok" value="{{ old('stok', $menu->stok) }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    </div>                    

                    {{-- <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Simpan Perubahan</button>
                    </div> --}}
                    <div class="mt-6">
                        <button 
                            type="button" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-md" 
                            onclick="submitEditForm()"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    <script>
        function submitEditForm() {
            // Tampilkan alert
            alert("Berhasil Edit Menu");
            
            // Kirim form
            document.getElementById('editForm').submit();
        }
    </script>
        
</x-app-layout>

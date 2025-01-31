<!-- resources/views/admin/patients/create.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pasien Baru') }}
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
                <form method="POST" action="{{ route('admin.patients.store') }}">
                    @csrf

                    <!-- No Kamar -->
                    <div>
                        <x-input-label for="no_kamar" :value="__('No Kamar')" />
                        <x-text-input id="no_kamar" class="block mt-1 w-full" type="text" name="no_kamar" value="{{ old('no_kamar') }}" required />
                        <x-input-error :messages="$errors->get('no_kamar')" class="mt-2" />
                    </div>

                    <!-- Nama Pasien -->
                    <div class="mt-4">
                        <x-input-label for="nama_pasien" :value="__('Nama Pasien')" />
                        <x-text-input id="nama_pasien" class="block mt-1 w-full" type="text" name="nama_pasien" value="{{ old('nama_pasien') }}" required />
                        <x-input-error :messages="$errors->get('nama_pasien')" class="mt-2" />
                    </div>

                    <!-- Riwayat Penyakit -->
                    <div class="mt-4">
                        <x-input-label for="riwayat_penyakit" :value="__('Riwayat Penyakit')" />
                        <textarea id="riwayat_penyakit" name="riwayat_penyakit" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="4" required>{{ old('riwayat_penyakit') }}</textarea>
                        <x-input-error :messages="$errors->get('riwayat_penyakit')" class="mt-2" />
                    </div>

                    <!-- Berat Badan -->
                    <div class="mt-4">
                        <x-input-label for="berat_badan" :value="__('Berat Badan (kg)')" />
                        <x-text-input id="berat_badan" class="block mt-1 w-full" type="number" name="berat_badan" value="{{ old('berat_badan') }}" required min="0" step="0.1" />
                        <x-input-error :messages="$errors->get('berat_badan')" class="mt-2" />
                    </div>

                    <!-- Tinggi Badan -->
                    <div class="mt-4">
                        <x-input-label for="tinggi_badan" :value="__('Tinggi Badan (cm)')" />
                        <x-text-input id="tinggi_badan" class="block mt-1 w-full" type="number" name="tinggi_badan" value="{{ old('tinggi_badan') }}" required min="0" step="0.1" />
                        <x-input-error :messages="$errors->get('tinggi_badan')" class="mt-2" />
                    </div>

                    <!-- Usia -->
                    <div class="mt-4">
                        <x-input-label for="usia" :value="__('Usia (tahun)')" />
                        <x-text-input id="usia" class="block mt-1 w-full" type="number" name="usia" value="{{ old('usia') }}" required min="0" />
                        <x-input-error :messages="$errors->get('usia')" class="mt-2" />
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="mt-4">
                        <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
                        <select id="jenis_kelamin" name="jenis_kelamin" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="pria" {{ old('jenis_kelamin') == 'pria' ? 'selected' : '' }}>Pria</option>
                            <option value="wanita" {{ old('jenis_kelamin') == 'wanita' ? 'selected' : '' }}>Wanita</option>
                        </select>
                        <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
                    </div>

                    <!-- Kalori Makanan -->
                    <div class="mt-4">
                        <x-input-label for="kalori_makanan" :value="__('Kalori Makanan (kcal)')" />
                        {{-- <x-text-input id="kalori_makanan" class="block mt-1 w-full" type="number" name="kalori_makanan" value="{{ old('kalori_makanan') }}" required min="0" />
                        <x-input-error :messages="$errors->get('kalori_makanan')" class="mt-2" /> --}}
                        <div class="grid grid-cols-5 gap-2">
                            <select id="tipe_makanan" name="makanan_pokok" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">-- Pilih Makanan Pokok --</option>
                                <option value="VVIP" {{ old('tipe_makanan') == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                                <option value="VIP" {{ old('tipe_makanan') == 'VIP' ? 'selected' : '' }}>VIP</option>
                                <option value="Normal" {{ old('tipe_makanan') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            </select>
                            <select id="tipe_makanan" name="lauk_pauk" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">-- Pilih Lauk Pauk --</option>
                                <option value="VVIP" {{ old('tipe_makanan') == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                                <option value="VIP" {{ old('tipe_makanan') == 'VIP' ? 'selected' : '' }}>VIP</option>
                                <option value="Normal" {{ old('tipe_makanan') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            </select>
                            <select id="tipe_makanan" name="sayuran" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">-- Pilih Sayuran --</option>
                                <option value="VVIP" {{ old('tipe_makanan') == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                                <option value="VIP" {{ old('tipe_makanan') == 'VIP' ? 'selected' : '' }}>VIP</option>
                                <option value="Normal" {{ old('tipe_makanan') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            </select>
                            <select id="tipe_makanan" name="buah" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">-- Pilih Buah --</option>
                                <option value="VVIP" {{ old('tipe_makanan') == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                                <option value="VIP" {{ old('tipe_makanan') == 'VIP' ? 'selected' : '' }}>VIP</option>
                                <option value="Normal" {{ old('tipe_makanan') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            </select>
                            <select id="tipe_makanan" name="susu" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">-- Pilih Susu --</option>
                                <option value="VVIP" {{ old('tipe_makanan') == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                                <option value="VIP" {{ old('tipe_makanan') == 'VIP' ? 'selected' : '' }}>VIP</option>
                                <option value="Normal" {{ old('tipe_makanan') == 'Normal' ? 'selected' : '' }}>Normal</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="tipe_pasien" :value="__('Tipe Pasien')" />
                        <select id="tipe_pasien" name="tipe_pasien" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option value="">-- Pilih Tipe Pasien --</option>
                            <option value="VVIP" {{ old('tipe_pasien') == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                            <option value="VIP" {{ old('tipe_pasien') == 'VIP' ? 'selected' : '' }}>VIP</option>
                            <option value="Normal" {{ old('tipe_pasien') == 'Normal' ? 'selected' : '' }}>Normal</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipe_pasien')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Tambah Pasien') }}
                        </x-primary-button>
                    </div>
                </form>
                <!-- Tambahkan di bawah form submit button -->

                <div class="mt-4">
                    <a href="{{ route('admin.patients.index') }}" class="text-indigo-600 hover:text-indigo-900">
                        &larr; Kembali ke Daftar Pasien
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

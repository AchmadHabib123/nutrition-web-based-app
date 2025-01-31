<!-- resources/views/admin/patients/edit.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pasien') }}
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
                <!-- Form Edit Pasien -->
                <form method="POST" action="{{ route('admin.patients.update', $patient->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- No Kamar -->
                    <div>
                        <x-input-label for="no_kamar" :value="__('No Kamar')" />
                        <x-text-input id="no_kamar" class="block mt-1 w-full" type="text" name="no_kamar" value="{{ old('no_kamar', $patient->no_kamar) }}" required />
                        <x-input-error :messages="$errors->get('no_kamar')" class="mt-2" />
                    </div>

                    <!-- Nama Pasien -->
                    <div class="mt-4">
                        <x-input-label for="nama_pasien" :value="__('Nama Pasien')" />
                        <x-text-input id="nama_pasien" class="block mt-1 w-full" type="text" name="nama_pasien" value="{{ old('nama_pasien', $patient->nama_pasien) }}" required />
                        <x-input-error :messages="$errors->get('nama_pasien')" class="mt-2" />
                    </div>

                    <!-- Riwayat Penyakit -->
                    <div class="mt-4">
                        <x-input-label for="riwayat_penyakit" :value="__('Riwayat Penyakit')" />
                        <textarea id="riwayat_penyakit" name="riwayat_penyakit" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" rows="4" required>{{ old('riwayat_penyakit', $patient->riwayat_penyakit) }}</textarea>
                        <x-input-error :messages="$errors->get('riwayat_penyakit')" class="mt-2" />
                    </div>

                    <!-- Berat Badan -->
                    <div class="mt-4">
                        <x-input-label for="berat_badan" :value="__('Berat Badan (kg)')" />
                        <x-text-input id="berat_badan" class="block mt-1 w-full" type="number" name="berat_badan" value="{{ old('berat_badan', $patient->berat_badan) }}" required min="0" step="0.1" />
                        <x-input-error :messages="$errors->get('berat_badan')" class="mt-2" />
                    </div>

                    <!-- Tinggi Badan -->
                    <div class="mt-4">
                        <x-input-label for="tinggi_badan" :value="__('Tinggi Badan (cm)')" />
                        <x-text-input id="tinggi_badan" class="block mt-1 w-full" type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $patient->tinggi_badan) }}" required min="0" step="0.1" />
                        <x-input-error :messages="$errors->get('tinggi_badan')" class="mt-2" />
                    </div>

                    <!-- Usia -->
                    <div class="mt-4">
                        <x-input-label for="usia" :value="__('Usia (tahun)')" />
                        <x-text-input id="usia" class="block mt-1 w-full" type="number" name="usia" value="{{ old('usia', $patient->usia) }}" required min="0" />
                        <x-input-error :messages="$errors->get('usia')" class="mt-2" />
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="mt-4">
                        <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
                        <select id="jenis_kelamin" name="jenis_kelamin" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option value="pria" {{ old('jenis_kelamin', $patient->jenis_kelamin) == 'pria' ? 'selected' : '' }}>Pria</option>
                            <option value="wanita" {{ old('jenis_kelamin', $patient->jenis_kelamin) == 'wanita' ? 'selected' : '' }}>Wanita</option>
                        </select>
                        <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
                    </div>

                    <!-- Kalori Makanan -->
                    <div class="mt-4">
                        <x-input-label for="kalori_makanan" :value="__('Kalori Makanan (kcal)')" />
                        <x-text-input id="kalori_makanan" class="block mt-1 w-full" type="number" name="kalori_makanan" value="{{ old('kalori_makanan', $patient->kalori_makanan) }}" required min="0" />
                        <x-input-error :messages="$errors->get('kalori_makanan')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="tipe_pasien" :value="__('Tipe Pasien')" />
                        <select id="tipe_pasien" name="tipe_pasien" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option value="">-- Pilih Tipe Pasien --</option>
                            <option value="VVIP" {{ old('tipe_pasien', $patient->tipe_pasien) == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                            <option value="VIP" {{ old('tipe_pasien', $patient->tipe_pasien) == 'VIP' ? 'selected' : '' }}>VIP</option>
                            <option value="Normal" {{ old('tipe_pasien', $patient->tipe_pasien) == 'Normal' ? 'selected' : '' }}>Normal</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipe_pasien')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Simpan') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Tambah Makanan yang Dikonsumsi -->
                {{-- <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900">Tambah Makanan yang Dikonsumsi</h3>
                    <form method="POST" action="{{ route('admin.food-consumptions.store', $patient->id) }}" class="mt-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Nama Makanan -->
                            <div>
                                <x-input-label for="nama_makanan" :value="__('Nama Makanan')" />
                                <x-text-input id="nama_makanan" class="block mt-1 w-full" type="text" name="nama_makanan" required />
                                <x-input-error :messages="$errors->get('nama_makanan')" class="mt-2" />
                            </div>

                            <!-- Kalori -->
                            <div>
                                <x-input-label for="kalori" :value="__('Kalori (kcal)')" />
                                <x-text-input id="kalori" class="block mt-1 w-full" type="number" name="kalori" min="0" required />
                                <x-input-error :messages="$errors->get('kalori')" class="mt-2" />
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-end">
                                <x-primary-button>
                                    {{ __('Tambah') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Daftar Makanan yang Dikonsumsi -->
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900">Makanan yang Dikonsumsi</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">Nama Makanan</th>
                                    <th class="py-2 px-4 border-b">Kalori</th>
                                    <th class="py-2 px-4 border-b">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patient->foodConsumptions as $food)
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $food->nama_makanan }}</td>
                                        <td class="py-2 px-4 border-b">{{ $food->kalori }} kcal</td>
                                        <td class="py-2 px-4 border-b flex space-x-2">
                                            <!-- Edit Makanan -->
                                            <a href="{{ route('admin.food-consumptions.edit', [$patient->id, $food->id]) }}" class="text-yellow-500 hover:text-yellow-700" title="Edit Makanan">
                                                <!-- Icon Edit -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m6 0l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                            </a>

                                            <!-- Delete Makanan -->
                                            <form method="POST" action="{{ route('admin.food-consumptions.destroy', [$patient->id, $food->id]) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus makanan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus Makanan">
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
                                        <td colspan="3" class="text-center py-4">Tidak ada makanan yang dikonsumsi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div> --}}
            </div>
        </div>
    </x-app-layout>

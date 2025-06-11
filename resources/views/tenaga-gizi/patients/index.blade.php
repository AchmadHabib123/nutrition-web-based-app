<!-- resources/views/ahli-gizi/patients/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pasien1') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">No Kamar</th>
                                <th class="py-2 px-4 border-b">Nama Pasien</th>
                                <th class="py-2 px-4 border-b">Riwayat Penyakit</th>
                                <th class="py-2 px-4 border-b">Kalori Makanan</th>
                                <th class="py-2 px-4 border-b">Kalori Harian</th>
                                <th class="py-2 px-4 border-b">Tipe Pasien</th>
                                <th class="py-2 px-4 border-b">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patients as $patients)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $patients->no_kamar }}</td>
                                    <td class="py-2 px-4 border-b">{{ $patients->nama_pasien }}</td>
                                    <td class="py-2 px-4 border-b">{{ $patients->riwayat_penyakit }}</td>
                                    <td class="py-2 px-4 border-b">{{ $patients->kalori_makanan }} kcal</td>
                                    <td class="py-2 px-4 border-b">{{ $patients->kalori_harian }} kcal</td>
                                    <td class="py-2 px-4 border-b">{{ $patients->tipe_pasien }}</td>
                                    <td class="py-2 px-4 border-b flex space-x-2">
                                        <!-- Rincian -->
                                        <a href="{{ route('ahli-gizi.patients.show', $patients->id) }}" class="text-blue-500 hover:text-blue-700">
                                            <!-- Icon Rincian -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.267-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('ahli-gizi.patients.edit', $patients->id) }}" class="text-green-500 hover:text-green-700">
                                            <!-- Icon Edit -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-1.414L19 7m-5 5l5 5M16.5 7.5l-5-5" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Tidak ada data pasien.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- Saat menampilkan Kalori Harian dikolom Kalori harian yang ada di dalam table "patients" dihitung dari penjumlahan kalori makanan sesuai jadwal makanans dan tipe pasiennya yang berlaku dihari tersebut
admin/patient/index.blade.php --}}

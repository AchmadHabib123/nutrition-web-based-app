<!-- resources/views/admin/patients/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rincian Pasien') }}
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
                <h3 class="text-lg font-medium text-gray-900">Informasi Pasien</h3>
                <div class="mt-4">
                    <p><strong>No Kamar:</strong> {{ $patient->no_kamar }}</p>
                    <p><strong>Nama Pasien:</strong> {{ $patient->nama_pasien }}</p>
                    <p><strong>Riwayat Penyakit:</strong> {{ $patient->riwayat_penyakit }}</p>
                    <p><strong>Berat Badan:</strong> {{ $patient->berat_badan }} kg</p>
                    <p><strong>Tinggi Badan:</strong> {{ $patient->tinggi_badan }} cm</p>
                    <p><strong>Usia:</strong> {{ $patient->usia }} tahun</p>
                    <p><strong>Jenis Kelamin:</strong> {{ ucfirst($patient->jenis_kelamin) }}</p>
                    <p><strong>Kalori Makanan:</strong> {{ $patient->kalori_makanan }} kcal</p>
                    <p><strong>Kalori Harian:</strong> {{ $patient->kalori_harian }} kcal</p>
                    <p><strong>Status Pasien:</strong> {{ $patient->status_pasien }} kcal</p>
                </div>

                <h3 class="text-lg font-medium text-gray-900 mt-6">Makanan yang Dikonsumsi</h3>
                <div class="mt-4">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Nama Makanan</th>
                                <th class="py-2 px-4 border-b">Kalori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->foodConsumptions as $food)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $food->nama_makanan }}</td>
                                    <td class="py-2 px-4 border-b">{{ $food->kalori }} kcal</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">Tidak ada makanan yang dikonsumsi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rincian Pasien') }} - {{ $patients->nama_pasien }} {{-- Menggunakan $patients --}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 font-medium text-sm text-red-600">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900">Informasi Pasien</h3>
                <div class="mt-4">
                    <p><strong>No Kamar:</strong> {{ $patients->no_kamar }}</p> {{-- Menggunakan $patients --}}
                    <p><strong>Nama Pasien:</strong> {{ $patients->nama_pasien }}</p> {{-- Menggunakan $patients --}}
                    <p><strong>Riwayat Penyakit:</strong> {{ $patients->riwayat_penyakit }}</p> {{-- Menggunakan $patients --}}
                    <p><strong>Berat Badan:</strong> {{ $patients->berat_badan }} kg</p> {{-- Menggunakan $patients --}}
                    <p><strong>Tinggi Badan:</strong> {{ $patients->tinggi_badan }} cm</p> {{-- Menggunakan $patients --}}
                    <p><strong>Usia:</strong> {{ $patients->usia }} tahun</p> {{-- Menggunakan $patients --}}
                    <p><strong>Jenis Kelamin:</strong> {{ ucfirst($patients->jenis_kelamin) }}</p> {{-- Menggunakan $patients --}}
                    <p><strong>Kalori Makanan:</strong> {{ $patients->kalori_makanan }} kcal</p> {{-- Menggunakan $patients --}}
                    <p><strong>Kalori Harian Target:</strong> {{ $patients->kalori_harian }} kcal</p> {{-- Menggunakan $patients --}}
                    <p><strong>Tipe Pasien:</strong> {{ $patients->tipe_pasien }}</p> {{-- Menggunakan $patients --}}
                    <p><strong>Status Pasien:</strong> {{ $patients->status_pasien }}</p> {{-- Menggunakan $patients --}}
                </div>

                <hr class="my-6">

                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Makanan untuk Tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                </h3>

                {{-- HAPUS BAGIAN FILTER TANGGAL INI:
                <div class="mb-4">
                    <label for="dateFilter" class="block text-sm font-medium text-gray-700">Pilih Tanggal:</label>
                    <input type="date" id="dateFilter" class="mt-1 block w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $date }}">
                </div>
                --}}

                <div class="mt-4">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Makan</th>
                                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Makanan</th>
                                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kalori (Menu)</th>
                                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Protein (Menu)</th>
                                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karbohidrat (Menu)</th>
                                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lemak (Menu)</th>
                                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi Validasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($foodConsumptionsForDate as $food)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b text-sm text-gray-900">{{ $food->waktu_makan }}</td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-900">{{ $food->nama_makanan }}</td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-900">{{ $food->menu->kalori ?? $food->kalori }} kcal</td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-900">{{ $food->menu->total_protein ?? 0 }} g</td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-900">{{ $food->menu->total_karbohidrat ?? 0 }} g</td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-900">{{ $food->menu->total_lemak ?? 0 }} g</td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($food->status == 'consumed') bg-green-100 text-green-800
                                            @elseif($food->status == 'delivered') bg-yellow-100 text-yellow-800
                                            @elseif($food->status == 'skipped') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($food->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b text-sm text-gray-900">
                                        @if (Auth::user()->can('markAsConsumed', $food) && $food->status === 'delivered')
                                            {{-- Tombol ini akan memicu modal validasi di Poin 4 --}}
                                            <button 
                                                class="validate-food-btn bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-1 px-3 rounded text-xs" 
                                                data-id="{{ $food->id }}"
                                                data-kalori-menu="{{ $food->menu->kalori ?? $food->kalori }}"
                                                data-protein-menu="{{ $food->menu->total_protein ?? 0 }}"
                                                data-carbs-menu="{{ $food->menu->total_karbohidrat ?? 0 }}"
                                                data-fat-menu="{{ $food->menu->total_lemak ?? 0 }}"
                                                data-nama-makanan="{{ $food->nama_makanan }}"
                                            >
                                                Validasi Konsumsi
                                            </button>
                                        @elseif ($food->status === 'consumed')
                                            <span class="text-green-500 text-xs">Sudah Divalidasi</span>
                                        @elseif ($food->status === 'skipped')
                                            <span class="text-red-500 text-xs">Dilewati</span>
                                        @else
                                            <span class="text-gray-500 text-xs">Menunggu Diantar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-gray-500">Tidak ada makanan untuk tanggal ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Bagian ringkasan kalori di halaman detail pasien --}}
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg shadow-sm">
                        <h4 class="font-semibold text-gray-800 mb-2">Ringkasan Konsumsi Tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</h4>
                        <p>Total Kalori Dikonsumsi (Sudah Divalidasi): <span class="font-bold">{{ $totalConsumedCaloriesToday }} kcal</span></p>
                        <p>Makanan Planned: <span class="font-bold">{{ $plannedToday->count() }} item</span></p>
                        <p>Makanan Delivered (Menunggu Validasi): <span class="font-bold">{{ $deliveredToday->count() }} item</span></p>
                        <p>Makanan Consumed: <span class="font-bold">{{ $consumedToday->count() }} item</span></p>
                        <p>Makanan Skipped: <span class="font-bold">{{ $skippedToday->count() }} item</span></p> {{-- Tambah ini jika ada status skipped --}}
                        <p>Total Item Konsumsi Hari Ini: <span class="font-bold">{{ $foodConsumptionsForDate->count() }} item</span></p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script untuk filter tanggal di halaman detail pasien (INI DIHAPUS JIKA TIDAK PERLU FILTER DI HALAMAN INI)
            // const dateFilter = document.getElementById('dateFilter');
            // if (dateFilter) {
            //     dateFilter.addEventListener('change', function() {
            //         const selectedDate = this.value;
            //         const patientId = {{ $patients->id }}; // Menggunakan $patients
            //         window.location.href = `/ahli-gizi/patients/${patientId}/show?date=${selectedDate}`;
            //     });
            // }

            // Ini akan menjadi bagian awal dari Poin 4: Modal Validasi
            document.querySelectorAll('.validate-food-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const foodId = this.dataset.id;
                    const kaloriMenu = parseFloat(this.dataset.kaloriMenu);
                    const proteinMenu = parseFloat(this.dataset.proteinMenu);
                    const carbsMenu = parseFloat(this.dataset.carbsMenu);
                    const fatMenu = parseFloat(this.dataset.fatMenu);
                    const namaMakanan = this.dataset.namaMakanan;

                    // Untuk saat ini, kita akan menampilkan alert.
                    // Di Poin 4, ini akan diganti dengan menampilkan modal validasi.
                    alert(`Validasi Konsumsi:\nMakanan: ${namaMakanan}\nID: ${foodId}\nKalori Awal: ${kaloriMenu} kcal\nProtein Awal: ${proteinMenu} g\nKarbohidrat Awal: ${carbsMenu} g\nLemak Awal: ${fatMenu} g\n\nIni akan membuka form modal validasi.`);

                    // Anda akan memanggil fungsi untuk menampilkan modal atau form validasi
                    // showValidationModal(foodId, namaMakanan, kaloriMenu, proteinMenu, carbsMenu, fatMenu);
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
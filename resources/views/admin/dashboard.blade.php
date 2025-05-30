<!-- resources/views/admin/dashboard.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-2 px-2">
        <div id="calendar"></div>
    </div>
    <!-- Tabel Pasien -->
    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4">
            <!-- Notifikasi Success -->
            @if(session('success'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Pasien</h3>
                    <a href="{{ route('admin.patients.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Tambah Pasien
                    </a>
                </div>

                <div class="overflow-x-auto">
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
                        <tbody id="patients-body">
                            {{-- <tr><td colspan="6" class="text-center py-4">Memuat data...</td></tr> --}}
                            {{-- @forelse($patients as $patient)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $patient->no_kamar }}</td>
                                    <td class="py-2 px-4 border-b">{{ $patient->nama_pasien }}</td>
                                    <td class="py-2 px-4 border-b">{{ $patient->riwayat_penyakit }}</td>
                                    <td class="py-2 px-4 border-b">{{ $patient->kalori_makanan }} kcal</td>
                                    <td class="py-2 px-4 border-b">{{ $patient->kalori_harian }} kcal</td>
                                    <td class="py-2 px-4 border-b">{{ $patient->tipe_pasien }}</td>
                                    <td class="py-2 px-4 border-b flex space-x-2">
                                        <!-- Rincian -->
                                        <a href="{{ route('admin.patients.show', $patient->id) }}" class="text-blue-500 hover:text-blue-700" title="Rincian">
                                            <!-- Icon Rincian -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.267-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('admin.patients.edit', $patient->id) }}" class="text-green-500 hover:text-green-700" title="Edit">
                                            <!-- Icon Edit -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m6 0l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Tidak ada data pasien.</td>
                                </tr>
                            @endforelse --}}
                            {{-- @include('admin.patient.partials.patient_rows', ['patients' => $patients]) --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var tableBody = document.querySelector('tbody');
    
            var today = new Date();
            var todayString = today.toISOString().split('T')[0];
    
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridWeek',
                initialDate: todayString,
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridWeek'
                },
                views: {
                    dayGridWeek: {
                        duration: { weeks: 1 },
                        dayHeaderFormat: { weekday: 'short' }
                    }
                },
                selectable: true,
                events: [],
                dayCellContent: function(info) {
                    return { html: `<span style="font-size:16px; font-weight:bold;">${info.date.getDate()}</span>` };
                },
                dateClick: function(info) {
                    console.log("Tanggal dipilih:", info.dateStr);
                    fetchPatients(info.dateStr);
                }
            });
    
            calendar.render();
    
            function fetchPatients(date) {
                console.log("Mengambil data untuk tanggal:", date);
    
                fetch(`/patients/filter?date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Data diterima dari server:", data);
    
                        tableBody.innerHTML = '';
    
                        if (data.length === 0) {
                            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">Tidak ada data pasien.</td></tr>`;
                            return;
                        }
    
                        data.forEach(patient => {
                            let row = `
                                <tr>
                                    <td class="py-2 px-4 border-b">${patient.no_kamar}</td>
                                    <td class="py-2 px-4 border-b">${patient.nama_pasien}</td>
                                    <td class="py-2 px-4 border-b">${patient.riwayat_penyakit}</td>
                                    <td class="py-2 px-4 border-b">${patient.kalori_makanan} kcal</td>
                                    <td class="py-2 px-4 border-b">${patient.kalori_harian} kcal</td>
                                    <td class="py-2 px-4 border-b">${patient.tipe_pasien}</td>
                                    <td class="flex py-2 px-4 border-b">
                                        <a href="/admin/patients/${patient.id}" class="text-blue-500 hover:text-blue-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.267-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="/admin/patients/edit/${patient.id}" text-green-500 hover:text-green-700" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m6 0l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }
    
            fetchPatients(todayString);
        });
    </script>
      
    <style>
        /* Pastikan hanya satu minggu yang ditampilkan */
        .fc-dayGrid-view .fc-scrollgrid {
            height: auto !important;
        }

        /* Memastikan tanggal selalu muncul */
        .fc-daygrid-day-number {
            font-size: 16px;
            font-weight: bold;
            padding: 8px;
            display: block !important;
        }

        .fc-toolbar-title {
            font-size: 18px;
            font-weight: bold;
        }

        /* Mengatur tampilan header agar hanya menampilkan hari */
        .fc-col-header-cell {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Menandai hari ini dengan warna khusus */
        .fc-day-today .fc-daygrid-day-number {
            background-color: #ffeb3b; /* Warna kuning cerah untuk hari ini */
            color: black;
        }

        /* Menyesuaikan ukuran kolom td */
        .fc-dayGrid-week td {
            width: 90px !important;  /* Menyesuaikan lebar kolom <td> */
            height: 80px !important; /* Menyesuaikan tinggi kolom <td> */
        }

        /* Mengurangi padding dan margin untuk membuat kolom lebih rapat */
        .fc-dayGrid-day .fc-daygrid-day-number {
            margin: 0;
            padding: 4px;
        }

        /* Membuat header lebih kompak */
        .fc-col-header-cell {
            padding: 4px 0;
            text-align: center;
        }
        .fc .fc-scrollgrid-section-body table{
            height: unset !important;
        }
        .fc .fc-view-harness{
            height: 100px !important;
        }
    </style>
</x-app-layout>

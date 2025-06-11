<!-- resources/views/user/dashboard.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Tenaga Gizi') }}
        </h2>
    </x-slot>

    <div class="py-2 px-2">
        <div id="calendar"></div>
    </div>
    <div class="flex flex-col md:flex-row items-start md:items-stretch">
        <div class="py-1 flex-grow">
            <div class="max-w-7xl mx-auto sm:px-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-col md:flex-row items-center justify-center md:justify-around gap-4">
                        <div class="relative w-48 h-48 flex items-center justify-center">
                            <canvas id="totalCaloriesChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-xs text-gray-500">Calories</span>
                                <span class="text-3xl font-bold text-gray-900" id="currentCalories">0</span>
                                <span class="text-sm text-gray-600">of <span id="targetCalories">0</span> Kcal</span>
                            </div>
                        </div>
    
                        <div class="w-full md:w-1/2">
                            <div class="bg-gray-50 p-2 rounded-lg shadow-sm">
                                <div class="flex justify-between items-center">
                                    <span class="font-normal text-gray-800">Protein</span>
                                    <span class="flex"><span id="proteinCurrent">0</span> / <span id="proteinTarget">0</span>g</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-500 h-2.5 rounded-full" style="width: 0%;"></div> {{-- Sesuaikan width % --}}
                                </div>
                            </div>
    
                            <div class="bg-gray-50 p-3 rounded-lg shadow-sm">
                                <div class="flex justify-between items-center">
                                    <span class="font-normal text-gray-800">Carbs</span>
                                    <span class="flex"><span id="carbsCurrent">0</span> / <span id="carbsTarget">0</span>g</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-yellow-500 h-2.5 rounded-full" style="width: 0%;"></div> {{-- Sesuaikan width % --}}
                                </div>
                            </div>
    
                            <div class="bg-gray-50 p-3 rounded-lg shadow-sm">
                                <div class="flex justify-between items-center">
                                    <span class="font-normal text-gray-800">Fat</span>
                                    <span class="flex"><span id="fatCurrent">0</span> / <span id="fatTarget">0</span>g</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-red-500 h-2.5 rounded-full" style="width: 0%;"></div> {{-- Sesuaikan width % --}}
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="text-center mt-6">
                        <span class="text-xl font-semibold text-gray-700">Eaten</span>
                        <span class="text-2xl font-bold text-gray-900 ml-2" id="caloriesEaten">0 Kcal</span>
                    </div>
                </div>
            </div>
        </div>
            
        <div class="py-1 flex-grow">
            <div class="max-w-7xl mx-auto sm:px-2 h-full">
                @if(session('success'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('success') }}
                    </div>
                @endif
    
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 h-full flex flex-col">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white h-full">
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
                            </tbody>
                        </table>
                    </div>
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
            let totalCaloriesChartInstance; // Variabel untuk menyimpan instance chart

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
                fetch(`/tenaga-gizi/patients/filter?date=${date}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(patientsData => { // Mengubah nama variabel menjadi patientsData karena langsung array pasien
                        console.log("Data pasien diterima dari server:", patientsData);

                        tableBody.innerHTML = ''; // Kosongkan tabel sebelum mengisi ulang

                        if (patientsData.length === 0) {
                            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">Tidak ada data pasien.</td></tr>`;
                            // Reset chart dan kalori jika tidak ada pasien
                            document.getElementById('currentCalories').textContent = '0';
                            document.getElementById('targetCalories').textContent = '0';
                            document.getElementById('caloriesEaten').textContent = '0 Kcal';
                            document.getElementById('proteinCurrent').textContent = '0';
                            document.getElementById('proteinTarget').textContent = '0';
                            document.getElementById('carbsCurrent').textContent = '0';
                            document.getElementById('carbsTarget').textContent = '0';
                            document.getElementById('fatCurrent').textContent = '0';
                            document.getElementById('fatTarget').textContent = '0';
                            renderTotalCaloriesChart(0, 0);
                            return;
                        }

                        let totalCurrentCals = 0;
                        let totalTargetCals = 0;
                        let totalProteinCurrent = 0;
                        let totalProteinTarget = 0;
                        let totalCarbsCurrent = 0;
                        let totalCarbsTarget = 0;
                        let totalFatCurrent = 0;
                        let totalFatTarget = 0;

                        patientsData.forEach(patient => {
                            // Akumulasi kalori dan makro dari setiap pasien
                            totalCurrentCals += patient.kalori_makanan || 0;
                            totalTargetCals += patient.kalori_harian || 0;
                            totalProteinCurrent += patient.protein || 0; // Asumsi ada kolom protein_makanan
                            totalProteinTarget += patient.total_protein || 0;  // Asumsi ada kolom protein_harian
                            totalCarbsCurrent += patient.karbohidrat_makanan || 0; // Asumsi ada kolom karbohidrat_makanan
                            totalCarbsTarget += patient.karbohidrat_harian || 0; // Asumsi ada kolom karbohidrat_harian
                            totalFatCurrent += patient.lemak_makanan || 0;     // Asumsi ada kolom lemak_makanan
                            totalFatTarget += patient.lemak_harian || 0;     // Asumsi ada kolom lemak_harian

                            let row = `
                                <tr>
                                    <td class="py-2 px-4 border-b">${patient.no_kamar}</td>
                                    <td class="py-2 px-4 border-b">${patient.nama_pasien}</td>
                                    <td class="py-2 px-4 border-b">${patient.riwayat_penyakit}</td>
                                    <td class="py-2 px-4 border-b">${patient.kalori_makanan} kcal</td>
                                    <td class="py-2 px-4 border-b">${patient.kalori_harian} kcal</td>
                                    <td class="py-2 px-4 border-b">${patient.tipe_pasien}</td>
                                    <td class="flex py-2 px-4 border-b">
                                        <a href="/ahli-gizi/patients/${patient.id}" class="text-blue-500 hover:text-blue-700 mr-2" title="Lihat">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.267-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });

                        // Update tampilan kalori dan makro
                        document.getElementById('currentCalories').textContent = totalCurrentCals;
                        document.getElementById('targetCalories').textContent = totalTargetCals;
                        document.getElementById('caloriesEaten').textContent = `${totalCurrentCals} Kcal`;
                        
                        document.getElementById('proteinCurrent').textContent = totalProteinCurrent;
                        document.getElementById('proteinTarget').textContent = totalProteinTarget;
                        document.getElementById('carbsCurrent').textContent = totalCarbsCurrent;
                        document.getElementById('carbsTarget').textContent = totalCarbsTarget;
                        document.getElementById('fatCurrent').textContent = totalFatCurrent;
                        document.getElementById('fatTarget').textContent = totalFatTarget;

                        // Render ulang chart dengan data yang baru
                        renderTotalCaloriesChart(totalCurrentCals, totalTargetCals);
                        updateMacroProgressBars(totalProteinCurrent, totalProteinTarget, totalCarbsCurrent, totalCarbsTarget, totalFatCurrent, totalFatTarget);

                    })
                    .catch(error => {
                        console.error('Error fetching patient data:', error);
                        // Tampilkan pesan error dan reset data jika fetch gagal
                        document.getElementById('currentCalories').textContent = 'Error';
                        document.getElementById('targetCalories').textContent = 'Error';
                        document.getElementById('caloriesEaten').textContent = 'Error';
                        document.getElementById('proteinCurrent').textContent = '0';
                        document.getElementById('proteinTarget').textContent = '0';
                        document.getElementById('carbsCurrent').textContent = '0';
                        document.getElementById('carbsTarget').textContent = '0';
                        document.getElementById('fatCurrent').textContent = '0';
                        document.getElementById('fatTarget').textContent = '0';
                        renderTotalCaloriesChart(0, 0); // Render chart dengan 0 jika ada error
                        tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-red-500">Gagal memuat data pasien.</td></tr>`;
                    });
            }

            function renderTotalCaloriesChart(currentCals, targetCals) {
                const ctx = document.getElementById('totalCaloriesChart').getContext('2d');

                // Hancurkan chart sebelumnya jika ada
                if (totalCaloriesChartInstance) {
                    totalCaloriesChartInstance.destroy();
                }

                const remainingCals = Math.max(0, targetCals - currentCals); // Pastikan tidak negatif

                totalCaloriesChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Consumed', 'Remaining'],
                        datasets: [{
                            data: [currentCals, remainingCals],
                            backgroundColor: [
                                '#4CAF50', // Hijau untuk kalori yang sudah dimakan
                                '#E0E0E0'  // Abu-abu untuk sisa kalori
                            ],
                            borderColor: [
                                'rgba(0,0,0,0)',
                                'rgba(0,0,0,0)'
                            ],
                            borderWidth: 0,
                            borderRadius: 10, // Membuat ujung bar membulat
                            cutout: '80%', // Ketebalan cincin
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false // Sembunyikan legenda
                            },
                            tooltip: {
                                enabled: false // Sembunyikan tooltip
                            }
                        }
                    }
                });
            }

            function updateMacroProgressBars(proteinCurrent, proteinTarget, carbsCurrent, carbsTarget, fatCurrent, fatTarget) {
                // Protein
                const proteinPercentage = proteinTarget > 0 ? (proteinCurrent / proteinTarget) * 100 : 0;
                document.getElementById('proteinCurrent').textContent = proteinCurrent;
                document.getElementById('proteinTarget').textContent = proteinTarget;
                const proteinProgressBar = document.querySelector('#proteinCurrent').closest('.bg-gray-50').querySelector('.bg-blue-500');
                if (proteinProgressBar) { // Penambahan cek ini membuat kode lebih aman
                    proteinProgressBar.style.width = `${Math.min(proteinPercentage, 100)}%`;
                }

                // Carbs
                const carbsPercentage = carbsTarget > 0 ? (carbsCurrent / carbsTarget) * 100 : 0;
                document.getElementById('carbsCurrent').textContent = carbsCurrent;
                document.getElementById('carbsTarget').textContent = carbsTarget;
                const carbsProgressBar = document.querySelector('#carbsCurrent').closest('.bg-gray-50').querySelector('.bg-yellow-500');
                if (carbsProgressBar) { // Penambahan cek ini membuat kode lebih aman
                    carbsProgressBar.style.width = `${Math.min(carbsPercentage, 100)}%`;
                }

                // Fat
                const fatPercentage = fatTarget > 0 ? (fatCurrent / fatTarget) * 100 : 0;
                document.getElementById('fatCurrent').textContent = fatCurrent;
                document.getElementById('fatTarget').textContent = fatTarget;
                const fatProgressBar = document.querySelector('#fatCurrent').closest('.bg-gray-50').querySelector('.bg-red-500');
                if (fatProgressBar) { // Penambahan cek ini membuat kode lebih aman
                    fatProgressBar.style.width = `${Math.min(fatPercentage, 100)}%`;
                }
            }
            fetchPatients(todayString);
        });
    </script>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
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
            width: 90px !important;
            height: 80px !important;
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

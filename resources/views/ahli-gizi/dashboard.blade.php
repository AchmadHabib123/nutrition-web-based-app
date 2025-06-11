<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Ahli Gizi') }}
        </h2>
    </x-slot>

    <div class="py-2 px-2">
        <div id="calendar"></div>
    </div>
    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Total Kalori</h3>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-center md:justify-around gap-6">
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

                    <div class="w-full md:w-1/2 space-y-4">
                        <div class="bg-gray-50 p-3 rounded-lg shadow-sm">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-medium text-gray-800">Protein</span>
                                <span><span id="proteinCurrent">0</span> / <span id="proteinTarget">0</span>g</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-500 h-2.5 rounded-full" style="width: 0%;"></div> {{-- Sesuaikan width % --}}
                            </div>
                        </div>

                        <div class="bg-gray-50 p-3 rounded-lg shadow-sm">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-medium text-gray-800">Carbs</span>
                                <span><span id="carbsCurrent">0</span> / <span id="carbsTarget">0</span>g</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-yellow-500 h-2.5 rounded-full" style="width: 0%;"></div> {{-- Sesuaikan width % --}}
                            </div>
                        </div>

                        <div class="bg-gray-50 p-3 rounded-lg shadow-sm">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-medium text-gray-800">Fat</span>
                                <span><span id="fatCurrent">0</span> / <span id="fatTarget">0</span>g</span>
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
        
    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4">
            @if(session('success'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Pasien</h3>
                    <a href="{{ route('ahli-gizi.patients.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                                <th class="py-2 px-4 border-b">Validasi</th>
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
    
    {{-- <script>
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
                fetch(`/ahli-gizi/patients/filter?date=${date}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        // Respons dari backend sekarang adalah objek { patients: [], summary: {} }
                        return response.json();
                    })
                    .then(data => { // Mengubah nama variabel menjadi 'data' karena berisi objek {patients, summary}
                        console.log("Data diterima dari server:", data);

                        const patientsData = data.patients || []; // Ambil array pasien
                        const summaryData = data.summary || {};   // Ambil objek summary

                        tableBody.innerHTML = ''; // Kosongkan tabel sebelum mengisi ulang

                        if (patientsData.length === 0) {
                            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">Tidak ada data pasien.</td></tr>`;
                            // Reset chart dan kalori jika tidak ada pasien
                            document.getElementById('currentCalories').textContent = '0';
                            document.getElementById('targetCalories').textContent = '0';
                            document.getElementById('caloriesEaten').textContent = '0 Kcal';
                            document.getElementById('proteinCurrent').textContent = '0';
                            // proteinTarget tidak lagi ada di HTML
                            document.getElementById('carbsCurrent').textContent = '0';
                            // carbsTarget tidak lagi ada di HTML
                            document.getElementById('fatCurrent').textContent = '0';
                            // fatTarget tidak lagi ada di HTML
                            renderTotalCaloriesChart(0, 0);
                            // Panggil updateMacroProgressBars dengan target 0 untuk meresetnya
                            updateMacroProgressBars(0, 0, 0, 0, 0, 0);
                            return;
                        }

                        // Variabel untuk mengakumulasi total Scheduled dari semua pasien
                        // Kita akan menggunakan summaryData dari backend, jadi variabel ini tidak lagi diperlukan di sini.
                        // Namun, karena ada `patient.kalori_makanan_hari_ini` dan `patient.kalori_harian`
                        // di loop `patientsData.forEach`, mari kita pastikan penamaan yang konsisten.
                        // Di sini, `totalCurrentCals` dan `totalTargetCals` akan diambil dari `summaryData`.

                        patientsData.forEach(patient => {
                            // Di sini, Anda masih bisa menampilkan kalori_makanan_terjadwal_hari_ini
                            // dan kalori_harian (target) per pasien di baris tabel.
                            let row = `
                                <tr>
                                    <td class="py-2 px-4 border-b">${patient.no_kamar}</td>
                                    <td class="py-2 px-4 border-b">${patient.nama_pasien}</td>
                                    <td class="py-2 px-4 border-b">${patient.riwayat_penyakit}</td>
                                    <td class="py-2 px-4 border-b">${patient.kalori_makanan_hari_ini ?? 0} kcal</td>
                                    <td class="py-2 px-4 border-b">${patient.kalori_harian ?? 0} kcal</td>
                                    <td class="py-2 px-4 border-b">${patient.tipe_pasien}</td>
                                    <td class="flex py-2 px-4 border-b">
                                        <a href="/ahli-gizi/patients/${patient.id}" class="text-blue-500 hover:text-blue-700 mr-2" title="Lihat">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.267-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="/ahli-gizi/patients/${patient.id}/edit" class="text-green-500 hover:text-green-700" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });

                        // Update tampilan kalori dan makro dari summaryData
                        // Total Kalori (terjadwal vs target)
                        document.getElementById('currentCalories').textContent = summaryData.total_scheduled_calories ?? 0;
                        document.getElementById('targetCalories').textContent = summaryData.total_target_calories ?? 0;
                        document.getElementById('caloriesEaten').textContent = `${summaryData.total_scheduled_calories ?? 0} Kcal`;
                        
                        // Makro (hanya current/terjadwal, tanpa target)
                        document.getElementById('proteinCurrent').textContent = (summaryData.total_scheduled_protein ?? 0).toFixed(1);
                        // document.getElementById('proteinTarget').textContent = totalProteinTarget; // Hapus baris ini dari HTML dan script
                        document.getElementById('carbsCurrent').textContent = (summaryData.total_scheduled_carbs ?? 0).toFixed(1);
                        // document.getElementById('carbsTarget').textContent = totalCarbsTarget; // Hapus baris ini dari HTML dan script
                        document.getElementById('fatCurrent').textContent = (summaryData.total_scheduled_fat ?? 0).toFixed(1);
                        // document.getElementById('fatTarget').textContent = totalFatTarget; // Hapus baris ini dari HTML dan script

                        // Render ulang chart kalori dengan data yang baru
                        renderTotalCaloriesChart(summaryData.total_scheduled_calories ?? 0, summaryData.total_target_calories ?? 0);
                        
                        // Update progress bar makro (dengan target 0 karena tidak ada target spesifik di patients)
                        updateMacroProgressBars(
                            summaryData.total_scheduled_protein ?? 0, 0, // Target protein 0
                            summaryData.total_scheduled_carbs ?? 0, 0,  // Target karbohidrat 0
                            summaryData.total_scheduled_fat ?? 0, 0    // Target lemak 0
                        );

                    })
                    .catch(error => {
                        console.error('Error fetching patient data:', error);
                        // Tampilkan pesan error dan reset data jika fetch gagal
                        document.getElementById('currentCalories').textContent = 'Error';
                        document.getElementById('targetCalories').textContent = 'Error';
                        document.getElementById('caloriesEaten').textContent = 'Error';
                        document.getElementById('proteinCurrent').textContent = '0';
                        // document.getElementById('proteinTarget').textContent = '0'; // Hapus ini
                        document.getElementById('carbsCurrent').textContent = '0';
                        // document.getElementById('carbsTarget').textContent = '0'; // Hapus ini
                        document.getElementById('fatCurrent').textContent = '0';
                        // document.getElementById('fatTarget').textContent = '0'; // Hapus ini
                        renderTotalCaloriesChart(0, 0); // Render chart dengan 0 jika ada error
                        updateMacroProgressBars(0, 0, 0, 0, 0, 0); // Reset progress bars
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

            // Sesuaikan fungsi ini untuk tidak menggunakan target makro (protein, carbs, fat)
            function updateMacroProgressBars(proteinCurrent, proteinTarget, carbsCurrent, carbsTarget, fatCurrent, fatTarget) {
                // Protein
                // Karena tidak ada target di HTML, progress bar akan selalu 100% jika ada nilai.
                // Atau Anda bisa mengubah style agar hanya menunjukkan nilai, bukan progres.
                // Jika tetap ingin progress bar mengisi penuh saat ada nilai:
                const proteinPercentage = proteinCurrent > 0 ? 100 : 0;
                document.getElementById('proteinCurrent').textContent = proteinCurrent.toFixed(1);
                // document.getElementById('proteinTarget').textContent = proteinTarget; // Baris ini tidak lagi ada di HTML
                document.querySelector('#proteinCurrent').closest('.bg-gray-50').querySelector('.bg-blue-500').style.width = `${proteinPercentage}%`;

                // Carbs
                const carbsPercentage = carbsCurrent > 0 ? 100 : 0;
                document.getElementById('carbsCurrent').textContent = carbsCurrent.toFixed(1);
                // document.getElementById('carbsTarget').textContent = carbsTarget; // Baris ini tidak lagi ada di HTML
                document.querySelector('#carbsCurrent').closest('.bg-gray-50').querySelector('.bg-yellow-500').style.width = `${carbsPercentage}%`;

                // Fat
                const fatPercentage = fatCurrent > 0 ? 100 : 0;
                document.getElementById('fatCurrent').textContent = fatCurrent.toFixed(1);
                // document.getElementById('fatTarget').textContent = fatTarget; // Baris ini tidak lagi ada di HTML
                document.querySelector('#fatCurrent').closest('.bg-gray-50').querySelector('.bg-red-500').style.width = `${fatPercentage}%`;
            }


            // Panggil fungsi fetchPatients dan renderTotalCaloriesChart saat halaman pertama kali dimuat
            // agar data awal terisi sesuai tanggal hari ini
            fetchPatients(todayString);
            // renderTotalCaloriesChart(0, 0); // Inisialisasi chart dengan 0, akan diperbarui oleh fetchPatients

        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var tableBody = document.querySelector('tbody');
    
            var today = new Date();
            var todayString = today.toISOString().split('T')[0];
            let totalCaloriesChartInstance;
    
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
                    fetchPatientsAndDashboardData(info.dateStr); // Mengubah nama fungsi
                }
            });
    
            calendar.render();
    
            // Mengubah nama fungsi fetchPatients menjadi fetchPatientsAndDashboardData
            function fetchPatientsAndDashboardData(date) {
                console.log("Mengambil data untuk tanggal:", date);
                fetch(`/ahli-gizi/patients/filter?date=${date}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Data diterima dari server:", data);
    
                        const patientsData = data.patients || [];
                        const summaryData = data.summary || {};
    
                        tableBody.innerHTML = ''; // Kosongkan tabel sebelum mengisi ulang
    
                        if (patientsData.length === 0) {
                            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">Tidak ada data pasien.</td></tr>`;
                            // Reset chart dan kalori jika tidak ada pasien
                            document.getElementById('currentCalories').textContent = '0';
                            document.getElementById('targetCalories').textContent = '0';
                            document.getElementById('caloriesEaten').textContent = '0 Kcal';
                            document.getElementById('proteinCurrent').textContent = '0';
                            document.getElementById('carbsCurrent').textContent = '0';
                            document.getElementById('fatCurrent').textContent = '0';
                            renderTotalCaloriesChart(0, 0);
                            updateMacroProgressBars(0, 0, 0, 0, 0, 0);
                            return;
                        }
    
                        patientsData.forEach(patient => {
                            let validationStatusHtml = '';
                            if (patient.pending_validation_count > 0) {
                                // Link ke halaman detail pasien dengan tanggal yang dipilih
                                const validationUrl = `/ahli-gizi/patients/${patient.id}`;
                                validationStatusHtml = `
                                    <a href="${validationUrl}" class="text-yellow-600 hover:text-yellow-800 font-bold flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        ${patient.pending_validation_count} Menunggu Validasi (${patient.pending_validation_calories ?? 0} kcal)
                                    </a>`;
                            } else {
                                validationStatusHtml = `<span class="text-gray-500">Sudah Tervalidasi</span>`;
                            }
                            let row = `
                                <tr>
                                    <td class="py-2 px-4 border-b">${patient.no_kamar}</td>
                                    <td class="py-2 px-4 border-b">${patient.nama_pasien}</td>
                                    <td class="py-2 px-4 border-b">${patient.riwayat_penyakit}</td>
                                    <td class="py-2 px-4 border-b">${patient.kalori_makanan_hari_ini ?? 0} kcal</td>
                                    <td class="py-2 px-4 border-b">${patient.kalori_harian ?? 0} kcal</td>
                                    <td class="py-2 px-4 border-b">${patient.tipe_pasien}</td>
                                    <td class="py-2 px-4 border-b">${validationStatusHtml}</td>
                                    <td class="flex py-2 px-4 border-b">
                                        <a href="/ahli-gizi/patients/${patient.id}" class="text-blue-500 hover:text-blue-700 mr-2" title="Lihat">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.267-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="/ahli-gizi/patients/${patient.id}/edit" class="text-green-500 hover:text-green-700" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });
    
                        // Update tampilan kalori dan makro dari summaryData (ini bagian yang disesuaikan)
                        // Total Kalori (current sekarang adalah total_consumed_calories)
                        document.getElementById('currentCalories').textContent = summaryData.total_consumed_calories ?? 0;
                        document.getElementById('targetCalories').textContent = summaryData.total_target_calories ?? 0;
                        document.getElementById('caloriesEaten').textContent = `${summaryData.total_consumed_calories ?? 0} Kcal`;
                        
                        // Makro (hanya current/terkonsumsi)
                        document.getElementById('proteinCurrent').textContent = (summaryData.total_consumed_protein ?? 0).toFixed(1);
                        document.getElementById('carbsCurrent').textContent = (summaryData.total_consumed_carbs ?? 0).toFixed(1);
                        document.getElementById('fatCurrent').textContent = (summaryData.total_consumed_fat ?? 0).toFixed(1);
    
                        // Render ulang chart kalori dengan data yang baru
                        renderTotalCaloriesChart(summaryData.total_consumed_calories ?? 0, summaryData.total_target_calories ?? 0);
                        
                        // Update progress bar makro (target 0 karena tidak ada target spesifik untuk makro)
                        updateMacroProgressBars(
                            summaryData.total_consumed_protein ?? 0, 0, // Target protein 0
                            summaryData.total_consumed_carbs ?? 0, 0,  // Target karbohidrat 0
                            summaryData.total_consumed_fat ?? 0, 0    // Target lemak 0
                        );
    
                    })
                    .catch(error => {
                        console.error('Error fetching patient data:', error);
                        // Tampilkan pesan error dan reset data jika fetch gagal
                        document.getElementById('currentCalories').textContent = 'Error';
                        document.getElementById('targetCalories').textContent = 'Error';
                        document.getElementById('caloriesEaten').textContent = 'Error';
                        document.getElementById('proteinCurrent').textContent = '0';
                        document.getElementById('carbsCurrent').textContent = '0';
                        document.getElementById('fatCurrent').textContent = '0';
                        renderTotalCaloriesChart(0, 0);
                        updateMacroProgressBars(0, 0, 0, 0, 0, 0);
                        tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-red-500">Gagal memuat data pasien.</td></tr>`;
                    });
            }
    
            function renderTotalCaloriesChart(currentCals, targetCals) {
                const ctx = document.getElementById('totalCaloriesChart').getContext('2d');
                if (totalCaloriesChartInstance) {
                    totalCaloriesChartInstance.destroy();
                }
                const remainingCals = Math.max(0, targetCals - currentCals);
                totalCaloriesChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Consumed', 'Remaining'],
                        datasets: [{
                            data: [currentCals, remainingCals],
                            backgroundColor: [ '#4CAF50', '#E0E0E0' ],
                            borderColor: [ 'rgba(0,0,0,0)', 'rgba(0,0,0,0)' ],
                            borderWidth: 0,
                            borderRadius: 10,
                            cutout: '80%',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        }
                    }
                });
            }
    
            function updateMacroProgressBars(proteinCurrent, proteinTarget, carbsCurrent, carbsTarget, fatCurrent, fatTarget) {
                // proteinTarget, carbsTarget, fatTarget akan selalu 0 dari pemanggil
                // jadi progress bar akan mengisi 100% jika current > 0
                
                // Protein
                const proteinPercentage = proteinCurrent > 0 ? 100 : 0;
                document.getElementById('proteinCurrent').textContent = proteinCurrent.toFixed(1);
                document.querySelector('#proteinCurrent').closest('.bg-gray-50').querySelector('.bg-blue-500').style.width = `${proteinPercentage}%`;
    
                // Carbs
                const carbsPercentage = carbsCurrent > 0 ? 100 : 0;
                document.getElementById('carbsCurrent').textContent = carbsCurrent.toFixed(1);
                document.querySelector('#carbsCurrent').closest('.bg-gray-50').querySelector('.bg-yellow-500').style.width = `${carbsPercentage}%`;
    
                // Fat
                const fatPercentage = fatCurrent > 0 ? 100 : 0;
                document.getElementById('fatCurrent').textContent = fatCurrent.toFixed(1);
                document.querySelector('#fatCurrent').closest('.bg-gray-50').querySelector('.bg-red-500').style.width = `${fatPercentage}%`;
            }
    
            // Panggil fungsi fetchPatientsAndDashboardData saat halaman pertama kali dimuat
            fetchPatientsAndDashboardData(todayString); // Mengubah pemanggilan fungsi
    
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
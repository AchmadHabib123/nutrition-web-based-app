<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Jadwal Makanan & Konsumsi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Notifikasi & Errors --}}
            @if(session('success')) <div class="mb-4 font-medium text-sm text-green-600">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="mb-4 font-medium text-sm text-red-600">{{ session('error') }}</div> @enderror
            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600">
                    <div class="font-medium">{{ __('Whoops! Something went wrong.') }}</div>
                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm rounded-lg">
                <form id="jadwal-form" action="{{ route('ahli-gizi.jadwal-makanans.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        {{-- Bagian Input Tanggal & Tipe Pasien Global --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border p-4 rounded-md bg-gray-50">
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('tanggal_mulai', \Carbon\Carbon::today()->toDateString()) }}" required>
                                @error('tanggal_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('tanggal_selesai', \Carbon\Carbon::today()->toDateString()) }}" required>
                                @error('tanggal_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tipe_pasien" class="block text-sm font-medium text-gray-700">Tipe Pasien (Global)</label>
                                <select name="tipe_pasien" id="tipe_pasien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="">Pilih Tipe Pasien</option>
                                    @foreach($tipePasienOptions as $option)
                                        <option value="{{ $option }}" @if(old('tipe_pasien') == $option) selected @endif>{{ $option }}</option>
                                    @endforeach
                                </select>
                                @error('tipe_pasien') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <hr class="my-6">

                        {{-- === Bagian Detail Jadwal per Pasien/Tanggal/Waktu === --}}
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Jadwal Konsumsi</h3>
                        
                        <div class="mb-4">
                            <button type="button" id="auto-generate-schedule" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Auto-Generate Jadwal (Berdasarkan Siklus & Pedoman)
                            </button>
                            <span class="ml-4 text-sm text-gray-600">Klik untuk mengisi jadwal secara otomatis.</span>
                        </div>

                        <div id="jadwal-detail-container" class="space-y-6">
                            {{-- Baris Jadwal akan ditambahkan di sini oleh JavaScript --}}
                        </div>
                        @error('jadwal_detail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('jadwal_detail.*.patient_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('jadwal_detail.*.date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('jadwal_detail.*.waktu_makan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('jadwal_detail.*.menu_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror


                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Semua Jadwal
                            </button>
                            <a href="{{ route('ahli-gizi.jadwal-makanans.index') }}" class="ml-2 text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- === JAVASCRIPT DINAMIS === --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const autoGenerateBtn = document.getElementById('auto-generate-schedule');
            const jadwalDetailContainer = document.getElementById('jadwal-detail-container');
            const tanggalMulaiInput = document.getElementById('tanggal_mulai');
            const tanggalSelesaiInput = document.getElementById('tanggal_selesai');
            const globalTipePasienSelect = document.getElementById('tipe_pasien');
            
            let rowIndex = 0; // Untuk indeks unik setiap baris form

            // Data yang diperlukan dari Controller (dilewatkan dari PHP ke JS)
            const allPatients = @json($patients);
            const allMenus = @json($menus);
            const allWaktuMakanOptions = @json($waktuMakanOptions);

            // Fungsi untuk membuat satu baris jadwal detail (pasien, tanggal, waktu, menu)
            function createJadwalDetailRow(patientId = '', date = '', waktuMakan = '', menuId = '', index = rowIndex) {
                const row = document.createElement('div');
                row.classList.add('jadwal-detail-item', 'border', 'p-4', 'rounded-md', 'bg-white', 'shadow-sm', 'grid', 'grid-cols-1', 'md:grid-cols-5', 'gap-4', 'items-end');
                row.innerHTML = `
                    <div>
                        <label for="patient_${index}" class="block text-sm font-medium text-gray-700">Pasien</label>
                        <select name="jadwal_detail[${index}][patient_id]" id="patient_${index}" class="jadwal-patient-select mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">Pilih Pasien</option>
                            ${allPatients.map(p => `<option value="${p.id}" ${patientId == p.id ? 'selected' : ''}>${p.nama_pasien} (${p.no_kamar})</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label for="date_${index}" class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="jadwal_detail[${index}][date]" id="date_${index}" class="jadwal-date-input mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="${date}" required>
                    </div>
                    <div>
                        <label for="waktu_makan_${index}" class="block text-sm font-medium text-gray-700">Waktu Makan</label>
                        <select name="jadwal_detail[${index}][waktu_makan]" id="waktu_makan_${index}" class="jadwal-waktu-makan-select mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">Pilih Waktu</option>
                            ${allWaktuMakanOptions.map(w => `<option value="${w}" ${waktuMakan == w ? 'selected' : ''}>${w.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase())}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label for="menu_${index}" class="block text-sm font-medium text-gray-700">Menu Rekomendasi / Manual</label>
                        <select name="jadwal_detail[${index}][menu_id]" id="menu_${index}" class="jadwal-menu-select mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">Pilih Menu</option>
                            ${allMenus.map(m => `<option value="${m.id}" ${menuId == m.id ? 'selected' : ''}>${m.nama} (${m.kalori} kcal)</option>`).join('')}
                        </select>
                        <div id="recommendation-list-${index}" class="text-sm mt-1 space-y-1">
                            </div>
                    </div>
                    <div>
                        <button type="button" class="remove-jadwal-btn bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md">Hapus</button>
                    </div>
                `;
                jadwalDetailContainer.appendChild(row);
                setupJadwalRowEventListeners(row, index);
                rowIndex++; // Increment global index
                return row;
            }

            // Fungsi untuk setup event listeners pada satu baris jadwal
            function setupJadwalRowEventListeners(rowElement, index) {
                const patientSelect = rowElement.querySelector(`#patient_${index}`);
                const dateInput = rowElement.querySelector(`#date_${index}`);
                const waktuMakanSelect = rowElement.querySelector(`#waktu_makan_${index}`);
                const menuSelect = rowElement.querySelector(`#menu_${index}`);
                const recommendationListDiv = rowElement.querySelector(`#recommendation-list-${index}`);
                const removeBtn = rowElement.querySelector('.remove-jadwal-btn');

                // Hapus baris
                removeBtn.addEventListener('click', function() {
                    rowElement.remove();
                });

                // Fungsi untuk memicu rekomendasi
                const fetchRecommendations = async () => {
                    const patientId = patientSelect.value;
                    const date = dateInput.value;
                    const waktuMakan = waktuMakanSelect.value;

                    if (!patientId || !date || !waktuMakan) {
                        recommendationListDiv.innerHTML = '<p class="text-yellow-600">Pilih pasien, tanggal, dan waktu untuk rekomendasi.</p>';
                        return;
                    }

                    recommendationListDiv.innerHTML = '<p class="text-blue-600">Memuat rekomendasi...</p>';

                    try {
                        const response = await fetch(`{{ route('ahli-gizi.jadwal-makanans.recommendations') }}?patient_id=${patientId}&date=${date}&waktu_makan=${waktuMakan}`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const recommendations = await response.json();
                        
                        renderRecommendations(recommendations, menuSelect, recommendationListDiv);

                    } catch (error) {
                        console.error('Error fetching recommendations:', error);
                        recommendationListDiv.innerHTML = `<p class="text-red-600">Gagal memuat rekomendasi: ${error.message}</p>`;
                    }
                };

                // Event listeners untuk memicu rekomendasi
                patientSelect.addEventListener('change', fetchRecommendations);
                dateInput.addEventListener('change', fetchRecommendations);
                waktuMakanSelect.addEventListener('change', fetchRecommendations);
            }

            // Fungsi untuk merender rekomendasi ke dalam dropdown dan list
            function renderRecommendations(recommendations, menuSelectElement, recommendationListDiv) {
                // Bersihkan rekomendasi sebelumnya
                recommendationListDiv.innerHTML = '';
                
                // Hapus opsi menu rekomendasi sebelumnya dari select
                menuSelectElement.querySelectorAll('.recommended-option').forEach(opt => opt.remove());

                if (recommendations.length === 0) {
                    recommendationListDiv.innerHTML = '<p class="text-gray-500">Tidak ada rekomendasi cocok.</p>';
                    return;
                }

                recommendations.forEach((rec, idx) => {
                    // Tambahkan opsi rekomendasi ke dropdown menu
                    const option = document.createElement('option');
                    option.value = rec.menu.id;
                    option.classList.add('recommended-option'); // Tandai sebagai opsi rekomendasi
                    option.textContent = `${rec.menu.nama} (${rec.menu.kalori} kcal) - Rekomendasi ${idx + 1}`;
                    if (rec.suitability === 'suitable') {
                        option.textContent += ' (Sangat Cocok)';
                    } else if (rec.suitability.includes('mismatch') || rec.suitability.includes('unsuitable')) {
                         option.textContent += ' (Perlu Perhatian)';
                    }
                    menuSelectElement.appendChild(option);

                    // Tampilkan detail rekomendasi di bawah dropdown
                    const recDetail = document.createElement('div');
                    recDetail.classList.add('p-1', 'rounded', 'text-xs', rec.suitability === 'suitable' ? 'bg-green-50' : 'bg-red-50');
                    recDetail.innerHTML = `
                        <strong>${rec.menu.nama}</strong> (${rec.menu.kalori} kcal)
                        <br>Suitability: ${rec.suitability.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase())}
                        <br>Warnings: ${rec.warnings.length ? rec.warnings.join(', ') : 'Tidak ada'}
                        <br>Score: ${rec.score.toFixed(1)}
                        <button type="button" class="select-recommendation-btn bg-blue-100 text-blue-800 px-2 py-0.5 rounded ml-1" data-menu-id="${rec.menu.id}">Pilih</button>
                    `;
                    recommendationListDiv.appendChild(recDetail);
                });

                // Event listener untuk tombol "Pilih" rekomendasi
                recommendationListDiv.querySelectorAll('.select-recommendation-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const menuId = this.dataset.menuId;
                        menuSelectElement.value = menuId; // Pilih di dropdown
                    });
                });

                // Pilih rekomendasi pertama secara default di dropdown jika 'suitable'
                const firstSuitable = recommendations.find(rec => rec.suitability === 'suitable');
                if (firstSuitable) {
                    menuSelectElement.value = firstSuitable.menu.id;
                } else if (recommendations.length > 0) {
                     menuSelectElement.value = recommendations[0].menu.id; // Pilih yang pertama jika tidak ada yang suitable
                }
            }


            // === Fitur Auto-Generate Seluruh Jadwal untuk Range Tanggal & Pasien ===
            autoGenerateBtn.addEventListener('click', async function() {
                const startDate = tanggalMulaiInput.value;
                const endDate = tanggalSelesaiInput.value;
                const globalTipePasien = globalTipePasienSelect.value;

                if (!startDate || !endDate || !globalTipePasien) {
                    alert('Mohon isi Tanggal Mulai, Tanggal Selesai, dan Tipe Pasien (Global) terlebih dahulu.');
                    return;
                }
                
                // Kosongkan kontainer jadwal sebelum diisi ulang
                jadwalDetailContainer.innerHTML = '';
                rowIndex = 0; // Reset index

                // Looping melalui setiap pasien
                for (const patient of allPatients) { // Memang tidak efisien untuk banyak pasien, bisa dioptimalkan di backend
                    // Looping melalui setiap tanggal dalam rentang
                    let currentDate = new Date(startDate);
                    const lastDate = new Date(endDate);

                    while (currentDate <= lastDate) {
                        const formattedDate = currentDate.toISOString().split('T')[0];
                        
                        // Looping melalui setiap waktu makan
                        for (const waktuMakan of allWaktuMakanOptions) {
                            // Panggil API rekomendasi untuk setiap slot
                            // Ini akan memanggil rekomendasi satu per satu, agak lambat.
                            // Idealnya, service backend akan punya method yang bisa generate banyak sekaligus.
                            const patientId = patient.id;

                            // Kita akan panggil service rekomendasi secara langsung di sini
                            // Ini akan mensimulasikan panggilan API per slot
                            const response = await fetch(`{{ route('ahli-gizi.jadwal-makanans.recommendations') }}?patient_id=${patientId}&date=${formattedDate}&waktu_makan=${waktuMakan}`);
                            if (!response.ok) {
                                console.error(`Failed to get recommendation for ${patient.nama_pasien} on ${formattedDate} at ${waktuMakan}`);
                                continue; // Lanjutkan ke slot berikutnya
                            }
                            const recommendations = await response.json();
                            
                            let selectedMenuId = '';
                            let selectedMenuName = '';
                            if (recommendations.length > 0) {
                                const suitableRec = recommendations.find(rec => rec.suitability === 'suitable');
                                if (suitableRec) {
                                    selectedMenuId = suitableRec.menu.id;
                                    selectedMenuName = suitableRec.menu.nama;
                                } else {
                                    selectedMenuId = recommendations[0].menu.id; // Pilih yang pertama jika tidak ada yang suitable
                                    selectedMenuName = recommendations[0].menu.nama;
                                }
                            }

                            // Tambahkan baris ke form
                            createJadwalDetailRow(patientId, formattedDate, waktuMakan, selectedMenuId);
                        }
                        currentDate.setDate(currentDate.getDate() + 1); // Lanjut ke tanggal berikutnya
                    }
                }
                alert('Jadwal otomatis berhasil digenerate! Silakan tinjau sebelum disimpan.');
            });

            // Tambahkan setidaknya satu baris jadwal kosong saat pertama kali memuat halaman
            if (jadwalDetailContainer.children.length === 0) {
                createJadwalDetailRow();
            }
        });
    </script>
    @endpush
</x-app-layout>
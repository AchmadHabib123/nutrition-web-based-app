<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Menu Makanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            {{-- Notifikasi & Errors --}}
            @if(session('success')) <div class="mb-4 font-medium text-sm text-green-600">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="mb-4 font-medium text-sm text-red-600">{{ session('error') }}</div> @endif
            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600">
                    <div class="font-medium">{{ __('Whoops! Something went wrong.') }}</div>
                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm rounded-lg">
                <form action="{{ route('ahli-gizi.menus.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-4">
                        {{-- Field Nama Menu --}}
                        <div class="mb-4">
                            <label class="block text-gray-700">Nama Menu</label>
                            <input type="text" name="nama" class="w-full rounded border-gray-300" value="{{ old('nama') }}" required>
                            @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Field Deskripsi --}}
                        <div class="mb-4">
                            <label class="block text-gray-700">Deskripsi</label>
                            <textarea name="deskripsi" class="w-full rounded border-gray-300">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Field Gambar --}}
                        <div class="mb-4">
                            <label class="block text-gray-700">Gambar (Opsional)</label>
                            <input type="file" name="gambar" class="w-full rounded border-gray-300">
                            @error('gambar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Field Tipe Pasien --}}
                        <div class="mb-4">
                            <label class="block text-gray-700">Tipe Pasien</label>
                            <select name="tipe_pasien" class="w-full rounded border-gray-300" required>
                                <option value="">Pilih Tipe Pasien</option>
                                <option value="VVIP" @if(old('tipe_pasien') == 'VVIP') selected @endif>VVIP</option>
                                <option value="VIP" @if(old('tipe_pasien') == 'VIP') selected @endif>VIP</option>
                                <option value="Normal" @if(old('tipe_pasien') == 'Normal') selected @endif>Normal</option>
                            </select>
                            @error('tipe_pasien') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <hr class="my-6">

                        {{-- === Bagian Komposisi Bahan Makanan Dinamis === --}}
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Komposisi Bahan Makanan</h3>
                        <div id="bahan-makanan-list" class="space-y-4">
                            {{-- Baris bahan makanan akan diinisialisasi oleh JavaScript --}}
                        </div>
                        <button type="button" id="add-bahan-btn" class="mt-4 bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Bahan Makanan
                        </button>
                        {{-- Error messages for dynamic fields --}}
                        @error('bahan_makanans') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('bahan_makanans.*.id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('bahan_makanans.*.jumlah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                        <hr class="my-6">

                        {{-- === Bagian Kompatibel dengan Diet Khusus === --}}
                        <div class="mb-4">
                            <label for="diet_khusus_ids" class="block text-sm font-medium text-gray-700">Kompatibel dengan Diet Khusus (Pilih beberapa)</label>
                            <select name="diet_khusus_ids[]" id="diet_khusus_ids" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" multiple>
                                <option value="" disabled>Pilih Diet Khusus (Opsional)</option>
                                @foreach($dietKhusus as $diet)
                                    <option value="{{ $diet->id }}" @if(in_array($diet->id, old('diet_khusus_ids', []))) selected @endif>
                                        {{ $diet->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('diet_khusus_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div> {{-- End of space-y-4 --}}

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Menu</button>
                </form>
            </div>
        </div>
    </div>

    {{-- === JAVASCRIPT DINAMIS === --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bahanMakananList = document.getElementById('bahan-makanan-list');
            const addBahanBtn = document.getElementById('add-bahan-btn');
            let bahanIndex = 0; // Untuk indeks unik setiap baris form (dimulai dari 0 di create)

            // Data semua bahan makanan yang tersedia (dari controller)
            const allBahanMakanans = [
                @foreach($bahanMakanans as $bahan)
                    {
                        id: {{ $bahan->id }},
                        nama: '{{ $bahan->nama }}',
                        portionValue: '{{ $bahan->standard_portion_value ?? '' }}',
                        portionUnit: '{{ $bahan->standard_portion_unit ?? 'g' }}'
                    },
                @endforeach
            ];

            // Fungsi untuk membuat baris bahan makanan baru (digunakan oleh JS)
            function createBahanMakananRowJs(selectedBahanId = '', jumlah = '', isSelected = false) {
                const row = document.createElement('div');
                row.classList.add('bahan-makanan-item', 'flex', 'flex-col', 'sm:flex-row', 'items-center', 'gap-2');
                
                let optionsHtml = '<option value="">Pilih Bahan Makanan</option>';
                allBahanMakanans.forEach(bahan => {
                    optionsHtml += `<option value="${bahan.id}"
                                            data-portion-value="${bahan.portionValue}"
                                            data-portion-unit="${bahan.portionUnit}"
                                            ${selectedBahanId == bahan.id ? 'selected' : ''}>
                                        ${bahan.nama}
                                        ${bahan.portionValue ? ` (Porsi Std: ${bahan.portionValue} ${bahan.portionUnit})` : ''}
                                    </option>`;
                });

                row.innerHTML = `
                    <div class="w-full sm:w-1/2">
                        <input type="checkbox" name="bahan_makanans[${bahanIndex}][selected]" value="1" class="bahan-selected-checkbox" ${isSelected ? 'checked' : ''}>
                        <label class="inline-block ml-2 text-sm font-medium text-gray-700">Pilih Bahan:</label>
                        <select name="bahan_makanans[${bahanIndex}][id]" class="bahan-select mt-1 block w-full rounded-md border-gray-300 shadow-sm" ${!isSelected ? 'disabled' : ''} required>
                            ${optionsHtml}
                        </select>
                        <input type="hidden" name="bahan_makanans[${bahanIndex}][hidden_id]" value="${selectedBahanId}">
                    </div>
                    <div class="w-full sm:w-1/4">
                        <label for="jumlah_${bahanIndex}" class="sr-only">Jumlah (gram)</label>
                        <input type="number" name="bahan_makanans[${bahanIndex}][jumlah]" id="jumlah_${bahanIndex}" class="bahan-jumlah mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="${jumlah}" min="1" placeholder="Jumlah (gram)" ${!isSelected ? 'disabled' : ''} required>
                    </div>
                    <div class="w-full sm:w-1/4 flex justify-end">
                        <button type="button" class="remove-bahan-btn bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-md text-sm">Hapus</button>
                    </div>
                `;
                bahanMakananList.appendChild(row);

                // Setup event listeners for the newly added row
                setupRowEventListeners(row);

                bahanIndex++; // Increment index for the next row
            }

            // Function to setup event listeners for a single row
            function setupRowEventListeners(rowElement) {
                // Remove button
                rowElement.querySelector('.remove-bahan-btn').addEventListener('click', function() {
                    rowElement.remove();
                });

                const selectElement = rowElement.querySelector('.bahan-select');
                const jumlahInput = rowElement.querySelector('.bahan-jumlah');
                const hiddenIdInput = rowElement.querySelector('input[type="hidden"]');
                const checkbox = rowElement.querySelector('.bahan-selected-checkbox');

                // Checkbox change: enable/disable inputs and manage required/hidden ID
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        selectElement.removeAttribute('disabled');
                        jumlahInput.removeAttribute('disabled');
                        selectElement.setAttribute('required', 'required');
                        jumlahInput.setAttribute('required', 'required');
                        hiddenIdInput.value = selectElement.value;
                    } else {
                        selectElement.setAttribute('disabled', 'disabled');
                        jumlahInput.setAttribute('disabled', 'disabled');
                        selectElement.removeAttribute('required');
                        jumlahInput.removeAttribute('required');
                        hiddenIdInput.value = '';
                    }
                });

                // Bahan select change: suggest default portion and update hidden ID
                selectElement.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const portionValue = selectedOption.dataset.portionValue;
                    const portionUnit = selectedOption.dataset.portionUnit || 'g';

                    jumlahInput.placeholder = `Jumlah (${portionUnit})`;

                    if (portionValue && !jumlahInput.value) {
                        jumlahInput.value = portionValue;
                    }
                    hiddenIdInput.value = this.value;
                });

                // Trigger change event for pre-selected items on load to set placeholder/value
                if (selectElement.value && checkbox.checked) {
                    selectElement.dispatchEvent(new Event('change'));
                }
            }

            // Add Bahan button click
            addBahanBtn.addEventListener('click', function() {
                createBahanMakananRowJs('', '', false);
            });

            // Initialize with existing data (for edit mode) or one empty row (for create mode)
            // Di mode CREATE, initialBahanData akan selalu array kosong, jadi langsung create satu baris
            // Jika ada old input setelah validasi gagal, old() akan menangani itu.
            @if(old('bahan_makanans') && !empty(old('bahan_makanans')))
                @php
                    $oldBahanMakanans = old('bahan_makanans');
                    $processedOldBahan = [];
                    foreach ($oldBahanMakanans as $bahanId => $data) {
                        if (isset($data['selected']) && $data['selected'] == '1') {
                            $processedOldBahan[] = [
                                'id' => $bahanId,
                                'jumlah' => $data['jumlah'] ?? '',
                                'selected' => true
                            ];
                        }
                    }
                @endphp
                @foreach($processedOldBahan as $bahanData)
                    createBahanMakananRowJs(
                        '{{ $bahanData['id'] ?? '' }}',
                        '{{ $bahanData['jumlah'] ?? '' }}',
                        {{ $bahanData['selected'] ? 'true' : 'false' }}
                    );
                @endforeach
            @else
                createBahanMakananRowJs(); // Untuk baris kosong pertama di mode create
            @endif

            // Pastikan bahanIndex diset dengan benar untuk penambahan dinamis selanjutnya
            bahanIndex = bahanMakananList.children.length; // Set index berdasarkan jumlah elemen yang sudah ada
        });
    </script>
    @endpush
</x-app-layout>
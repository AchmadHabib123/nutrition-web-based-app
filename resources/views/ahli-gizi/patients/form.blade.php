<div class="space-y-4">
    {{-- Bagian form yang sudah ada (Anda salin dari create/edit.blade.php) --}}
    <div>
        <label for="no_kamar" class="block text-sm font-medium text-gray-700">No Kamar</label>
        <input type="text" name="no_kamar" id="no_kamar" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('no_kamar', $patients->no_kamar ?? '') }}" required>
        @error('no_kamar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="nama_pasien" class="block text-sm font-medium text-gray-700">Nama Pasien</label>
        <input type="text" name="nama_pasien" id="nama_pasien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('nama_pasien', $patients->nama_pasien ?? '') }}" required>
        @error('nama_pasien') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="riwayat_penyakit" class="block text-sm font-medium text-gray-700">Riwayat Penyakit</label>
        <textarea name="riwayat_penyakit" id="riwayat_penyakit" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('riwayat_penyakit', $patients->riwayat_penyakit ?? '') }}</textarea>
        @error('riwayat_penyakit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- <div>
        <label for="kalori_makanan" class="block text-sm font-medium text-gray-700">Kalori Makanan (Target Awal)</label>
        <input type="number" name="kalori_makanan" id="kalori_makanan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('kalori_makanan', $patients->kalori_makanan ?? '') }}" min="0" required>
        @error('kalori_makanan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div> --}}

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="berat_badan" class="block text-sm font-medium text-gray-700">Berat Badan (kg)</label>
            <input type="number" name="berat_badan" id="berat_badan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('berat_badan', $patients->berat_badan ?? '') }}" min="0" step="any" required>
            @error('berat_badan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="tinggi_badan" class="block text-sm font-medium text-gray-700">Tinggi Badan (cm)</label>
            <input type="number" name="tinggi_badan" id="tinggi_badan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('tinggi_badan', $patients->tinggi_badan ?? '') }}" min="0" step="any" required>
            @error('tinggi_badan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="usia" class="block text-sm font-medium text-gray-700">Usia (tahun)</label>
            <input type="number" name="usia" id="usia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('usia', $patients->usia ?? '') }}" min="0" required>
            @error('usia') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                <option value="">Pilih Jenis Kelamin</option>
                <option value="pria" @if(old('jenis_kelamin', $patients->jenis_kelamin ?? '') == 'pria') selected @endif>Pria</option>
                <option value="wanita" @if(old('jenis_kelamin', $patients->jenis_kelamin ?? '') == 'wanita') selected @endif>Wanita</option>
            </select>
            @error('jenis_kelamin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="tipe_pasien" class="block text-sm font-medium text-gray-700">Tipe Pasien</label>
        <select name="tipe_pasien" id="tipe_pasien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="">Pilih Tipe Pasien</option>
            <option value="Normal" @if(old('tipe_pasien', $patients->tipe_pasien ?? '') == 'Normal') selected @endif>Normal</option>
            <option value="VIP" @if(old('tipe_pasien', $patients->tipe_pasien ?? '') == 'VIP') selected @endif>VIP</option>
            <option value="VVIP" @if(old('tipe_pasien', $patients->tipe_pasien ?? '') == 'VVIP') selected @endif>VVIP</option>
        </select>
        @error('tipe_pasien') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- === Bagian input baru untuk Konsep Gizi Cerdas === --}}
    <div>
        <label for="kondisi_diet_klinis" class="block text-sm font-medium text-gray-700">Kondisi Diet Klinis</label>
        {{-- Anda bisa membuat ini sebagai select dengan opsi standar jika ada --}}
        <input type="text" name="kondisi_diet_klinis" id="kondisi_diet_klinis" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('kondisi_diet_klinis', $patients->kondisi_diet_klinis ?? '') }}" placeholder="Contoh: Pasca Operasi Hari 1, Diet Cair Awal">
        @error('kondisi_diet_klinis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="standar_diit_id" class="block text-sm font-medium text-gray-700">Standar Diit Utama</label>
        <select name="standar_diit_id" id="standar_diit_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">Pilih Standar Diit (Opsional)</option>
            @foreach($standarDiits as $standar)
                <option value="{{ $standar->id }}" @if(old('standar_diit_id', $patients->standar_diit_id ?? '') == $standar->id) selected @endif>
                    {{ $standar->nama }}
                </option>
            @endforeach
        </select>
        @error('standar_diit_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="diet_khusus_ids" class="block text-sm font-medium text-gray-700">Diet Khusus Terkait (Pilih beberapa)</label>
        <select name="diet_khusus_ids[]" id="diet_khusus_ids" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" multiple>
            <option value="" disabled>Pilih Diet Khusus (Opsional)</option>
            @foreach($dietKhusus as $diet)
                <option value="{{ $diet->id }}" @if(in_array($diet->id, old('diet_khusus_ids', $selectedDietKhususIds ?? []))) selected @endif>
                    {{ $diet->nama }}
                </option>
            @endforeach
        </select>
        @error('diet_khusus_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status_pasien" class="block text-sm font-medium text-gray-700">Status Pasien</label>
        <select name="status_pasien" id="status_pasien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="aktif" @if(old('status_pasien', $patients->status_pasien ?? '') == 'aktif') selected @endif>Aktif</option>
            <option value="nonaktif" @if(old('status_pasien', $patients->status_pasien ?? '') == 'nonaktif') selected @endif>Nonaktif</option>
        </select>
        @error('status_pasien') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>
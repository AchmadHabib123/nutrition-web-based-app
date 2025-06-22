<div class="space-y-4">
    <div>
        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Standar Diit</label>
        <input type="text" name="nama" id="nama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('nama', $standarDiit->nama ?? '') }}" required>
        @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4"> {{-- Ubah dari 3 ke 2 untuk layout --}}
        <div>
            <label for="min_kalori" class="block text-sm font-medium text-gray-700">Kalori Minimal</label>
            <input type="number" name="min_kalori" id="min_kalori" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('min_kalori', $standarDiit->min_kalori ?? '') }}" min="0" step="any">
            @error('min_kalori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="max_kalori" class="block text-sm font-medium text-gray-700">Kalori Maksimal</label>
            <input type="number" name="max_kalori" id="max_kalori" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('max_kalori', $standarDiit->max_kalori ?? '') }}" min="0" step="any">
            @error('max_kalori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="target_protein_ratio" class="block text-sm font-medium text-gray-700">Rasio Protein (0-1)</label>
            <input type="number" name="target_protein_ratio" id="target_protein_ratio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('target_protein_ratio', $standarDiit->target_protein_ratio ?? '') }}" min="0" max="1" step="0.01">
            @error('target_protein_ratio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="target_karbohidrat_ratio" class="block text-sm font-medium text-gray-700">Rasio Karbohidrat (0-1)</label>
            <input type="number" name="target_karbohidrat_ratio" id="target_karbohidrat_ratio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('target_karbohidrat_ratio', $standarDiit->target_karbohidrat_ratio ?? '') }}" min="0" max="1" step="0.01">
            @error('target_karbohidrat_ratio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="target_lemak_ratio" class="block text-sm font-medium text-gray-700">Rasio Lemak (0-1)</label>
            <input type="number" name="target_lemak_ratio" id="target_lemak_ratio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('target_lemak_ratio', $standarDiit->target_lemak_ratio ?? '') }}" min="0" max="1" step="0.01">
            @error('target_lemak_ratio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="kategori_bahan_terlarang" class="block text-sm font-medium text-gray-700">Kategori Bahan Terlarang (Pilih beberapa)</label>
        <select name="kategori_bahan_terlarang[]" id="kategori_bahan_terlarang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" multiple>
            @php
                // Contoh daftar kategori bahan makanan. Anda bisa mengirim ini dari controller.
                $kategoriBahanOptions = ['makanan_pokok', 'lauk_hewani', 'lauk_nabati', 'sayur', 'buah', 'susu', 'minyak', 'gula_pasir', 'snack', 'roti', 'teh_pisang']; // Tambahkan sesuai data Anda
                $selectedKategori = old('kategori_bahan_terlarang', $standarDiit->kategori_bahan_terlarang ?? []);
            @endphp
            @foreach($kategoriBahanOptions as $kategori)
                <option value="{{ $kategori }}" @if(in_array($kategori, $selectedKategori)) selected @endif>{{ Str::title(str_replace('_', ' ', $kategori)) }}</option>
            @endforeach
        </select>
        @error('kategori_bahan_terlarang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="bahan_makanan_terlarang_ids" class="block text-sm font-medium text-gray-700">Bahan Makanan Spesifik Terlarang (Pilih beberapa)</label>
        <select name="bahan_makanan_terlarang_ids[]" id="bahan_makanan_terlarang_ids" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" multiple>
            @foreach (\App\Models\BahanMakanan::all() as $bahan)
                <option value="{{ $bahan->id }}" @if(in_array($bahan->id, old('bahan_makanan_terlarang_ids', $standarDiit->bahan_makanan_terlarang_ids ?? []))) selected @endif>
                    {{ $bahan->nama }}
                </option>
            @endforeach
        </select>
        @error('bahan_makanan_terlarang_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <hr class="my-6">

    {{-- === Bagian Input Pola Porsi Kategori === --}}
    <div>
        <label class="block text-lg font-semibold text-gray-800 mb-2">Pola Porsi Kategori per Waktu Makan (JSON)</label>
        <p class="text-sm text-gray-600 mb-2">Isi pola porsi kategori dalam format JSON. Contoh struktur:</p>
        <pre class="bg-gray-100 p-2 text-xs rounded-md overflow-auto whitespace-pre-wrap">
{
  "pagi": {
    "makanan_pokok": {"1-3 tahun": 75, "4-6 tahun": 100, "DM B": 100},
    "lauk_hewani": {"1-3 tahun": 25, "4-6 tahun": 50, "DM B": 50},
    "sayur": {"1-3 tahun": 50, "4-6 tahun": 50, "DM B": 70}
  },
  "siang": {
    "makanan_pokok": {"1-3 tahun": 75, "4-6 tahun": 150, "DM B": 100},
    "buah": {"umum": 100}
  },
  "selingan_pagi": {
    "snack": {"umum": 1}
  }
}
        </pre>
        <textarea name="pola_porsi_kategori" id="pola_porsi_kategori" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm font-mono text-sm">{{ old('pola_porsi_kategori', json_encode($standarDiit->pola_porsi_kategori ?? [], JSON_PRETTY_PRINT)) }}</textarea>
        @error('pola_porsi_kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
        <textarea name="catatan" id="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('catatan', $standarDiit->catatan ?? '') }}</textarea>
        @error('catatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>
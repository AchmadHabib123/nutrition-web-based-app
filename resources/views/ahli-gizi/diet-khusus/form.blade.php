<div class="space-y-4">
    <div>
        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Diet Khusus</label>
        <input type="text" name="nama" id="nama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('nama', $dietKhusu->nama ?? '') }}" required>
        @error('nama')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
        <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('deskripsi', $dietKhusu->deskripsi ?? '') }}</textarea>
        @error('deskripsi')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
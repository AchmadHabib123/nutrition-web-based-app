<div class="space-y-4">
    {{-- Bagian input nutrisi total (sudah ada) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="total_protein" class="block text-sm font-medium text-gray-700">Total Protein (g)</label>
            <input type="number" name="total_protein" id="total_protein" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('total_protein', $menu->total_protein ?? '') }}" min="0" required>
            @error('total_protein') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="total_karbohidrat" class="block text-sm font-medium text-gray-700">Total Karbohidrat (g)</label>
            <input type="number" name="total_karbohidrat" id="total_karbohidrat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('total_karbohidrat', $menu->total_karbohidrat ?? '') }}" min="0" required>
            @error('total_karbohidrat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="total_lemak" class="block text-sm font-medium text-gray-700">Total Lemak (g)</label>
            <input type="number" name="total_lemak" id="total_lemak" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('total_lemak', $menu->total_lemak ?? '') }}" min="0" required>
            @error('total_lemak') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- === Bagian input baru untuk Diet Khusus === --}}
    <div>
        <label for="diet_khusus_ids" class="block text-sm font-medium text-gray-700">Kompatibel dengan Diet Khusus (Pilih beberapa)</label>
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

    </div>
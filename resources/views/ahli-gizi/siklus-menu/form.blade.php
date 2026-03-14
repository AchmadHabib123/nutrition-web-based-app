{{-- Variabel yang diharapkan: $siklusMenu (untuk edit), $menus, $waktuMakanOptions, $tipePasienOptions --}}

<div class="space-y-4">
    <div>
        <label for="hari_siklus" class="block text-sm font-medium text-gray-700">Hari Siklus (1-11)</label>
        <input type="number" name="hari_siklus" id="hari_siklus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('hari_siklus', $siklusMenu->hari_siklus ?? '') }}" min="1" max="11" required>
        @error('hari_siklus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="waktu_makan" class="block text-sm font-medium text-gray-700">Waktu Makan</label>
        <select name="waktu_makan" id="waktu_makan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="">Pilih Waktu Makan</option>
            @foreach($waktuMakanOptions as $option)
                <option value="{{ $option }}" @if(old('waktu_makan', $siklusMenu->waktu_makan ?? '') == $option) selected @endif>
                    {{ Str::title(str_replace('_', ' ', $option)) }}
                </option>
            @endforeach
        </select>
        @error('waktu_makan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="tipe_pasien" class="block text-sm font-medium text-gray-700">Tipe Pasien</label>
        <select name="tipe_pasien" id="tipe_pasien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="">Pilih Tipe Pasien</option>
            @foreach($tipePasienOptions as $option)
                <option value="{{ $option }}" @if(old('tipe_pasien', $siklusMenu->tipe_pasien ?? '') == $option) selected @endif>
                    {{ $option }}
                </option>
            @endforeach
        </select>
        @error('tipe_pasien') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="menu_id" class="block text-sm font-medium text-gray-700">Pilih Menu</label>
        <select name="menu_id" id="menu_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="">Pilih Menu</option>
            @foreach($menus as $menu)
                <option value="{{ $menu->id }}" @if(old('menu_id', $siklusMenu->menu_id ?? '') == $menu->id) selected @endif>
                    {{ $menu->nama }} ({{ $menu->tipe_pasien }})
                </option>
            @endforeach
        </select>
        @error('menu_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>
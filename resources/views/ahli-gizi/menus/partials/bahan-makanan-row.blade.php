{{-- Variabel yang diharapkan: $bahanMakanans, $index, $selectedBahanId, $jumlah, $isSelected --}}
<div class="bahan-makanan-item flex flex-col sm:flex-row items-center gap-2">
    <div class="w-full sm:w-1/2">
        {{-- Checkbox untuk menandakan bahan ini dipilih --}}
        <input type="checkbox" name="bahan_makanans[{{ $index }}][selected]" value="1" class="bahan-selected-checkbox" @if($isSelected) checked @endif>
        <label class="inline-block ml-2 text-sm font-medium text-gray-700">Pilih Bahan:</label>
        <select name="bahan_makanans[{{ $index }}][id]" class="bahan-select mt-1 block w-full rounded-md border-gray-300 shadow-sm" @if(!$isSelected) disabled @endif> {{-- Disabled jika tidak terpilih --}}
            <option value="">Pilih Bahan Makanan</option>
            @foreach($bahanMakanans as $bahan)
                <option value="{{ $bahan->id }}"
                        data-portion-value="{{ $bahan->standard_portion_value ?? '' }}"
                        data-portion-unit="{{ $bahan->standard_portion_unit ?? 'g' }}"
                        @if($selectedBahanId == $bahan->id) selected @endif>
                    {{ $bahan->nama }}
                    @if($bahan->standard_portion_value) (Porsi Std: {{ $bahan->standard_portion_value }} {{ $bahan->standard_portion_unit ?? 'g' }}) @endif
                </option>
            @endforeach
        </select>
        {{-- Input hidden untuk menyimpan ID bahan makanan yang dipilih di select --}}
        {{-- Ini akan diupdate oleh JS saat select berubah, dan memastikan ID tetap terkirim --}}
        <input type="hidden" name="bahan_makanans[{{ $index }}][hidden_id]" value="{{ $selectedBahanId }}">
    </div>
    <div class="w-full sm:w-1/4">
        <label for="jumlah_{{ $index }}" class="sr-only">Jumlah (gram)</label>
        <input type="number" name="bahan_makanans[{{ $index }}][jumlah]" id="jumlah_{{ $index }}" class="bahan-jumlah mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $jumlah }}" min="1" placeholder="Jumlah (gram)" @if(!$isSelected) disabled @endif required> {{-- Disabled jika tidak terpilih --}}
    </div>
    <div class="w-full sm:w-1/4 flex justify-end">
        <button type="button" class="remove-bahan-btn bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded-md text-sm">Hapus</button>
    </div>
</div>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Menu Makanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-lg">
                <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700">Nama Menu</label>
                        <input type="text" name="nama" class="w-full rounded border-gray-300" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" class="w-full rounded border-gray-300"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Gambar</label>
                        <input type="file" name="gambar" class="w-full rounded border-gray-300">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Tipe Pasien</label>
                        <select name="tipe_pasien" class="w-full rounded border-gray-300">
                            <option value="VVIP">VVIP</option>
                            <option value="VIP">VIP</option>
                            <option value="Normal">Normal</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Pilih Bahan Makanan & Jumlah</label>
                        <div class="space-y-2">
                            @foreach ($bahanMakanans as $bahan)
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" name="bahan_makanans[{{ $bahan->id }}][id]" value="{{ $bahan->id }}">
                                    <span class="w-1/3">{{ $bahan->nama }}</span>
                                    <input type="number" name="bahan_makanans[{{ $bahan->id }}][jumlah]" class="w-1/3 rounded border-gray-300" placeholder="Jumlah (gram)">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Menu</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
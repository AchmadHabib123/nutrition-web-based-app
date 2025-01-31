<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Menu Makanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="nama" class="block text-gray-700">Nama Makanan</label>
                        <input type="text" name="nama" id="nama" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="gambar" class="block text-gray-700">Gambar</label>
                        <input type="file" name="gambar" id="gambar" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="">
                            <label for="protein" class="block text-gray-700">Protein (gram)</label>
                            <input type="number" name="protein" id="protein" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>
    
                        <div class="">
                            <label for="karbohidrat" class="block text-gray-700">Karbohidrat (gram)</label>
                            <input type="number" name="karbohidrat" id="karbohidrat" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>
    
                        <div class="">
                            <label for="total_lemak" class="block text-gray-700">Total Lemak (gram)</label>
                            <input type="number" name="total_lemak" id="total_lemak" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="kategori_bahan_masakan" class="block text-gray-700">Kategori Bahan Makanan/Minuman</label>
                            <select name="kategori_bahan_masakan" id="kategori_bahan_masakan" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="makanan_pokok">Makanan Pokok</option>
                                <option value="lauk">Lauk Pauk</option>
                                <option value="sayur">Sayuran</option>
                                <option value="buah">Buah</option>
                                <option value="susu">Susu</option>
                            </select>
                        </div>
                        <div class="">
                            <label for="tipe_pasien" class="block text-gray-700">Tipe Pasien</label>
                            <select name="tipe_pasien" id="tipe_pasien" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="VVIP">VVIP</option>
                                <option value="VIP">VIP</option>
                                <option value="Normal">Normal</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

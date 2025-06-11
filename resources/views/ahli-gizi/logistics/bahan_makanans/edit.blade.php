<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Menggunakan nama bahan makanan yang sedang diedit agar lebih dinamis --}}
            {{ __('Edit Bahan Makanan: ') }} {{ $bahanMakanan->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Analitik Stok (30 Hari Terakhir)
                </h3>
                <canvas id="stockChart" height="100"></canvas>
            </div>


            <div class="bg-white shadow-sm rounded-lg p-6">
                <form id="editForm" action="{{ route('ahli-gizi.logistics.bahan_makanans.update', $bahanMakanan) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="nama" class="block text-gray-700">Nama Makanan</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $bahanMakanan->nama) }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label for="gambar" class="block text-gray-700">Gambar</label>
                        <input type="file" name="gambar" id="gambar" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                        @if($bahanMakanan->gambar)
                            <img src="{{ asset('storage/' . $bahanMakanan->gambar) }}" alt="{{ $bahanMakanan->nama }}" class="mt-2 h-20 object-cover rounded-md">
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="mb-4">
                            <label for="protein" class="block text-gray-700">Protein (gram)</label>
                            <input type="number" name="protein" id="protein" step="any" value="{{ old('protein', $bahanMakanan->protein) }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>
    
                        <div class="mb-4">
                            <label for="karbohidrat" class="block text-gray-700">Karbohidrat (gram)</label>
                            <input type="number" name="karbohidrat" id="karbohidrat" step="any" value="{{ old('karbohidrat', $bahanMakanan->karbohidrat) }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>
    
                        <div class="mb-4">
                            <label for="total_lemak" class="block text-gray-700">Total Lemak (gram)</label>
                            <input type="number" name="total_lemak" id="total_lemak" step="any" value="{{ old('total_lemak', $bahanMakanan->total_lemak) }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="kategori_bahan_masakan" class="block text-gray-700">Kategori Bahan Makanan/Minuman</label>
                            <select name="kategori_bahan_masakan" id="kategori_bahan_masakan" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="makanan_pokok" {{ old('kategori_bahan_masakan', $bahanMakanan->kategori_bahan_masakan) == 'makanan_pokok' ? 'selected' : '' }}>Makanan Pokok</option>
                                <option value="lauk" {{ old('kategori_bahan_masakan', $bahanMakanan->kategori_bahan_masakan) == 'lauk' ? 'selected' : '' }}>Lauk Pauk</option>
                                <option value="sayur" {{ old('kategori_bahan_masakan', $bahanMakanan->kategori_bahan_masakan) == 'sayur' ? 'selected' : '' }}>Sayuran</option>
                                <option value="buah" {{ old('kategori_bahan_masakan', $bahanMakanan->kategori_bahan_masakan) == 'buah' ? 'selected' : '' }}>Buah</option>
                                <option value="susu" {{ old('kategori_bahan_masakan', $bahanMakanan->kategori_bahan_masakan) == 'susu' ? 'selected' : '' }}>Susu</option>
                            </select>
                        </div>
    
                        <div class="mb-4">
                            <label for="tipe_pasien" class="block text-gray-700">Tipe Pasien</label>
                            <select name="tipe_pasien" id="tipe_pasien" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="VVIP" {{ old('tipe_pasien', $bahanMakanan->tipe_pasien) == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                                <option value="VIP" {{ old('tipe_pasien', $bahanMakanan->tipe_pasien) == 'VIP' ? 'selected' : '' }}>VIP</option>
                                <option value="Normal" {{ old('tipe_pasien', $bahanMakanan->tipe_pasien) == 'Normal' ? 'selected' : '' }}>Normal</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="stok" class="block text-gray-700">Stok</label>
                        <input type="number" name="stok" id="stok" value="{{ old('stok', $bahanMakanan->stok) }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    </div>                      
                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('ahli-gizi.logistics.index') }}" class="text-gray-600 hover:text-gray-900 inline-flex items-center">
                            &larr; Kembali
                        </a>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" 
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Pastikan library Chart.js sudah di-load di layout utama Anda (layouts/app.blade.php) --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Cek apakah variabel chartData ada dan tidak kosong, untuk menghindari error jika data tidak ada
            @if(isset($chartData))
                const ctx = document.getElementById('stockChart').getContext('2d');
                
                // Mengambil data dari controller yang sudah di-encode sebagai JSON
                const chartData = @json($chartData);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Stok Masuk',
                            data: chartData.dataMasuk,
                            borderColor: 'rgb(34, 197, 94)', // green-500
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            fill: true,
                            tension: 0.4,
                        }, {
                            label: 'Stok Keluar',
                            data: chartData.dataKeluar,
                            borderColor: 'rgb(234, 179, 8)', // amber-500
                            backgroundColor: 'rgba(234, 179, 8, 0.1)',
                            fill: true,
                            tension: 0.4,
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            @endif
        });
    </script>
</x-app-layout>
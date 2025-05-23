<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Menu Makanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-lg">
                <a href="{{ route('admin.menus.create') }}" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded">
                    + Tambah Menu
                </a>

                @if ($menus->isEmpty())
                    <p class="text-gray-500">Belum ada menu makanan.</p>
                @else
                    <div class="space-y-6">
                        @foreach ($menus as $menu)
                            <div class="p-4 border rounded shadow">
                                <h3 class="text-xl font-bold">{{ $menu->nama }}</h3>
                                <p class="text-sm text-gray-600 mb-2">{{ $menu->deskripsi }}</p>
                                <p><strong>Tipe Pasien:</strong> {{ $menu->tipe_pasien }}</p>

                                @if($menu->gambar)
                                    <img src="{{ asset('storage/' . $menu->gambar) }}" alt="Gambar Menu" class="w-32 h-32 object-cover mt-2">
                                @endif

                                <div class="mt-4">
                                    <h4 class="font-semibold">Bahan Makanan:</h4>
                                    <table class="w-full text-sm mt-2 border rounded">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-2 py-1 text-left">Nama</th>
                                                <th class="px-2 py-1 text-left">Jumlah (gram)</th>
                                                <th class="px-2 py-1 text-left">Protein</th>
                                                <th class="px-2 py-1 text-left">Karbohidrat</th>
                                                <th class="px-2 py-1 text-left">Lemak</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($menu->bahanMakanans as $bahan)
                                                <tr>
                                                    <td class="px-2 py-1">{{ $bahan->nama }}</td>
                                                    <td class="px-2 py-1">{{ $bahan->pivot->jumlah }} gr</td>
                                                    <td class="px-2 py-1">{{ $bahan->protein }} gr</td>
                                                    <td class="px-2 py-1">{{ $bahan->karbohidrat }} gr</td>
                                                    <td class="px-2 py-1">{{ $bahan->total_lemak }} gr</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if ($menu->total_protein || $menu->total_karbohidrat || $menu->total_lemak)
                                    <div class="mt-4 text-sm text-gray-700">
                                        <p><strong>Total Protein:</strong> {{ $menu->total_protein ?? 0 }} gr</p>
                                        <p><strong>Total Karbohidrat:</strong> {{ $menu->total_karbohidrat ?? 0 }} gr</p>
                                        <p><strong>Total Lemak:</strong> {{ $menu->total_lemak ?? 0 }} gr</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

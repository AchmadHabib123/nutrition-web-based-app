<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Standar Diit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 font-medium text-sm text-red-600">
                    {{ session('error') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('ahli-gizi.standar-diit.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Standar Diit
                        </a>
                    </div>

                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">ID</th>
                                <th class="py-2 px-4 border-b">Nama Standar</th>
                                <th class="py-2 px-4 border-b">Kalori (Min-Max)</th>
                                <th class="py-2 px-4 border-b">Ras. P:K:L</th>
                                <th class="py-2 px-4 border-b">Terlarang</th>
                                <th class="py-2 px-4 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($standarDiits as $standar)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $standar->id }}</td>
                                    <td class="py-2 px-4 border-b">{{ $standar->nama }}</td>
                                    <td class="py-2 px-4 border-b">{{ $standar->min_kalori ?? 'N/A' }} - {{ $standar->max_kalori ?? 'N/A' }} kcal</td>
                                    <td class="py-2 px-4 border-b">{{ ($standar->target_protein_ratio * 100) ?? 'N/A' }}:{{ ($standar->target_karbohidrat_ratio * 100) ?? 'N/A' }}:{{ ($standar->target_lemak_ratio * 100) ?? 'N/A' }}%</td>
                                    <td class="py-2 px-4 border-b">
                                        @if($standar->kategori_bahan_terlarang)
                                            {{ implode(', ', $standar->kategori_bahan_terlarang) }}
                                        @endif
                                        @if($standar->bahan_makanan_terlarang_ids)
                                            ({{ count($standar->bahan_makanan_terlarang_ids) }} item)
                                        @endif
                                    </td>
                                    <td class="py-2 px-4 border-b flex space-x-2">
                                        <a href="{{ route('ahli-gizi.standar-diit.edit', $standar->id) }}" class="text-green-500 hover:text-green-700">Edit</a>
                                        <form action="{{ route('ahli-gizi.standar-diit.destroy', $standar->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus standar diit ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Tidak ada data Standar Diit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
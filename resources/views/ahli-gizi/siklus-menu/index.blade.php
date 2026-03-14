<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Siklus Menu') }}
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
                        <a href="{{ route('ahli-gizi.siklus-menu.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Entri Siklus
                        </a>
                    </div>

                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">ID</th>
                                <th class="py-2 px-4 border-b">Hari Siklus</th>
                                <th class="py-2 px-4 border-b">Waktu Makan</th>
                                <th class="py-2 px-4 border-b">Tipe Pasien</th>
                                <th class="py-2 px-4 border-b">Menu</th>
                                <th class="py-2 px-4 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siklusMenus as $siklus)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $siklus->id }}</td>
                                    <td class="py-2 px-4 border-b">{{ $siklus->hari_siklus }}</td>
                                    <td class="py-2 px-4 border-b">{{ Str::title(str_replace('_', ' ', $siklus->waktu_makan)) }}</td>
                                    <td class="py-2 px-4 border-b">{{ $siklus->tipe_pasien }}</td>
                                    <td class="py-2 px-4 border-b">{{ $siklus->menu->nama ?? 'Menu Dihapus' }}</td>
                                    <td class="py-2 px-4 border-b flex space-x-2">
                                        <a href="{{ route('ahli-gizi.siklus-menu.edit', $siklus->id) }}" class="text-green-500 hover:text-green-700">Edit</a>
                                        <form action="{{ route('ahli-gizi.siklus-menu.destroy', $siklus->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus entri siklus ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Tidak ada data Siklus Menu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
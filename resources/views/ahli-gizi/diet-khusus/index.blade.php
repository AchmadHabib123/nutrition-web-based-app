<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Diet Khusus') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('success') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('ahli-gizi.diet-khusus.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Diet Khusus
                        </a>
                    </div>

                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">ID</th>
                                <th class="py-2 px-4 border-b">Nama Diet</th>
                                <th class="py-2 px-4 border-b">Deskripsi</th>
                                <th class="py-2 px-4 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dietKhusus as $diet)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $diet->id }}</td>
                                    <td class="py-2 px-4 border-b">{{ $diet->nama }}</td>
                                    <td class="py-2 px-4 border-b">{{ Str::limit($diet->deskripsi, 50) }}</td>
                                    <td class="py-2 px-4 border-b flex space-x-2">
                                        <a href="{{ route('ahli-gizi.diet-khusus.edit', $diet->id) }}" class="text-green-500 hover:text-green-700">Edit</a>
                                        <form action="{{ route('ahli-gizi.diet-khusus.destroy', $diet->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus diet khusus ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Tidak ada data Diet Khusus.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
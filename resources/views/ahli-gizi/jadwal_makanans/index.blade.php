<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Jadwal Makanan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
            <a href="{{ route('ahli-gizi.jadwal-makanans.create') }}" class="bg-green-600 text-white px-4 py-2 rounded mb-4 inline-block">
                + Buat Jadwal Baru
            </a>

            <table class="table-auto w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2">#</th>
                        <th class="border px-4 py-2">Tanggal</th>
                        <th class="border px-4 py-2">Tipe Pasien</th>
                        <th class="border px-4 py-2">Total Menu</th>
                        <th class="border px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadwals as $jadwal)
                        <tr>
                            <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="border px-4 py-2">{{ $jadwal->tanggal_mulai }} - {{ $jadwal->tanggal_selesai }}</td>
                            <td class="border px-4 py-2">{{ $jadwal->tipe_pasien }}</td>
                            <td class="border px-4 py-2">{{ $jadwal->menus_count ?? '-' }}</td>
                            {{-- <td class="border px-4 py-2">
                                <a href="{{ route('ahli-gizi.jadwal-makanans.show', $jadwal->id) }}" class="text-blue-600 hover:underline">Lihat</a>
                            </td> --}}
                            <td class="border px-4 py-2 space-x-2">
                                <a href="{{ route('ahli-gizi.jadwal-makanans.show', $jadwal->id) }}" class="bg-blue-500 text-white px-2 py-1 rounded text-sm">Lihat</a>
                                <a href="{{ route('ahli-gizi.jadwal-makanans.edit', $jadwal->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

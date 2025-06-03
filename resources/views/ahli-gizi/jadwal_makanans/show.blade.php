<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Jadwal Makanan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Informasi Umum</h3>
                <p><strong>Tanggal Mulai:</strong> {{ $jadwal->tanggal_mulai }}</p>
                <p><strong>Tanggal Selesai:</strong> {{ $jadwal->tanggal_selesai }}</p>
                <p><strong>Tipe Pasien:</strong> {{ $jadwal->tipe_pasien }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-2">Menu per Hari & Waktu</h3>
                @foreach ($menuPerTanggal as $tanggal => $waktus)
                    <div class="mb-4">
                        <h4 class="font-bold">{{ $tanggal }}</h4>
                        <ul class="list-disc ml-6">
                            @foreach ($waktus as $waktu => $menu)
                                <li><strong>{{ ucfirst($waktu) }}:</strong> {{ $menu->nama ?? '-' }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('ahli-gizi.jadwal-makanans.index') }}" class="inline-block mt-4 text-blue-600 hover:underline">← Kembali ke Daftar</a>
        </div>
    </div>
</x-app-layout>

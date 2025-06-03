@forelse($patients as $patients)
    <tr>
        <td class="py-2 px-4 border-b">{{ $patients->no_kamar }}</td>
        <td class="py-2 px-4 border-b">{{ $patients->nama_pasien }}</td>
        <td class="py-2 px-4 border-b">{{ $patients->riwayat_penyakit }}</td>
        <td class="py-2 px-4 border-b">{{ $patients->kalori_makanan }} kcal</td>
        <td class="py-2 px-4 border-b">{{ $patients->kalori_harian }} kcal</td>
        <td class="py-2 px-4 border-b">{{ $patients->tipe_pasien }}</td>
        <td class="py-2 px-4 border-b flex space-x-2">
            <a href="{{ route('ahli-gizi.patients.show', $patients->id) }}" class="text-blue-500 hover:text-blue-700">🔍</a>
            <a href="{{ route('ahli-gizi.patients.edit', $patients->id) }}" class="text-green-500 hover:text-green-700">✏️</a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-4">Tidak ada data pasien untuk tanggal ini.</td>
    </tr>
@endforelse

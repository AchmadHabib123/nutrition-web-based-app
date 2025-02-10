@forelse($patients as $patient)
    <tr>
        <td class="py-2 px-4 border-b">{{ $patient->no_kamar }}</td>
        <td class="py-2 px-4 border-b">{{ $patient->nama_pasien }}</td>
        <td class="py-2 px-4 border-b">{{ $patient->riwayat_penyakit }}</td>
        <td class="py-2 px-4 border-b">{{ $patient->kalori_makanan }} kcal</td>
        <td class="py-2 px-4 border-b">{{ $patient->kalori_harian }} kcal</td>
        <td class="py-2 px-4 border-b">{{ $patient->tipe_pasien }}</td>
        <td class="py-2 px-4 border-b flex space-x-2">
            <a href="{{ route('admin.patients.show', $patient->id) }}" class="text-blue-500 hover:text-blue-700">🔍</a>
            <a href="{{ route('admin.patients.edit', $patient->id) }}" class="text-green-500 hover:text-green-700">✏️</a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-4">Tidak ada data pasien untuk tanggal ini.</td>
    </tr>
@endforelse

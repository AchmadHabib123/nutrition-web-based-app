<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Makanan yang Perlu Diantar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">No Kamar</th>
                                <th class="py-2 px-4 border-b">Nama Pasien</th>
                                <th class="py-2 px-4 border-b">Waktu Makan</th>
                                <th class="py-2 px-4 border-b">Nama Makanan</th>
                                <th class="py-2 px-4 border-b">Status</th>
                                <th class="py-2 px-4 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($foodConsumptions as $food)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $food->patient->no_kamar }}</td>
                                    <td class="py-2 px-4 border-b">{{ $food->patient->nama_pasien }}</td>
                                    <td class="py-2 px-4 border-b">{{ $food->waktu_makan }}</td>
                                    <td class="py-2 px-4 border-b">{{ $food->nama_makanan }}</td>
                                    <td class="py-2 px-4 border-b">{{ $food->status }}</td>
                                    <td class="py-2 px-4 border-b">
                                        @if ($food->status === 'planned')
                                            <button 
                                                class="mark-delivered-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs" 
                                                data-id="{{ $food->id }}"
                                            >
                                                Telah Diantar
                                            </button>
                                        @else
                                            <span class="text-gray-500 text-xs">{{ ucfirst($food->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Tidak ada makanan yang perlu diantar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ... (kode Anda yang lain) ...

            const buttons = document.querySelectorAll('.mark-delivered-btn');
            console.log('Jumlah tombol "Telah Diantar" yang ditemukan:', buttons.length); // <--- TAMBAHKAN INI

            buttons.forEach(button => {
                console.log('Memasang event listener pada tombol dengan ID:', button.dataset.id); // <--- TAMBAHKAN INI
                button.addEventListener('click', function() {
                    console.log('Tombol diklik untuk ID:', this.dataset.id); // <--- TAMBAHKAN INI
                    const foodId = this.dataset.id;
                    const url = `/tenaga-gizi/food-delivery/${foodId}/mark-delivered`;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    })
                    .then(response => {
                        console.log('Respons diterima:', response); // <--- TAMBAHKAN INI
                        if (!response.ok) {
                            // ... (penanganan error) ...
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data JSON diterima:', data); // <--- TAMBAHKAN INI
                        alert(data.message);
                        const row = this.closest('tr');
                        row.querySelector('td:nth-child(5)').textContent = 'delivered';
                        this.remove();
                    })
                    .catch(error => {
                        console.error('Error saat fetch:', error); // <--- TAMBAHKAN INI
                        alert('Terjadi kesalahan: ' + error.message);
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
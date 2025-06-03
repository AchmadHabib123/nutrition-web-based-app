<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Jadwal Makanan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
            <form action="{{ route('ahli-gizi.jadwal-makanans.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="w-full border rounded" required>
                </div>

                <div class="mb-4">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="w-full border rounded" required>
                </div>

                <div class="mb-4">
                    <label>Tipe Pasien</label>
                    <select name="tipe_pasien" class="w-full border rounded" required>
                        <option value="VVIP">VVIP</option>
                        <option value="VIP">VIP</option>
                        <option value="Normal">Normal</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label>Pilih Menu untuk Setiap Hari & Waktu Makan</label>

                    <div id="menu-selection-area" class="space-y-4 mt-4">
                        <!-- JavaScript akan mengisi card menu di sini -->
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                    Simpan Jadwal Makanan
                </button>
            </form>
        </div>
    </div>

    <script>
        const menus = @json($menus);
        const area = document.getElementById('menu-selection-area');

        document.querySelector('input[name="tanggal_mulai"]').addEventListener('change', generateMenuFields);
        document.querySelector('input[name="tanggal_selesai"]').addEventListener('change', generateMenuFields);

        function generateMenuFields() {
            const start = document.querySelector('input[name="tanggal_mulai"]').value;
            const end = document.querySelector('input[name="tanggal_selesai"]').value;

            if (!start || !end || start > end) return;

            area.innerHTML = ''; // Reset

            const waktuMakan = ['pagi', 'siang', 'malam'];
            const startDate = new Date(start);
            const endDate = new Date(end);

            for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                const tanggal = d.toISOString().split('T')[0];

                waktuMakan.forEach(waktu => {
                    const label = `Menu untuk ${tanggal} - ${waktu}`;
                    let select = `<label class="block font-medium">${label}</label>
                                  <select name="menus[${tanggal}][${waktu}]" class="w-full border rounded mt-1 mb-4" required>
                                    <option value="">Pilih Menu</option>`;

                    menus.forEach(menu => {
                        select += `<option value="${menu.id}">${menu.nama}</option>`;
                    });

                    select += '</select>';
                    area.innerHTML += select;
                });
            }
        }
    </script>
</x-app-layout>

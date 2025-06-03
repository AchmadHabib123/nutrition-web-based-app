<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Jadwal Makanan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
            <form action="{{ route('ahli-gizi.jadwal-makanans.update', $jadwal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="w-full border rounded" value="{{ $jadwal->tanggal_mulai }}" required>
                </div>

                <div class="mb-4">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="w-full border rounded" value="{{ $jadwal->tanggal_selesai }}" required>
                </div>

                <div class="mb-4">
                    <label>Tipe Pasien</label>
                    <select name="tipe_pasien" class="w-full border rounded" required>
                        <option value="VVIP" {{ $jadwal->tipe_pasien == 'VVIP' ? 'selected' : '' }}>VVIP</option>
                        <option value="VIP" {{ $jadwal->tipe_pasien == 'VIP' ? 'selected' : '' }}>VIP</option>
                        <option value="Normal" {{ $jadwal->tipe_pasien == 'Normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label>Pilih Menu untuk Setiap Hari & Waktu Makan</label>

                    <div id="menu-selection-area" class="space-y-4 mt-4">
                        <!-- Diisi oleh JavaScript -->
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                    Perbarui Jadwal Makanan
                </button>
            </form>
        </div>
    </div>

    <script>
        const menus = @json($menus);
        const jadwalDetails = @json($menuPerTanggal);
        const area = document.getElementById('menu-selection-area');
    
        const inputStart = document.querySelector('input[name="tanggal_mulai"]');
        const inputEnd = document.querySelector('input[name="tanggal_selesai"]');
    
        inputStart.addEventListener('change', generateMenuFields);
        inputEnd.addEventListener('change', generateMenuFields);
    
        window.onload = generateMenuFields;
    
        function generateMenuFields() {
            const start = inputStart.value;
            const end = inputEnd.value;
    
            if (!start || !end || start > end) return;
    
            area.innerHTML = '';
    
            const waktuMakan = ['pagi', 'siang', 'malam'];
            const startDate = new Date(start);
            const endDate = new Date(end);
    
            for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                const tanggal = d.toISOString().split('T')[0];
    
                waktuMakan.forEach(waktu => {
                    const label = `Menu untuk ${tanggal} - ${waktu}`;
                    const selectedMenuId = jadwalDetails[tanggal]?.[waktu] ?? '';
    
                    let select = `<label class="block font-medium">${label}</label>
                                  <select name="menus[${tanggal}][${waktu}]" class="w-full border rounded mt-1 mb-4" required>
                                    <option value="">Pilih Menu</option>`;
    
                    menus.forEach(menu => {
                        const selected = menu.id == selectedMenuId ? 'selected' : '';
                        select += `<option value="${menu.id}" ${selected}>${menu.nama}</option>`;
                    });
    
                    select += '</select>';
                    area.innerHTML += select;
                });
            }
        }
    </script>
    
</x-app-layout>

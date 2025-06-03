// export function fetchJadwalMakanan(date) {
//     fetch(`${window.jadwalMakananUrl}?date=${date}`)
//         .then(res => res.json())
//         .then(data => {
//             const list = document.getElementById('jadwal-makanan-list');
//             list.innerHTML = '';

//             if (data.length === 0) {
//                 list.innerHTML = '<li>Tidak ada jadwal makanan untuk tanggal ini.</li>';
//                 return;
//             }

//             data.forEach(item => {
//                 list.innerHTML += `<li>ID Jadwal: ${item.jadwal_makanan_id} | Waktu: ${item.waktu_makan} | Menu: ${item.nama_menu}</li>`;
//             });
//         })
//         .catch(err => {
//             console.error('Gagal ambil jadwal makanan:', err);
//         });
// }
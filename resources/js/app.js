import './bootstrap';
import Alpine from 'alpinejs';
import './dashboardChart';

window.Alpine = Alpine;

Alpine.start();

// import { fetchJadwalMakanan } from './dashboard';

// Jalankan saat DOM siap
// document.addEventListener('DOMContentLoaded', () => {
//     const today = new Date().toISOString().split('T')[0];
//     fetchJadwalMakanan(today);

//     window.fetchJadwalMakanan = fetchJadwalMakanan; // agar bisa dipanggil dari calendar JS di blade
// });
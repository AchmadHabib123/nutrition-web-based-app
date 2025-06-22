// resources/js/validation-logic.js

// Asumsi Anda memiliki Modal library global (misal dari Flowbite)
// Atau jika tidak, gunakan dummy Modal class yang sudah ada di show.blade.php
class Modal {
    constructor(element) { this.element = element; }
    show() {
        this.element.classList.remove('hidden');
        this.element.classList.add('flex');
    }
    hide() {
        this.element.classList.add('hidden');
        this.element.classList.remove('flex');
    }
}

let validationModalInstance;
let currentFoodConsumptionId = null;
let originalBahanMakanansData = []; // Untuk menyimpan data bahan makanan awal dari backend

document.addEventListener('DOMContentLoaded', function() {
    const validationModalElement = document.getElementById('validationModal');
    if (validationModalElement) {
        validationModalInstance = new Modal(validationModalElement);
    }

    // Event listener untuk tombol Validasi Konsumsi di tabel
    document.querySelectorAll('.validate-food-btn').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.dataset.id;
            const namaMakanan = this.dataset.namaMakanan;
            const menuId = this.dataset.menuId; // menuId mungkin tidak langsung dipakai di frontend, tapi bisa jadi debugging

            showValidationModal(foodId, namaMakanan);
        });
    });

    // Event listener untuk submit form validasi di modal
    document.getElementById('validateConsumptionForm').addEventListener('submit', async function(event) {
        event.preventDefault(); // Mencegah submit form standar

        const notes = document.getElementById('notesInput').value;
        const bahanConsumptionData = [];

        originalBahanMakanansData.forEach(bahan => {
            const itemDiv = document.querySelector(`[data-bahan-id="${bahan.id}"]`);
            if (!itemDiv) return;

            const statusSelect = itemDiv.querySelector('.bahan-status-select');
            const sisaInput = itemDiv.querySelector('.bahan-sisa-input');

            const status = statusSelect ? statusSelect.value : 'consumed_full';
            const sisaGram = parseFloat(sisaInput ? sisaInput.value : 0) || 0;

            bahanConsumptionData.push({
                bahan_id: bahan.id,
                status: status,
                sisa_gram: sisaGram
            });
        });

        // Dapatkan total nutrisi aktual yang sudah dihitung di preview
        const finalActualKalori = parseFloat(document.getElementById('actualTotalKaloriPreview').textContent);
        const finalActualProtein = parseFloat(document.getElementById('actualTotalProteinPreview').textContent);
        const finalActualCarbs = parseFloat(document.getElementById('actualTotalCarbsPreview').textContent);
        const finalActualFat = parseFloat(document.getElementById('actualTotalFatPreview').textContent);

        // Tentukan status akhir FoodConsumption
        const isFullySkipped = bahanConsumptionData.every(b => b.status === 'skipped');
        const finalFoodConsumptionStatus = isFullySkipped ? 'skipped' : 'consumed';

        const url = `/ahli-gizi/patients/food-consumption/${currentFoodConsumptionId}/validate`;

        const formData = {
            final_status: finalFoodConsumptionStatus,
            actual_kalori: finalActualKalori,
            actual_protein: finalActualProtein,
            actual_karbohidrat: finalActualCarbs,
            actual_lemak: finalActualFat,
            notes: notes,
            bahan_consumptions: bahanConsumptionData,
            // _method: 'PUT' // Penting jika route PUT/PATCH
        };

        try {
            const response = await fetch(url, {
                method: 'POST', // Gunakan POST dengan _method: PUT jika route PUT/PATCH
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal menyimpan validasi.');
            }

            const data = await response.json();
            alert(data.message);
            validationModalInstance.hide();
            window.location.reload();
        } catch (error) {
            console.error('Error saat submit validasi:', error);
            alert('Terjadi kesalahan: ' + error.message);
        }
    });
});

// Fungsi untuk menampilkan modal validasi
async function showValidationModal(foodConsumptionId, namaMakanan) {
    currentFoodConsumptionId = foodConsumptionId;
    document.getElementById('validationFoodName').textContent = namaMakanan;
    document.getElementById('bahanMakananList').innerHTML = '<p class="text-gray-500">Memuat bahan makanan...</p>';
    document.getElementById('notesInput').value = ''; // Reset notes

    document.getElementById('actualTotalKaloriPreview').textContent = '0.00 kcal';
    document.getElementById('actualTotalProteinPreview').textContent = '0.00 g';
    document.getElementById('actualTotalCarbsPreview').textContent = '0.00 g';
    document.getElementById('actualTotalFatPreview').textContent = '0.00 g';

    validationModalInstance.show();

    try {
        const response = await fetch(`/ahli-gizi/patients/food-consumption/${foodConsumptionId}/menu-details`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Data menu details diterima:', data);

        const menuDetails = data.menu_details;
        originalBahanMakanansData = menuDetails.bahan_makanans;

        // Isi detail nutrisi menu awal
        document.getElementById('originalMenuKalori').textContent = menuDetails.original_kalori + ' kcal';
        document.getElementById('originalMenuProtein').textContent = menuDetails.original_protein + ' g';
        document.getElementById('originalMenuCarbs').textContent = menuDetails.original_karbohidrat + ' g';
        document.getElementById('originalMenuFat').textContent = menuDetails.original_lemak + ' g';

        renderBahanMakananList(originalBahanMakanansData);
        calculateAndDisplayTotalActualNutrition(); // Hitung nilai awal (semua habis)

    } catch (error) {
        console.error('Error fetching menu details:', error);
        alert('Gagal memuat detail menu: ' + error.message);
        validationModalInstance.hide();
    }
}
window.showValidationModal = showValidationModal;

// Fungsi untuk merender daftar bahan makanan ke dalam modal
function renderBahanMakananList(bahanMakanans) {
    const bahanListContainer = document.getElementById('bahanMakananList');
    bahanListContainer.innerHTML = '';
    if (bahanMakanans.length === 0) {
        bahanListContainer.innerHTML = '<p class="text-gray-500">Tidak ada bahan makanan yang terdaftar untuk menu ini.</p>';
        return;
    }

    bahanMakanans.forEach((bahan, index) => {
        const bahanItemHtml = `
            <div class="p-3 border rounded-md bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-2" data-bahan-id="${bahan.id}" data-index="${index}">
                <div class="sm:w-1/2">
                    <p class="font-medium text-gray-900">${bahan.nama} (<span class="text-sm text-gray-600">${bahan.jumlah_di_menu_gram} g</span>)</p>
                    <p class="text-sm text-gray-600">Awal: ${bahan.kalori_kontribusi_awal} kcal, P: ${bahan.protein_kontribusi_awal}g, K: ${bahan.karbohidrat_kontribusi_awal}g, L: ${bahan.lemak_kontribusi_awal}g</p>
                </div>
                <div class="sm:w-1/2 flex items-center gap-2">
                    <select name="status_bahan_${bahan.id}" class="bahan-status-select form-select block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="consumed_full">Habis Semua</option>
                        <option value="partial">Tidak Habis (Sebagian)</option>
                        <option value="skipped">Tidak Dikonsumsi</option>
                    </select>
                    <input type="number" name="sisa_gram_bahan_${bahan.id}" class="bahan-sisa-input hidden w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="Sisa (gram)" min="0" max="${bahan.jumlah_di_menu_gram}">
                </div>
            </div>
        `;
        bahanListContainer.insertAdjacentHTML('beforeend', bahanItemHtml);
    });

    setupBahanMakananEventListeners();
}

function setupBahanMakananEventListeners() {
    document.querySelectorAll('.bahan-status-select').forEach(select => {
        select.addEventListener('change', function() {
            const parentDiv = this.closest('[data-bahan-id]');
            const sisaInput = parentDiv.querySelector('.bahan-sisa-input');
            if (this.value === 'partial') {
                sisaInput.classList.remove('hidden');
                sisaInput.focus();
            } else {
                sisaInput.classList.add('hidden');
                sisaInput.value = ''; // Reset nilai
            }
            calculateAndDisplayTotalActualNutrition();
        });
    });

    document.querySelectorAll('.bahan-sisa-input').forEach(input => {
        input.addEventListener('input', calculateAndDisplayTotalActualNutrition);
    });
}

function calculateAndDisplayTotalActualNutrition() {
    let totalActualKalori = 0;
    let totalActualProtein = 0;
    let totalActualCarbs = 0;
    let totalActualFat = 0;

    originalBahanMakanansData.forEach(bahan => {
        const itemDiv = document.querySelector(`[data-bahan-id="${bahan.id}"]`);
        if (!itemDiv) return;

        const statusSelect = itemDiv.querySelector('.bahan-status-select');
        const sisaInput = itemDiv.querySelector('.bahan-sisa-input');

        const status = statusSelect ? statusSelect.value : 'consumed_full';
        const sisaGram = parseFloat(sisaInput ? sisaInput.value : 0) || 0;

        const quantityInMenu = bahan.jumlah_di_menu_gram;
        const proteinPer100g = bahan.protein_per_100g;
        const carbsPer100g = bahan.karbohidrat_per_100g;
        const fatPer100g = bahan.lemak_per_100g;
        const kaloriPer100g = bahan.kalori_per_100g;

        let actualQuantityConsumed = 0;
        if (status === 'consumed_full') {
            actualQuantityConsumed = quantityInMenu;
        } else if (status === 'partial') {
            actualQuantityConsumed = Math.max(0, quantityInMenu - sisaGram);
        } else if (status === 'skipped') {
            actualQuantityConsumed = 0;
        }

        const consumedFactor = actualQuantityConsumed / 100; // Karena nutrisi bahan per 100g

        totalActualKalori += kaloriPer100g * consumedFactor;
        totalActualProtein += proteinPer100g * consumedFactor;
        totalActualCarbs += carbsPer100g * consumedFactor;
        totalActualFat += fatPer100g * consumedFactor;
    });

    document.getElementById('actualTotalKaloriPreview').textContent = totalActualKalori.toFixed(2) + ' kcal';
    document.getElementById('actualTotalProteinPreview').textContent = totalActualProtein.toFixed(2) + ' g';
    document.getElementById('actualTotalCarbsPreview').textContent = totalActualCarbs.toFixed(2) + ' g';
    document.getElementById('actualTotalFatPreview').textContent = totalActualFat.toFixed(2) + ' g';
}

// Ekspor fungsi showValidationModal agar bisa diakses dari show.blade.php
// Jika Anda mengimpor validation-logic.js di app.js
// export { showValidationModal };
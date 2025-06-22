<div id="validationModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Validasi Konsumsi Makanan: <span id="validationFoodName" class="text-indigo-600"></span>
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="validationModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <form id="validateConsumptionForm" class="space-y-4">
                    {{-- Detail Nutrisi Menu Awal --}}
                    <div class="p-3 bg-gray-100 rounded-md">
                        <p class="font-medium text-gray-800 mb-1">Nutrisi Menu Awal:</p>
                        <p>Kalori: <span id="originalMenuKalori" class="font-semibold"></span> kcal</p>
                        <p>Protein: <span id="originalMenuProtein" class="font-semibold"></span> g</p>
                        <p>Karbohidrat: <span id="originalMenuCarbs" class="font-semibold"></span> g</p>
                        <p>Lemak: <span id="originalMenuFat" class="font-semibold"></span> g</p>
                    </div>

                    {{-- Daftar Bahan Makanan untuk Validasi --}}
                    <div>
                        <label class="block text-base font-semibold text-gray-800 mb-2">Detail Konsumsi per Bahan Makanan:</label>
                        <div id="bahanMakananList" class="space-y-3">
                            <p class="text-gray-500">Memuat bahan makanan...</p>
                        </div>
                    </div>

                    {{-- Preview Nutrisi Aktual Total --}}
                    <div id="actualNutritionPreview" class="p-3 bg-indigo-50 rounded-md">
                        <h5 class="font-semibold text-indigo-800 mb-1">Estimasi Total Nutrisi yang Dikonsumsi:</h5>
                        <p>Kalori: <span id="actualTotalKaloriPreview" class="font-bold">0.00 kcal</span></p>
                        <p>Protein: <span id="actualTotalProteinPreview" class="font-bold">0.00 g</span></p>
                        <p>Karbohidrat: <span id="actualTotalCarbsPreview" class="font-bold">0.00 g</span></p>
                        <p>Lemak: <span class="font-bold" id="actualTotalFatPreview">0.00 g</span></p>
                    </div>

                    <div>
                        <label for="notesInput" class="block text-sm font-medium text-gray-700">Catatan Ahli Gizi (Opsional):</label>
                        <textarea id="notesInput" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Contoh: Pasien mual, hanya habis setengah ayam dan tidak menyentuh sayur."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">
                        Simpan Validasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
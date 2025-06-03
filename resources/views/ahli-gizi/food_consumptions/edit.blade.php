<!-- resources/views/ahli-gizi/food_consumptions/edit.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Makanan yang Dikonsumsi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notifikasi Success -->
            @if(session('success'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Form Edit Makanan -->
                <form method="POST" action="{{ route('ahli-gizi.food-consumptions.update', [$patients->id, $food->id]) }}">
                    @csrf
                    @method('PUT')

                    <!-- Nama Makanan -->
                    <div>
                        <x-input-label for="nama_makanan" :value="__('Nama Makanan')" />
                        <x-text-input id="nama_makanan" class="block mt-1 w-full" type="text" name="nama_makanan" value="{{ old('nama_makanan', $food->nama_makanan) }}" required />
                        <x-input-error :messages="$errors->get('nama_makanan')" class="mt-2" />
                    </div>

                    <!-- Kalori -->
                    <div class="mt-4">
                        <x-input-label for="kalori" :value="__('Kalori (kcal)')" />
                        <x-text-input id="kalori" class="block mt-1 w-full" type="number" name="kalori" value="{{ old('kalori', $food->kalori) }}" required min="0" />
                        <x-input-error :messages="$errors->get('kalori')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Tautan Kembali -->
                <div class="mt-4">
                    <a href="{{ route('ahli-gizi.patients.edit', $patients->id) }}" class="text-indigo-600 hover:text-indigo-900">
                        &larr; Kembali ke Edit Pasien
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Bahan Makanan') }}
            </h2>
        </div>
    </x-slot>
    <div class="py-7">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden p-6">
                <div class="flex gap-4">
                    @if($bahanMakanan->gambar)
                        @if (Str::startsWith($bahanMakanan->gambar, ['http://', 'https://']))
                            <img src="{{ $bahanMakanan->gambar }}" alt="{{ $bahanMakanan->nama }}" class="h-32 w-32 object-cover rounded-xl">
                        @else
                            <img src="{{ asset('storage/' . $bahanMakanan->gambar) }}" alt="{{ $bahanMakanan->nama }}" class="h-32 w-32 object-cover rounded-xl">
                        @endif
                    @else
                        <img src="https://via.placeholder.com/150" alt="Default Image" class="h-32 w-32 object-cover rounded-xl">
                    @endif
                    <div class="">
                        <h3 class="text-2xl font-bold">{{ $bahanMakanan->nama }}</h3>
                        <div class="text-gray-500 text-lg mt-1">
                            <span>
                                {{
                                    ($bahanMakanan->karbohidrat * 4) + 
                                    ($bahanMakanan->protein * 4) + 
                                    ($bahanMakanan->total_lemak * 9)
                                }} Kalori . 100g
                            </span>
                        </div>
                    </div>
                </div>
    
                <div class="mt-6">
                    <h4 class="text-lg font-semibold mb-2">Kandungan Gizi:</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="flex">
                            <div class="flex h-20 bg-slate-300 items-end rounded-full w-6">
                                <div class="bg-green-500 w-full rounded-full" style="height: {{ $bahanMakanan->protein }}%;"></div>
                            </div>
                            <div class="ml-2 text-gray-700">
                                <span class="font-semibold text-green-500">Protein:</span> 
                                <span>{{ $bahanMakanan->protein }}g</span>
                            </div>
                        </div>
    
                        <div class="flex">
                            <div class="flex h-20 bg-slate-300 items-end rounded-full w-6">
                                <div class="bg-yellow-500 w-full rounded-full" style="height: {{ $bahanMakanan->karbohidrat }}%;"></div>
                            </div>
                            <div class="ml-2 text-gray-700">
                                <span class="font-semibold text-yellow-500">Karbohidrat:</span>
                                <span>{{ $bahanMakanan->karbohidrat }}g</span>
                            </div>
                        </div>
    
                        <div class="flex">
                            <div class="flex h-20 bg-slate-300 items-end rounded-full w-6">
                                <div class="bg-purple-500 w-full rounded-full" style="height: {{ $bahanMakanan->total_lemak }}%;"></div>
                            </div>
                            <div class="ml-2 text-gray-700">
                                <span class="font-semibold text-purple-500">Lemak:</span>
                                <span>{{ $bahanMakanan->total_lemak }}g</span>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="ml-2 text-gray-700">
                                <span class="font-semibold text-purple-500">Stok:</span>
                                <span>{{ $bahanMakanan->stok }}</span>
                            </div>
                        </div>
                    </div>
                </div>
    
                <p class="text-gray-500 text-lg mt-4">Tipe Pasien: <strong>{{ $bahanMakanan->tipe_pasien }}</strong></p>
    
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('ahli-gizi.bahan_makanans.edit', $bahanMakanan->id) }}" 
                       class="bg-blue-500 text-white px-4 py-2 rounded inline-flex items-center">
                        <svg class="fill-current h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M17.414 2.586a2 2 0 010 2.828l-2 2-2.828-2.828 2-2a2 2 0 012.828 0zm-3.828 3.828L5 15l-2.5.5.5-2.5L13.586 4l2.828 2.828z" />
                        </svg>
                        Edit Menu
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
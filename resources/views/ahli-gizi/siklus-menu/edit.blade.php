<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Entri Siklus Menu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('ahli-gizi.siklus-menu.update', $siklusMenu->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('ahli-gizi.siklus-menu.form', ['siklusMenu' => $siklusMenu])
                        <div class="mt-4">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Perbarui Entri Siklus
                            </button>
                            <a href="{{ route('ahli-gizi.siklus-menu.index') }}" class="ml-2 text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
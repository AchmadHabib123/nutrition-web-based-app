@props(['active'])

@php
$classes = ($active ?? false)
            // ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            // : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
    //         ? 'flex items-center px-4 py-3 bg-white text-emerald-600 rounded-l-3xl font-medium transition'
    // : 'flex items-center px-4 py-3 text-gray-600 hover:text-emerald-600 hover:bg-gray-200 rounded-l-3xl transition';
    ? 'flex items-center px-4 py-2 bg-white text-emerald-600 rounded-l-full font-semibold shadow'
    : 'flex items-center px-4 py-2 text-gray-700 hover:text-emerald-600 hover:bg-gray-200 rounded-l-full transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

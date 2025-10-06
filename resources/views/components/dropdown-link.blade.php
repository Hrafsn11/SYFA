@props(['href' => '#'])

<a {{ $attributes->merge(['href' => $href, 'class' => 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</a>
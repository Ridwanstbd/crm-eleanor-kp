@props(['name', 'id'])
<input type="file" 
    name="{{ $name }}" 
    id="{{ $id }}"
    {{ $attributes->merge(['class' => 'mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100']) }}>
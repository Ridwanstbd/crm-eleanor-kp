@props(['name', 'id', 'rows' => 3])
<textarea 
    name="{{ $name }}" 
    id="{{ $id }}"
    rows="{{ $rows }}"
    {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}>{{ $slot }}</textarea>

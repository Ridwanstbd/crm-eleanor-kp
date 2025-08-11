@props(['name', 'id'])
<div class="mt-1 flex rounded-md shadow-sm">
    <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm">
        +62
    </span>
    <x-Atoms.Input 
        :name="$name" 
        :id="$id" 
        {{ $attributes->merge(['class' => 'block w-full flex-1 rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500']) }} />
</div>
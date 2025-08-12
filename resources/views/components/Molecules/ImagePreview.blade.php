@props(['title', 'image'])
<div class="space-y-4">
    <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
    <div class="relative h-64 w-full mx-auto rounded-lg border-2 border-dashed border-gray-300 p-4">
        @if ($image)
            <img src="{{ asset('storage/' . $image) }}" 
                alt="Favicon preview" 
                class="mx-auto max-h-full max-w-full object-contain">
        @else
            <div class="text-center text-gray-500">No image selected</div>
        @endif
    </div>
</div>

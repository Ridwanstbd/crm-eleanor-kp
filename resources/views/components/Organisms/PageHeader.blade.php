@props([
    'title',
    'containerClass' => 'flex items-center justify-between py-3'
])

<header {{ $attributes->merge(['class' => $containerClass]) }}>
    <div>
        <x-Atoms.Typography.Heading level="2">
            {{ $title }}
        </x-Atoms.Typography.Heading>
        
    </div>
    
    <div class="flex gap-4 items-center">
        @if(isset($filters))
            <div class="flex gap-2">
                {{ $filters }}
            </div>
        @endif
        @if(isset($actions))
            <div class="flex gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</header>
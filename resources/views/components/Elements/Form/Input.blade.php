@props([
    'type' => 'text',
    'name',
    'id'=> null,
    'value',
    'placeholder' => ''
])

<input
    type="{{$type}}"
    name="{{$name}}"
    value="{{$value ?? null}}"
    id="{{$id}}"
    placeholder="{{$placeholder ?? null}}"
    {{ $attributes->merge([
        'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
    ])}}
>

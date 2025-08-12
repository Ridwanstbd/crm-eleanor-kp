@props([
    'name' => 'email',
    'id' => 'email',
    'label' => 'Email',
    'placeholder' => 'Email'
])

<div>
    <x-Atoms.Label for="{{$id}}">
        {{$label}}
    </x-Atoms.Label>
    <x-Atoms.Form.Input 
        type="email"
        :id="$id"
        :name="$name"
        :placeholder="$placeholder"
        {{ $attributes }}
    />
    <x-Atoms.Form.InputError :message="$errors->first($name)"/>
</div>
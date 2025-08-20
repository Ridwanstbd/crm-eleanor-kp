@props(['id' => null, 'name', 'label' => null])

<div class="flex items-center">
    <input 
        @if($id) id="{{$id}}" @endif 
        name="{{$name}}" 
        type="checkbox"
        {{$attributes->merge(['class'=>"h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"])}} 
    >
    @if($label)
        <label @if($id) for="{{$id}}" @endif class="ml-2 block text-sm text-gray-900">
            {{$label}}
        </label>
    @endif
</div>
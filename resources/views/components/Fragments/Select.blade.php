{{-- components/Fragments/Select.blade.php --}}
@props([
    'options' => [],
    'valueField' => 'id',
    'textField' => 'name',
    'placeholder' => 'Pilih...',
    'selected' => null,
    'name' => '',
    'allowEmpty' => true
])

<x-Elements.Select {{ $attributes }}>
    @if($allowEmpty)
        <x-Elements.Option value="" :selected="empty($selected) && empty(old($name))">
            {{ $placeholder }}
        </x-Elements.Option>
    @endif
    
    @foreach($options as $option)
        @php
            $optionValue = is_array($option) ? $option[$valueField] : $option->{$valueField};
            $optionText = is_array($option) ? $option[$textField] : $option->{$textField};
            $isSelected = $selected == $optionValue || old($name) == $optionValue;
        @endphp
        
        <x-Elements.Option 
            :value="$optionValue" 
            :selected="$isSelected"
        >
            {{ $optionText }}
        </x-Elements.Option>
    @endforeach
</x-Elements.Select>
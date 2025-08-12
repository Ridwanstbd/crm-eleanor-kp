@props([
    'options' => [],
    'valueField' => 'id',
    'textField' => 'name',
    'placeholder' => 'Pilih...',
    'selected' => null,
    'name' => '',
    'allowEmpty' => true
])

<x-Atoms.Select {{ $attributes }}>
    @if($allowEmpty)
        <x-Atoms.Option value="" :selected="empty($selected) && empty(old($name))">
            {{ $placeholder }}
        </x-Atoms.Option>
    @endif
    
    @foreach($options as $option)
        @php
            $optionValue = is_array($option) ? $option[$valueField] : $option->{$valueField};
            $optionText = is_array($option) ? $option[$textField] : $option->{$textField};
            $isSelected = $selected == $optionValue || old($name) == $optionValue;
        @endphp
        
        <x-Atoms.Option 
            :value="$optionValue" 
            :selected="$isSelected"
        >
            {{ $optionText }}
        </x-Atoms.Option>
    @endforeach
</x-Atoms.Select>
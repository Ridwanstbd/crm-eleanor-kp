{{-- components/Elements/Table/td.blade.php --}}
<td {{ $attributes->merge(['class' => 'px-4 py-3 border-t']) }}>
    {{ $slot }}
</td>

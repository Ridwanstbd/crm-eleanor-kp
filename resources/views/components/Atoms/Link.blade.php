@props(['href'])

<a href="{{$href}}" {{$attributes->merge(['class'=>"font-medium"])}} >
    {{$slot}}
</a>

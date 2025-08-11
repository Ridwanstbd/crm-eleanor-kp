<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-Atoms.Head :title="$title ?? 'Login'" />

<body class="bg-gray-200">
    <div class="min-h-screen justify-center items-center flex">
        <div class="">
            {{$slot}}
        </div>
    </div>
</body>
</html>
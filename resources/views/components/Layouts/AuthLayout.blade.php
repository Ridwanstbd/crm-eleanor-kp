<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-Atoms.Head :title="$title ?? 'Login'" />

<body class="bg-gray-200">
    <div class="min-h-screen flex justify-center items-center p-2 sm:p-4">
        <div class="w-full md:max-w-2xl mx-auto">
            {{$slot}}
        </div>
    </div>
</body>
</html>
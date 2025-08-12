<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-Atoms.Head :title="$title ?? 'Admin Dashboard'" />
    <body>
        <main class="max-w-7xl px-4 mx-auto relative">
            {{$slot}}
            <x-Layouts.Footer/> 
        </main>
        <x-Molecules.CallCenter />
    </body>
</html>
@props(['title' => 'Default Title', 'paginator'])

<x-Layouts.AdminLayout :title="$title">
    <x-Layouts.PageHeader :title="$title">
        @if (isset($filters))
            <x-slot name="filters">
                {{ $filters }}
            </x-slot>
        @endif
        @if (isset($actions))
            <x-slot name="actions">
                {{ $actions }}
            </x-slot>
        @endif
    </x-Layouts.PageHeader>

    <x-Layouts.Table>
        <x-Molecules.Table.Header>
            {{ $tableHeader }}
        </x-Molecules.Table.Header>

        <x-Molecules.Table.Body>
            {{ $slot }}
        </x-Molecules.Table.Body>

        <x-slot name="pagination">
            <x-Molecules.Table.Pagination :paginator="$paginator" />
        </x-slot>
    </x-Layouts.Table>
</x-Layouts.AdminLayout>
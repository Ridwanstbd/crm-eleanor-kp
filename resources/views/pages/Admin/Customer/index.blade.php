<x-Layouts.AdminLayout title="Pelanggan">
  <x-Layouts.PageHeader title="Grup Pelanggan">
  </x-Layouts.PageHeader>

  <x-Layouts.Table>
    <x-Molecules.Table.Header>
      <x-Atoms.Table.th>Nama Grup</x-Atoms.Table.th>
      <x-Atoms.Table.th class="w-24 text-center">Jumlah</x-Atoms.Table.th>
      <x-Atoms.Table.th class="w-36 text-center">Aksi</x-Atoms.Table.th>
    </x-Molecules.Table.Header>

    <x-Molecules.Table.Body>
      @forelse ($groups as $group)
        <tr>
          <x-Atoms.Table.td>{{ $group->name }}</x-Atoms.Table.td>
          <x-Atoms.Table.td class="text-center">
            <span class="inline-block text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-700">
              {{ $group->customers_count }}
            </span>
          </x-Atoms.Table.td>
          <x-Atoms.Table.td class="text-center">
            <x-Atoms.Button variant="secondary">
              <x-Atoms.Link :href="route('customer-groups.show', $group)">Detail</x-Atoms.Link>
            </x-Atoms.Button>
          </x-Atoms.Table.td>
        </tr>
      @empty
        <tr>
          <td colspan="3" class="px-6 py-8 text-center text-gray-500">
            Belum ada grup pelanggan.
          </td>
        </tr>
      @endforelse
    </x-Molecules.Table.Body>

    <x-slot name="pagination">
      {{ $groups->links() }}
    </x-slot>
  </x-Layouts.Table>

</x-Layouts.AdminLayout>

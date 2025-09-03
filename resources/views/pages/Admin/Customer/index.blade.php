<x-Templates.IndexTemplate title="Grup Pelanggan" :paginator="$groups">
  <x-slot name="tableHeader">
      <x-Atoms.Table.th>Nama Grup</x-Atoms.Table.th>
      <x-Atoms.Table.th>Jumlah</x-Atoms.Table.th>
      <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
  </x-slot>
      @forelse ($groups as $group)
        <tr>
          <x-Atoms.Table.td>{{ $group->name }}</x-Atoms.Table.td>
          <x-Atoms.Table.td>
            <span class="inline-block text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-700">
              {{ $group->customers_count }}
            </span>
          </x-Atoms.Table.td>
          <x-Atoms.Table.td>
            <x-Atoms.Button variant="secondary" :href="route('customer-groups.show', $group)">
              Detail
            </x-Atoms.Button>
            <x-Atoms.Button variant="danger" @click="$dispatch('open-modal', 'delete-confirmation-{{ $group->id }}')">Hapus</x-Atoms.Button>
          </x-Atoms.Table.td>
          <x-Layouts.Modal
                  name="delete-confirmation-{{ $group->id }}"
                  title=""
                  maxWidth="sm"
                  :showIcon="false"
              >
                  <form action="{{ route('customers.destroy-group', $group) }}" method="POST">
                      @csrf
                      @method('DELETE')
                      <x-Molecules.ConfirmationContent :id="$group->id">
                          Yakin, Hapus {{$group->name}}?
                      </x-Molecules.ConfirmationContent>
                  </form>
              </x-Layouts.Modal>
        </tr>
      @empty
        <tr>
          <td colspan="3" class="px-6 py-8 text-center text-gray-500">
            Belum ada grup pelanggan.
          </td>
        </tr>
      @endforelse
</x-Templates.IndexTemplate>

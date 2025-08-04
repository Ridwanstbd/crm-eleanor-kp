<x-Layouts.AdminLayout>
    <x-Organisms.PageHeader title="Detail Kampanye {{$campaign->name}}" />
    <div class="grid grid-cols-2 gap-2">
        <x-Fragments.Form.FormGroup label="Nama Kampanye" for="name">
            <x-Elements.Input name="name" value="{{$campaign->name}}" disabled readonly/>
        </x-Fragments.Form.FormGroup>
        <x-Fragments.Form.FormGroup label="Produk" for="product">
            <x-Fragments.Select 
                name="product" 
                id="product"
                :options="collect([$product])"
                valueField="id"
                textField="name"
                :selected="$product->id"
                :allowEmpty="false"
                disabled
                readonly>
            </x-Fragments.Select>
            @error('product')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </x-Fragments.Form.FormGroup>

        <x-Fragments.Form.FormGroup label="Tanggal Terjual" for="tanggal_terjual">
            <x-Elements.InputDate
                name="tanggal_terjual"
                id="tanggal_terjual"
                value="{{ $campaign->schedule }}"
                readonly
                disabled
            />
            @error('tanggal_terjual')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </x-Fragments.Form.FormGroup>
        
        <x-Fragments.Form.FormGroup label="Waktu Kirim Kampanye" for="time_send">
            <x-Elements.InputTime
                name="time_send"
                id="time_send"
                value="{{ $campaign->time_send }}"
                readonly
                disabled
            />
            @error('time_send')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </x-Fragments.Form.FormGroup>
    </div>
    <x-Fragments.Form.FormGroup label="Template" for="template">
        <x-Atoms.TextArea 
            name="content" 
            id="messageTemplate-{{ $template->id }}"
            rows="8"
            height="200px"
            resize="vertical"
            placeholder="Ketik template pesan Anda..."
            class="mb-2"
            :class="$errors->has('content') ? 'border-red-500 focus:ring-red-500' : ''"
            disabled readonly>{{ $template->content }}</x-Atoms.TextArea>
    </x-Fragments.Form.FormGroup>
    <x-Layouts.Table>
            <x-Fragments.Table.Header>
                <x-Elements.Table.th>No</x-Elements.Table.th>
                <x-Elements.Table.th>
                    Nama Pelanggan
                </x-Elements.Table.th>
                <x-Elements.Table.th>
                    Nomor Telepon
                </x-Elements.Table.th>
                <x-Elements.Table.th>
                    Jumlah Beli
                </x-Elements.Table.th>
            </x-Fragments.Table.Header>

            <x-Fragments.Table.Body>
                @forelse ($customers as $customer)
                    <tr>
                        <x-Elements.Table.td>{{ $loop->iteration }}</x-Elements.Table.td>
                        <x-Elements.Table.td>{{ $customer->name }}</x-Elements.Table.td>
                        <x-Elements.Table.td>{{ $customer->phone }}</x-Elements.Table.td>
                        <x-Elements.Table.td>{{ $customer->purchase_quantity}}</x-Elements.Table.td>
                    </tr>
                @empty
                    <x-Elements.Table.empty colspan="4">
                        <p class="mt-1">Belum ada pelanggan.</p>
                    </x-Elements.Table.empty>
                @endforelse
            </x-Fragments.Table.Body>

            <x-slot name="pagination">
                <x-Fragments.Table.Pagination :paginator="$customers" />
            </x-slot>
        </x-Layouts.Table>
</x-Layouts.AdminLayout>

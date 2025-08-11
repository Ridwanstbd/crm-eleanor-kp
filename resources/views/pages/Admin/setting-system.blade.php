<x-Layouts.AdminLayout title="Informasi System">
    <x-Layouts.ContainerLayout>
        <div x-data="{ 
            imagePreview: $store.system.favicon ? '{{ asset('storage/') }}' + $store.system.favicon : '',
            name: $store.system.name,
            description: $store.system.description,
            
            handleFileUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        }">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column - Form Fields -->
                    <div>
                        <x-Fragments.Form.FormGroup :message="$errors->first('name')" label="Nama Sistem" for="name">
                            <x-Atoms.Form.Input name="name" id="name" x-model="name" readonly/>
                        </x-Fragments.Form.FormGroup>
        
                        <x-Fragments.Form.FormGroup :message="$errors->first('description')" label="Description" for="description">
                            <x-Atoms.Form.Textarea name="description" id="description" x-model="description" readonly/>
                        </x-Fragments.Form.FormGroup>
                    </div>
        
                    <!-- Right Column - Image Preview -->
                    <div>
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Logo</h3>
                            <div class="relative h-64 w-full mx-auto rounded-lg border-2 border-dashed border-gray-300 p-4">
                                <img src="{{ asset($system['favicon']) }}" 
                                alt="Favicon preview" 
                                class="mx-auto max-h-full max-w-full object-contain">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-Layouts.ContainerLayout>
</x-Layouts.AdminLayout>
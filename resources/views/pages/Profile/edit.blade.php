<x-Layouts.AdminLayout title="Informasi Profil">
    <x-Layouts.ContainerLayout>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                @include('pages.Profile.partials.update-profile-information-form')
                @include('pages.Profile.partials.update-password-form')
            </div>
        </div>
    </x-Layouts.ContainerLayout>
</x-Layouts.AdminLayout>

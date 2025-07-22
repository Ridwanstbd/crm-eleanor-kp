@props(['title'])
<div class="flex w-full mx-auto bg-white rounded-2xl overflow-hidden">
    <div class="w-1/2 bg-[url('/assets/background/auth-bg.png')] bg-cover bg-center bg-no-repeat p-48 flex flex-col justify-center items-center relative">
        <div class="text-center text-white z-10">
            <h1 class="text-4xl font-bold text-nowrap mb-4">Eleanor CRM</h1>
            <p class="text-md opacity-90">Kepuasan Anda tujuan Kami</p>
        </div>
    </div>

    <div class="w-1/2 px-12 py-32 flex flex-col justify-center">
        <div class="mx-auto w-full">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">
                {{$title}}
            </h2>
            {{$slot}}
        </div>
    </div>
</div>
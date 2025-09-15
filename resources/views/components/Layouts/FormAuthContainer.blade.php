@props(['title'])
<div class="flex flex-col lg:flex-row w-full mx-auto bg-white rounded-xl lg:rounded-2xl overflow-hidden  max-h-[95vh] lg:max-h-[90vh]">
    <div class="w-full lg:w-1/2 bg-[url('/assets/background/auth-bg.png')] bg-cover bg-center bg-no-repeat p-4 sm:p-6 lg:p-12 xl:p-16 flex flex-col justify-center items-center relative min-h-[200px] sm:min-h-[250px] lg:min-h-full">
        <div class="text-center text-white z-10">
            <h1 class="text-xl md:text-2xl lg:text-3xl xl:text-4xl font-bold mb-1 sm:mb-2 lg:mb-4 whitespace-nowrap">Eleanor CRM</h1>
            <p class="text-xs sm:text-sm lg:text-base opacity-90 whitespace-nowrap">Kepuasan Anda tujuan Kami</p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 px-4 sm:px-4 lg:px-6 xl:px-10 py-4 sm:py-6 lg:py-8 flex flex-col justify-center overflow-y-auto">
        <div class="mx-auto w-full max-w-sm">
            <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-4 sm:mb-6 lg:mb-8 whitespace-nowrap">
                {{$title}}
            </h2>
            {{$slot}}
        </div>
    </div>
</div>
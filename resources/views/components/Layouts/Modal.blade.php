{{-- components/Layouts/Modal.blade.php --}}
@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'trigger' => null,
    'title' => null,
    'mode' => 'default',
    'message' => null,
    'loading' => false
])

@php
use Illuminate\Support\Str;
$maxWidth = Str::startsWith($maxWidth, '[')
    ? "sm:max-w-$maxWidth"
    : [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth] ?? 'sm:max-w-2xl';
@endphp

<div
    x-data="{
        show: @js($show),
        mode: '{{ $mode }}',
        loading: @js($loading),
        modalName: '{{ $name }}',
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
        hasForm() { return this.$el.querySelector('form') !== null },
        resetForm() {
            if (this.hasForm()) {
                this.$el.querySelector('form').reset()
            }
        },
        openModal(modalName) {
            if (modalName === this.modalName) {
                this.show = true;
            }
        },
        closeModal(modalName) {
            if (!modalName || modalName === this.modalName) {
                this.show = false;
            }
        }
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
            resetForm();
        }
    })"
    x-on:open-modal.window="openModal($event.detail)"
    x-on:close-modal.window="closeModal($event.detail)"
    x-on:close.stop="closeModal()"
    x-on:keydown.escape.window="show && closeModal()"
    x-on:keydown.tab.prevent="show && ($event.shiftKey || nextFocusable().focus())"
    x-on:keydown.shift.tab.prevent="show && prevFocusable().focus()"
>
    @if($trigger)
    <div @click="show = true">
        {{ $trigger }}
    </div>
    @endif

    <div
        x-show="show"
        x-cloak
        class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
        style="display: none;"
    >
        <div
            x-show="show"
            class="fixed inset-0 transform transition-all"
            x-on:click="closeModal()"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="show"
            class="mb-6 bg-white p-4 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            @if($title)
            <div class="flex items-center justify-between px-0 py-2">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    {{ $title }}
                </h3>
                <button
                    @click="closeModal()"
                    type="button"
                    class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @endif

            <div x-show="loading" class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center z-50">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            </div>

            @if($mode === 'destroy')
                <div class="p-6">
                    <div class="flex items-center justify-center">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    {{ $message ?? 'Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    {{ $slot }}
                </div>
            @else
                <div :class="{ 'pointer-events-none opacity-75': loading }">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</div>
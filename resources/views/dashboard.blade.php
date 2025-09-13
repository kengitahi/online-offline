<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div
        class="mb-4 p-4 rounded-md shadow"
        :class="isOnline ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
        x-show.transition.duration.500ms="showMessage"
        x-cloak
    >
        <span
            class="font-semibold"
            x-text="messageContent"
        ></span>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
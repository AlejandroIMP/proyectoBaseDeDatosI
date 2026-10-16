<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
            @foreach (\App\Support\Modules::groups() as $group => $modules)
                <section>
                    <h3 class="px-4 text-sm font-semibold tracking-wider text-gray-500 uppercase sm:px-0">{{ $group }}</h3>

                    <div class="mt-3 grid grid-cols-2 gap-4 px-4 sm:grid-cols-3 sm:px-0 lg:grid-cols-4">
                        @foreach ($modules as $slug => $module)
                            <a href="{{ route('crud.'.$slug) }}" wire:navigate class="rounded-lg bg-white p-5 shadow-sm transition hover:shadow-md hover:ring-1 hover:ring-indigo-200">
                                <p class="text-sm text-gray-500">{{ $module['label'] }}</p>
                                <p class="mt-1 text-2xl font-semibold text-gray-900 tabular-nums">{{ number_format($module['model']::query()->count()) }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
            @forelse (\App\Support\Modules::groupsFor(auth()->user()) as $group => $modules)
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
            @empty
                @can('adopt')
                    @php($adopter = auth()->user()->load('person'))
                    <div class="mx-4 rounded-lg bg-white p-8 shadow-sm sm:mx-0">
                        <h3 class="text-lg font-semibold text-gray-900">¡Bienvenido, {{ $adopter->name }}!</h3>
                        <p class="mt-2 text-gray-600">Desde aquí puedes conocer a las mascotas que buscan hogar y seguir tus solicitudes de adopción.</p>
                        @if ($adopter->person === null)
                            <p class="mt-4 rounded-md bg-indigo-50 p-4 text-sm text-indigo-800">
                                Primero <a href="{{ route('adopter.profile') }}" wire:navigate class="font-semibold underline">completa tus datos de adoptante</a>; los necesitamos para evaluar tus solicitudes.
                            </p>
                        @endif
                    </div>

                    <div class="grid gap-4 px-4 sm:grid-cols-3 sm:px-0">
                        @foreach ([
                            ['route' => 'adopter.pets', 'title' => 'Mascotas disponibles', 'text' => 'Explora el catálogo y elige a tu nuevo compañero.'],
                            ['route' => 'adopter.requests', 'title' => 'Mis solicitudes', 'text' => 'Revisa en qué estado va cada solicitud.'],
                            ['route' => 'adopter.profile', 'title' => 'Mis datos', 'text' => 'Actualiza la información que evalúa el refugio.'],
                        ] as $card)
                            <a href="{{ route($card['route']) }}" wire:navigate class="rounded-lg bg-white p-5 shadow-sm transition hover:shadow-md hover:ring-1 hover:ring-indigo-200">
                                <p class="font-semibold text-gray-900">{{ $card['title'] }}</p>
                                <p class="mt-1 text-sm text-gray-500">{{ $card['text'] }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="mx-4 rounded-lg bg-white p-8 shadow-sm sm:mx-0">
                        <h3 class="text-lg font-semibold text-gray-900">¡Bienvenido, {{ auth()->user()->name }}!</h3>
                        <p class="mt-2 text-gray-600">
                            Tu cuenta está registrada como <span class="font-medium text-indigo-700">{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'sin rol' }}</span>.
                            Por ahora no tienes acceso a los módulos de gestión del refugio; si formas parte del personal, pide a un administrador que te asigne el rol correspondiente.
                        </p>
                    </div>
                @endcan
            @endforelse
        </div>
    </div>
</x-app-layout>

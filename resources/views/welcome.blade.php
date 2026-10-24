@php
    use App\Models\Adoption;
    use App\Models\Pet;
    use App\Models\Rescue;
    use App\Models\Shelter;
    use App\Support\Modules;

    // La portada es publica: si la base no responde se muestra sin cifras en lugar de fallar.
    $stats = collect([
        'Mascotas registradas' => Pet::class,
        'Adopciones' => Adoption::class,
        'Rescates' => Rescue::class,
        'Refugios' => Shelter::class,
    ])->map(fn ($model) => rescue(fn () => $model::query()->count(), null, false));

    $groupDescriptions = [
        'Mascotas' => 'Expediente de cada animal: historial, tratamientos, rescates y citas veterinarias.',
        'Adopciones' => 'Solicitudes de adopción, contratos firmados y seguimiento posterior a la entrega.',
        'Personas' => 'Adoptantes, donantes y el registro de cada donación recibida.',
        'Catálogos' => 'Especies, razas, refugios y demás datos base que usa el sistema.',
        'Seguridad' => 'Usuarios del sistema, roles y permisos de acceso.',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col">
            <header class="bg-white/80 backdrop-blur border-b border-gray-200 sticky top-0 z-10">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                    <a href="/" class="flex items-center gap-2 font-semibold text-gray-900">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white">
                            <x-application-logo class="h-5 w-5" />
                        </span>
                        <span class="hidden sm:inline">{{ config('app.name') }}</span>
                    </a>

                    @if (Route::has('login'))
                        <livewire:welcome.navigation />
                    @endif
                </div>
            </header>

            <main class="flex-1">
                <section class="bg-gradient-to-b from-indigo-50 to-gray-50">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24 grid gap-12 lg:grid-cols-2 items-center">
                        <div>
                            <p class="text-sm font-semibold tracking-wider text-indigo-600 uppercase">Sistema de gestión</p>
                            <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                                Adopciones de mascotas, de principio a fin
                            </h1>
                            <p class="mt-5 text-lg text-gray-600 max-w-xl">
                                Registra a cada animal desde su rescate, lleva su historial médico, gestiona las
                                solicitudes de adopción y da seguimiento a cada familia que abre su hogar.
                            </p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                                        Ir al panel
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                                        Iniciar sesión
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-md bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                                            Crear cuenta
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <dl class="grid grid-cols-2 gap-4">
                            @foreach ($stats as $label => $count)
                                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                                    <dt class="text-sm text-gray-500">{{ $label }}</dt>
                                    <dd class="mt-2 text-3xl font-semibold text-gray-900 tabular-nums">{{ is_null($count) ? '—' : number_format($count) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </section>

                <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Todo lo que el refugio necesita</h2>
                        <p class="mt-2 text-gray-600">Cada módulo cuenta con su propio catálogo para registrar, consultar, editar y eliminar información.</p>
                    </div>

                    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach (Modules::groups() as $group => $modules)
                            <article class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $group }}</h3>
                                <p class="mt-2 text-sm text-gray-600">{{ $groupDescriptions[$group] ?? '' }}</p>
                                <ul class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($modules as $module)
                                        <li class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $module['label'] }}</li>
                                    @endforeach
                                </ul>
                            </article>
                        @endforeach
                    </div>
                </section>
            </main>

            <footer class="border-t border-gray-200 bg-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-500 flex flex-col sm:flex-row justify-between gap-2">
                    <p>{{ config('app.name') }} · Proyecto de Bases de Datos 1</p>
                    <p>Ingeniería en Sistemas · 6.º semestre</p>
                </div>
            </footer>
        </div>
    </body>
</html>

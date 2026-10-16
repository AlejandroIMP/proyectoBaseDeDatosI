@props(['title', 'records', 'singular' => 'registro'])

<div>
    <header class="bg-white shadow">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <h2 class="text-xl leading-tight font-semibold text-gray-800">{{ $title }}</h2>

            <div class="flex items-center gap-3">
                <x-text-input wire:model.live.debounce.300ms="search" type="search" placeholder="Buscar..." class="w-full sm:w-64" />
                <x-primary-button type="button" wire:click="create" class="shrink-0">Nuevo</x-primary-button>
            </div>
        </div>
    </header>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                {{ $head }}
                                <th class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            {{ $slot }}
                        </tbody>
                    </table>
                </div>

                @if ($records->isEmpty())
                    <p class="p-6 text-center text-gray-500">No hay registros.</p>
                @endif
            </div>

            <div>{{ $records->links() }}</div>
        </div>
    </div>

    {{-- Modal del formulario de creacion/edicion --}}
    <div x-cloak x-show="$wire.showForm" x-on:keydown.escape.window="$wire.showForm = false" class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0">
        <div x-show="$wire.showForm" x-transition.opacity class="fixed inset-0 bg-gray-500/75" x-on:click="$wire.showForm = false"></div>

        <div x-show="$wire.showForm" x-transition class="relative mx-auto mb-6 overflow-hidden rounded-lg bg-white shadow-xl sm:w-full sm:max-w-2xl">
            <form wire:submit="save" class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                    <span x-text="$wire.editingId ? 'Editar' : 'Registrar'"></span> {{ $singular }}
                </h3>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{ $form }}
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="$wire.showForm = false">Cancelar</x-secondary-button>
                    <x-primary-button wire:loading.attr="disabled" wire:target="save">Guardar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>

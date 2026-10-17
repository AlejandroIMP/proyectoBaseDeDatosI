@props(['name' => 'pet_id', 'label' => 'Mascota', 'pets' => [], 'placeholder' => 'Seleccione...'])

{{-- Selector de mascotas con busqueda; muestra especie/raza y color para distinguir nombres repetidos --}}
@php
$items = collect($pets)->map(fn ($pet) => [
    'id' => $pet->getKey(),
    'name' => $pet->name,
    'species' => $pet->species?->name,
    'breed' => $pet->breed?->name,
    'color' => $pet->color?->name,
    'hex' => $pet->color?->hex_color,
])->values();
@endphp

<div
    x-data="{
        value: $wire.entangle('form.{{ $name }}'),
        open: false,
        search: '',
        items: @js($items),
        get selected() {
            return this.items.find(item => item.id == this.value);
        },
        get filtered() {
            const term = this.search.toLowerCase().trim();
            if (!term) return this.items;
            return this.items.filter(item =>
                [item.name, item.species, item.breed, item.color].some(text => text?.toLowerCase().includes(term))
            );
        },
        detail(item) {
            return [item.species, item.breed].filter(Boolean).join(' / ');
        },
        choose(item) {
            this.value = item ? item.id : '';
            this.open = false;
            this.search = '';
        },
    }"
    x-on:click.outside="open = false"
    x-on:keydown.escape="if (open) { open = false; $event.stopPropagation() }"
    class="relative"
>
    <x-input-label :for="$name" :value="$label" />

    <button
        type="button"
        id="{{ $name }}"
        x-on:click="open = !open; if (open) $nextTick(() => $refs.search.focus())"
        class="mt-1 flex w-full items-center justify-between gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-left text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
    >
        <template x-if="selected">
            <span class="flex min-w-0 items-center gap-2">
                <span class="size-3 shrink-0 rounded-full border border-gray-300" x-bind:style="{ backgroundColor: selected.hex || 'transparent' }"></span>
                <span class="truncate font-medium text-gray-900" x-text="selected.name"></span>
                <span class="truncate text-gray-500" x-text="detail(selected)"></span>
            </span>
        </template>
        <template x-if="!selected">
            <span class="text-gray-500">{{ $placeholder }}</span>
        </template>
        <svg class="size-4 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 111.08 1.04l-4.25 4.39a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
    </button>

    <div x-cloak x-show="open" x-transition class="absolute z-10 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-lg">
        <div class="border-b border-gray-100 p-2">
            <input x-ref="search" x-model="search" type="text" placeholder="Buscar por nombre, especie, raza o color..." class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <ul class="max-h-64 overflow-y-auto py-1 text-sm">
            <li>
                <button type="button" x-on:click="choose(null)" class="w-full px-3 py-2 text-left text-gray-500 hover:bg-gray-50">{{ $placeholder }}</button>
            </li>
            <template x-for="item in filtered" x-bind:key="item.id">
                <li>
                    <button
                        type="button"
                        x-on:click="choose(item)"
                        x-bind:class="item.id == value ? 'bg-indigo-50' : 'hover:bg-gray-50'"
                        class="flex w-full items-center gap-3 px-3 py-2 text-left"
                    >
                        <span class="size-4 shrink-0 rounded-full border border-gray-300" x-bind:style="{ backgroundColor: item.hex || 'transparent' }" x-bind:title="item.color"></span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-medium text-gray-900">
                                <span x-text="item.name"></span>
                                <span class="font-normal text-gray-400" x-text="'#' + item.id"></span>
                            </span>
                            <span class="block truncate text-xs text-gray-500">
                                <span x-text="detail(item) || 'Sin especie/raza'"></span>
                                <template x-if="item.color">
                                    <span>&middot; <span x-text="item.color"></span></span>
                                </template>
                            </span>
                        </span>
                    </button>
                </li>
            </template>
            <li x-show="filtered.length === 0" class="px-3 py-2 text-gray-500">Sin resultados.</li>
        </ul>
    </div>

    <x-input-error :messages="$errors->get('form.'.$name)" class="mt-2" />
</div>

<?php

use App\Http\Controllers\Api\ColorController;
use App\Livewire\CrudComponent;
use App\Models\Color;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Colores')] class extends CrudComponent
{
    protected string $model = Color::class;

    protected string $controller = ColorController::class;

    protected array $searchable = ['name', 'hex_color'];

    protected array $labels = ['name' => 'nombre', 'hex_color' => 'color hexadecimal'];
}; ?>

<x-crud.page title="Colores" singular="color" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Hexadecimal</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>
                @if ($record->hex_color)
                    <span class="inline-flex items-center gap-2">
                        <span class="size-4 rounded-full border" style="background-color: {{ $record->hex_color }}"></span>
                        {{ $record->hex_color }}
                    </span>
                @endif
            </x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.input name="hex_color" label="Color hexadecimal" placeholder="#A0522D" />
    </x-slot:form>
</x-crud.page>

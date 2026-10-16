<?php

use App\Http\Controllers\Api\PersonController;
use App\Livewire\CrudComponent;
use App\Models\Occupation;
use App\Models\Person;
use App\Models\Residence;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Personas')] class extends CrudComponent
{
    protected string $model = Person::class;

    protected string $controller = PersonController::class;

    protected array $with = ['occupation', 'residence'];

    protected array $searchable = ['name', 'document_number', 'email'];

    protected array $defaults = ['document_type' => 'DPI', 'current_pets_count' => 0, 'cantidad_hijos' => 0];

    protected array $labels = [
        'name' => 'nombre',
        'document_type' => 'tipo de documento',
        'document_number' => 'número de documento',
        'email' => 'correo',
        'current_pets_count' => 'mascotas actuales',
        'estado_civil' => 'estado civil',
        'cantidad_hijos' => 'cantidad de hijos',
        'occupation_id' => 'ocupación',
        'residence_id' => 'vivienda',
        'income' => 'ingresos',
    ];

    protected function options(): array
    {
        return [
            'occupations' => Occupation::query()->orderBy('name')->get(),
            'residences' => Residence::query()->orderBy('name')->get(),
        ];
    }
}; ?>

<x-crud.page title="Personas" singular="persona" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Documento</x-crud.th>
        <x-crud.th>Correo</x-crud.th>
        <x-crud.th>Ocupación</x-crud.th>
        <x-crud.th>Vivienda</x-crud.th>
        <x-crud.th class="text-right">Ingresos</x-crud.th>
        <x-crud.th class="text-right">Mascotas</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->document_type === 'PASSPORT' ? 'Pasaporte' : 'DPI' }} {{ $record->document_number }}</x-crud.td>
            <x-crud.td>{{ $record->email }}</x-crud.td>
            <x-crud.td>{{ $record->occupation?->name }}</x-crud.td>
            <x-crud.td>{{ $record->residence?->name ?? '#'.$record->residence_id }}</x-crud.td>
            <x-crud.td class="text-right tabular-nums">Q {{ number_format($record->income) }}</x-crud.td>
            <x-crud.td class="text-right tabular-nums">{{ $record->current_pets_count }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" span />
        <x-crud.select name="document_type" label="Tipo de documento" :options="['DPI' => 'DPI', 'PASSPORT' => 'Pasaporte']" />
        <x-crud.input name="document_number" label="Número de documento" />
        <x-crud.input name="email" label="Correo" type="email" span />
        <x-crud.input name="estado_civil" label="Estado civil" />
        <x-crud.input name="cantidad_hijos" label="Cantidad de hijos" type="number" min="0" />
        <x-crud.select name="occupation_id" label="Ocupación" :options="$occupations" />
        <x-crud.select name="residence_id" label="Vivienda" :options="$residences" />
        <x-crud.input name="income" label="Ingresos (Q)" type="number" min="0" />
        <x-crud.input name="current_pets_count" label="Mascotas actuales" type="number" min="0" />
    </x-slot:form>
</x-crud.page>

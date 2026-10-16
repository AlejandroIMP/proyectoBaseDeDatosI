<?php

use App\Http\Controllers\Api\TreatmentController;
use App\Livewire\CrudComponent;
use App\Models\Treatment;
use App\Models\TreatmentType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Tratamientos')] class extends CrudComponent
{
    protected string $model = Treatment::class;

    protected string $controller = TreatmentController::class;

    protected array $with = ['treatmentType'];

    protected array $searchable = ['name', 'treatmentType.name'];

    protected array $defaults = ['requires_veterinarian' => false];

    protected array $labels = ['name' => 'nombre', 'requires_veterinarian' => 'requiere veterinario', 'cost' => 'costo', 'treatment_type_id' => 'tipo de tratamiento'];

    protected function options(): array
    {
        return ['treatmentTypes' => TreatmentType::query()->orderBy('name')->get()];
    }
}; ?>

<x-crud.page title="Tratamientos" singular="tratamiento" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Tipo</x-crud.th>
        <x-crud.th class="text-right">Costo</x-crud.th>
        <x-crud.th>Veterinario</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->treatmentType?->name }}</x-crud.td>
            <x-crud.td class="text-right tabular-nums">Q {{ number_format((float) $record->cost, 2) }}</x-crud.td>
            <x-crud.td>
                <x-crud.badge :color="$record->requires_veterinarian ? 'yellow' : 'gray'">{{ $record->requires_veterinarian ? 'Sí' : 'No' }}</x-crud.badge>
            </x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.select name="treatment_type_id" label="Tipo de tratamiento" :options="$treatmentTypes" />
        <x-crud.input name="cost" label="Costo (Q)" type="number" step="0.01" min="0" />
        <x-crud.checkbox name="requires_veterinarian" label="Requiere veterinario" />
    </x-slot:form>
</x-crud.page>

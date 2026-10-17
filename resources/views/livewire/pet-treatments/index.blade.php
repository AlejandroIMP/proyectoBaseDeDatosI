<?php

use App\Http\Controllers\Api\PetTreatmentController;
use App\Livewire\CrudComponent;
use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\Treatment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Tratamientos aplicados')] class extends CrudComponent
{
    protected string $model = PetTreatment::class;

    protected string $controller = PetTreatmentController::class;

    protected array $with = ['pet.species', 'pet.breed', 'pet.color', 'treatment'];

    protected array $searchable = ['instructions', 'pet.name', 'treatment.name'];

    protected array $labels = ['pet_id' => 'mascota', 'treatment_id' => 'tratamiento', 'instructions' => 'indicaciones', 'treated_at' => 'fecha'];

    protected function options(): array
    {
        return [
            'pets' => Pet::query()->with(['species', 'breed', 'color'])->orderBy('name')->get(),
            'treatments' => Treatment::query()->orderBy('name')->get(),
        ];
    }
}; ?>

<x-crud.page title="Tratamientos aplicados" singular="tratamiento aplicado" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Fecha</x-crud.th>
        <x-crud.th>Mascota</x-crud.th>
        <x-crud.th>Tratamiento</x-crud.th>
        <x-crud.th>Indicaciones</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->treated_at?->format('Y-m-d') }}</x-crud.td>
            <x-crud.td><x-crud.pet-name :pet="$record->pet" /></x-crud.td>
            <x-crud.td>{{ $record->treatment?->name }}</x-crud.td>
            <x-crud.td class="max-w-xs truncate" :title="$record->instructions">{{ $record->instructions }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.pet-select :pets="$pets" />
        <x-crud.select name="treatment_id" label="Tratamiento" :options="$treatments" />
        <x-crud.input name="treated_at" label="Fecha" type="date" />
        <x-crud.textarea name="instructions" label="Indicaciones" />
    </x-slot:form>
</x-crud.page>

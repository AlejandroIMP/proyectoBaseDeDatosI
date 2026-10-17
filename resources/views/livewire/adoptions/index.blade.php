<?php

use App\Http\Controllers\Api\AdoptionController;
use App\Livewire\CrudComponent;
use App\Models\Adoption;
use App\Models\AdoptionStatus;
use App\Models\Person;
use App\Models\Pet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Adopciones')] class extends CrudComponent
{
    protected string $model = Adoption::class;

    protected string $controller = AdoptionController::class;

    protected array $with = ['pet.species', 'pet.breed', 'pet.color', 'person', 'adoptionStatus', 'user'];

    protected array $searchable = ['notes', 'pet.name', 'person.name'];

    protected array $labels = [
        'pet_id' => 'mascota',
        'person_id' => 'adoptante',
        'requested_at' => 'fecha de solicitud',
        'approved_at' => 'fecha de aprobación',
        'delivered_at' => 'fecha de entrega',
        'notes' => 'observaciones',
        'document' => 'documento',
        'adoption_status_id' => 'estado',
    ];

    protected function options(): array
    {
        return [
            'pets' => Pet::query()->with(['species', 'breed', 'color'])->orderBy('name')->get(),
            'people' => Person::query()->orderBy('name')->get(),
            'adoptionStatuses' => AdoptionStatus::query()->orderBy('name')->get(),
        ];
    }
}; ?>

<x-crud.page title="Adopciones" singular="adopción" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Mascota</x-crud.th>
        <x-crud.th>Adoptante</x-crud.th>
        <x-crud.th>Solicitud</x-crud.th>
        <x-crud.th>Aprobación</x-crud.th>
        <x-crud.th>Entrega</x-crud.th>
        <x-crud.th>Estado</x-crud.th>
        <x-crud.th>Gestionó</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td><x-crud.pet-name :pet="$record->pet" /></x-crud.td>
            <x-crud.td>{{ $record->person?->name }}</x-crud.td>
            <x-crud.td>{{ $record->requested_at?->format('Y-m-d') }}</x-crud.td>
            <x-crud.td>{{ $record->approved_at?->format('Y-m-d') ?? '—' }}</x-crud.td>
            <x-crud.td>{{ $record->delivered_at?->format('Y-m-d') ?? '—' }}</x-crud.td>
            <x-crud.td><x-crud.badge color="blue">{{ $record->adoptionStatus?->name }}</x-crud.badge></x-crud.td>
            <x-crud.td>{{ $record->user?->name }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.pet-select :pets="$pets" />
        <x-crud.select name="person_id" label="Adoptante" :options="$people" />
        <x-crud.input name="requested_at" label="Fecha de solicitud" type="date" />
        <x-crud.select name="adoption_status_id" label="Estado" :options="$adoptionStatuses" />
        <x-crud.input name="approved_at" label="Fecha de aprobación" type="date" />
        <x-crud.input name="delivered_at" label="Fecha de entrega" type="date" />
        <x-crud.input name="document" label="Documento" span />
        <x-crud.textarea name="notes" label="Observaciones" />
    </x-slot:form>
</x-crud.page>

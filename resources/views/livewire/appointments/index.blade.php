<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Livewire\CrudComponent;
use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Person;
use App\Models\Pet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Citas')] class extends CrudComponent
{
    protected string $model = Appointment::class;

    protected string $controller = AppointmentController::class;

    protected array $with = ['pet', 'appointmentType', 'person', 'user'];

    protected array $searchable = ['notes', 'pet.name', 'person.name'];

    protected array $defaults = ['status' => 'pending'];

    protected array $labels = [
        'pet_id' => 'mascota',
        'appointment_date' => 'fecha',
        'notes' => 'observaciones',
        'status' => 'estado',
        'appointment_type_id' => 'tipo de cita',
        'person_id' => 'persona',
    ];

    public const STATUSES = ['pending' => 'Pendiente', 'completed' => 'Completada', 'missed' => 'No asistió'];

    protected function options(): array
    {
        return [
            'pets' => Pet::query()->orderBy('name')->get(),
            'people' => Person::query()->orderBy('name')->get(),
            'appointmentTypes' => AppointmentType::query()->orderBy('name')->get(),
        ];
    }
}; ?>

<x-crud.page title="Citas" singular="cita" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Fecha</x-crud.th>
        <x-crud.th>Tipo</x-crud.th>
        <x-crud.th>Mascota</x-crud.th>
        <x-crud.th>Persona</x-crud.th>
        <x-crud.th>Estado</x-crud.th>
        <x-crud.th>Atiende</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->appointment_date?->format('Y-m-d') }}</x-crud.td>
            <x-crud.td>{{ $record->appointmentType?->name }}</x-crud.td>
            <x-crud.td>{{ $record->pet?->name }}</x-crud.td>
            <x-crud.td>{{ $record->person?->name }}</x-crud.td>
            <x-crud.td>
                <x-crud.badge :color="match ($record->status) { 'completed' => 'green', 'missed' => 'red', default => 'yellow' }">{{ $this::STATUSES[$record->status] ?? $record->status }}</x-crud.badge>
            </x-crud.td>
            <x-crud.td>{{ $record->user?->name }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="appointment_date" label="Fecha" type="date" />
        <x-crud.select name="appointment_type_id" label="Tipo de cita" :options="$appointmentTypes" />
        <x-crud.select name="pet_id" label="Mascota" :options="$pets" />
        <x-crud.select name="person_id" label="Persona" :options="$people" />
        <x-crud.select name="status" label="Estado" :options="$this::STATUSES" />
        <x-crud.textarea name="notes" label="Observaciones" />
    </x-slot:form>
</x-crud.page>

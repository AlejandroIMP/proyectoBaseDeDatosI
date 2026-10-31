<?php

use App\Http\Controllers\Api\AdoptionController;
use App\Livewire\CrudComponent;
use App\Models\Adoption;
use App\Models\AdoptionStatus;
use App\Models\Person;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
        'notes' => 'observaciones',
        'document' => 'documento',
    ];

    // El estado y sus fechas solo cambian desde el flujo de solicitudes (adoption-requests).
    protected function rules(Request $request, ?Model $record): array
    {
        return Arr::except(parent::rules($request, $record), ['adoption_status_id', 'approved_at', 'delivered_at']);
    }

    protected function persistableData(array $validated, ?Model $record): array
    {
        $data = parent::persistableData($validated, $record);

        return $record ? $data : [...$data, 'adoption_status_id' => AdoptionStatus::query()->where('name', AdoptionStatus::PENDING)->value('id')];
    }

    protected function options(): array
    {
        return [
            // Solo mascotas elegibles, mas la que ya tiene la adopcion que se esta editando.
            'pets' => Pet::query()
                ->with(['species', 'breed', 'color'])
                ->where(fn (Builder $query) => $query->available()->orWhere('id', $this->form['pet_id'] ?? null))
                ->orderBy('name')
                ->get(),
            'people' => Person::query()->orderBy('name')->get(),
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
        <x-crud.input name="document" label="Documento" span />
        <x-crud.textarea name="notes" label="Observaciones" />
        <p class="text-xs text-gray-500 sm:col-span-2">El estado se gestiona desde <a href="{{ route('crud.adoption-requests') }}" wire:navigate class="font-medium text-indigo-600 hover:text-indigo-500">Solicitudes</a>; las nuevas inician como Pendiente.</p>
    </x-slot:form>
</x-crud.page>

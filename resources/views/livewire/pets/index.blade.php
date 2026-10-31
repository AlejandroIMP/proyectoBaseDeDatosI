<?php

use App\Http\Controllers\Api\PetController;
use App\Livewire\CrudComponent;
use App\Models\Breed;
use App\Models\Color;
use App\Models\Pet;
use App\Models\PetStatus;
use App\Models\Shelter;
use App\Models\Species;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Mascotas')] class extends CrudComponent
{
    protected string $model = Pet::class;

    protected string $controller = PetController::class;

    protected array $with = ['species', 'breed', 'color', 'petStatus', 'shelter'];

    protected array $searchable = ['name', 'species.name', 'breed.name', 'shelter.name'];

    protected array $defaults = ['status' => 'active'];

    protected array $labels = [
        'name' => 'nombre',
        'species_id' => 'especie',
        'breed_id' => 'raza',
        'sex' => 'sexo',
        'birth_date' => 'fecha de nacimiento',
        'estimated_age' => 'edad estimada',
        'color_id' => 'color',
        'weight_kg' => 'peso',
        'notes' => 'observaciones',
        'pet_status_id' => 'estado de salud',
        'registered_at' => 'fecha de registro',
        'status' => 'estado',
        'shelter_id' => 'refugio',
    ];

    // La raza depende de la especie seleccionada.
    public function updatedFormSpeciesId(): void
    {
        $this->form['breed_id'] = null;
    }

    protected function options(): array
    {
        return [
            'speciesOptions' => Species::query()->orderBy('name')->get(),
            'breeds' => Breed::query()->where('species_id', $this->form['species_id'] ?? null)->orderBy('name')->get(),
            'colors' => Color::query()->orderBy('name')->get(),
            // Reservado y Adoptado los asigna el flujo de solicitudes; la mascota editada conserva el suyo.
            'petStatuses' => PetStatus::query()
                ->whereNotIn('name', PetStatus::WORKFLOW)
                ->orWhere('id', $this->form['pet_status_id'] ?? null)
                ->orderBy('name')
                ->get(),
            'shelters' => Shelter::query()->orderBy('name')->get(),
        ];
    }
}; ?>

<x-crud.page title="Mascotas" singular="mascota" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Especie / Raza</x-crud.th>
        <x-crud.th>Sexo</x-crud.th>
        <x-crud.th>Nacimiento</x-crud.th>
        <x-crud.th>Color</x-crud.th>
        <x-crud.th>Salud</x-crud.th>
        <x-crud.th>Refugio</x-crud.th>
        <x-crud.th>Estado</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td class="font-medium text-gray-900">{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->species?->name }} · {{ $record->breed?->name }}</x-crud.td>
            <x-crud.td>{{ $record->sex === 'M' ? 'Macho' : 'Hembra' }}</x-crud.td>
            <x-crud.td>{{ $record->birth_date?->format('Y-m-d') }}</x-crud.td>
            <x-crud.td>{{ $record->color?->name }}</x-crud.td>
            <x-crud.td>{{ $record->petStatus?->name }}</x-crud.td>
            <x-crud.td>{{ $record->shelter?->name }}</x-crud.td>
            <x-crud.td>
                <x-crud.badge :color="$record->status === 'active' ? 'green' : 'gray'">{{ $record->status === 'active' ? 'Activo' : 'Inactivo' }}</x-crud.badge>
            </x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.select name="sex" label="Sexo" :options="['M' => 'Macho', 'F' => 'Hembra']" />
        <x-crud.select name="species_id" label="Especie" :options="$speciesOptions" live />
        <x-crud.select name="breed_id" label="Raza" :options="$breeds" :placeholder="empty($form['species_id']) ? 'Seleccione una especie primero' : 'Seleccione...'" />
        <x-crud.input name="birth_date" label="Fecha de nacimiento" type="date" />
        <x-crud.input name="estimated_age" label="Edad estimada (años)" type="number" min="0" />
        <x-crud.select name="color_id" label="Color" :options="$colors" />
        <x-crud.input name="weight_kg" label="Peso (kg)" type="number" step="0.01" min="0" />
        <x-crud.select name="pet_status_id" label="Estado de salud" :options="$petStatuses" />
        <x-crud.select name="shelter_id" label="Refugio" :options="$shelters" />
        <x-crud.input name="registered_at" label="Fecha de registro" type="date" />
        <x-crud.select name="status" label="Estado" :options="['active' => 'Activo', 'inactive' => 'Inactivo']" />
        <x-crud.textarea name="notes" label="Observaciones" />
    </x-slot:form>
</x-crud.page>

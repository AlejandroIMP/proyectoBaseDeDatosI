<?php

use App\Http\Controllers\Api\ShelterController;
use App\Livewire\CrudComponent;
use App\Models\Shelter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Refugios')] class extends CrudComponent
{
    protected string $model = Shelter::class;

    protected string $controller = ShelterController::class;

    protected array $searchable = ['name', 'address', 'email'];

    protected array $labels = ['name' => 'nombre', 'address' => 'dirección', 'phone' => 'teléfono', 'email' => 'correo'];
}; ?>

<x-crud.page title="Refugios" singular="refugio" :records="$records">
    <x-slot:head>
        <x-crud.th>ID</x-crud.th>
        <x-crud.th>Nombre</x-crud.th>
        <x-crud.th>Dirección</x-crud.th>
        <x-crud.th>Teléfono</x-crud.th>
        <x-crud.th>Correo</x-crud.th>
    </x-slot:head>

    @foreach ($records as $record)
        <tr wire:key="{{ $record->id }}">
            <x-crud.td>{{ $record->id }}</x-crud.td>
            <x-crud.td>{{ $record->name }}</x-crud.td>
            <x-crud.td>{{ $record->address }}</x-crud.td>
            <x-crud.td>{{ $record->phone }}</x-crud.td>
            <x-crud.td>{{ $record->email }}</x-crud.td>
            <x-crud.actions :id="$record->id" />
        </tr>
    @endforeach

    <x-slot:form>
        <x-crud.input name="name" label="Nombre" />
        <x-crud.input name="phone" label="Teléfono" />
        <x-crud.input name="address" label="Dirección" span />
        <x-crud.input name="email" label="Correo" type="email" span />
    </x-slot:form>
</x-crud.page>

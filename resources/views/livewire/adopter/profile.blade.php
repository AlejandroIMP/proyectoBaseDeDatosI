<?php

use App\Models\Occupation;
use App\Models\Person;
use App\Models\Residence;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] #[Title('Mis datos de adoptante')] class extends Component
{
    /** @var array<string, mixed> */
    public array $form = [
        'document_type' => 'DPI',
        'document_number' => '',
        'estado_civil' => '',
        'cantidad_hijos' => 0,
        'occupation_id' => '',
        'residence_id' => '',
        'income' => '',
        'current_pets_count' => 0,
    ];

    public function mount(): void
    {
        $person = auth()->user()->person;

        if ($person !== null) {
            $this->form = $person->only(array_keys($this->form));
        }
    }

    public function save(): void
    {
        $user = auth()->user();
        $person = $user->person;

        // Mismas reglas que PersonController; nombre y correo se toman de la cuenta.
        $validated = $this->validate([
            'form.document_type' => ['required', 'in:DPI,PASSPORT'],
            'form.document_number' => ['required', 'string', 'max:25', Rule::unique('people', 'document_number')->ignore($person)],
            'form.estado_civil' => ['required', 'string', 'max:50'],
            'form.cantidad_hijos' => ['required', 'integer', 'min:0', 'max:99'],
            'form.occupation_id' => ['required', 'integer', 'exists:occupations,id'],
            'form.residence_id' => ['required', 'integer', 'exists:residences,id'],
            'form.income' => ['required', 'integer', 'min:0'],
            'form.current_pets_count' => ['required', 'integer', 'min:0'],
        ], attributes: [
            'form.document_type' => 'tipo de documento',
            'form.document_number' => 'número de documento',
            'form.estado_civil' => 'estado civil',
            'form.cantidad_hijos' => 'cantidad de hijos',
            'form.occupation_id' => 'ocupación',
            'form.residence_id' => 'vivienda',
            'form.income' => 'ingresos',
            'form.current_pets_count' => 'mascotas actuales',
        ])['form'];

        Person::updateOrCreate(
            ['user_id' => $user->id],
            [...$validated, 'name' => mb_substr($user->name, 0, 50), 'email' => $user->email],
        );

        session()->flash('status', 'Tus datos se guardaron correctamente.');

        $this->redirectRoute('adopter.profile', navigate: true);
    }

    public function with(): array
    {
        return [
            'occupations' => Occupation::query()->orderBy('name')->get(),
            'residences' => Residence::query()->orderBy('name')->get(),
            'hasProfile' => auth()->user()->person()->exists(),
        ];
    }
}; ?>

<div>
    <x-adopter.header title="Mis datos de adoptante" subtitle="El refugio usa esta información para evaluar tus solicitudes de adopción." />

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @elseif (! $hasProfile)
                <div class="rounded-md bg-indigo-50 p-4 text-sm text-indigo-800">Completa tus datos para poder enviar solicitudes de adopción.</div>
            @endif

            <form wire:submit="save" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label value="Nombre" />
                        <p class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-gray-700">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <x-input-label value="Correo" />
                        <p class="mt-1 truncate rounded-md bg-gray-50 px-3 py-2 text-gray-700">{{ auth()->user()->email }}</p>
                    </div>

                    <x-crud.select name="document_type" label="Tipo de documento" :options="['DPI' => 'DPI', 'PASSPORT' => 'Pasaporte']" />
                    <x-crud.input name="document_number" label="Número de documento" />
                    <x-crud.select name="estado_civil" label="Estado civil" :options="array_combine(Person::MARITAL_STATUSES, Person::MARITAL_STATUSES)" />
                    <x-crud.input name="cantidad_hijos" label="Cantidad de hijos" type="number" min="0" />
                    <x-crud.select name="occupation_id" label="Ocupación" :options="$occupations" />
                    <x-crud.select name="residence_id" label="Vivienda" :options="$residences" />
                    <x-crud.input name="income" label="Ingresos mensuales (Q)" type="number" min="0" />
                    <x-crud.input name="current_pets_count" label="Mascotas que tienes actualmente" type="number" min="0" />
                </div>

                <p class="mt-4 text-xs text-gray-500">Tu nombre y correo se toman de tu cuenta; puedes cambiarlos en tu perfil.</p>

                <div class="mt-6 flex justify-end">
                    <x-primary-button wire:loading.attr="disabled">Guardar datos</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>

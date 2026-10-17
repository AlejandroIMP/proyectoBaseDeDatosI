<?php

use App\Models\Adoption;
use App\Models\AdoptionStatus;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Pet $pet;

    public string $notes = '';

    public function mount(Pet $pet): void
    {
        // Solo se muestran mascotas disponibles, o las que el adoptante ya solicito (para ver el estado).
        abort_unless($this->isAvailable($pet) || $this->openRequest($pet) !== null, 404);

        $this->pet = $pet->load(['species', 'breed', 'color', 'shelter', 'petStatus']);
    }

    public function requestAdoption(): void
    {
        $person = auth()->user()->person;

        if ($person === null) {
            $this->redirectRoute('adopter.profile', navigate: true);

            return;
        }

        $this->validate(['notes' => ['nullable', 'string', 'max:1000']], attributes: ['notes' => 'comentarios']);

        if (! $this->isAvailable($this->pet)) {
            $this->addError('notes', 'Esta mascota ya no está disponible para adopción.');

            return;
        }

        if ($this->openRequest($this->pet) !== null) {
            $this->addError('notes', 'Ya tienes una solicitud en curso para esta mascota.');

            return;
        }

        Adoption::create([
            'pet_id' => $this->pet->id,
            'person_id' => $person->id,
            'requested_at' => today(),
            'notes' => $this->notes !== '' ? $this->notes : null,
            'adoption_status_id' => AdoptionStatus::query()->where('name', AdoptionStatus::PENDING)->value('id'),
            'user_id' => auth()->id(),
        ]);

        session()->flash('status', "Tu solicitud para adoptar a {$this->pet->name} fue enviada. El refugio la revisará pronto.");

        $this->redirectRoute('adopter.requests', navigate: true);
    }

    public function rendering(View $view): void
    {
        $view->title($this->pet->name);
    }

    public function with(): array
    {
        return [
            'openRequest' => $this->openRequest($this->pet),
            'hasProfile' => auth()->user()->person()->exists(),
        ];
    }

    private function isAvailable(Pet $pet): bool
    {
        return Pet::query()->available()->whereKey($pet->getKey())->exists();
    }

    private function openRequest(Pet $pet): ?Adoption
    {
        return Adoption::query()
            ->with('adoptionStatus')
            ->where('pet_id', $pet->getKey())
            ->whereHas('person', fn (Builder $query) => $query->where('user_id', auth()->id()))
            ->whereHas('adoptionStatus', fn (Builder $query) => $query->whereIn('name', AdoptionStatus::OPEN))
            ->first();
    }
}; ?>

<div>
    <x-adopter.header :title="$pet->name" :subtitle="$pet->species?->name.' · '.$pet->breed?->name">
        <x-slot:actions>
            <a href="{{ route('adopter.pets') }}" wire:navigate class="text-sm font-medium text-indigo-600 hover:text-indigo-500">&larr; Volver al catálogo</a>
        </x-slot:actions>
    </x-adopter.header>

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 lg:col-span-2">
                <div class="flex h-48 items-center justify-center bg-gradient-to-br from-indigo-50 to-indigo-100 text-indigo-300">
                    <svg class="h-20 w-20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="5" cy="9" r="2.2"/><circle cx="9.5" cy="5" r="2.2"/><circle cx="14.5" cy="5" r="2.2"/><circle cx="19" cy="9" r="2.2"/><path d="M12 10.5c-3.2 0-6.5 4.6-6.5 7.4 0 1.7 1.3 2.6 3 2.6 1.3 0 2.2-.7 3.5-.7s2.2.7 3.5.7c1.7 0 3-.9 3-2.6 0-2.8-3.3-7.4-6.5-7.4z"/></svg>
                </div>

                <dl class="grid grid-cols-2 gap-x-6 gap-y-5 p-6 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm text-gray-500">Sexo</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $pet->sex === 'M' ? 'Macho' : 'Hembra' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Edad</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $pet->ageLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Color</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $pet->color?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Peso</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $pet->weight_kg ? $pet->weight_kg.' kg' : 'Sin registrar' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Refugio</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $pet->shelter?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">En el refugio desde</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $pet->registered_at?->format('d/m/Y') }}</dd>
                    </div>
                </dl>

                @if ($pet->notes)
                    <div class="border-t border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-900">Sobre {{ $pet->name }}</h3>
                        <p class="mt-2 text-sm whitespace-pre-line text-gray-600">{{ $pet->notes }}</p>
                    </div>
                @endif
            </section>

            <aside class="h-fit rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                @if ($openRequest)
                    <h3 class="text-lg font-semibold text-gray-900">Ya solicitaste a {{ $pet->name }}</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Tu solicitud del {{ $openRequest->requested_at->format('d/m/Y') }} está
                        <x-adopter.status-badge :status="$openRequest->adoptionStatus?->name" />
                    </p>
                    <a href="{{ route('adopter.requests') }}" wire:navigate class="mt-4 inline-flex text-sm font-medium text-indigo-600 hover:text-indigo-500">Ver mis solicitudes &rarr;</a>
                @elseif (! $hasProfile)
                    <h3 class="text-lg font-semibold text-gray-900">¿Quieres adoptar a {{ $pet->name }}?</h3>
                    <p class="mt-2 text-sm text-gray-600">Antes de enviar una solicitud necesitamos conocerte: completa tus datos de adoptante.</p>
                    <a href="{{ route('adopter.profile') }}" wire:navigate class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Completar mis datos</a>
                @else
                    <h3 class="text-lg font-semibold text-gray-900">Solicitar adopción</h3>
                    <p class="mt-2 text-sm text-gray-600">El refugio revisará tu solicitud y te contactará para continuar el proceso.</p>

                    <form wire:submit="requestAdoption" class="mt-4 space-y-4">
                        <div>
                            <x-input-label for="notes" value="¿Por qué quieres adoptarlo? (opcional)" />
                            <textarea id="notes" wire:model="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <x-primary-button class="w-full justify-center" wire:loading.attr="disabled">Enviar solicitud</x-primary-button>
                    </form>
                @endif
            </aside>
        </div>
    </div>
</div>

<?php

use App\Models\Adoption;
use App\Models\AdoptionStatus;
use App\Models\Person;
use App\Models\Pet;
use App\Models\PetStatus;
use Database\Seeders\DatabaseSeeder;

test('a fresh database can be seeded', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Person::query()->whereNull('user_id')->count())->toBe(40)
        ->and(Pet::query()->count())->toBe(50);

    foreach (Pet::query()->with('petStatus')->get() as $pet) {
        $held = $pet->adoptions()->holding()->with('adoptionStatus')->first();

        expect($pet->histories()->orderByDesc('id')->value('pet_status_id'))->toBe($pet->pet_status_id)
            ->and($pet->petStatus->name)->toBe(match ($held?->adoptionStatus?->name) {
                AdoptionStatus::DELIVERED => PetStatus::ADOPTED,
                AdoptionStatus::APPROVED => PetStatus::RESERVED,
                default => $pet->petStatus->name,
            });

        if ($held === null) {
            expect($pet->petStatus->name)->not->toBeIn(PetStatus::WORKFLOW);
        }
    }

    expect(Adoption::query()->holding()->count())->toBe(Adoption::query()->whereRelation('adoptionStatus', fn ($query) => $query->whereIn('name', AdoptionStatus::HOLDING))->count());
});

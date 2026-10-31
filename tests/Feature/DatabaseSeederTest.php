<?php

use App\Models\Person;
use App\Models\Pet;
use Database\Seeders\DatabaseSeeder;

test('a fresh database can be seeded', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Person::query()->whereNull('user_id')->count())->toBe(40)
        ->and(Pet::query()->count())->toBe(50);
});

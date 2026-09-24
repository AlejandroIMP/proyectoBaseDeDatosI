<?php

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Breed;
use App\Models\Color;
use App\Models\Occupation;
use App\Models\Permission;
use App\Models\Person;
use App\Models\Pet;
use App\Models\PetStatus;
use App\Models\Residence;
use App\Models\Role;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

const API_RESOURCES = [
    'users', 'species', 'colors', 'pet-statuses', 'shelters', 'residences', 'adoption-statuses',
    'roles', 'permissions', 'treatment-types', 'breeds', 'occupations', 'people', 'donors',
    'treatments', 'pets', 'pet-histories', 'pet-treatments', 'appointment-types', 'appointments',
    'donations', 'rescues', 'adoptions', 'adoption-follow-ups', 'contracts',
];

function petPayload(array $overrides = []): array
{
    $species = Species::create(['name' => 'Perro', 'description' => 'Canino']);
    $breed = Breed::create(['species_id' => $species->id, 'name' => 'Labrador', 'description' => 'Grande']);
    $color = Color::create(['name' => 'Negro']);
    $status = PetStatus::create(['name' => 'Disponible']);
    $shelter = Shelter::create(['name' => 'Refugio', 'address' => 'Zona 1']);

    return [
        'name' => 'Firulais',
        'species_id' => $species->id,
        'breed_id' => $breed->id,
        'sex' => 'M',
        'birth_date' => '2022-01-15',
        'color_id' => $color->id,
        'pet_status_id' => $status->id,
        'registered_at' => '2024-03-01',
        'shelter_id' => $shelter->id,
        ...$overrides,
    ];
}

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

test('every resource requires authentication', function (string $uri) {
    app('auth')->forgetGuards();

    $this->getJson("/api/$uri")->assertUnauthorized();
})->with(API_RESOURCES);

test('every resource lists paginated records', function (string $uri) {
    $this->getJson("/api/$uri")
        ->assertOk()
        ->assertJsonStructure(['data', 'current_page', 'per_page', 'total']);
})->with(API_RESOURCES);

test('index caps per_page at 100', function () {
    $this->getJson('/api/species?per_page=5000')->assertOk()->assertJsonPath('per_page', 100);
});

test('species supports the full crud cycle', function () {
    $id = $this->postJson('/api/species', ['name' => 'Gato', 'description' => 'Felino'])
        ->assertCreated()
        ->assertJsonPath('name', 'Gato')
        ->json('id');

    $this->getJson("/api/species/$id")->assertOk()->assertJsonPath('description', 'Felino');

    $this->patchJson("/api/species/$id", ['name' => 'Gatito'])
        ->assertOk()
        ->assertJsonPath('name', 'Gatito')
        ->assertJsonPath('description', 'Felino');

    $this->deleteJson("/api/species/$id")->assertNoContent();
    $this->getJson("/api/species/$id")->assertNotFound();
});

test('store validates required fields', function () {
    $this->postJson('/api/species', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'description']);
});

test('a referenced record cannot be deleted', function () {
    $species = Species::create(['name' => 'Perro', 'description' => 'Canino']);
    Breed::create(['species_id' => $species->id, 'name' => 'Labrador', 'description' => 'Grande']);

    $this->deleteJson("/api/species/{$species->id}")->assertConflict();

    $this->assertModelExists($species);
});

test('unique names ignore the record being updated', function () {
    $role = Role::create(['name' => 'admin']);
    Role::create(['name' => 'vet']);

    $this->putJson("/api/roles/{$role->id}", ['name' => 'admin', 'description' => 'Todo'])->assertOk();
    $this->putJson("/api/roles/{$role->id}", ['name' => 'vet'])->assertUnprocessable()->assertJsonValidationErrors('name');
});

test('pets are created with their relations loaded', function () {
    $this->postJson('/api/pets', petPayload())
        ->assertCreated()
        ->assertJsonPath('name', 'Firulais')
        ->assertJsonPath('species.name', 'Perro')
        ->assertJsonPath('breed.name', 'Labrador')
        ->assertJsonPath('birth_date', '2022-01-15');
});

test('a pet breed must belong to the pet species', function () {
    $payload = petPayload();
    $otherSpecies = Species::create(['name' => 'Gato', 'description' => 'Felino']);

    $this->postJson('/api/pets', [...$payload, 'species_id' => $otherSpecies->id])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('breed_id');
});

test('updating a pet validates the breed against the stored species', function () {
    $pet = Pet::create(petPayload());
    $otherSpecies = Species::create(['name' => 'Gato', 'description' => 'Felino']);
    $otherBreed = Breed::create(['species_id' => $otherSpecies->id, 'name' => 'Siames', 'description' => 'Delgado']);

    $this->patchJson("/api/pets/{$pet->id}", ['breed_id' => $otherBreed->id])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('breed_id');
});

test('user_id defaults to the authenticated user and can be overridden', function () {
    $pet = Pet::create(petPayload());
    $status = PetStatus::first();
    $payload = ['pet_id' => $pet->id, 'pet_status_id' => $status->id, 'notes' => 'Rescatado', 'rescued_at' => '2024-03-02'];

    $this->postJson('/api/rescues', $payload)
        ->assertCreated()
        ->assertJsonPath('user_id', $this->user->id);

    $other = User::factory()->create();
    $this->postJson('/api/rescues', [...$payload, 'user_id' => $other->id])
        ->assertCreated()
        ->assertJsonPath('user_id', $other->id);
});

test('people are created with occupation and residence', function () {
    $occupation = Occupation::create(['name' => 'Docente', 'formalidad' => 'Formal', 'horarios' => 'Lunes a viernes']);
    $residence = Residence::create(['name' => 'Casa', 'animal_limit' => 3]);

    $this->postJson('/api/people', [
        'name' => 'Ana',
        'document_type' => 'DPI',
        'document_number' => '1234567890101',
        'email' => 'ana@example.com',
        'estado_civil' => 'Soltera',
        'cantidad_hijos' => 0,
        'occupation_id' => $occupation->id,
        'residence_id' => $residence->id,
        'income' => 5000,
    ])
        ->assertCreated()
        ->assertJsonPath('occupation.name', 'Docente')
        ->assertJsonPath('residence.animal_limit', 3);

    $this->postJson('/api/people', ['document_type' => 'CEDULA'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['document_type', 'name', 'occupation_id']);
});

test('appointments reference an appointment type, a person and a user', function () {
    $pet = Pet::create(petPayload());
    $type = AppointmentType::create(['name' => 'Vacunacion']);
    $person = Person::create([
        'name' => 'Ana', 'document_type' => 'DPI', 'document_number' => '1', 'email' => 'a@a.com',
        'estado_civil' => 'Soltera', 'cantidad_hijos' => 0, 'income' => 1,
        'occupation_id' => Occupation::create(['name' => 'X', 'formalidad' => 'Formal', 'horarios' => '8-5'])->id,
        'residence_id' => Residence::create(['animal_limit' => 1])->id,
    ]);

    $this->postJson('/api/appointments', [
        'pet_id' => $pet->id,
        'appointment_date' => '2026-10-01',
        'appointment_type_id' => $type->id,
        'person_id' => $person->id,
    ])
        ->assertCreated()
        ->assertJsonPath('appointment_type.name', 'Vacunacion')
        ->assertJsonPath('status', 'pending')
        ->assertJsonPath('user_id', $this->user->id);

    expect(Appointment::count())->toBe(1);
});

test('permissions can be synced to a role', function () {
    $role = Role::create(['name' => 'admin']);
    $permissions = collect(['pets.create', 'pets.delete'])->map(fn ($n) => Permission::create(['name' => $n]));

    $this->putJson("/api/roles/{$role->id}/permissions", ['permission_ids' => $permissions->pluck('id')->all()])
        ->assertOk()
        ->assertJsonCount(2, 'permissions');

    $this->putJson("/api/roles/{$role->id}/permissions", ['permission_ids' => []])
        ->assertOk()
        ->assertJsonCount(0, 'permissions');

    $this->putJson("/api/roles/{$role->id}/permissions", ['permission_ids' => [999]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('permission_ids.0');
});

test('roles can be synced to a user', function () {
    $role = Role::create(['name' => 'admin']);
    $target = User::factory()->create();

    $this->putJson("/api/users/{$target->id}/roles", ['role_ids' => [$role->id]])
        ->assertOk()
        ->assertJsonPath('roles.0.name', 'admin');
});

test('users are stored with a hashed password that is never exposed', function () {
    $response = $this->postJson('/api/users', [
        'name' => 'Nuevo', 'email' => 'nuevo@example.com', 'password' => 'password123',
    ])->assertCreated()->assertJsonMissingPath('password');

    $created = User::findOrFail($response->json('id'));
    expect($created->password)->not->toBe('password123')
        ->and(Hash::check('password123', $created->password))->toBeTrue();
});

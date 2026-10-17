<?php

use App\Models\Adoption;
use App\Models\AdoptionStatus;
use App\Models\Breed;
use App\Models\Color;
use App\Models\Occupation;
use App\Models\Person;
use App\Models\Pet;
use App\Models\PetStatus;
use App\Models\Residence;
use App\Models\Role;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use Database\Seeders\AdoptionStatusSeeder;
use Database\Seeders\PermissionRoleSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PetStatusSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class, PermissionRoleSeeder::class, PetStatusSeeder::class, AdoptionStatusSeeder::class]);

    $this->adopter = User::factory()->create();
    $this->adopter->roles()->attach(Role::where('name', Role::ADOPTER)->firstOrFail());
});

function makePet(string $name, string $status = PetStatus::AVAILABLE): Pet
{
    $species = Species::firstOrCreate(['name' => 'Perro'], ['description' => 'Canino']);

    return Pet::create([
        'name' => $name,
        'species_id' => $species->id,
        'breed_id' => Breed::firstOrCreate(['name' => 'Mestizo', 'species_id' => $species->id], ['description' => 'Mezcla'])->id,
        'sex' => 'M',
        'birth_date' => now()->subYears(2),
        'color_id' => Color::firstOrCreate(['name' => 'Café'])->id,
        'pet_status_id' => PetStatus::where('name', $status)->value('id'),
        'registered_at' => now()->subMonth(),
        'status' => 'active',
        'shelter_id' => Shelter::firstOrCreate(['name' => 'Refugio Central'], ['address' => 'Zona 1'])->id,
    ]);
}

function makeProfile(User $user): Person
{
    return Person::create([
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'document_type' => 'DPI',
        'document_number' => fake()->unique()->numerify('#############'),
        'estado_civil' => 'Soltero(a)',
        'cantidad_hijos' => 0,
        'occupation_id' => Occupation::create(['name' => 'Docente', 'formalidad' => 'Formal', 'horarios' => 'Diurno'])->id,
        'residence_id' => Residence::create(['name' => 'Casa', 'animal_limit' => 3])->id,
        'income' => 5000,
    ]);
}

test('the catalog only lists available pets', function () {
    makePet('Firulais');
    makePet('Toby', 'En tratamiento');

    $this->actingAs($this->adopter)->get('/adoptar')
        ->assertOk()
        ->assertSee('Firulais')
        ->assertDontSee('Toby');
});

test('unavailable pets cannot be opened', function () {
    $pet = makePet('Toby', 'Adoptado');

    $this->actingAs($this->adopter)->get("/adoptar/{$pet->id}")->assertNotFound();
});

test('the adopter profile creates a people record linked to the account', function () {
    $occupation = Occupation::create(['name' => 'Docente', 'formalidad' => 'Formal', 'horarios' => 'Diurno']);
    $residence = Residence::create(['name' => 'Casa', 'animal_limit' => 3]);

    $this->actingAs($this->adopter);

    Volt::test('adopter.profile')
        ->set('form.document_number', '1234567890101')
        ->set('form.estado_civil', 'Casado(a)')
        ->set('form.occupation_id', $occupation->id)
        ->set('form.residence_id', $residence->id)
        ->set('form.income', 6000)
        ->call('save')
        ->assertHasNoErrors();

    expect($this->adopter->person()->first())
        ->name->toBe($this->adopter->name)
        ->email->toBe($this->adopter->email)
        ->document_number->toBe('1234567890101');
});

test('requesting an adoption without a profile redirects to the profile form', function () {
    $pet = makePet('Firulais');

    $this->actingAs($this->adopter);

    Volt::test('adopter.pet', ['pet' => $pet])
        ->call('requestAdoption')
        ->assertRedirect(route('adopter.profile'));

    expect(Adoption::count())->toBe(0);
});

test('an adopter can request a pet once and cancel the request', function () {
    $pet = makePet('Firulais');
    $person = makeProfile($this->adopter);

    $this->actingAs($this->adopter);

    Volt::test('adopter.pet', ['pet' => $pet])
        ->set('notes', 'Tengo patio grande')
        ->call('requestAdoption')
        ->assertRedirect(route('adopter.requests'));

    $adoption = Adoption::sole();
    expect($adoption->person_id)->toBe($person->id)
        ->and($adoption->user_id)->toBe($this->adopter->id)
        ->and($adoption->adoptionStatus->name)->toBe(AdoptionStatus::PENDING);

    Volt::test('adopter.pet', ['pet' => $pet])
        ->call('requestAdoption')
        ->assertHasErrors('notes');
    expect(Adoption::count())->toBe(1);

    Volt::test('adopter.requests')
        ->assertSee('Firulais')
        ->call('cancel', $adoption->id);
    expect($adoption->fresh()->adoptionStatus->name)->toBe(AdoptionStatus::CANCELLED);
});

test('adopters only see and cancel their own requests', function () {
    $pet = makePet('Firulais');
    $other = User::factory()->create();
    $adoption = Adoption::create([
        'pet_id' => $pet->id,
        'person_id' => makeProfile($other)->id,
        'requested_at' => today(),
        'adoption_status_id' => AdoptionStatus::where('name', AdoptionStatus::PENDING)->value('id'),
        'user_id' => $other->id,
    ]);

    makeProfile($this->adopter);
    $this->actingAs($this->adopter);

    Volt::test('adopter.requests')->assertDontSee('Firulais');

    expect(fn () => Volt::test('adopter.requests')->call('cancel', $adoption->id))
        ->toThrow(ModelNotFoundException::class);
});

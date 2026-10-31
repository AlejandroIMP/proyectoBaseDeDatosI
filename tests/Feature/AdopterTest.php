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
use Laravel\Sanctum\Sanctum;
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

function makeRequest(User $adopter, Pet $pet, string $status = AdoptionStatus::PENDING): Adoption
{
    return Adoption::create([
        'pet_id' => $pet->id,
        'person_id' => ($adopter->person ?? makeProfile($adopter))->id,
        'requested_at' => today()->subDay(),
        'adoption_status_id' => AdoptionStatus::where('name', $status)->value('id'),
        'user_id' => $adopter->id,
    ]);
}

function makeAdopter(): User
{
    return tap(User::factory()->create(), fn (User $user) => $user->roles()->attach(Role::where('name', Role::ADOPTER)->firstOrFail()));
}

function makeAdmin(): User
{
    return tap(User::factory()->create(), fn (User $user) => $user->roles()->attach(Role::where('name', 'Administrador')->firstOrFail()));
}

test('approving reserves the pet, rejects the other requests and stamps the date', function () {
    $pet = makePet('Rocky');
    $chosen = makeRequest($this->adopter, $pet);
    $other = makeRequest(makeAdopter(), $pet);

    Volt::actingAs(makeAdmin())->test('adoption-requests.index')
        ->call('select', $chosen->id)
        ->call('move', AdoptionStatus::APPROVED);

    expect($pet->fresh()->petStatus->name)->toBe(PetStatus::RESERVED)
        ->and(Pet::available()->whereKey($pet->id)->exists())->toBeFalse()
        ->and($chosen->fresh()->approved_at->isToday())->toBeTrue()
        ->and($other->fresh()->adoptionStatus->name)->toBe(AdoptionStatus::REJECTED);
});

test('delivering marks the pet as adopted', function () {
    $pet = makePet('Rocky');
    $adoption = makeRequest($this->adopter, $pet);

    $adoption->moveTo(AdoptionStatus::APPROVED);

    Volt::actingAs(makeAdmin())->test('adoption-requests.index')
        ->call('select', $adoption->id)
        ->call('move', AdoptionStatus::DELIVERED);

    expect($pet->fresh()->petStatus->name)->toBe(PetStatus::ADOPTED)
        ->and($adoption->fresh()->delivered_at->isToday())->toBeTrue();
});

test('rejecting an approved adoption releases the pet', function () {
    $pet = makePet('Rocky');
    $adoption = makeRequest($this->adopter, $pet);
    $adoption->moveTo(AdoptionStatus::APPROVED);

    $adoption->moveTo(AdoptionStatus::REJECTED);

    expect($pet->fresh()->petStatus->name)->toBe(PetStatus::AVAILABLE);
});

test('deleting an approved adoption releases the pet', function () {
    $pet = makePet('Rocky');
    $adoption = makeRequest($this->adopter, $pet);
    $adoption->moveTo(AdoptionStatus::APPROVED);

    $adoption->delete();

    expect($pet->fresh()->petStatus->name)->toBe(PetStatus::AVAILABLE);
});

test('a pet in a non adoptable status cannot be approved', function () {
    $pet = makePet('Rocky');
    $adoption = makeRequest($this->adopter, $pet);
    $pet->update(['pet_status_id' => PetStatus::where('name', 'Fallecido')->value('id')]);

    Volt::actingAs(makeAdmin())->test('adoption-requests.index')
        ->call('select', $adoption->id)
        ->assertSee('no es elegible')
        ->call('move', AdoptionStatus::APPROVED)
        ->assertSee('no es elegible');

    expect($adoption->fresh()->adoptionStatus->name)->toBe(AdoptionStatus::PENDING)
        ->and($pet->fresh()->petStatus->name)->toBe('Fallecido');
});

test('an approved pet that dies cannot be delivered but the request can be rejected', function () {
    $pet = makePet('Rocky');
    $adoption = makeRequest($this->adopter, $pet);
    $adoption->moveTo(AdoptionStatus::APPROVED);
    $pet->update(['pet_status_id' => PetStatus::where('name', 'Fallecido')->value('id')]);

    expect(fn () => $adoption->moveTo(AdoptionStatus::DELIVERED))->toThrow(DomainException::class);

    $adoption->moveTo(AdoptionStatus::REJECTED);

    expect($adoption->fresh()->adoptionStatus->name)->toBe(AdoptionStatus::REJECTED)
        ->and($pet->fresh()->petStatus->name)->toBe('Fallecido');
});

test('eligibility comes from the status flag, not its name', function () {
    $status = PetStatus::create(['name' => 'Hogar temporal', 'is_adoptable' => true]);
    $pet = makePet('Rocky', 'Hogar temporal');
    $blocked = makePet('Toby');
    PetStatus::where('name', PetStatus::AVAILABLE)->update(['is_adoptable' => false]);

    expect(Pet::available()->pluck('id')->all())->toBe([$pet->id])
        ->and($status->fresh()->is_adoptable)->toBeTrue()
        ->and($blocked->isAdoptable())->toBeFalse();
});

test('the status catalog sets the eligibility flag', function () {
    Volt::actingAs(makeAdmin())->test('pet-statuses.index')
        ->call('create')
        ->set('form.name', 'Hogar temporal')
        ->set('form.is_adoptable', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(PetStatus::where('name', 'Hogar temporal')->value('is_adoptable'))->toBeTrue();
});

test('a pet cannot be held by two adoptions', function () {
    $pet = makePet('Rocky');
    makeRequest($this->adopter, $pet)->moveTo(AdoptionStatus::APPROVED);
    $other = makeRequest(makeAdopter(), $pet);

    Volt::actingAs(makeAdmin())->test('adoption-requests.index')
        ->call('select', $other->id)
        ->call('move', AdoptionStatus::APPROVED);

    expect($other->fresh()->adoptionStatus->name)->toBe(AdoptionStatus::PENDING);
});

test('the api applies the workflow checks to status changes', function () {
    Sanctum::actingAs(makeAdmin());
    $pet = makePet('Rocky');
    $adoption = makeRequest($this->adopter, $pet);
    $pet->update(['pet_status_id' => PetStatus::where('name', 'Fallecido')->value('id')]);

    $this->putJson("/api/adoptions/{$adoption->id}", ['adoption_status_id' => AdoptionStatus::where('name', AdoptionStatus::APPROVED)->value('id')])
        ->assertJsonValidationErrors('adoption_status_id');
});

test('the api refuses requests for non adoptable pets', function () {
    $pet = makePet('Rocky', 'Fallecido');

    $this->postJson('/api/public/adoptions', ['pet_id' => $pet->id, 'name' => 'Ana', 'email' => 'ana@example.com'])
        ->assertJsonValidationErrors('pet_id');
});

test('a workflow step not allowed from the current status is refused', function () {
    $adoption = makeRequest($this->adopter, makePet('Rocky'));

    Volt::actingAs(makeAdmin())->test('adoption-requests.index')
        ->call('select', $adoption->id)
        ->call('move', AdoptionStatus::DELIVERED);

    expect($adoption->fresh()->adoptionStatus->name)->toBe(AdoptionStatus::PENDING);
});

test('adoption dates must follow the process order', function () {
    Sanctum::actingAs(makeAdmin());
    $adoption = makeRequest($this->adopter, makePet('Rocky'));

    $this->putJson("/api/adoptions/{$adoption->id}", ['approved_at' => today()->subDays(5)->toDateString()])
        ->assertJsonValidationErrors('approved_at');
});

test('requests are filtered by status and the pending ones show first', function () {
    makeRequest($this->adopter, makePet('Rocky'));
    makeRequest(makeAdopter(), makePet('Luna'), AdoptionStatus::CANCELLED);

    Volt::actingAs(makeAdmin())->test('adoption-requests.index')
        ->assertSee('Rocky')
        ->assertDontSee('Luna')
        ->set('status', AdoptionStatus::CANCELLED)
        ->assertSee('Luna')
        ->assertDontSee('Rocky');
});

test('the workflow buttons render a valid livewire call', function () {
    $adoption = makeRequest($this->adopter, makePet('Rocky'));

    $html = Volt::actingAs(makeAdmin())->test('adoption-requests.index')
        ->call('select', $adoption->id)
        ->html();

    expect($html)->not->toContain('@js')
        ->and(html_entity_decode($html, ENT_QUOTES))->toContain("move('Aprobada')", "move('Rechazada')", "move('En revisión')");
});

test('the requests view needs the adoptions permission', function () {
    $this->actingAs($this->adopter)->get('/adoption-requests')->assertForbidden();
    $this->actingAs(makeAdmin())->get('/adoption-requests')->assertOk();
});

test('the adoptions form starts requests as pending and cannot set the status', function () {
    $pet = makePet('Rocky');
    $person = makeProfile($this->adopter);
    $approved = AdoptionStatus::where('name', AdoptionStatus::APPROVED)->value('id');

    Volt::actingAs(makeAdmin())->test('adoptions.index')
        ->call('create')
        ->set('form.pet_id', $pet->id)
        ->set('form.person_id', $person->id)
        ->set('form.requested_at', today()->toDateString())
        ->set('form.adoption_status_id', $approved)
        ->call('save')
        ->assertHasNoErrors();

    expect(Adoption::sole()->adoptionStatus->name)->toBe(AdoptionStatus::PENDING)
        ->and($pet->fresh()->petStatus->name)->toBe(PetStatus::AVAILABLE);
});

test('the adoptions form refuses pets that are not eligible', function () {
    $pet = makePet('Rocky', 'Fallecido');
    $person = makeProfile($this->adopter);

    Volt::actingAs(makeAdmin())->test('adoptions.index')
        ->call('create')
        ->set('form.pet_id', $pet->id)
        ->set('form.person_id', $person->id)
        ->set('form.requested_at', today()->toDateString())
        ->call('save')
        ->assertHasErrors('form.pet_id');

    expect(Adoption::count())->toBe(0);
});

test('an existing adoption can be edited after its pet stops being eligible', function () {
    $pet = makePet('Rocky');
    $adoption = makeRequest($this->adopter, $pet);
    $pet->update(['pet_status_id' => PetStatus::where('name', 'Fallecido')->value('id')]);

    Volt::actingAs(makeAdmin())->test('adoptions.index')
        ->call('edit', $adoption->id)
        ->set('form.notes', 'Actualizada')
        ->call('save')
        ->assertHasNoErrors();

    expect($adoption->fresh()->notes)->toBe('Actualizada');
});

test('follow ups can be searched by pet name', function () {
    Volt::actingAs(makeAdmin())->test('adoption-follow-ups.index')->set('search', 'Rocky')->assertOk();
});

test('an account with adoptions is kept and stays signed in', function () {
    makeRequest($this->adopter, makePet('Rocky'));

    Volt::actingAs($this->adopter)->test('profile.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasErrors('password')
        ->assertNoRedirect();

    expect(User::whereKey($this->adopter->id)->exists())->toBeTrue();
    $this->assertAuthenticated();
});

test('an account without adoptions is deleted and not recreated by the logout', function () {
    $this->adopter->update(['remember_token' => 'token']);
    $person = makeProfile($this->adopter);

    Volt::actingAs($this->adopter)->test('profile.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertRedirect('/');

    expect(User::whereKey($this->adopter->id)->exists())->toBeFalse()
        ->and($person->fresh()->user_id)->toBeNull();
    $this->assertGuest();
});

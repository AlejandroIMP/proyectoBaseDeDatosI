<?php

namespace App\Support;

use App\Models\Adoption;
use App\Models\AdoptionFollowUp;
use App\Models\AdoptionStatus;
use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Breed;
use App\Models\Color;
use App\Models\Contract;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Occupation;
use App\Models\Permission;
use App\Models\Person;
use App\Models\Pet;
use App\Models\PetHistory;
use App\Models\PetStatus;
use App\Models\PetTreatment;
use App\Models\Rescue;
use App\Models\Residence;
use App\Models\Role;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\Treatment;
use App\Models\TreatmentType;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Registry of the CRUD modules: drives the web routes, the navigation menu and the dashboard.
 *
 * Each slug maps to the Volt page resources/views/livewire/{slug}/index.blade.php, the route "crud.{slug}"
 * and the API resource "api/{slug}"; both routes require the module's permission.
 */
class Modules
{
    /**
     * @return array<string, array<string, array{label: string, model: class-string<Model>, permission: string}>>
     */
    public static function groups(): array
    {
        return [
            'Mascotas' => [
                'pets' => ['label' => 'Mascotas', 'model' => Pet::class, 'permission' => 'manage_pets'],
                'pet-histories' => ['label' => 'Historial', 'model' => PetHistory::class, 'permission' => 'manage_pets'],
                'pet-treatments' => ['label' => 'Tratamientos aplicados', 'model' => PetTreatment::class, 'permission' => 'manage_treatments'],
                'rescues' => ['label' => 'Rescates', 'model' => Rescue::class, 'permission' => 'manage_rescues'],
                'appointments' => ['label' => 'Citas', 'model' => Appointment::class, 'permission' => 'manage_appointments'],
            ],
            'Adopciones' => [
                'adoptions' => ['label' => 'Adopciones', 'model' => Adoption::class, 'permission' => 'manage_adoptions'],
                'adoption-follow-ups' => ['label' => 'Seguimientos', 'model' => AdoptionFollowUp::class, 'permission' => 'manage_adoptions'],
                'contracts' => ['label' => 'Contratos', 'model' => Contract::class, 'permission' => 'manage_adoptions'],
            ],
            'Personas' => [
                'people' => ['label' => 'Personas', 'model' => Person::class, 'permission' => 'manage_adoptions'],
                'donors' => ['label' => 'Donantes', 'model' => Donor::class, 'permission' => 'manage_donations'],
                'donations' => ['label' => 'Donaciones', 'model' => Donation::class, 'permission' => 'manage_donations'],
            ],
            'Catálogos' => [
                'species' => ['label' => 'Especies', 'model' => Species::class, 'permission' => 'manage_catalogs'],
                'breeds' => ['label' => 'Razas', 'model' => Breed::class, 'permission' => 'manage_catalogs'],
                'colors' => ['label' => 'Colores', 'model' => Color::class, 'permission' => 'manage_catalogs'],
                'pet-statuses' => ['label' => 'Estados de mascota', 'model' => PetStatus::class, 'permission' => 'manage_catalogs'],
                'shelters' => ['label' => 'Refugios', 'model' => Shelter::class, 'permission' => 'manage_catalogs'],
                'residences' => ['label' => 'Viviendas', 'model' => Residence::class, 'permission' => 'manage_catalogs'],
                'occupations' => ['label' => 'Ocupaciones', 'model' => Occupation::class, 'permission' => 'manage_catalogs'],
                'adoption-statuses' => ['label' => 'Estados de adopción', 'model' => AdoptionStatus::class, 'permission' => 'manage_catalogs'],
                'treatment-types' => ['label' => 'Tipos de tratamiento', 'model' => TreatmentType::class, 'permission' => 'manage_treatments'],
                'treatments' => ['label' => 'Tratamientos', 'model' => Treatment::class, 'permission' => 'manage_treatments'],
                'appointment-types' => ['label' => 'Tipos de cita', 'model' => AppointmentType::class, 'permission' => 'manage_catalogs'],
            ],
            'Seguridad' => [
                'users' => ['label' => 'Usuarios', 'model' => User::class, 'permission' => 'manage_users'],
                'roles' => ['label' => 'Roles', 'model' => Role::class, 'permission' => 'manage_users'],
                'permissions' => ['label' => 'Permisos', 'model' => Permission::class, 'permission' => 'manage_users'],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_keys(array_merge(...array_values(static::groups())));
    }

    /**
     * Groups filtered to the modules the user is allowed to manage; empty groups are dropped.
     *
     * @return array<string, array<string, array{label: string, model: class-string<Model>, permission: string}>>
     */
    public static function groupsFor(?Authenticatable $user): array
    {
        return array_filter(array_map(
            fn (array $modules) => array_filter($modules, fn (array $module) => $user?->can($module['permission']) ?? false),
            static::groups(),
        ));
    }

    public static function permission(string $slug): string
    {
        return array_merge(...array_values(static::groups()))[$slug]['permission'];
    }
}

<?php

use App\Http\Controllers\Api\AdoptionController;
use App\Http\Controllers\Api\AdoptionFollowUpController;
use App\Http\Controllers\Api\AdoptionStatusController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AppointmentTypeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BreedController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\DonorController;
use App\Http\Controllers\Api\OccupationController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\PetHistoryController;
use App\Http\Controllers\Api\PetStatusController;
use App\Http\Controllers\Api\PetTreatmentController;
use App\Http\Controllers\Api\RescueController;
use App\Http\Controllers\Api\ResidenceController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ShelterController;
use App\Http\Controllers\Api\SpeciesController;
use App\Http\Controllers\Api\TreatmentController;
use App\Http\Controllers\Api\TreatmentTypeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions']);
    Route::put('users/{user}/roles', [UserController::class, 'syncRoles']);

    Route::apiResources([
        'users' => UserController::class,
        'species' => SpeciesController::class,
        'colors' => ColorController::class,
        'pet-statuses' => PetStatusController::class,
        'shelters' => ShelterController::class,
        'residences' => ResidenceController::class,
        'adoption-statuses' => AdoptionStatusController::class,
        'roles' => RoleController::class,
        'permissions' => PermissionController::class,
        'treatment-types' => TreatmentTypeController::class,
        'breeds' => BreedController::class,
        'occupations' => OccupationController::class,
        'people' => PersonController::class,
        'donors' => DonorController::class,
        'treatments' => TreatmentController::class,
        'pets' => PetController::class,
        'pet-histories' => PetHistoryController::class,
        'pet-treatments' => PetTreatmentController::class,
        'appointment-types' => AppointmentTypeController::class,
        'appointments' => AppointmentController::class,
        'donations' => DonationController::class,
        'rescues' => RescueController::class,
        'adoptions' => AdoptionController::class,
        'adoption-follow-ups' => AdoptionFollowUpController::class,
        'contracts' => ContractController::class,
    ]);
});

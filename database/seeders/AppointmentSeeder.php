<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Person;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');
        $petIds = Pet::pluck('id');
        $appointmentTypeIds = AppointmentType::pluck('id');
        $personIds = Person::pluck('id');
        $userIds = User::pluck('id');
        $statuses = ['pending', 'completed', 'missed'];

        for ($i = 0; $i < 40; $i++) {
            $appointmentDate = $faker->dateTimeBetween('-6 months', '+2 months');
            $status = $appointmentDate > now() ? 'pending' : $faker->randomElement($statuses);

            Appointment::create([
                'pet_id' => $faker->randomElement($petIds),
                'appointment_date' => $appointmentDate->format('Y-m-d'),
                'notes' => $faker->optional(0.5)->sentence(8),
                'status' => $status,
                'appointment_type_id' => $faker->randomElement($appointmentTypeIds),
                'person_id' => $faker->randomElement($personIds),
                'user_id' => $faker->randomElement($userIds),
            ]);
        }
    }
}

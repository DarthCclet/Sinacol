<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Abogado;
use Faker\Generator as Faker;

$factory->define(Abogado::class, function (Faker $faker) {
    return [
        'nombre' => $faker->name,
        'primer_apellido' => $faker->lastName,
        'segundo_apellido' => $faker->lastName,
        'cedula_profesional' => (string)$faker->randomNumber(8),
        'numero_empleado' => $faker->randomNumber(5),
        'email' => $faker->email,
        'profedet' => $faker->boolean(),
    ];
});

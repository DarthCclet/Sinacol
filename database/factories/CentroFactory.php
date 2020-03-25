<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Centro;
use Faker\Generator as Faker;

$factory->define(Centro::class, function (Faker $faker) {
    return [
        'nombre' => $faker->randomElement(["Estado de Mexico","CDMX","Nayarit","Puebla"]),
        'abreviatura' => $faker->lexify('???'),
        'duracionAudiencia' => $faker->time
    ];
});

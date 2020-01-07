<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Nacionalidad;
use Faker\Generator as Faker;

$factory->define(Nacionalidad::class, function (Faker $faker) {
    return [
        'nombre' => $faker->randomElement(["Mexicana","Otro"])
    ];
});

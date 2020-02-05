<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Resolucion;
use Faker\Generator as Faker;

$factory->define(Resolucion::class, function (Faker $faker) {
    return [
        //
        'nombre' => $faker->sentence
    ];
});

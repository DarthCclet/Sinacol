<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Genero;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Genero::class, function (Faker $faker) {
    return [
        'nombre' => $faker->randomElement(["Masculino","Femenino","Sin especificar"])
    ];
});

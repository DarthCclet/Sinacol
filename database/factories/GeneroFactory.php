<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Genero;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Genero::class, function (Faker $faker) {
    $genero =  Genero::inRandomOrder()->first();
    return [
        'id' => $genero->id,
        'nombre' => $genero->nombre
    ];
});

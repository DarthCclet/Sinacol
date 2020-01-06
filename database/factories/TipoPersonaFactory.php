<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TipoPersona;
use Faker\Generator as Faker;

$factory->define(TipoPersona::class, function (Faker $faker) {
    return [
        'nombre' => $faker->randomElement(["Fisica","Moral"])
    ];
});

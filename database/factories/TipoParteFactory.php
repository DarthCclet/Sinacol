<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TipoParte;
use Faker\Generator as Faker;

$factory->define(TipoParte::class, function (Faker $faker) {
    return [
        'nombre' => $faker->randomElement(["Solicitante","Solicitado","Patron","Otro"])
    ];
});

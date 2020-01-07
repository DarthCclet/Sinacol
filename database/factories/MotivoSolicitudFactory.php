<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MotivoSolicitud;
use Faker\Generator as Faker;

$factory->define(MotivoSolicitud::class, function (Faker $faker) {
    return [
        'nombre' => $faker->randomElement(["Despido","Acoso","Otro"])
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\EstatusSolicitud;
use Faker\Generator as Faker;

$factory->define(EstatusSolicitud::class, function (Faker $faker) {
    return [
        'nombre' => $faker->randomElement(["Sin Ratificar","Ratificada","Terminada"])
    ];
});

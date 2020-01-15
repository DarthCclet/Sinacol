<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TipoPersona;
use Faker\Generator as Faker;

$factory->define(TipoPersona::class, function (Faker $faker) {
    $tipoPersona = TipoPersona::inRandomOrder()->first();
    return [
        'nombre' => $tipoPersona->nombre,
        'abreviatura' => $tipoPersona->abreviatura
    ];
});

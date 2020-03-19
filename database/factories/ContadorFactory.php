<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contador;
use App\TipoPersona;
use App\Centro;
use Faker\Generator as Faker;

$factory->define(Contador::class, function (Faker $faker) {
    $tipoContador = TipoPersona::inRandomOrder()->first();
    $centro = factory(Centro::class)->create();
    return [
        'centro_id' => $centro->id,
        'tipo_contador_id' => $tipoContador->id,
        'anio' => $faker->year,
        'contador' => $faker->randomNumber()
    ];
});

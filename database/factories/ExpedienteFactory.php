<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Expediente;
use Faker\Generator as Faker;

$factory->define(Expediente::class, function (Faker $faker) {
  // se llama el factory de solicitud para crear un registro y probar su relacion
  $solicitud = factory(\App\Solicitud::class)->create();
  // se crea el registro de Expediente usando los datos obtenidos anteriormente
    return [
        'folio' => strtoupper($faker->lexify("??????????????????")),
        'anio' => $faker->year,
        'consecutivo' => $faker->randomNumber(4),
        'solicitud_id' => $solicitud->id,
    ];

});

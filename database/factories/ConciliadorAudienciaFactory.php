<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ConciliadorAudiencia;
use Faker\Generator as Faker;

$factory->define(ConciliadorAudiencia::class, function (Faker $faker) {
    $conciliador = factory(\App\Conciliador::class)->create();
    $audiencia = factory(\App\Audiencia::class)->create();
    return [
        'conciliador_id' => $conciliador->id,
        'audiencia_id' => $audiencia->id,
        'solicitante' => $faker->boolean
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AgendaAudiencia;
use Faker\Generator as Faker;

$factory->define(AgendaAudiencia::class, function (Faker $faker) {
    $audiencia = factory(App\Audiencia::class)->create();
    $conciliador = factory(App\Conciliador::class)->create();
    $sala = factory(App\Sala::class)->create();
    return [
        'audiencia_id'  => $audiencia->id,
        'conciliador_id' => $conciliador->id,
        'sala_id' => $sala->id,
        'solicitante' => $faker->boolean
    ];
});

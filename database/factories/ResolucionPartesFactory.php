<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audiencia;
use App\Parte;
use App\Resolucion;
use App\ResolucionPartes;
use App\TerminacionBilateral;
use Faker\Generator as Faker;

$factory->define(ResolucionPartes::class, function (Faker $faker) {
    $terminacion_bilateral = TerminacionBilateral::inRandomOrder()->first();
    return [
        "audiencia_id" => function () {
            return factory(App\Audiencia::class)->create()->id;
        },
        "parte_solicitante_id" => function () {
            return factory(App\Parte::class)->state("solicitante")->create()->id;
        },
        "parte_solicitada_id" => function () {
            return factory(App\Parte::class)->state("solicitado")->create()->id;
        },
        "terminacion_bilateral_id" => $terminacion_bilateral
    ];
});


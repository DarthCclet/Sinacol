<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Resolucion;
use App\ResolucionParteExcepcion;
use Faker\Generator as Faker;

$factory->define(ResolucionParteExcepcion::class, function (Faker $faker) {
    $resolucion_id = Resolucion::inRandomOrder()->first();
    return [
        "parte_solicitante_id" => function () {
            return factory(App\Parte::class)->state("solicitante")->create()->id;
        },
        "parte_solicitada_id" => function () {
            return factory(App\Parte::class)->state("solicitado")->create()->id;
        },
        "conciliador_id" => function () {
            return factory(App\Conciliador::class)->create()->id;
        },
        "resolucion_id" => $resolucion_id
    ];
});

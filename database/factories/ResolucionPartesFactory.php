<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audiencia;
use App\Parte;
use App\Resolucion;
use App\ResolucionPartes;
use Faker\Generator as Faker;

$factory->define(ResolucionPartes::class, function (Faker $faker) {
    $audiencia = Audiencia::inRandomOrder()->first();
    $solicitante = Parte::inRandomOrder()->where('tipo_parte_id',1)->first();;
    $solicitado = Parte::inRandomOrder()->where('tipo_parte_id',2)->first();;
    $resolucion_id = Resolucion::inRandomOrder()->first();
    return [
        "audiencia_id" => $audiencia->id,
        "parte_solicitante_id" => $solicitante->parte_id,
        "parte_solicitada_id" => $solicitado->parte_id,
        "resolucion_id" => $resolucion_id
    ];
});
$factory->state(ResolucionPartes::class, 'completo', function (Faker $faker) {
	$audiencia = factory(\App\Audiencia::class)->create();
    $solicitante = factory(\App\Parte::class)->state('solicitante')->create();
    $solicitado = factory(\App\Parte::class)->state('solicitado')->create();
    $resolucion_id = Resolucion::inRandomOrder()->first();
    return [
        "audiencia_id" => $audiencia->id,
        "parte_solicitante_id" => $solicitante->parte_id,
        "parte_solicitada_id" => $solicitado->parte_id,
        "resolucion_id" => $resolucion_id
    ];
});


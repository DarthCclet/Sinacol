<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audiencia;
use App\Expediente;
use App\Conciliador;
use App\Sala;
use App\Resolucion;
use App\Parte;
use Faker\Generator as Faker;

$factory->define(Audiencia::class, function (Faker $faker) {
	$expediente = factory(App\Expediente::class)->create();
	$conciliador = factory(App\Conciliador::class)->create();
	$sala = factory(App\Sala::class)->create();
	$resolucion = factory(App\Resolucion::class)->create();
	$parteR = factory(App\Parte::class)->create();
    return [
        //
	'expediente_id' => $expediente->id,
	'conciliador_id' => $conciliador->id,
	'sala_id' => $sala->id,
	'resolucion_id' => $resolucion->id,
	'parte_responsable_id' => $parteR->id,
	'fecha_audiencia' => $faker->date,
	'hora_inicio' => $faker->time,
	'hora_fin' => $faker->time,
	'numero_audiencia' => $faker->randomDigit,
	'reprogramada' => $faker->boolean,
	'desahogo' => $faker->sentence,
	'convenio' => $faker->sentence
    ];
});

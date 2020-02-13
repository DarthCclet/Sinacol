<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audiencia;
use App\Expediente;
use App\Resolucion;
use App\Parte;
use Faker\Generator as Faker;

$factory->define(Audiencia::class, function (Faker $faker) {
	$expediente = factory(App\Expediente::class)->create();
	$resolucion = factory(App\Resolucion::class)->create();
	$parteR = factory(App\Parte::class)->create();
    return [
        //
	'expediente_id' => $expediente->id,
	'resolucion_id' => $resolucion->id,
	'parte_responsable_id' => $parteR->id,
	'fecha_audiencia' => $faker->date,
	'multiple' => $faker->boolean,
	'hora_inicio' => $faker->time,
	'hora_fin' => $faker->time,
	'numero_audiencia' => $faker->randomDigit,
	'reprogramada' => $faker->boolean,
	'desahogo' => $faker->sentence,
	'convenio' => $faker->sentence
    ];
});

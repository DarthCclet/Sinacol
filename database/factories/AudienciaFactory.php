<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audiencia;
use App\Expediente;
use App\Conciliador;
use App\Resolucion;
use App\Parte;
use Faker\Generator as Faker;

$factory->define(Audiencia::class, function (Faker $faker) {
	$expediente = Expediente::inRandomOrder()->first();
	$conciliador = factory(App\Conciliador::class)->create();
	
//	$resolucion = Resolucion::inRandomOrder()->first();
	$resolucion = 3;
	$parteR = Parte::inRandomOrder()->first();
    return [
        //
	'expediente_id' => $expediente->id,
        'conciliador_id' => $conciliador->id,
//	'resolucion_id' => $resolucion->id,
	'resolucion_id' => $resolucion,
	'parte_responsable_id' => $parteR->id,
	'fecha_audiencia' => $faker->dateTimeBetween('-2 years')->format("Y-m-d"),
	'multiple' => $faker->boolean,
	'hora_inicio' => $faker->time,
	'hora_fin' => $faker->time,
	'numero_audiencia' => $faker->randomDigit,
	'reprogramada' => $faker->boolean,
	'desahogo' => $faker->sentence,
	'convenio' => $faker->sentence
    ];
});
$factory->state(Audiencia::class, 'audienciaMultiple', function (Faker $faker) {
	return [    
	'multiple' => true,
    ];
});

$factory->state(Audiencia::class, 'audienciaSimple', function (Faker $faker) {
	return [    
	'multiple' => false,
    ];
});
$factory->state(Audiencia::class, 'completa', function (Faker $faker) {
	$expediente = factory(App\Expediente::class)->create();
	$conciliador = factory(App\Conciliador::class)->create();
	$parteR = factory(App\Parte::class)->create();
	return [    
	'expediente_id' => $expediente->id,
	'conciliador_id' => $conciliador->id,
	'parte_responsable_id' => $parteR->id,
    ];
});

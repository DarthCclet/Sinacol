<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audiencia;
use App\Expediente;
use App\Conciliador;
use App\Resolucion;
use App\Parte;
use Faker\Generator as Faker;

$factory->define(Audiencia::class, function (Faker $faker) {
	$resolucion = 3;
	$parteR = Parte::inRandomOrder()->first();
    return [
	'expediente_id' => function () {
		return factory(App\Expediente::class)->create()->id;
	},
    'conciliador_id' => function () {
		return factory(App\Conciliador::class)->create()->id;
	},
	'parte_responsable_id' => function () {
		return factory(App\Parte::class)->create()->id;
	},
	'resolucion_id' => function () {
		return Resolucion::inRandomOrder()->first()->id;
	},
	'fecha_audiencia' => $faker->dateTimeBetween('-2 years')->format("Y-m-d"),
	'multiple' => $faker->boolean,
	'hora_inicio' => $faker->time,
	'hora_fin' => $faker->time,
	'numero_audiencia' => $faker->randomDigit,
	'reprogramada' => $faker->boolean,
	'desahogo' => $faker->sentence,
	'convenio' => $faker->sentence,
	'finalizada' => $faker->boolean
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


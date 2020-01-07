<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Incidencia;
use Faker\Generator as Faker;

$factory->define(Incidencia::class, function (Faker $faker) {
    // id del registro a la tabla que requiera un agregar incidencias
	$incidenciable_id = 1;
	// Modelo de la tabla que requiere incidencias
	$incidenciable_type = 'App\Sala';
	return [
		'incidenciable_id' => $incidenciable_id,
		'incidenciable_type' => $incidenciable_type,
		'fecha_inicio' => $faker->date,
		'fecha_fin' => $faker->date,
		'hora_inicio' => $faker->time,
		'hora_fin' => $faker->time
	];
});

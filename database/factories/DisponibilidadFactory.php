<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Disponibilidad;
use Faker\Generator as Faker;

$factory->define(Disponibilidad::class, function (Faker $faker) {
    // id del registro a la tabla que requiera un agregar disponibilidad
	$disponibilidad_id = 1;
	// Modelo de la tabla que requiere disponibilidad
	$disponibilidad_type = 'App\Sala';
	return [
		'disponibilidad_id' => $disponibilidad_id,
		'disponibilidad_type' => $disponibilidad_type,
		'dia' => $faker->randomDigit,
		'hora_inicio' => $faker->time,
		'hora_fin' => $faker->time
	];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Disponibilidad;
use Faker\Generator as Faker;

$factory->define(Disponibilidad::class, function (Faker $faker) {
    // id del registro a la tabla que requiera un agregar disponibilidad
	$disponibiliable_id = 1;
	// Modelo de la tabla que requiere disponibilidad
	$disponibiliable_type = 'App\Sala';
	return [
		'disponibiliable_id' => $domiciliable_id,
		'disponibiliable_type' => $domiciliable_type,
		'dia' => $faker->randomDigit,
		'hora_inicio' => $faker->time,
		'hora_fin' => $faker->time
	];
});

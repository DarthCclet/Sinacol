<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Persona;
use App\TipoPersona;
use Faker\Generator as Faker;

$factory->define(Persona::class, function (Faker $faker) {
	$TipoPersona = TipoPersona::inRandomOrder()->first();
    return [
        'nombre' => $faker->name,
        'paterno' => $faker->lastName,
        'materno' => $faker->lastName,
        'razon_social' => $faker->sentence(1,false),
		'rfc' => strtoupper($faker->lexify("??????????????????")),
        'curp' => strtoupper($faker->lexify("?????????????")),
        'fecha_nacimiento' => $faker->dateTimeBetween('-2 years', 'now', 'America/New_York'),
        'tipo_persona_id' => $TipoPersona->id
    ];
});

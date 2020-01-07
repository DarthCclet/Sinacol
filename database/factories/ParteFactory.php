<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Parte;
use App\Solicitud;
use App\TipoParte;
use App\Genero;
use App\TipoPersona;
use App\Nacionalidad;
use App\Estado;
use Faker\Generator as Faker;

$factory->define(Parte::class, function (Faker $faker) {
    $solicitud = factory(\App\Solicitud::class)->create();
    $tipo_parte = TipoParte::inRandomOrder()->first();
    $genero = Genero::inRandomOrder()->first();
    $tipo_persona = TipoPersona::inRandomOrder()->first();
    $nacionalidad = Nacionalidad::inRandomOrder()->first();
    $entidad_nacimiento = Estado::inRandomOrder()->first();
    return [
        'solicitud_id' => $solicitud->id,
        'tipo_parte_id' => $tipo_parte->id,
        'genero_id' => $genero->id,
        'tipo_persona_id' => $tipo_persona->id,
        'nacionalidad_id' => $nacionalidad->id,
        'entidad_nacimiento_id' => $entidad_nacimiento->id,
        'nombre' => $faker->firstName,
        'apellido_paterno' => $faker->lastName,
        'apellido_materno' => $faker->lastName,
        'nombre_comercial' => $faker->optional(0.6)->company,
        'fecha_nacimiento' => $faker->date,
        'edad' => $faker->randomNumber(2),
        'rfc' => strtoupper($faker->lexify("??????????????????")),
        'curp' => strtoupper($faker->lexify("?????????????")),
    ];
});

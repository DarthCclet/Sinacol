<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Persona;
use App\TipoPersona;
use Faker\Generator as Faker;

/**
 * Factory para persona random
 */
$factory->define(Persona::class, function (Faker $faker) {
	$tipoPersona = TipoPersona::inRandomOrder()->first();
    return [
        'nombre' => $faker->firstName,
        'primer_apellido' => $faker->lastName,
        'segundo_apellido' => $faker->lastName,
        'razon_social' => $faker->sentence(1,false),
        'rfc' => strtoupper($faker->lexify("??????????????????")),
        'curp' => strtoupper($faker->lexify("?????????????")),
        'fecha_nacimiento' => $faker->dateTimeBetween('-2 years', 'now', 'America/New_York'),
        'tipo_persona_id' => $tipoPersona->id
    ];
});

/**
 * Factory para persona fisica
 */
$factory->state(Persona::class, 'fisica', function (Faker $faker) {
	$tipoPersona = TipoPersona::where('abreviatura', 'F')->first();
    return [
        'razon_social' => null,
        'fecha_nacimiento' => $faker->dateTimeBetween('-70 years', '-15 years', 'America/Mexico_City'),
        'tipo_persona_id' => $tipoPersona->id
    ];
});

/**
 * Factory para persona moral
 */
$factory->state(Persona::class, 'moral', function (Faker $faker) {
	$tipoPersona = TipoPersona::where('abreviatura', 'M')->first();
	$razon_social = $faker->company;
    return [
        'nombre' => $razon_social,
        'paterno' => null,
        'materno' => null,
        'razon_social' => $razon_social,
        'rfc' => strtoupper($faker->lexify("??????????????????")),
        'curp' => null,
        'fecha_nacimiento' => $faker->dateTimeBetween('-90 years', '-1 years', 'America/Mexico_City'),
        'tipo_persona_id' => $tipoPersona->id
    ];
});

/**
 * Factory para agregar Admin
 */
$factory->state(Persona::class, 'admin', function (Faker $faker) {
    $tipoPersona = TipoPersona::where('abreviatura', 'F')->first();
    return [
        'nombre' => 'STPS',
        'primer_apellido' => null,
        'segundo_apellido' => null,
        'razon_social' => null,
        'fecha_nacimiento' => null,
        'tipo_persona_id' => $tipoPersona->id
    ];
});
/**
 * Factory para agregar Admin
 */
$factory->state(Persona::class, 'root', function (Faker $faker) {
    $tipoPersona = TipoPersona::where('abreviatura', 'F')->first();
    return [
        'nombre' => 'root',
        'primer_apellido' => null,
        'segundo_apellido' => null,
        'razon_social' => null,
        'fecha_nacimiento' => null,
        'tipo_persona_id' => $tipoPersona->id
    ];
});
/**
 * Factory para agregar Orientador
 */
$factory->state(Persona::class, 'orientador', function (Faker $faker) {
    $tipoPersona = TipoPersona::where('abreviatura', 'F')->first();
    return [
        'nombre' => 'orientador',
        'primer_apellido' => '',
        'segundo_apellido' => '',
        'razon_social' => null,
        'fecha_nacimiento' => null,
        'tipo_persona_id' => $tipoPersona->id
    ];
});
/**
 * Factory para agregar Orientador
 */
$factory->state(Persona::class, 'orientador', function (Faker $faker) {
    $tipoPersona = TipoPersona::where('abreviatura', 'F')->first();
    return [
        'nombre' => 'Orientador',
        'primer_apellido' => '',
        'segundo_apellido' => '',
        'razon_social' => null,
        'fecha_nacimiento' => null,
        'tipo_persona_id' => $tipoPersona->id
    ];
});
/**
 * Factory para agregar personal conciliador
 */
$factory->state(Persona::class, 'personal_conciliador', function (Faker $faker) {
    $tipoPersona = TipoPersona::where('abreviatura', 'F')->first();
    return [
        'nombre' => 'Personal',
        'primer_apellido' => 'Conciliador',
        'segundo_apellido' => '',
        'razon_social' => null,
        'fecha_nacimiento' => null,
        'tipo_persona_id' => $tipoPersona->id
    ];
});
/**
 * Factory para agregar Orientador
 */
$factory->state(Persona::class, 'supervisor_conciliacion', function (Faker $faker) {
    $tipoPersona = TipoPersona::where('abreviatura', 'F')->first();
    return [
        'nombre' => 'Supervisor',
        'primer_apellido' => 'de',
        'segundo_apellido' => 'Conciliacion',
        'razon_social' => null,
        'fecha_nacimiento' => null,
        'tipo_persona_id' => $tipoPersona->id
    ];
});

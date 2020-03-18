<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Parte;
use App\Solicitud;
use App\TipoParte;
use App\Genero;
use App\TipoPersona;
use App\Nacionalidad;
use App\Estado;
use App\GiroComercial;
use App\GrupoPrioritario;
use Faker\Generator as Faker;

$factory->define(Parte::class, function (Faker $faker) {
    // se llama el factory de solicitud para crear un registro y probar su relacion
    $solicitud = factory(\App\Solicitud::class)->create();
    // al ser catalogos se mada a llamar de forma aleatoria
    //  tipoParte, genero, tipo persona, nacionalidad y estado
    //  ya que se segura que existen registros al generar la migracion
    $tipo_parte = TipoParte::inRandomOrder()->first();
    $grupo_prioritario = GrupoPrioritario::inRandomOrder()->first();
    $giro_comercia = GiroComercial::inRandomOrder()->first();
    $genero = Genero::inRandomOrder()->first();
    $tipo_persona = TipoPersona::inRandomOrder()->first();
    $nacionalidad = Nacionalidad::inRandomOrder()->first();
    $entidad_nacimiento = Estado::inRandomOrder()->first();
    // se crea el registro de parte usando los datos obtenidos anteriormente
    return [
        'solicitud_id' => $solicitud->id,
        'tipo_parte_id' => $tipo_parte->id,
        'genero_id' => $genero->id,
        'tipo_persona_id' => $tipo_persona->id,
        'nacionalidad_id' => $nacionalidad->id,
        'entidad_nacimiento_id' => $entidad_nacimiento->id,
        'nombre' => $faker->firstName,
        'primer_apellido' => $faker->lastName,
        'segundo_apellido' => $faker->lastName,
        'nombre_comercial' => $faker->optional(0.6)->company,
        'fecha_nacimiento' => $faker->date,
        'giro_comercial_id' => $giro_comercia->id,
        'grupo_prioritario_id' => $grupo_prioritario->id,
        'edad' => $faker->randomNumber(2),
        'rfc' => strtoupper($faker->lexify("??????????????????")),
        'curp' => strtoupper($faker->lexify("?????????????")),
    ];
});

$factory->state(Parte::class, 'solicitante', function (Faker $faker) {
	$tipo_parte = TipoParte::where('id', '1')->first();
    $solicitud = factory(\App\Solicitud::class)->create();
    // al ser catalogos se mada a llamar de forma aleatoria
    //  tipoParte, genero, tipo persona, nacionalidad y estado
    //  ya que se segura que existen registros al generar la migracion
    $grupo_prioritario = GrupoPrioritario::inRandomOrder()->first();
    $giro_comercia = GiroComercial::inRandomOrder()->first();
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
        'primer_apellido' => $faker->lastName,
        'segundo_apellido' => $faker->lastName,
        'nombre_comercial' => $faker->company,
        'fecha_nacimiento' => $faker->date,
        'giro_comercial_id' => $giro_comercia->id,
        'grupo_prioritario_id' => $grupo_prioritario->id,
        'edad' => $faker->randomNumber(2),
        'rfc' => strtoupper($faker->lexify("??????????????????")),
        'curp' => strtoupper($faker->lexify("?????????????")),
    ];
});

$factory->state(Parte::class, 'solicitado', function (Faker $faker) {
	$tipo_parte = TipoParte::where('id', '2')->first();
    $solicitud = factory(\App\Solicitud::class)->create();
    // al ser catalogos se mada a llamar de forma aleatoria
    //  tipoParte, genero, tipo persona, nacionalidad y estado
    //  ya que se segura que existen registros al generar la migracion
    $grupo_prioritario = GrupoPrioritario::inRandomOrder()->first();
    $giro_comercia = GiroComercial::inRandomOrder()->first();
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
        'primer_apellido' => $faker->lastName,
        'segundo_apellido' => $faker->lastName,
        'nombre_comercial' => $faker->optional(0.6)->company,
        'fecha_nacimiento' => $faker->date,
        'giro_comercial_id' => $giro_comercia->id,
        'grupo_prioritario_id' => $grupo_prioritario->id,
        'edad' => $faker->randomNumber(2),
        'rfc' => strtoupper($faker->lexify("??????????????????")),
        'curp' => strtoupper($faker->lexify("?????????????")),
    ];
});

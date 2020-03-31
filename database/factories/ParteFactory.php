<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Estado;
use App\Genero;
use App\GiroComercial;
use App\GrupoPrioritario;
use App\LenguaIndigena;
use App\Nacionalidad;
use App\Parte;
use App\TipoParte;
use App\TipoPersona;
use Faker\Generator as Faker;

$factory->define(Parte::class, function (Faker $faker) {
    $faker = app('FakerCurp');
    // se llama el factory de solicitud para crear un registro y probar su relacion
    $solicitud = factory(\App\Solicitud::class)->create();
    // al ser catalogos se mada a llamar de forma aleatoria
    //  tipoParte, genero, tipo persona, nacionalidad y estado
    //  ya que se segura que existen registros al generar la migracion
    $tipo_parte = TipoParte::whereIn('id',[1,2])->inRandomOrder()->first();
    $grupo_prioritario = GrupoPrioritario::inRandomOrder()->first();
    $giro_comercia = GiroComercial::inRandomOrder()->first();
    $genero = Genero::inRandomOrder()->first();
    $tipo_persona = TipoPersona::inRandomOrder()->first();
    $nacionalidad = Nacionalidad::inRandomOrder()->first();
    $entidad_nacimiento = Estado::inRandomOrder()->first();
    $fecha_nacimiento = $faker->dateTimeBetween('-70 years', '-15 years');
    $edad = $fecha_nacimiento->diff(now(), true)->y;
    $solicita_traductor = $faker->boolean();
    $lengua_indigena = LenguaIndigena::inRandomOrder()->first();
    // se crea el registro de parte usando los datos obtenidos anteriormente
    return [
        'solicitud_id' => $solicitud->id,
        'tipo_parte_id' => $tipo_parte->id,
        'genero_id' => ($tipo_persona->abreviatura == 'F') ? $genero->id : null,
        'tipo_persona_id' => $tipo_persona->id,
        'nacionalidad_id' => $nacionalidad->id,
        'entidad_nacimiento_id' => $entidad_nacimiento->id,
        'nombre' => ($tipo_persona->abreviatura == 'F') ? $faker->firstName : null,
        'primer_apellido' => ($tipo_persona->abreviatura == 'F') ? $faker->lastName : null,
        'segundo_apellido' => ($tipo_persona->abreviatura == 'F') ? $faker->lastName : null,
        'nombre_comercial' => ($tipo_persona->abreviatura == 'M') ? $faker->company : null,
        'fecha_nacimiento' => $fecha_nacimiento->format("Y-m-d"),
        'solicita_traductor' =>  $solicita_traductor,
        'lengua_indigena_id' =>  ($solicita_traductor) ? $lengua_indigena->id : null,
//        'giro_comercial_id' => ($tipo_persona->abreviatura == 'M') ? $giro_comercia->id : null,
        'giro_comercial_id' =>  $giro_comercia->id,
        'grupo_prioritario_id' => ($tipo_persona->abreviatura == 'F') ? $grupo_prioritario->id : null,
        'edad' => $edad,
        'rfc' => $faker->rfc,
        'curp' => ($tipo_persona->abreviatura == 'F') ? $faker->curp : null,
    ];
});

$factory->state(Parte::class, 'solicitante', function (Faker $faker) {
	$tipo_parte = TipoParte::where('id', '1')->first();
    return ['tipo_parte_id' => $tipo_parte->id];
});

$factory->state(Parte::class, 'solicitado', function (Faker $faker) {
	$tipo_parte = TipoParte::where('id', '2')->first();
    return ['tipo_parte_id' => $tipo_parte->id];
});

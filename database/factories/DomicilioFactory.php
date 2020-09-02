<?php

/** @var Factory $factory */

use App\Domicilio;
use App\Estado;
use App\Municipio;
use App\TipoAsentamiento;
use App\TipoVialidad;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\DB;

$factory->define(Domicilio::class, function (Faker $faker) {
    $tipo_asentamiento = TipoAsentamiento::inRandomOrder()->first();
    $tipo_vialidad = TipoVialidad::inRandomOrder()->first();
    $estado = Estado::inRandomOrder()->first();
    $municipio = Municipio::whereRaw(DB::raw("lower(unaccent('{$estado->nombre}')) = lower(unaccent(estado))"))->get('municipio')->first();
    //TODO: Extraer algún domiciliable cuando ya esté disponible algun modelo docmiciliable
    $domiciliable_id = 1;
    $domiciliable_type = 'App\User';
    $cp = $faker->randomNumber(5);
    return [
        'domiciliable_id' => $domiciliable_id,
        'domiciliable_type' => $domiciliable_type,

        'tipo_vialidad' => $tipo_vialidad->nombre,
        'tipo_vialidad_id' => $tipo_vialidad->id,

        'vialidad' => $faker->streetName,
        'num_ext' => $faker->randomNumber(),
        'num_int' => $faker->randomNumber(),
        'tipo_asentamiento' => $tipo_asentamiento->nombre,
        'tipo_asentamiento_id' => $tipo_asentamiento->id,

        'asentamiento' => implode(" ",$faker->words(3)),
        'municipio' => $municipio->municipio,

        'estado' => $estado->nombre,
        'estado_id' => $estado->id,

        'cp' => $cp,
        'cp_por_geo' => $cp,
        'latitud' => $faker->latitude,
        'longitud' => $faker->longitude,
        'entre_calle1' => $faker->streetName,
        'entre_calle2' => $faker->streetName,
        'referencias' => $faker->paragraph,

    ];
});

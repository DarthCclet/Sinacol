<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\DatoLaboral;
use App\GiroComercial;
use App\Jornada;
use App\Ocupacion;

$factory->define(DatoLaboral::class, function (Faker $faker) {
  //$jornada = factory(\App\Jornada::class)->create();
  $jornada = Jornada::inRandomOrder()->first();
  $ocupacion = Ocupacion::inRandomOrder()->first();
  $parte =  factory(\App\Parte::class)->create();
  $giro_comercia = GiroComercial::inRandomOrder()->first();
    return [
      'jornada_id' => $jornada->id,
      'parte_id' => $parte->id,
      'nombre_jefe_directo' => $faker->randomElement(['Luis Lopez','Juana Martinez','Eduardo Sanchez','Ana Juarez']),
      'ocupacion_id' =>$ocupacion->id,
      'nss'=> $faker->randomElement(['123412341234','9987987987987','8347348374','3478347']),
      'no_afore'=> $faker->randomElement(['123412341234','9987987987987','8347348374','3478347']),
      'no_issste'=> $faker->randomElement(['123412341234','9987987987987','8347348374','3478347']),
      'fecha_ingreso' => $faker->date,
      'fecha_salida' => $faker->date,
      'giro_comercial_id' =>  $giro_comercia->id,
      'percepcion_mensual_neta' => $faker->randomFloat(2,1,1000),
      'percepcion_mensual_bruta' => $faker->randomFloat(2,1,1000),
      'labora_actualmente' => $faker->boolean,
      'horas_semanales' => $faker->randomNumber(2)
        //
    ];

});

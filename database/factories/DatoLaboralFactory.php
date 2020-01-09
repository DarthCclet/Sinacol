<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\DatoLaboral;
use App\Jornada;


$factory->define(DatoLaboral::class, function (Faker $faker) {
  //$jornada = factory(\App\Jornada::class)->create();
  $jornada = Jornada::inRandomOrder()->first();

    return [
      'jornada_id' => $jornada->id,
      'nombre_jefe_directo' => $faker->randomElement(['Luis Lopez','Juana Martinez','Eduardo Sanchez','Ana Juarez']),
      'puesto' =>$faker->randomElement(['obrero','administrador','gerente','contador']),
      'nss'=> $faker->randomNumber(6),
      'no_afore'=> $faker->randomNumber(4),
      'no_issste'=> $faker->randomNumber(3),
      'fecha_ingreso' => $faker->date,
      'fecha_salida' => $faker->date,
      'percepcion_mensual_neta' => $faker->randomFloat(2,1,1000),
      'percepcion_mensual_bruta' => $faker->randomFloat(2,1,1000),
      'labora_actualmente' => $faker->boolean,
      'horas_semanales' => $faker->randomNumber(2)
        //
    ];

});

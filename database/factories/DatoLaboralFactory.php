<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\DatoLaboral;
use App\Jornada;
use App\Ocupacion;
use App\Parte;
use App\Periodicidad;

$factory->define(DatoLaboral::class, function (Faker $faker) {
  //$jornada = factory(\App\Jornada::class)->create();
  $jornada = Jornada::inRandomOrder()->first();
  $ocupacion = Ocupacion::inRandomOrder()->first();
  $parte = Parte::inRandomOrder()->first();
  $periodicidad = Periodicidad::inRandomOrder()->first();
    return [
      'parte_id' => function(){
        return factory(\App\Parte::class)->create()->id;
      },
      'nombre_jefe_directo' => $faker->randomElement(['Luis Lopez','Juana Martinez','Eduardo Sanchez','Ana Juarez']),
      'ocupacion_id' =>$ocupacion->id,
      'nss'=> $faker->randomElement(['12341234123','99879879879','83473483743','39483478347']),
      'fecha_ingreso' => $faker->date,
      'fecha_salida' => $faker->date,
      'remuneracion' => $faker->randomFloat(2,1,1000),
      'periodicidad_id' => $periodicidad->id,
      'labora_actualmente' => $faker->boolean,
      'jornada_id' => $jornada->id,
      'puesto' => $faker->randomElement(['MECANICO','DESARROLLADOR','OBRERO','ANALISTA DE PROYECTOS']),
      'horas_semanales' => $faker->randomNumber(2)
        //
    ];

});
$factory->state(DatoLaboral::class, 'parte', function (Faker $faker) {
	$parte =  factory(\App\Parte::class)->create();
    return ['parte_id' => $parte->id];
});

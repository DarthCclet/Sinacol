<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\DatoLaboral;
use App\Jornada;
use App\Ocupacion;
use App\Parte;
use App\Periodicidad;
use Carbon\Carbon;

$factory->define(DatoLaboral::class, function (Faker $faker) {
  //$jornada = factory(\App\Jornada::class)->create();
  $jornada = Jornada::inRandomOrder()->first();
  $ocupacion = Ocupacion::inRandomOrder()->first();
  $parte = Parte::inRandomOrder()->first();
  $periodicidad = Periodicidad::inRandomOrder()->first();
  $aniosExp = $faker->randomNumber(1);
  $diasExp = $faker->randomNumber(2);
  $fechaInicio = Carbon::today()->subYears($aniosExp)->subDays($diasExp);
  $fechaFin = Carbon::today();
  $labora = $faker->boolean;
    return [
      'parte_id' => function(){
        return factory(\App\Parte::class)->create()->id;
      },
      'ocupacion_id' =>$ocupacion->id,
      'nss'=> $faker->randomElement(['12341234123','99879879879','83473483743','39483478347']),
      'fecha_ingreso' => $fechaInicio,
      'fecha_salida' => (!$labora) ? $fechaFin : null,
      'remuneracion' => $faker->randomFloat(2,1,1000),
      'periodicidad_id' => $periodicidad->id,
      'labora_actualmente' => $labora,
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

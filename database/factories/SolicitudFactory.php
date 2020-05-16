<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Solicitud;
use App\EstatusSolicitud;
use App\ObjetoSolicitud;
use App\Centro;
use App\User;
use Faker\Generator as Faker;

$factory->define(Solicitud::class, function (Faker $faker) {
  
  // al ser catalogos se mada a llamar de forma aleatoria
  // estatus solicitud, objeto solicitud, centro,
  //  ya que se segura que existen registros al generar la migracion
  $estatus_solicitud = EstatusSolicitud::inRandomOrder()->first();

  $centro = Centro::inRandomOrder()->first();

  // se crea el registro de Solicitud usando los datos obtenidos anteriormente
    return [
        'ratificada' => ($estatus_solicitud->id != 1) ? true : false,
        'estatus_solicitud_id' => $estatus_solicitud->id,
        'centro_id' => $centro->id,
        'user_id' => function(){ 
          return factory(\App\User::class)->create()->id;
        },
        'folio' => $faker->randomNumber(3),
        'anio' => $faker->year(),
        'solicita_excepcion' => $faker->boolean(),
        'fecha_ratificacion' => ($estatus_solicitud->id != 1) ? $faker->dateTime : null,
        'fecha_recepcion' => $faker->dateTime,
        'fecha_conflicto' => $faker->dateTime,
        'observaciones' => $faker->text(100),
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Solicitud;
use App\EstatusSolicitud;
use App\ObjetoSolicitud;
use App\Centro;
use App\User;
use Faker\Generator as Faker;

$factory->define(Solicitud::class, function (Faker $faker) {
  $usuario = factory(\App\User::class)->create();
  // al ser catalogos se mada a llamar de forma aleatoria
  // estatus solicitud, objeto solicitud, centro,
  //  ya que se segura que existen registros al generar la migracion
  $estatus_solicitud = EstatusSolicitud::inRandomOrder()->first();

  $centro = Centro::inRandomOrder()->first();

  // se crea el registro de Solicitud usando los datos obtenidos anteriormente
    return [
        'estatus_solicitud_id' => $estatus_solicitud->id,
        'centro_id' => $centro->id,
        'user_id' => $usuario->id,
        'folio' => $faker->randomNumber(3),
        'anio' => $faker->year(),
        'ratificada' => $faker->boolean(),
        'solicita_excepcion' => $faker->boolean(),
        'fecha_ratificacion' => $faker->dateTime,
        'fecha_recepcion' => $faker->dateTime,
        'fecha_conflicto' => $faker->dateTime,
        'observaciones' => $faker->text(100),
    ];
});

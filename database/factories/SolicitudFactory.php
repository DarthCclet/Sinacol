<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Solicitud;
use App\Abogado;
use App\EstatusSolicitud;
use App\MotivoSolicitud;
use App\Centro;
use App\User;
use Faker\Generator as Faker;

$factory->define(Solicitud::class, function (Faker $faker) {
  $abogado = factory(\App\Abogado::class)->create();
  $estatus_solicitud = EstatusSolicitud::inRandomOrder()->first();
  $motivo_solicitud = MotivoSolicitud::inRandomOrder()->first();
  $centro = Centro::inRandomOrder()->first();
  $usuario = factory(\App\User::class)->create();
    return [
        'abogado_id' => $abogado->id,
        'estatus_solicitud_id' => $estatus_solicitud->id,
        'motivo_solicitud_id' => $motivo_solicitud->id,
        'centro_id' => $centro->id,
        'user_id' => $usuario->id,
        'ratificada' => $faker->boolean(),
        'fecha_ratificacion' => $faker->dateTime,
        'fecha_recepcion' => $faker->dateTime,
        'observaciones' => $faker->text(100),
        'presenta_abogado' => $faker->boolean(),
    ];
});

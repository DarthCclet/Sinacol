<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\RolConciliador;

$factory->define(RolConciliador::class, function (Faker $faker) {
    $rol_atencion = factory(\App\RolAtencion::class)->create();
    $conciliador = factory(\App\Conciliador::class)->create();
    return [
      'rol_atencion_id'=> $rol_atencion->id,
      'conciliador_id'=> $conciliador->id
    ];
});

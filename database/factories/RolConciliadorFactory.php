<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\RolConciliador;

$factory->define(RolConciliador::class, function (Faker $faker) {
    return [
      'rol_atencion_id'=> function(){
        return factory(\App\RolAtencion::class)->create()->id;
      },
      'conciliador_id'=> function(){
        return factory(\App\Conciliador::class)->create()->id;
      }
    ];
});

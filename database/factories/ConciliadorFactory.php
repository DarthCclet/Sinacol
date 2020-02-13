<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Conciliador;
use Faker\Generator as Faker;

$factory->define(Conciliador::class, function (Faker $faker) {
    $persona = factory(App\Persona::class)->create();
    $centro = factory(App\Centro::class)->create();
    return [
        //
        'persona_id' => $persona->id,
        'centro_id' => $centro->id,
    ];
});

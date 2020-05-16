<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Centro;
use App\Conciliador;
use Faker\Generator as Faker;

$factory->define(Conciliador::class, function (Faker $faker) {
    $centro = Centro::inRandomOrder()->first();
    return [
        //
        'persona_id' => function(){
            return factory(App\Persona::class)->create()->id;
        },
        'centro_id' => $centro->id,
    ];
});

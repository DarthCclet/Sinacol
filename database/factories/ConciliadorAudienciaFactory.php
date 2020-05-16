<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audiencia;
use App\Conciliador;
use App\ConciliadorAudiencia;
use Faker\Generator as Faker;

$factory->define(ConciliadorAudiencia::class, function (Faker $faker) {
    
    return [
        'conciliador_id' => function () {
            return factory(App\Conciliador::class)->create()->id;
        },
        'audiencia_id' => function () {
            return factory(App\Audiencia::class)->create()->id;
        },
        'solicitante' => $faker->boolean
    ];
});
<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Sala;
use App\Audiencia;
use App\SalaAudiencia;
use Faker\Generator as Faker;

$factory->define(SalaAudiencia::class, function (Faker $faker) {
    $sala = Sala::inRandomOrder()->first();
    $audiencia = Audiencia::inRandomOrder()->first();
    return [
        'sala_id' => function () {
            return factory(App\Sala::class)->create()->id;
        },
        'audiencia_id' => function () {
            return factory(App\Audiencia::class)->create()->id;
        },
        'solicitante' => $faker->boolean
    ];
});

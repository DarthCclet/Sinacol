<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audiencia;
use App\Compareciente;
use App\Parte;
use Faker\Generator as Faker;

$factory->define(Compareciente::class, function (Faker $faker) {
    return [
        'audiencia_id' => function () {
            return factory(App\Audiencia::class)->create()->id;
        },
        'parte_id' => function () {
            return factory(App\Parte::class)->create()->id;
        },
        'presentado' => $faker->boolean,
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Compareciente;
use Faker\Generator as Faker;

$factory->define(Compareciente::class, function (Faker $faker) {
    $audiencia = factory(App\Audiencia::class)->create();
    $parte = factory(App\Parte::class)->create();
    return [
        'audiencia_id' => $audiencia->id,
        'parte_id' => $parte->id,
        'presentado' => $faker->boolean,
    ];
});

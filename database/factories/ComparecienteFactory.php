<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audiencia;
use App\Compareciente;
use App\Parte;
use Faker\Generator as Faker;

$factory->define(Compareciente::class, function (Faker $faker) {
    $audiencia = Audiencia::inRandomOrder()->first();
    $parte = Parte::inRandomOrder()->first();
    return [
        'audiencia_id' => $audiencia->id,
        'parte_id' => $parte->id,
        'presentado' => $faker->boolean,
    ];
});

$factory->state(Compareciente::class, 'completo', function (Faker $faker) {
    $audiencia = factory(App\Audiencia::class)->create();
    $parte = factory(App\Parte::class)->create();
	return [
        'audiencia_id' => $audiencia->id,
        'parte_id' => $parte->id,
    ];
});

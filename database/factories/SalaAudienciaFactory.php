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
        'sala_id' => $sala->id,
        'audiencia_id' => $audiencia->id,
        'solicitante' => $faker->boolean
    ];
});

$factory->state(SalaAudiencia::class, 'completo', function (Faker $faker) {
    $audiencia = factory(App\Audiencia::class)->create();
    $sala = factory(App\Sala::class)->create();
	return [
        'audiencia_id' => $audiencia->id,
        'sala_id' => $sala->id,
    ];
});
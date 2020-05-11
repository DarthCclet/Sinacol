<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Audiencia;
use App\Conciliador;
use App\ConciliadorAudiencia;
use Faker\Generator as Faker;

$factory->define(ConciliadorAudiencia::class, function (Faker $faker) {
    $conciliador = Conciliador::inRandomOrder()->first();
    $audiencia = Audiencia::inRandomOrder()->first();
    return [
        'conciliador_id' => $conciliador->id,
        'audiencia_id' => $audiencia->id,
        'solicitante' => $faker->boolean
    ];
});
$factory->state(ConciliadorAudiencia::class, 'completo', function (Faker $faker) {
    $audiencia = factory(App\Audiencia::class)->create();
    $conciliador = factory(\App\Conciliador::class)->create();
	return [
        'audiencia_id' => $audiencia->id,
        'conciliador_id' => $conciliador->id,
    ];
});

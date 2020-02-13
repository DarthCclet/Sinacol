<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Sala;
use App\Audiencia;
use App\SalaAudiencia;
use Faker\Generator as Faker;

$factory->define(SalaAudiencia::class, function (Faker $faker) {
    $sala = factory(Sala::class)->create();
    $audiencia = factory(Audiencia::class)->create();
    return [
        'sala_id' => $sala->id,
        'audiencia_id' => $audiencia->id
    ];
});
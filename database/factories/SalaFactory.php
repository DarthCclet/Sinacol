<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Sala;
use App\Centro;
use Faker\Generator as Faker;

$factory->define(Sala::class, function (Faker $faker) {
	$centro = Centro::inRandomOrder()->first();
    return [
        'sala' => $faker->sentence,
        'centro_id' => $centro->id
    ];
});

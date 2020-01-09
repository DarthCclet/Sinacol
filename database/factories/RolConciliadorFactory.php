<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\RolConciliador;

$factory->define(RolConciliador::class, function (Faker $faker) {
    return [
      'nombre'=> $faker->randomElement(['en sala','con preacuerdo'])
        //
    ];
});

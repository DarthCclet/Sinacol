<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\Jornada;
$factory->define(Jornada::class, function (Faker $faker) {
    return [
      'nombre'=> $faker->randomElement(['tiempo completo','medio tiempo'])
        //
    ];
});

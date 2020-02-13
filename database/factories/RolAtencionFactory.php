<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\RolAtencion::class, function (Faker $faker) {
    return [
      'nombre'=> $faker->randomElement(['en sala','con preacuerdo'])
    ];
});

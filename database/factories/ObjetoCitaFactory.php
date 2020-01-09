<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\ObjetoCita;

$factory->define(ObjetoCita::class, function (Faker $faker) {
    return [
      'nombre'=> $faker->randomElement(['Despido','Incapacidad parcial','pension','prestaciones'])
        //
    ];
});

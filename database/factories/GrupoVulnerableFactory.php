<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\GrupoPrioritario;

$factory->define(GrupoPrioritario::class, function (Faker $faker) {
    return [
      'nombre'=> $faker->randomElement(['Mujeres embarazadas','adultos mayores','niños'])
        //
    ];
});

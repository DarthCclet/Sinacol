<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\GiroComercial;

$factory->define(GiroComercial::class, function (Faker $faker) {
    return [
        'nombre'=> $faker->randomElement(['Cultivo de soya','Cultivo de trigo','Mineria de carbon','corporativos'])
        //
    ];
});

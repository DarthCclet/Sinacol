<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SalarioMinimo;
use Faker\Generator as Faker;

$factory->define(SalarioMinimo::class, function (Faker $faker) {
    return [
        'salario_minimo' => 123.22,
        'salario_minimo_zona_libre' => 185.56
    ];
});

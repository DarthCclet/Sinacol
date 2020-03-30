<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contacto;
use App\Model;
use App\TipoContacto;
use Faker\Generator as Faker;

$factory->define(Contacto::class, function (Faker $faker) {
    $tipo_contacto = TipoContacto::inRandomOrder()->first();
    $contactable_id = 1;
    $contactable_type = 'App\User';
    return [
        'contactable_id' => $contactable_id,
        'contactable_type' => $contactable_type,
        'tipo_contacto_id' => $tipo_contacto->id,
        'contacto' => ($tipo_contacto->id == 1) ? $faker->phoneNumber : $faker->email,
    ];
});

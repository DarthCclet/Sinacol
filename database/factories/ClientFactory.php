<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Laravel\Passport\Client;

$factory->define(Client::class, function (Faker $faker) {

    $user = factory(User::class)->create();

    return [

        'user_id' => $user->id,
        'name' => $faker->sentence(2),
        'secret' => Str::random(40),
        'redirect' => '',
        'personal_access_client' => false,
        'password_client' => false,
        'revoked' => false,
    ];
});

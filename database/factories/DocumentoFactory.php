<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Documento;
use Faker\Generator as Faker;

$factory->define(Documento::class, function (Faker $faker) {
	// id del registro a la tabla que requiera un documento
    $domiciliable_id = 1;
	// Modelo de la tabla que requiere el documento
    $domiciliable_type = 'App\Audiencia';
	$ruta = 'storage/file/file.jpg';
    return [
        'documentable_id' => $domiciliable_id,
        'documentable_type' => $domiciliable_type,
        'descripcion' => $faker->sentence,
        'ruta' => $ruta
    ];
});

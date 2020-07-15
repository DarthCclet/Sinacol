<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ConceptoPagoResolucion;
use App\ResolucionParteConcepto;
use Faker\Generator as Faker;

$factory->define(ResolucionParteConcepto::class, function (Faker $faker) {
    $concepto_pago_resoluciones = ConceptoPagoResolucion::inRandomOrder()->first();
    $numero_dias = $faker->randomNumber(2);
    return [
        "resolucion_partes_id" => function () {
            return factory(App\ResolucionPartes::class)->create()->id;
        },
        "concepto_pago_resoluciones_id"=> $concepto_pago_resoluciones->id,
        "dias"=>$numero_dias,
        "monto"=>($numero_dias * $faker->randomFloat(2,1,1000)),
        "otro"=>$faker->text
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Expediente;
use Faker\Generator as Faker;

$factory->define(Expediente::class, function (Faker $faker) {
  
  // se llama el factory de solicitud para crear un registro y probar su relacion
  $solicitud = factory(\App\Solicitud::class)->create();
  $solicitud->objeto_solicitudes()->sync([1]);
  // se crea parte solicitado
  $parteSolicitado = factory(App\Parte::class)->states('solicitado')->create(['solicitud_id'=>$solicitud->id]);
  $domicilioSolicitado = factory(App\Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
  // se crea parte solicitado
  $parteSolicitante = factory(App\Parte::class)->states('solicitante')->create(['solicitud_id'=>$solicitud->id]);
  factory(App\Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
  factory(\App\DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
  $year = date('Y');
  return [
    'folio'=> 'asd',
    'anio'=>$year,
    'consecutivo'=>'1',
    'solicitud_id'=>$solicitud->id
  ];
});

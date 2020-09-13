<?php

namespace Tests\Feature;

use App\Contacto;
use App\DatoLaboral;
use App\Domicilio;
use App\Parte;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SolicitudPreRatificacion extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function testExpedienteFisicaFisica()
    // {
    //     // se llama el factory de solicitud para crear un registro y probar su relacion
    //     $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>1,"fecha_ratificacion"=>null,'user_id'=>1,'fecha_recepcion'=>Carbon::now()]);

    //     $solicitud->objeto_solicitudes()->sync([1]);
    //     // se crea parte solicitado
    //     $parteSolicitado = factory(Parte::class)->states('solicitadoFisico')->create(['solicitud_id'=>$solicitud->id,'rfc'=>null]);
    //     $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
    //     $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
    //     // se crea parte solicitado
    //     $parteSolicitante = factory(Parte::class)->states('solicitanteFisico')->create(['solicitud_id'=>$solicitud->id,'rfc'=>null]);
    //     factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
    //     factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
    //     factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);
        
    //     $this->assertInstanceOf('\App\Solicitud',$solicitud);   
    // }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function testExpedienteFisicaMoral()
    // {
    //     // se llama el factory de solicitud para crear un registro y probar su relacion
    //     // se llama el factory de solicitud para crear un registro y probar su relacion
    //     $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>1,"fecha_ratificacion"=>null,'user_id'=>1,'fecha_recepcion'=>Carbon::now()]);

    //     $solicitud->objeto_solicitudes()->sync([1]);

    //     //se crea parte solicitado
    //     $parteSolicitante = factory(Parte::class)->states('solicitanteFisico')->create(['solicitud_id'=>$solicitud->id,'rfc'=>null]);
    //     factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
    //     factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
    //     factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);

    //     // se crea parte solicitado
    //     $parteSolicitado = factory(Parte::class)->states('solicitadoMoral')->create(['solicitud_id'=>$solicitud->id,'rfc'=>null]);
    //     $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
    //     $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
       
    //     $this->assertInstanceOf('\App\Solicitud',$solicitud);   
    // }
     /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExpedienteFisicaMorales()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>1,"fecha_ratificacion"=>null,'user_id'=>1,'fecha_recepcion'=>Carbon::now(),'ratificada'=>false]);

        $solicitud->objeto_solicitudes()->sync([1]);

        //se crea parte solicitante
        $parteSolicitante = factory(Parte::class)->states('solicitanteFisico')->create(['solicitud_id'=>$solicitud->id,'rfc'=>null,'curp'=>'YAPH800313HHGNRR05']);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);

        $parteSolicitante = factory(Parte::class)->states('solicitanteFisico')->create(['solicitud_id'=>$solicitud->id,'rfc'=>null,'curp'=>'YAPH800313HHGNRR05']);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);

        // se crea parte solicitado
        $parteSolicitado = factory(Parte::class)->states('solicitadoMoral')->create(['solicitud_id'=>$solicitud->id,'rfc'=>null]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);

        // se crea parte solicitado
        $parteSolicitado = factory(Parte::class)->states('solicitadoMoral')->create(['solicitud_id'=>$solicitud->id,'rfc'=>null]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
       
        // se crea parte solicitado
        // $parteSolicitado = factory(Parte::class)->states('solicitadoMoral')->create(['solicitud_id'=>$solicitud->id,'rfc'=>null]);
        // $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
        // $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
       
        $this->assertInstanceOf('\App\Solicitud',$solicitud);   
    }
}

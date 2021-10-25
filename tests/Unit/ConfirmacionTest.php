<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use App\User;

class ConfirmacionTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        //Creamos la solicitud
        $num = (int) env("PRUEBAS_CONFIRMACION",10);
        $solicitudes = factory(\App\Solicitud::class,$num)->create();
        $user = null;
        foreach($solicitudes as $solicitud){
            if($user == null){
                $user = User::whereCentroId($solicitud->centro_id)->whereHas('roles' ,function($q){
                    $q->where('name', 'Personal conciliador');
                })->first();
            }
            //Creamos al solicitante
            $solicitud->partes()->createMany(factory(\App\Parte::class,1)->states('solicitanteFisico')->make()->toArray());
            //Creamos al citado
            $solicitud->partes()->createMany(factory(\App\Parte::class,2)->states('solicitadoFisico')->make()->toArray());
            //Agregamos los domicilios
            foreach($solicitud->partes as $sol){
                $sol->domicilios()->createMany(factory(\App\Domicilio::class,1)->make()->toArray());
            }
    
            //Usuario a confirmar
    
            $response = $this->actingAs($user)->withSession(['foo' => 'bar'])->json('POST','solicitud/ratificar', array(
                'id' => $solicitud->id,
                'tipo_notificacion_id' => 2,
                'inmediata' => 'false',
                'fecha_cita' => '', 
                'url_virtual' => '',
                'separados' => 'false',
                'acepta_buzon' => 'true'
            ));
    
            $this->assertEquals(201, $response->getStatusCode());
        }
    }
}

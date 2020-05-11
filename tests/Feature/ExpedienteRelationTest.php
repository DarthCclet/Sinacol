<?php

namespace Tests\Feature;

use App\Audiencia;
use App\AudienciaParte;
use App\Compareciente;
use App\Conciliador;
use App\ConciliadorAudiencia;
use App\Contacto;
use App\DatoLaboral;
use App\Domicilio;
use App\Expediente;
use App\Parte;
use App\Sala;
use App\SalaAudiencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Traits\GenerateDocument;

class ExpedienteRelationTest extends TestCase
{
    use GenerateDocument;
   
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExpedienteFisicaFisica()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create();

        $solicitud->objeto_solicitudes()->sync([1]);
        // se crea parte solicitado
        $parteSolicitado = factory(Parte::class)->states('solicitadoFisico')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
        // se crea parte solicitado
        $parteSolicitante = factory(Parte::class)->states('solicitanteFisico')->create(['solicitud_id'=>$solicitud->id]);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);
        
        $year = date('Y');
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $year."/".sprintf("%06d", "123456");
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $year;
        $expediente->consecutivo = 1;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();
        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,]);
        $compareciente_audiencia = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $compareciente_audiencia1 = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
        // termina audiencia 
        // se genera documento
        $this->generarConstancia($audiencia->id,"audiencia",1);
        // termina documento
        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExpedienteFisicaMoral()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create();

        $solicitud->objeto_solicitudes()->sync([1]);

        //se crea parte solicitado
        $parteSolicitante = factory(Parte::class)->states('solicitanteMoral')->create(['solicitud_id'=>$solicitud->id]);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);

        // se crea parte solicitado
        $parteSolicitado = factory(Parte::class)->states('solicitadoFisico')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
        
        $year = date('Y');
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $year."/".sprintf("%06d", "123456");
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $year;
        $expediente->consecutivo = 1;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();

        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,'resolucion_id'=>1]);
        $compareciente_audiencia = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $compareciente_audiencia1 = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
        // Se agrega representante legal
        $representante_legal = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitante->id]);
        // representante legal
        // termina audiencia 
        // se genera documento
        $this->generarConstancia($audiencia->id,"audiencia",1);
        // termina documento

        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExpedienteMoralMoral()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create();

        $solicitud->objeto_solicitudes()->sync([1]);

        //se crea parte solicitado
        $parteSolicitante = factory(Parte::class)->states('solicitanteMoral')->create(['solicitud_id'=>$solicitud->id]);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);
        
        // se crea parte solicitado
        $parteSolicitado = factory(Parte::class)->states('solicitadoMoral')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
        
        $year = date('Y');
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $year."/".sprintf("%06d", "123456");
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $year;
        $expediente->consecutivo = 1;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();

        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,'resolucion_id'=>2]);
        $compareciente_audiencia = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $compareciente_audiencia1 = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
        // Se agrega representante legal
        $representante_legal_solicitante = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitante->id]);
        $representante_legal_solicitado = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitado->id]);
        // representante legal
        // termina audiencia 
        // se genera documento
        $this->generarConstancia($audiencia->id,"audiencia",1);
        // termina documento

        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }
    // /**
    //  * A basic feature test example.
    //  *
    //  * @return void
    //  */
    public function testExpedienteFisicasMorales()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create();

        $solicitud->objeto_solicitudes()->sync([1]);

        //se crea parte solicitado
        $parteSolicitante = factory(Parte::class)->states('solicitanteFisico')->create(['solicitud_id'=>$solicitud->id]);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);
        
        $parteSolicitante2 = factory(Parte::class)->states('solicitanteFisico')->create(['solicitud_id'=>$solicitud->id]);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante2->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante2->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante2->id, 'contactable_type'=>'App\Parte']);
        
        // se crea parte solicitado
        $parteSolicitado = factory(Parte::class)->states('solicitadoMoral')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
        $parteSolicitado2 = factory(Parte::class)->states('solicitadoMoral')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado2->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado2->id, 'contactable_type'=>'App\Parte']);
        
        $year = date('Y');
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $year."/".sprintf("%06d", "123456");
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $year;
        $expediente->consecutivo = 1;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();

        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitado->id,'resolucion_id'=>4]);
        $compareciente_audiencia = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $compareciente_audiencia = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado2->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado2->id,]);
        $compareciente_audiencia1 = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $compareciente_audiencia1 = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante2->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante2->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
        // Se agrega representante legal
        $representante_legal_solicitado = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitado->id]);
        $representante_legal_solicitado = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitado2->id]);
        // representante legal
        // termina audiencia 
        // se genera documento
        $this->generarConstancia($audiencia->id,"audiencia",1);
        // termina documento

        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }
    // /**
    //  * A basic feature test example.
    //  *
    //  * @return void
    //  */
    public function testExpedienteMoralesFisicas()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create();

        $solicitud->objeto_solicitudes()->sync([1]);

        //se crea parte solicitado
        $parteSolicitante = factory(Parte::class)->states('solicitanteMoral')->create(['solicitud_id'=>$solicitud->id]);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);
        
        $parteSolicitante2 = factory(Parte::class)->states('solicitanteMoral')->create(['solicitud_id'=>$solicitud->id]);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante2->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante2->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante2->id, 'contactable_type'=>'App\Parte']);
        
        // se crea parte solicitado
        $parteSolicitado = factory(Parte::class)->states('solicitadoFisico')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
        $parteSolicitado2 = factory(Parte::class)->states('solicitadoFisico')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado2->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado2->id, 'contactable_type'=>'App\Parte']);
        
        $year = date('Y');
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $year."/".sprintf("%06d", "123456");
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $year;
        $expediente->consecutivo = 1;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();

        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,]);
        $compareciente_audiencia = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $compareciente_audiencia = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado2->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado2->id,]);
        $compareciente_audiencia1 = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $compareciente_audiencia1 = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante2->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante2->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
        // Se agrega representante legal
        $representante_legal_solicitante = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitante->id]);
        $representante_legal_solicitante = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitante2->id]);
        // representante legal
        // termina audiencia 
        // se genera documento
        $this->generarConstancia($audiencia->id,"audiencia",1);
        // termina documento

        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }
}

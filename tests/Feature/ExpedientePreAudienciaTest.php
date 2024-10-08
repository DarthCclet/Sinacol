<?php

namespace Tests\Feature;

use App\Audiencia;
use App\AudienciaParte;
use App\Conciliador;
use App\ConciliadorAudiencia;
use App\Contacto;
use App\DatoLaboral;
use App\Domicilio;
use App\Events\GenerateDocumentResolution;
use App\Expediente;
use App\Parte;
use App\Sala;
use App\SalaAudiencia;
use App\Http\Controllers\ContadorController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpedientePreAudienciaTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExpedienteFisicaFisica()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>2]);

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
        
        $ContadorController = new ContadorController();
        $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $folioC->anio;
        $expediente->consecutivo = $folioC->contador;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();
        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,'finalizada' =>false]);
        event(new GenerateDocumentResolution($audiencia->id,$expediente->solicitud_id,4,4));
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
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
        $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>2]);

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
        
        $ContadorController = new ContadorController();
        $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $folioC->anio;
        $expediente->consecutivo = $folioC->contador;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();

        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,'finalizada' =>false]);
        event(new GenerateDocumentResolution($audiencia->id,$expediente->solicitud_id,4,4));
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
        // Se agrega representante legal
        $representante_legal = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitante->id]);
        $audiencia_parte_solicitante_rep = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $representante_legal->id,]);

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
        $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>2]);

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
        
        $ContadorController = new ContadorController();
        $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $folioC->anio;
        $expediente->consecutivo = $folioC->contador;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();

        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,'finalizada' =>false]);
        event(new GenerateDocumentResolution($audiencia->id,$expediente->solicitud_id,4,4));
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
        // Se agrega representante legal
        $representante_legal_solicitante = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitante->id]);
        $audiencia_parte_solicitante_rep = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $representante_legal_solicitante->id,]);
        $representante_legal_solicitado = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitado->id]);
        $audiencia_parte_solicitante_rep = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $representante_legal_solicitado->id,]);

        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExpedienteFisicasMorales()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>2]);

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
        
        $ContadorController = new ContadorController();
        $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $folioC->anio;
        $expediente->consecutivo = $folioC->contador;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();

        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitado->id,'finalizada' =>false]);
        event(new GenerateDocumentResolution($audiencia->id,$expediente->solicitud_id,4,4));
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado2->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante2->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
        // Se agrega representante legal
        $representante_legal_solicitado = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitado->id]);
        $audiencia_parte_solicitante_rep = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $representante_legal_solicitado->id,]);
        $representante_legal_solicitado2 = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitado2->id]);
        $audiencia_parte_solicitante_rep = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $representante_legal_solicitado2->id,]);
        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExpedienteMoralesFisicas()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>2]);

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
        
        $ContadorController = new ContadorController();
        $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $folioC->anio;
        $expediente->consecutivo = $folioC->contador;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();

        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,'finalizada' =>false]);
        event(new GenerateDocumentResolution($audiencia->id,$expediente->solicitud_id,4,4));
        // Se agrega representante legal
            $representante_legal_solicitante = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitante->id]);
            // $contactoRepSolicitante2 = factory(Contacto::class)->create(['contactable_id'=>$representante_legal_solicitante->id, 'contactable_type'=>'App\Parte']);
            $representante_legal_solicitante2 = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitante2->id]);
            // $contactoRepSolicitante2 = factory(Contacto::class)->create(['contactable_id'=>$representante_legal_solicitante2->id, 'contactable_type'=>'App\Parte']);
            $audiencia_parte_solicitante_rep = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $representante_legal_solicitante->id,]);
            $audiencia_parte_solicitante_rep = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $representante_legal_solicitante2->id,]);
        // representante legal
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado2->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante2->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
        
        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }
     /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExpedienteFisicaMoralFisica()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>2]);

        $solicitud->objeto_solicitudes()->sync([1]);

        //se crea parte solicitado
        $parteSolicitante = factory(Parte::class)->states('solicitanteFisico')->create(['solicitud_id'=>$solicitud->id]);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);
        
        // se crea parte solicitado
        $parteSolicitado = factory(Parte::class)->states('solicitadoFisico')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
        $parteSolicitado2 = factory(Parte::class)->states('solicitadoMoral')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado2->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado2->id, 'contactable_type'=>'App\Parte']);
        
        $ContadorController = new ContadorController();
        $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $folioC->anio;
        $expediente->consecutivo = $folioC->contador;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();

        //Se captura Audiencia
        $sala = factory(Sala::class)->create();
        $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,'finalizada' =>false]);
        event(new GenerateDocumentResolution($audiencia->id,$expediente->solicitud_id,4,4));
        // Se agrega representante legal
            $representante_legal_solicitante = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitado2->id]);
            $contactoRepSolicitante2 = factory(Contacto::class)->create(['contactable_id'=>$representante_legal_solicitante->id, 'contactable_type'=>'App\Parte']);
            $audiencia_parte_solicitante_rep = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $representante_legal_solicitante->id,]);
        // representante legal
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
    
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado2->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);

        
        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExpedienteFisicaFisicaMultiple()
    {
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>2]);

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
        
        $ContadorController = new ContadorController();
        $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $folioC->anio;
        $expediente->consecutivo = $folioC->contador;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();
        //Se captura Audiencia
        $audiencia = factory(Audiencia::class)->states('audienciaMultiple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,'finalizada' =>false]);
        event(new GenerateDocumentResolution($audiencia->id,$expediente->solicitud_id,4,4));
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        //Se asigna sala multiple
        $sala = factory(Sala::class)->create();
        $sala2 = factory(Sala::class)->create();
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,'solicitante'=>true]);
        $sala_audiencia2 = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala2->id,'solicitante'=>false]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador2 = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,'solicitante'=>true]);
        $conciliador_audiencia2 = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador2->id,'solicitante'=>false]);
        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExpedienteFisicaMoralesMultiple()
    {
         // se llama el factory de solicitud para crear un registro y probar su relacion
        // se llama el factory de solicitud para crear un registro y probar su relacion
        $solicitud = factory(\App\Solicitud::class)->create(['estatus_solicitud_id'=>2]);

        $solicitud->objeto_solicitudes()->sync([1]);

        //se crea parte solicitado
        $parteSolicitante = factory(Parte::class)->states('solicitanteFisico')->create(['solicitud_id'=>$solicitud->id]);
        factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
        factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
        factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);

        // se crea parte solicitado
        $parteSolicitado = factory(Parte::class)->states('solicitadoMoral')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
        $parteSolicitado2 = factory(Parte::class)->states('solicitadoMoral')->create(['solicitud_id'=>$solicitud->id]);
        $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado2->id, 'domiciliable_type'=>'App\Parte']);
        $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado2->id, 'contactable_type'=>'App\Parte']);
        
        $ContadorController = new ContadorController();
        $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
        $edo_folio = $solicitud->centro->abreviatura;
        $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
        $expediente = new Expediente();
        $expediente->folio = $folio;
        $expediente->anio = $folioC->anio;
        $expediente->consecutivo = $folioC->contador;
        $expediente->solicitud_id = $solicitud->id;
        $expediente->save();

        //Se captura Audiencia
        $audiencia = factory(Audiencia::class)->states('audienciaMultiple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,'finalizada' =>false]);
        event(new GenerateDocumentResolution($audiencia->id,$expediente->solicitud_id,4,4));
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
        $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado2->id,]);
        $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
        // se asigna sala multiple
        $sala = factory(Sala::class)->create();
        $sala2 = factory(Sala::class)->create();
        $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,'solicitante'=>true]);
        $sala_audiencia2 = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala2->id,'solicitante'=>false]);
        $conciliador = factory(Conciliador::class)->create();
        $conciliador2 = factory(Conciliador::class)->create();
        $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,'solicitante'=>true]);
        $conciliador_audiencia2 = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador2->id,'solicitante'=>false]);
        // Se agrega representante legal
        $representante_legal = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitado->id]);
        $audiencia_parte_solicitante_rep = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $representante_legal->id,]);
        $representante_legal2 = factory(Parte::class)->states('representanteLegal')->create(['solicitud_id'=>$solicitud->id,'parte_representada_id'=>$parteSolicitado2->id]);
        $audiencia_parte_solicitante_rep = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $representante_legal2->id,]);
        // representante legal

        $this->assertInstanceOf('\App\Solicitud',$expediente->solicitud);   
    }
    
}

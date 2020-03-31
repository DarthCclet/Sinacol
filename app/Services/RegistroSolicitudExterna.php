<?php

namespace App\Services;

use App\Audiencia;
use App\Exceptions\FechaInvalidaException;
use App\Exceptions\ParametroNoValidoException;
use App\Http\Controllers\ContadorController;
use App\DatoLaboral;
use App\ObjetoSolicitud;
use App\Parte;
use App\Solicitud;
use App\TipoParte;
use Carbon\Carbon;

/**
 * Operaciones para el registro de solicitudes de conciliación
 * Class RegistroSolicitudExterna
 * @package App\Services
 */
class RegistroSolicitudExterna
{
    public function registro($datosSolicitud, $limit=15, $page=1)
    {
        //Obtenemos el contador
        $ContadorController = new ContadorController();
        $folio = $ContadorController->getContador(1,$datosSolicitud->centro_id);
        $solicitud = array();
        
        // Construimos la solicitud
        $solicitud['user_id'] = 1;
        $solicitud['estatus_solicitud_id'] = 1;
        $solicitud['folio'] = $folio->contador;
        $solicitud['anio'] = $folio->anio;
        $solicitud['centro_id'] = $datosSolicitud->centro_id;
        $solicitud['ratificada'] = false;
        $solicitud['solicita_excepcion'] = false;
        $solicitud['fecha_recepcion'] = date("Y-m-d");
        $solicitud['fecha_conflicto'] = $datosSolicitud->fecha_conflicto;
        $solicitud['observaciones'] = $datosSolicitud->observaciones;
        
        
        
        $solicitudSaved = Solicitud::create($solicitud);
        // Recorremos todos los objetos de la solicitus
        foreach($datosSolicitud->objetos_solicitud as $objeto_id){
            $solicitudSaved->objeto_solicitudes()->attach($objeto_id);
        }
        
        // Recorremos los actores
        $partes = array_merge($datosSolicitud->parte_actora, $datosSolicitud->parte_demandada);
        foreach($partes as $parte){
            $parteArray = array();
            $parteArray["tipo_parte_id"] = $parte->tipo_parte_id;
            $parteArray["tipo_persona_id"] = $parte->tipo_persona_id;
            $parteArray["nombre"] = $parte->nombre;
            $parteArray["primer_apellido"] = $parte->primer_apellido;
            $parteArray["segundo_apellido"] = $parte->segundo_apellido;
            $parteArray["nombre_comercial"] = $parte->denominacion;
            $parteArray["curp"] = $parte->curp;
            $parteArray["rfc"] = $parte->rfc;
            $parteArray["fecha_nacimiento"] = $parte->fecha_nacimiento;
            $parteArray["giro_comercial_id"] = $parte->giro_comercial_id;
            $parteArray["nacionalidad_id"] = $parte->nacionalidad_id;
            $parteArray["entidad_nacimiento_id"] = $parte->entidad_nacimiento_id;
            $parteArray["solicitud_id"] = $solicitudSaved->id;
            $parteSaved = Parte::create($parteArray);
            // Guardamos el domicilio
            foreach($parte->domicilios as $domicilio){
                $domicilio->tipo_vialidad = "as";
                $domicilio->estado = "as";
                $domicilioSaved = $parteSaved->domicilios()->create((array)$domicilio);
            }
            // validamos si es solicitante para guardar sus datos laborales
            if($parte->tipo_parte_id == 1 && isset($parte->datos_laborales) && $parte->datos_laborales != null){
                $datosLaborales = $parte->datos_laborales;
                $datosLaborales->parte_id = $parteSaved->id;
                DatoLaboral::create((array)$datosLaborales);
            }
        }
        $respuesta = array();
        $respuesta["folio_solicitud"] = $solicitudSaved->folio;
        $respuesta["anio"] = $solicitudSaved->anio;
        $respuesta["fecha_recepcion"] = $solicitudSaved->fecha_recepcion;
        return $respuesta;
    }
    public function validaEstructuraParametros($params)
    {
        $paramsJSON = json_decode($params);
        if($paramsJSON === NULL){
            throw new ParametroNoValidoException("Los datos enviados no pueden interpretarse como una estructura JSON válida, favor de revisar.", 1000);
            return null;
        }
        // validamos la fecha del conflicto
        if(!isset($paramsJSON->fecha_conflicto) || !$paramsJSON->fecha_conflicto ){
            throw new ParametroNoValidoException("La fecha del conflicto es requerida.", 1020);
            return null;
        }
        // validamos el centro
        if(!isset($paramsJSON->centro_id) || !$paramsJSON->centro_id ){
            throw new ParametroNoValidoException("El centro de conciliación es requerido.", 1020);
            return null;
        }
        // validamos si vienen objetos en la solicitud
        if(!isset($paramsJSON->objetos_solicitud) || !$paramsJSON->objetos_solicitud || !count($paramsJSON->objetos_solicitud) ){
            throw new ParametroNoValidoException("Se debe agregar al menos un objeto de la solicitud.", 1020);
            return null;
        }
        
        // validar parte_actora
        if(!isset($paramsJSON->parte_actora) || !$paramsJSON->parte_actora || !count($paramsJSON->parte_actora) ){
            throw new ParametroNoValidoException("Se debe agregar al menos una parte actora.", 1020);
            return null;
        }else{
            $parteActora = [];
            foreach($paramsJSON->parte_actora as $parte_actora){
                $parteActora[]= $this->validarParte($parte_actora,true);
            }
            $paramsJSON->parte_actora = $parteActora;
        }
        // validar parte_actora
        if(!isset($paramsJSON->parte_demandada) || !$paramsJSON->parte_demandada || !count($paramsJSON->parte_demandada) ){
            throw new ParametroNoValidoException("Se debe agregar al menos una parte demandada.", 1020);
            return null;
        }else{
            $parteDemandada = [];
            foreach($paramsJSON->parte_demandada as $parte_demandada){
                $parteDemandada[]= $this->validarParte($parte_demandada,false);
            }
            $paramsJSON->parte_demandada = $parteDemandada;
        }
        return $paramsJSON;
    }
    private function validarParte($parte,$actor){
        //Buscamos el tipo de persona
//        dd($parte);
        if(isset($parte->tipo_persona_id) && $parte->tipo_persona_id == 1){
//            $parte->caracter_persona = 'FISICA';
            if(!isset($parte->nombre) || !trim($parte->nombre)){
                throw new ParametroNoValidoException("El nombre de la parte es requerido.", 1020);
                return null;
            }
            if(!isset($parte->primer_apellido) || !trim($parte->primer_apellido)){
                throw new ParametroNoValidoException("El primer apellido de la parte es requerido.", 1021);
                return null;
            }
            if(!isset($parte->segundo_apellido) || !trim($parte->segundo_apellido)){
                throw new ParametroNoValidoException("El parámetro del segundo apellido es requerido.", 1022);
                return null;
            }
            if(!isset($parte->curp) || !trim($parte->curp)){
                throw new ParametroNoValidoException("La clave curp es requerida para personas físicas.", 1023);
                return null;
            }
            if(!isset($parte->fecha_nacimiento) || !trim($parte->fecha_nacimiento)){
                throw new ParametroNoValidoException("La fecha de nacimiento es requerida para personas físicas.", 1024);
                return null;
            }
            if(!isset($parte->edad) || !trim($parte->edad)){
                throw new ParametroNoValidoException("La edad es requerida para personas físicas.", 1025);
                return null;
            }
            if(!isset($parte->genero) || !trim($parte->genero)){
                throw new ParametroNoValidoException("El genero es requerido para personas físicas.", 1026);
                return null;
            }
            if(!isset($parte->entidad_nacimiento_id) || !trim($parte->entidad_nacimiento_id)){
                throw new ParametroNoValidoException("El estado de nacimiento es requerido para personas físicas.", 1029);
                return null;
            }
            $parte->domicilio = $this->validarDomicilio($parte->domicilio);
        }else if(isset($parte->tipo_persona_id) && $parte->tipo_persona_id == 2){
            $parte->primer_apellido = '';
            $parte->segundo_apellido = '';
            $parte->nombre = '';
            $parte->fecha_nacimiento = null;
            if(!isset($parte->denominacion) || !trim($parte->denominacion)){
                throw new ParametroNoValidoException("La denominación o razón social de la persona moral es requerido.", 1020);
                return null;
            }
            if(!isset($parte->rfc) || !trim($parte->rfc)){
                throw new ParametroNoValidoException("El rfc de la persona moral es requerido.", 1021);
                return null;
            }
            if(!isset($parte->giro_comercial_id) || !trim($parte->giro_comercial_id)){
                throw new ParametroNoValidoException("El giro es requerido para personas morales.", 1027);
                return null;
            }
            $parte->domicilios = $this->validarDomicilio($parte->domicilios);
        }else{
            throw new ParametroNoValidoException("Agrega un tipo de persona valido.", 1020);
            return null;
        }
        if(!isset($parte->nacionalidad_id) || !trim($parte->nacionalidad_id)){
            throw new ParametroNoValidoException("La nacionalidad es requerida.", 1028);
            return null;
        }
        if($actor){
            $parte->datos_laborales = $this->validarLaborales($parte->datos_laborales);
            $parte->tipo_parte_id = 1;
        }else{
            $parte->tipo_parte_id = 2;
            
        }
        return $parte;
    }
    private function validarDomicilio($domicilios){
        if(!isset($domicilios) || !count($domicilios) ){
            throw new ParametroNoValidoException("Todas las partes deben incluir domicilio.", 1020);
            return null;
        }else{
            $domiciliosNew = [];
            foreach($domicilios as $domicilio){
                if(!isset($domicilio->estado_id) || !trim($domicilio->estado_id) ){
                    throw new ParametroNoValidoException("El estado es obligatorio para los domicios.", 1020);
                    return null;
                }
                if(!isset($domicilio->municipio) || !trim($domicilio->municipio) ){
                    throw new ParametroNoValidoException("El municipio es obligatorio para los domicios.", 1020);
                    return null;
                }
                if(!isset($domicilio->cp) || !trim($domicilio->cp) ){
                    throw new ParametroNoValidoException("El codigo postal es obligatorio para los domicios.", 1020);
                    return null;
                }
                if(!isset($domicilio->tipo_vialidad_id) || !trim($domicilio->tipo_vialidad_id) ){
                    throw new ParametroNoValidoException("El tipo de vialidad es obligatorio para los domicios.", 1020);
                    return null;
                }
                if(!isset($domicilio->num_ext) || !trim($domicilio->num_ext) ){
                    throw new ParametroNoValidoException("El número exterior es obligatorio para los domicios.", 1020);
                    return null;
                }
                if(!isset($domicilio->num_int)){
                    throw new ParametroNoValidoException("El numero interior es obligatorio, aunque este vacio.", 1020);
                    return null;
                }
                if(!isset($domicilio->tipo_asentamiento_id) || !trim($domicilio->tipo_asentamiento_id) ){
                    throw new ParametroNoValidoException("El tipo de asentamiento es obligatorio para los domicios.", 1020);
                    return null;
                }
                if(!isset($domicilio->asentamiento) || !trim($domicilio->asentamiento) ){
                    throw new ParametroNoValidoException("El asentamiento es obligatorio para los domicios.", 1020);
                    return null;
                }
                if(!isset($domicilio->entre_calle1) || !trim($domicilio->entre_calle1) ){
                    throw new ParametroNoValidoException("Entre calle 1 es obligatorio para los domicios.", 1020);
                    return null;
                }
                if(!isset($domicilio->entre_calle2) || !trim($domicilio->entre_calle2) ){
                    throw new ParametroNoValidoException("Entre calle 2 es obligatorio para los domicios.", 1020);
                    return null;
                }
                if(!isset($domicilio->referencias) || !trim($domicilio->referencias) ){
                    throw new ParametroNoValidoException("Las referencias son obligatorio para los domicios.", 1020);
                    return null;
                }
                $domiciliosNew[]=$domicilio;
            }
            return $domiciliosNew;
        }
    }
    private function validarLaborales($datos_laborales){
        if(!isset($datos_laborales)){
            throw new ParametroNoValidoException("TLos actores deben incluir su información laboral.", 1020);
            return null;
        }else{
            if(!isset($datos_laborales->nombre_jefe_directo) || !trim($datos_laborales->nombre_jefe_directo)){
                throw new ParametroNoValidoException("El jefe directo es obligatorio para los datos laborales.", 1021);
                return null;
            }
            if(!isset($datos_laborales->ocupacion_id) || !trim($datos_laborales->ocupacion_id)){
                throw new ParametroNoValidoException("La ocupación es obligatoria para los datos laborales.", 1021);
                return null;
            }
            if(!isset($datos_laborales->percepcion_mensual_neta) || !trim($datos_laborales->percepcion_mensual_neta)){
                throw new ParametroNoValidoException("La percepción mensual neta es obligatoria para los datos laborales.", 1021);
                return null;
            }
            if(!isset($datos_laborales->percepcion_mensual_bruta) || !trim($datos_laborales->percepcion_mensual_bruta)){
                throw new ParametroNoValidoException("La percepción mensual bruta es obligatoria para los datos laborales.", 1021);
                return null;
            }
            if(!isset($datos_laborales->labora_actualmente) || !trim($datos_laborales->labora_actualmente)){
                throw new ParametroNoValidoException("Se debe indicar si aun se labora para los datos laborales.", 1021);
                return null;
            }
            if(!isset($datos_laborales->fecha_ingreso) || !trim($datos_laborales->fecha_ingreso)){
                throw new ParametroNoValidoException("La fecha de ingreso es obligatoria para los datos laborales.", 1021);
                return null;
            }
            if(!isset($datos_laborales->fecha_salida)){
                throw new ParametroNoValidoException("La fecha de salida para los datos laborales es necesaria, aunque puede estar vacia.", 1021);
                return null;
            }
            if(!isset($datos_laborales->jornada_id) || !trim($datos_laborales->jornada_id)){
                throw new ParametroNoValidoException("El turno(jornada) es obligatorio para los datos laborales.", 1021);
                return null;
            }
            if(!isset($datos_laborales->horas_semanales) || !trim($datos_laborales->horas_semanales)){
                throw new ParametroNoValidoException("Las horas semanales son obligatorias para los datos laborales.", 1021);
                return null;
            }
            return $datos_laborales;
        }
    }
}
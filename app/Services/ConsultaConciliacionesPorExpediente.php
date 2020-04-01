<?php


namespace App\Services;

use App\Audiencia;
use App\Exceptions\FechaInvalidaException;
use App\Exceptions\ParametroNoValidoException;
use App\Expediente;
use App\Parte;
use App\Solicitud;
use App\TipoParte;
use App\Traits\Transformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Operaciones para la consulta de expedientes por rango de fechas
 * Class ConsultaConciliacionesPorRangoFechas
 * @package App\Services
 */
class ConsultaConciliacionesPorExpediente
{
    use Transformer;

    public function consulta($parametro, $resolucion_id, $limit=15, $page=1)
    {
        $expediente = Expediente::where('folio','ilike', $parametro)->first();
        if(!$expediente) {
            return ['data'=>[]];
        }
        $audiencia = $expediente->audiencia()->where('resolucion_id', $resolucion_id)->first();
        if(!$audiencia) {
            return ['data'=>[]];
        }

        //TODO: Extraer todos los involucrados, si hay mas solicitados o solicitantes hay que ponerlos todos aqui: (Atendido)
        //TODO: Integrar en la estructura los datos laborales de cada demandante o solicitante (Atendido)
        //TODO: Integrar los datos telefónicos y de contacto de las personas en la estructura que se responde (Revizar)
        $partes = $expediente->solicitud->partes;
        $parte_demandada = $this->partesTransformer($partes, 'solicitado', true);
        $parte_actora= $this->partesTransformer($partes, 'solicitante', true);

        $res[] = [
            'numero_expediente_oij' => $audiencia->expediente->folio,
            'fecha_audiencia' => $audiencia->fecha_audiencia,
            'fecha_conflicto' => $audiencia->expediente->solicitud->fecha_conflicto,
            'fecha_ratificacion' => $audiencia->expediente->solicitud->fecha_ratificacion,
            'organo_impartidor_de_justicia' => $audiencia->expediente->solicitud->centro->id,
            'organo_impartidor_de_justicia_nombre' => $audiencia->expediente->solicitud->centro->nombre,
            'actores' => [$parte_actora],
            'demandados' => [$parte_demandada],
        ];

        //TODO: Mandar llamar el documento real relacionado con el registro (Atendido).
        //TODO: Firma de documentos (PEndiente)
        //TODO: Implementar el catálogo de clasificación de archivo (Pendiente).
        if(Storage::disk('local')->exists('Prueba.pdf')){
            $contents = base64_encode(Storage::get('Prueba.pdf'));
            $info = pathinfo('Prueba.pdf');
            $size = Storage::size('Prueba.pdf');
            return [
                'data' => $res,
                'documento' => [
                    'documento_id' => 1553,
                    'nombre' => $info["basename"],
                    'extension' => $info["extension"],
                    'filebase64'=> $contents,
                    'longitud' => $size,
                    'firmado' => 0,
                    'pkcs7base64' => "",
                    'fecha_firmado' => '',
                    'clasificacion_archivo' => 1
                ]
            ];
        }else{
            return [
                'data' => $res,
                'documento' => []
            ];
        }
        
    }
    
    /**
     * Transforma los datos de las partes
     * @param $datos
     * @param $parte
     * @param bool $domicilio
     * @return array
     */
    public function partesTransformer($datos, $parte, $domicilio = false)
    {
        $array = array();
        $parteCat = TipoParte::where('nombre', 'ilike', $parte)->first();
        $personas =  $datos->where('tipo_parte_id', $parteCat->id);
        $resultado = [];
        foreach($personas as $persona){
            if($persona->tipoPersona->abreviatura == 'F'){
                $resultado = [
                    'nombre' => $persona->nombre,
                    'primer_apellido' => $persona->primer_apellido,
                    'segundo_apellido' => $persona->segundo_apellido,
                    'rfc' => $persona->rfc,
                    'curp' => $persona->curp,
                    'caracter_persona' => $persona->tipoPersona->nombre,
                    'solicita_traductor' => $persona->solicita_traductor,
                    'lenguaIndigena' => $persona->lenguaIndigena->nombre,
                    'padece_discapacidad' => $persona->padece_discapacidad,
                    'discapacidad' => $persona->tipoDiscapacidad->nombre,
                    'publicacion_datos' => $persona->publicacion_datos,
                    'domicilios' => $this->domiciliosTransformer($persona->domicilios),
                    'contactos' => $this->contactoTransformer($persona->contactos),
                    'dato_laboral' => $persona->dato_laboral
                ];
            }
            if($persona->tipoPersona->abreviatura == 'M'){
                $resultado = [
                    'denominacion' => $persona->nombre_comercial,
                    'rfc' => $persona->rfc,
                    'caracter_persona' => $persona->tipoPersona->nombre,
                    'solicita_traductor' => $persona->solicita_traductor,
                    'lenguaIndigena' => $persona->lenguaIndigena->nombre,
                    'padece_discapacidad' => false,
                    'discapacidad' => 'N/A',
                    'publicacion_datos' => $persona->publicacion_datos,
                    'domicilios' => $this->domiciliosTransformer($persona->domicilios),
                    'contactos' => $this->contactoTransformer($persona->contactos),
                    'dato_laboral' => $persona->dato_laboral
                ];
            }
            if(!$domicilio){
                unset($resultado['domicilios']);
            }
            
        }

        return $resultado;
    }

    /**
     * Valida la estructura de los parametros eviados en el post
     * @param $params
     * @return mixed|null
     * @throws ParametroNoValidoException
     */
    public function validaEstructuraParametros($params)
    {
        $paramsJSON = json_decode($params);
        if($paramsJSON === NULL){
            throw new ParametroNoValidoException("Los datos enviados no pueden interpretarse como una estructura JSON válida, favor de revisar.", 1000);
            return null;
        }
        if(!isset($paramsJSON->expediente) || !$paramsJSON->expediente ){
            throw new ParametroNoValidoException("El número de expediente es requerido.", 1040);
            return null;
        }
        //TODO: Validar la estructura del expediente que sea conformante y emitir excepción de lo contrario
        return $paramsJSON;
    }
    public function contactoTransformer($datos){
        $contacto = [];
        foreach($datos as $contact){
            $contacto[] = [
                'tipo_contacto' => $contact->tipo_contacto->nombre,
                'contacto' => $contact->contacto
            ];
        }
        return $contacto;
    }

}

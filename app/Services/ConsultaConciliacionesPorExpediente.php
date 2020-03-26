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

        //TODO: Extraer todos los involucrados, si hay mas solicitados o solicitantes hay que ponerlos todos aqui:
        //TODO: Integrar en la estructura los datos laborales de cada demandante o solicitante
        //TODO: Integrar los datos telefónicos y de contacto de las personas en la estructura que se responde

        $parte_actora = $this->partesTransformer($audiencia->expediente->solicitud->partes, 'solicitante', true);
        $parte_demandada = $this->partesTransformer($audiencia->expediente->solicitud->partes, 'solicitado', true);

        $res[] = [
            'numero_expediente_oij' => $audiencia->expediente->folio,
            'fecha_audiencia' => $audiencia->fecha_audiencia,
            'organo_impartidor_de_justicia' => $audiencia->expediente->solicitud->centro->id,
            'organo_impartidor_de_justicia_nombre' => $audiencia->expediente->solicitud->centro->nombre,
            'actores' => [$parte_actora],
            'demandados' => [$parte_demandada],
        ];

        //TODO: Mandar llamar el documento real relacionado con el registro.
        //TODO: Firma de documentos
        //TODO: Implementar el catálogo de clasificación de archivo.
        return [
            'data' => $res,
            'documento' => [
                'documento_id' => 1553,
                'nombre' => "ConstanciaNoConciliacion2020000001201.pdf",
                'extension' => "pdf",
                'filebase64'=> "JVBERi0xLjUKJcOkw7zDtsOfCjIgMCBvYmoKPDwvTGVuZ3RoIDMgMCBSL0ZpbHRlci9GbGF0ZURlY29kZT....",
                'longitud' => '30541',
                'firmado' => 0,
                'pkcs7base64' => "",
                'fecha_firmado' => '',
                'clasificacion_archivo' => 1
            ]
        ];
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

}

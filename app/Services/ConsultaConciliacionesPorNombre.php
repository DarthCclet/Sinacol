<?php


namespace App\Services;

use App\Audiencia;
use App\Exceptions\FechaInvalidaException;
use App\Parte;
use App\TipoParte;
use Carbon\Carbon;

/**
 * Operaciones para la consulta de expedientes por rango de fechas
 * Class ConsultaConciliacionesPorRangoFechas
 * @package App\Services
 */
class ConsultaConciliacionesPorNombre
{
    public function consulta($nombre, $primer_apellido, $segundo_apellido, $tipo_persona, $tipo_parte, $limit=15, $page=1)
    {
        $partes = [];
        if($tipo_persona == 1) {
            $partes = Parte::where('nombre', 'ilike', $nombre)
                ->where('primer_apellido', 'ilike', $primer_apellido)
                ->where('tipo_parte_id', $tipo_parte)
                ->where('segundo_apellido', 'ilike', $segundo_apellido)->get();
        }
        if($tipo_persona == 2){
            $partes = Parte::where('nombre_comercial', 'ilike', mb_strtoupper($nombre))->get();
        }

        foreach($partes as $parte){
            $audiencias = $parte->solicitud->expediente->audiencia()->paginate();
        }

        $res = [];

        foreach ($audiencias as $audiencia){
            $parte_actora = $this->partesTransformer($audiencia->expediente->solicitud->partes, 'solicitante');
            $parte_demandada = $this->partesTransformer($audiencia->expediente->solicitud->partes, 'solicitado');
            $res[] = [
                'numero_expediente_oij' => $audiencia->expediente->folio,
                'fecha_audiencia' => $audiencia->fecha_audiencia,
                'organo_impartidor_de_justicia' => $audiencia->expediente->solicitud->centro->id,
                'organo_impartidor_de_justicia_nombre' => $audiencia->expediente->solicitud->centro->nombre,
                'parte_actora' => $parte_actora,
                'parte_demandada' => $parte_demandada,
            ];
        }

        return [
            'data' => $res,
            'total' => $audiencias->total(),
            'per_page' => $audiencias->perPage(),
            'current_page' => $audiencias->currentPage(),
            'last_page' => $audiencias->lastPage(),
            'has_more_pages' => $audiencias->hasMorePages(),
            'previous_page_url' => $audiencias->previousPageUrl(),
            'next_page_url' => $audiencias->nextPageUrl(),
            'url' => $audiencias->url($audiencias->currentPage()),
        ];
    }

    public function validaFechas($fecha)
    {
        //Se espera que la fecha venga en epoch milisegundos con timezone
        $match = [];
        if(preg_match("/(\d+)(\D{1})(\d+)/", $fecha, $match)){

            try {
                if(strlen($match[1]) == 13)
                    return Carbon::createFromTimestampMs($match[1], $match[2].$match[3]);
                elseif(strlen($match[1]) == 10)
                    return Carbon::createFromTimestamp($match[1], $match[2].$match[3]);
                else
                    throw new FechaInvalidaException("La fecha $fecha no es válida");
            }catch (\Exception $e){
                throw new FechaInvalidaException("La fecha $fecha no es válida");
            }

        }else {
            throw new FechaInvalidaException("La fecha $fecha no es válida");
        }
    }

    public function partesTransformer($datos, $parte, $domicilio = false)
    {
        $parteCat = TipoParte::where('nombre', 'ilike', $parte)->first();
        $persona =  $datos->where('tipo_parte_id', $parteCat->id)->first();

        $resultado = [];
        if($persona->tipoPersona->abreviatura == 'F'){
            $resultado = [
                'nombre' => $persona->nombre,
                'primer_apellido' => $persona->primer_apellido,
                'segundo_apellido' => $persona->segundo_apellido,
                'rfc' => $persona->rfc,
                'curp' => $persona->curp,
                'caracter_persona' => $persona->tipoPersona->nombre,
                'domicilios' => $this->domiciliosTransformer($persona->domicilios)
            ];
        }
        if($persona->tipoPersona->abreviatura == 'M'){
            $resultado = [
                'denominacion' => $persona->nombre,
                'rfc' => $persona->rfc,
                'caracter_persona' => $persona->tipoPersona->nombre,
                'domicilios' => $this->domiciliosTransformer($persona->domicilios)
            ];
        }
        if(!$domicilio){
            unset($resultado['domicilios']);
        }
        return $resultado;
    }

    public function domiciliosTransformer($datos)
    {
        $domicilios = [];
        foreach($datos as $domicilio){
            $domicilios[] = [
                'tipo_vialidad' => $domicilio->tipo_vialidad,
                'vialidad' => $domicilio->vialidad,
                'num_ext' => $domicilio->num_ext,
                'num_int' => $domicilio->num_int,
                'tipo_asentamiento' => $domicilio->tipo_asentamiento,
                'asentamiento' => $domicilio->asentamiento,
                'municipio' => $domicilio->municipio,
                'estado' => $domicilio->estado,
                'cp' => $domicilio->cp,
                'latitud' => $domicilio->latitud,
                'longitud' => $domicilio->longitud,
                'entre_calle1' => $domicilio->entre_calle1,
                'entre_calle2' => $domicilio->entre_calle2,
                'referencias' => $domicilio->referencias,
            ];
        }
        return $domicilios;
    }
}

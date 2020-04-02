<?php

namespace App\Traits;


use App\TipoParte;

trait Transformer
{
    /**
     * Transforma los datos de las partes
     * @param $datos
     * @param $parte
     * @param bool $domicilio
     * @return array
     */
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
                'solicita_traductor' => $persona->solicita_traductor,
                'lenguaIndigena' => $persona->lenguaIndigena->nombre,
                'padece_discapacidad' => $persona->padece_discapacidad,
                'discapacidad' => $persona->tipoDiscapacidad->nombre,
                'publicacion_datos' => $persona->publicacion_datos,
                'domicilios' => $this->domiciliosTransformer($persona->domicilios),
                'contactos' => $this->contactoTransformer($persona->contactos)
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
                'discapacidad' => "N/A",
                'publicacion_datos' => $persona->publicacion_datos,
                'domicilios' => $this->domiciliosTransformer($persona->domicilios),
                'contactos' => $this->contactoTransformer($persona->contactos)
            ];
        }
        if(!$domicilio){
            unset($resultado['domicilios']);
        }
        return $resultado;
    }

    /**
     * Transforma los datos que se van a reportar de domicilios
     * @param $datos
     * @return array
     */
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

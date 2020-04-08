<?php

namespace App\Parsers;

use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\GiroComercial;
use DB;
use Log;
use Validator;


class GiroComercialParser
{
    /**
     * Se implementa la extracción y guardado de datos de un archivo excel con la estructura esperada en columnas
     * A => codigo : es el código o clave de centro de costos de 3 pares de dígitos indicando la jerarquia relacional
     * B => nombre: Es el nombre del centro de costos o nodo
     *
     * @param $archivo Path del archivo que se va a procesar
     *
     * @return bool
     */
    public function parse($archivo)
    {
        //Si no existe el archivo regresamos
        if (!$archivo) {
            return false;
        }

        //Se hace delete de los centros de costo cada que se carga un archivo nuevo.
        DB::table('giro_comerciales')->delete();

        $workbook = SpreadsheetParser::open($archivo, 'xlsx');

        //Se itera cada elemento del archivo y vamos contando si es un insert, el total y si hay mensajes de validación que reportar.
        $totales = 0;
        $inserts = 0;
        $validacion = [];
        $errores = [];
        $valores = [];
        $total_errores = 0;
        $mensajes = [];
        $row = [];

        foreach ($workbook->createRowIterator(0) as $rowIndex => $values) {
            //Saltamos las cabeceras del archivo que siempre deben estar en el rowindex=1
            if ($rowIndex == 1) {
                continue;
            }//cierre id $rowIndex
            $totales++;

            //validamos la variable de control
            $codigo = isset($values[0]) ? $values[0] : null;
            $nombre = isset($values[1]) ? $values[1] : null;

            if (isset($codigo)) {
                $codigo = str_pad($codigo, 6, 0);;
                $valores['codigo'] = $codigo;
            } else {
                $errores[$rowIndex] = 'El código de centro de costo en la linea: ' . $rowIndex . ', no puede estar vacío.';
                $total_errores++;
                continue;
            }

            if (isset($nombre)) {
                $valores['nombre'] = $nombre;
            } else {
                $errores[$rowIndex] = 'El nombre de centro de costo en la linea: ' . $rowIndex . ', no puede estar vacío.';
                $total_errores++;
                continue;
            }

            //creamos las reglas de validacion
            $reglas = [
                "codigo" => "required|digits:6|numeric|unique:giro_comerciales,codigo",
                "nombre" => "required",
            ];

            try {
                //creamos un array de los campos requeridos para la table USERS
                $camp = [
                    'codigo' => $codigo,
                    'nombre' => $nombre,
                ];
                $messages = [
                    'codigo.required' => '¡El campo CODIGO, en la linea: ' . $rowIndex . ', es requerido!.',
                    'codigo.digits' => '¡El campo CODIGO, en la linea: ' . $rowIndex . ', no contiene la cantidad exacta de digitos!.',
                    'codigo.numeric' => '¡El campo CODIGO, en la linea: ' . $rowIndex . ', debe ser un valor numérico, el valor actual es: ' . $codigo . '!.',
                    'codigo.unique' => '¡El campo CODIGO, en la linea: ' . $rowIndex . ', ya existe en el invetario del sistema!.',
                    'name.required' => '¡El campo NOMBRE, en la linea: ' . $rowIndex . ', es requerido!.',
                ];

                //ejecutamos el validator
                $v = Validator::make($camp, $reglas, $messages);
                if ($v->fails()) {
                    $messages = $v->errors();
                    foreach ($messages->all() as $mensaje) {
                        $mensjes_error[] = ' ' . $mensaje;
                    }
                    $mensjes_error = [];
                    $total_errores++;
                } else {
                    //Obtenemos los valores en un arreglo
                    $valores['codigo'] = $codigo;
                    //procedemos a guardar los valores del giro
                    $codigo = GiroComercial::create($valores);
                    $inserts++;
                    continue;
                }//cierre del else del validador
                //cerramos el try
            } catch (PDOException $e) {
                Log::error($archivo, $rowIndex, $values, $total_errores);
            }//cierre del catch

        }//cierre del foreach

        foreach ($row as $keyr => $valuer) {
            foreach ($mensajes as $key => $value) {
                if ($key === $keyr) {
                    $errores[$valuer] = 'El : ' . $key . ', detectada por primera vez en la línea: ' . $valuer . ', no se encuentra en el inventario, debido a esto no se guardaron: ' . $value . ' registros de vehiculos.';
                }
            }
        }

        GiroComercial::reordenar();
    }

}

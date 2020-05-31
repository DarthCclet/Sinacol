<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class RFC implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $aceptarGenerico = true;
        $reg = "/[A-Z]{4}\d{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9][0-9]/";
        $re = "/^([A-ZÑ&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/";
        if($value == ""){
            return true;
        }
        if(!preg_match($re,$value))
        {
            return false;
        }

        //Separar el dígito verificador del resto del RFC
        $digitoVerificador = substr($value,-1);
        $rfcSinDigito = substr($value,0,-1);
        $len = strlen($rfcSinDigito);

            //Obtener el digito esperado
            $diccionario = "0123456789ABCDEFGHIJKLMN&OPQRSTUVWXYZÑ";
            $indice            = $len + 1;

            if ($len == 12) {
                $suma = 0;
            }
            else {
                $suma = 481; //Ajuste para persona moral
            } 
            
            for($i=0; $i<$len; $i++){
                
                $suma += strpos($diccionario,substr($rfcSinDigito,$i,1)) * ($indice - $i);
            }
            $digitoEsperado = 11 - $suma % 11;
            if ($digitoEsperado == 11){
                $digitoEsperado = 0;
            }
            else if ($digitoEsperado == 10){ 
                $digitoEsperado = "A";
            }
            
            //El dígito verificador coincide con el esperado?
            // o es un RFC Genérico (ventas a público general)?
            
            if (($digitoVerificador != $digitoEsperado) && (!$aceptarGenerico || $rfcSinDigito . $digitoVerificador != "XAXX010101000"))
            {
                return false;
            }
            else if (!$aceptarGenerico && $rfcSinDigito . $digitoVerificador == "XEXX010101000")
            {
                return false;
            }
            return $rfcSinDigito . $digitoVerificador;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'RFC invalido.';
    }
}

<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Curp implements Rule
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
        $reg = "";

      if(strlen($value) == ""){
        return true;
      }
      if(strlen($value) == 18)
      {
        $digito = $this->verifica($value);

        $reg = "/[A-Z]{4}\d{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9][0-9]/";
        if(!preg_match($reg,$value))
        {
          return false;
        }

        if(!($digito == substr($value,17,1)))
        {
          return false;
        }
        return true;
      }
      else
      {
        return false;
      }
    }

    private function verifica($curp){
        $segRaiz      = substr($curp,0,17); //.substring(0,17);
        $chrCaracter  = utf8_decode("0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ");
        $lngSuma = 0.0;
        $lngDigito = 0.0;

        for($i=0; $i<17; $i++)
        {
          for($j=0;$j<37; $j++)
          {
            if(substr($segRaiz,$i,1)==substr($chrCaracter,$j,1))
            {
              $intFactor[$i]=$j;
            }
          }
        }

        for($k = 0; $k < 17; $k++)
        {
          $lngSuma= $lngSuma + ((int)($intFactor[$k]) * (18 - $k));
        }

        $lngDigito= (10 - ($lngSuma % 10));

        if($lngDigito==10)
        {
          $lngDigito=0;
        }

        return $lngDigito;
      }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'CURP no válida.';
    }
}

<?php
namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

class StringTemplate
{
    /**
     * Traduce a HTML una plantilla blade pasada como cadena
     * @param $string string Plantilla blade en una cadena
     * @param $vars array Variables que se van a sustituir en la plantilla
     * @return string
     * @throws Exception
     * @throws FatalThrowableError
     */
    public static function render($string, $vars)
    {
        $php = Blade::compileString($string);

        $obLevel = ob_get_level();
        ob_start();
        extract($vars, EXTR_SKIP);
        try {
            eval('?' . '>' . $php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw new FatalThrowableError($e);
        }
        return ob_get_clean();
    }

    /**
     * Sustituye los placeholders del tipo: <strong class="mceNonEditable" var="variable">[--equis-texto--]</strong>
     * por {{$variable}}
     * @param $string
     * @return string
     */
    public static function sustituyePlaceholders($string){
        $string = preg_replace('/\[ESPACIO_FIRMA\]/','&nbsp;&nbsp;&nbsp;', $string);
        $blade = preg_replace('/<strong class="mceNonEditable" data-nombre="(\w+)">\[([\\p{L}_ &;]+)\]<\/strong>/i',
            '<strong>{!! \$$1 !!}</strong>', $string);
        return $blade;
    }

    /**
     * Sustituye los placeholders del tipo: <strong class="mceNonEditable" var="variable">[--equis-texto--]</strong>
     * por {{$variable}}
     * @param $string
     * @return string
     */
    public static function sustituyePlaceholdersConditionals($string, $vars){
      if(Str::contains($string, '[REPETIR')) {
        $countRepetir = substr_count($string, '[FIN_REPETIR');
        // dd($countRepetir);
      }
      if(Str::contains($string, '[SI_')){
        $countSi = substr_count($string, '[FIN_SI');

        $partes = ['solicitado','solicitante'];
        foreach ($partes as $key => $parteL) {
          $htmlA ="";
          $htmlB ="";
          $slice  ="";
            $parte = strtoupper($parteL);

            if($vars[$parteL.'_genero_id'] != null){
              switch ($vars[$parteL.'_genero_id']) {

              case 2:
                $count = substr_count($string, '[SI_'.$parte.'_GENERO_MASCULINO]');
                if($count > 0){
                  //Texto entre condiciones
                  $slice = Str::after($string, '[SI_'.$parte.'_GENERO_MASCULINO]');
                  $slice = Str::before($slice, '[SI');

                  $htmlA = Str::before($string, '[SI_');
                  $htmlB = Str::after($string, '[FIN_SI_'.$parte.'_GENERO]');

                  $string = $htmlA . $slice . $htmlB;
                }
                break;
              case 1:
                $count = substr_count($string, '[SI_'.$parte.'_GENERO_FEMENINO]');
                if($count > 0){
                  $slice = Str::after($string, '[SI_'.$parte.'_GENERO_FEMENINO]');
                  $slice = Str::before($slice, '[FIN_SI');

                  $htmlA = Str::before($string, '[SI_'.$parte);
                  $htmlB = Str::after($string, '[FIN_SI_'.$parte.'_GENERO]');
                  $string = $htmlA . $slice . $htmlB;
                }
                break;
              }
          }
          if($vars[$parteL.'_tipo_persona_id'] != null){
            switch ($vars[$parteL.'_tipo_persona_id']) {
            case 2:
              $count = substr_count($string, '[SI_'.$parte.'_TIPO_PERSONA_FISICA]');
              if($count > 0){
                //Texto entre condiciones
                $slice = Str::after($string, '[SI_'.$parte.'_TIPO_PERSONA_FISICA]');
                $slice = Str::before($slice, '[SI');

                $htmlA = Str::before($string, '[SI_');
                $htmlB = Str::after($string, '[FIN_SI_'.$parte.'TIPO_PERSONA]');
                $string = $htmlA . $slice . $htmlB;
              }
              break;
            case 1:
              $count = substr_count($string, '[SI_'.$parte.'_TIPO_PERSONA_MORAL]');
              if($count > 0){
                $slice = Str::after($string, '[SI_'.$parte.'_TIPO_PERSONA_MORAL]');
                $slice = Str::before($slice, '[FIN_SI');

                $htmlA = Str::before($string, '[SI_');
                $htmlB = Str::after($string, '[FIN_SI_'.$parte.'_TIPO_PERSONA]');
                $string = $htmlA . $slice . $htmlB;
              }
              break;
          }
        }
      }

      }
      return $string;
    }

    /**
     * Regresa una cadena HTML compilada desde placeholders pasando por plantilla blade hasta html
     * @param $string string Cadena con placeholders
     * @param $vars array Variables a sustituir en plantilla blade
     * @return string
     * @throws Exception
     * @throws FatalThrowableError
     */
    public static function renderPlantillaPlaceholders($string, $vars)
    {
        $string = self::sustituyePlaceholdersConditionals($string,$vars);
        $blade = self::sustituyePlaceholders($string);

        return self::render($blade, $vars);
    }
    /**
     * Regresa una cadena HTML compilada desde placeholders pasando por plantilla blade hasta html
     * @param $string string Cadena con placeholders
     * @param $vars array Variables a sustituir en plantilla blade
     * @return string
     * @throws Exception
     * @throws FatalThrowableError
     */
    public static function renderOficioPlaceholders($string, $vars)
    {
        // $string = self::sustituyePlaceholdersConditionals($string,$vars);
        $blade = self::sustituyePlaceholders($string);

        return self::render($blade, $vars);
    }
}

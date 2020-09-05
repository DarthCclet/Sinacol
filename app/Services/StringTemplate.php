<?php
namespace App\Services;

use ErrorException;
use Exception;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use ParseError;
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
        } catch (ErrorException $err) {
            dd($err);
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
      }
      if(Str::contains($string, '[SI_')){
        $countSi = substr_count($string, '[FIN_SI');
        $countTipoNotificacion = substr_count($string,'[SI_SOLICITANTE_NOTIFICA]');
        $countAudienciaSeparada = substr_count($string,'[SI_AUDIENCIA_POR_SEPARADO]');
        if (isset($vars['solicitado_tipo_notificacion'])){
          if($vars['solicitado_tipo_notificacion'] != null && $countTipoNotificacion >0){
            switch ($vars['solicitado_tipo_notificacion']) {
              case 1: // El solicitante entrega citatorio a solicitados
                // texto de notificacion por solicitante
                $sliceNotificacion = Str::after($string, '[SI_SOLICITANTE_NOTIFICA]');
                $sliceNotificacion = Str::before($sliceNotificacion, '[SI_NO_NOTIFICA]');

                $htmlA = Str::before($string, '[SI_SOLICITANTE_N');
                $htmlB = Str::after($string, '[FIN_SI_SOLICITANTE_NOTIFICA]');

                $string = $htmlA . $sliceNotificacion . $htmlB;
              break;
              case 2: //El actuario del centro entrega citatorio a solicitados
                // texto de notificacion por actuario
                $sliceNotificacion = Str::after($string, '[SI_NO_NOTIFICA]');
                $sliceNotificacion = Str::before($sliceNotificacion, '[FIN_SI_SOLICITANTE_NOTIFICA]');

                $htmlA = Str::before($string, '[SI_SOLICITANTE_N');
                $htmlB = Str::after($string, '[FIN_SI_SOLICITANTE_NOTIFICA]');

                $string = $htmlA . $sliceNotificacion . $htmlB;
              break;
            }
          }
        }
        if (isset($vars['audiencia_multiple'])){
          if($vars['audiencia_multiple'] != null && $countAudienciaSeparada > 0){
            if($vars['audiencia_multiple'] == 'Si') { // Audiencia en salas diferentes
                // texto de audiencia por separado
                $sliceSeparado = Str::after($string, '[SI_AUDIENCIA_POR_SEPARADO]');
                $sliceSeparado = Str::before($sliceSeparado, '[FIN_SI_AUDIENCIA_POR_SEPARADO]');
                $htmlA = Str::before($string, '[SI_AUDIENCIA_POR_SEPARADO');
                $htmlB = Str::after($string, '[FIN_SI_AUDIENCIA_POR_SEPARADO]');

                $string = $htmlA . $sliceSeparado . $htmlB;
            }else{//audiencia en misma sala
                // texto de
                $sliceSeparado = "";
                $htmlA = Str::before($string, '[SI_AUDIENCIA_POR_SEPARADO');
                $htmlB = Str::after($string, '[FIN_SI_AUDIENCIA_POR_SEPARADO]');

                $string = $htmlA . $sliceSeparado . $htmlB;
            //   // break;
            }
          }
        }


        $partes = ['solicitado','solicitante'];
        foreach ($partes as $key => $parteL) {
          $htmlA ="";
          $htmlB ="";
          $slice  ="";
          $parte = strtoupper($parteL);
          $countPersona = substr_count($string,'[SI_'.$parte.'_TIPO_PERSONA_FISICA]');
          // $countPersonaMoral = substr_count($string,'[SI_'.$parte.'_IPO_PERSONA_MORAL]');
          $countGenero = substr_count($string,'[SI_'.$parte.'_GENERO_MASCULINO]');
          // $countGeneroFem = substr_count($string,'[SI_'.$parte.'_IPO_PERSONA_MORAL]');

          if($vars[$parteL.'_genero_id'] != null && $countGenero >0){
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

          if($vars[$parteL.'_tipo_persona_id'] != null  && ($countPersona >0) ){
            switch ($vars[$parteL.'_tipo_persona_id']) {
              case 1: //fisica
                $count = substr_count($string, '[SI_'.$parte.'_TIPO_PERSONA_FISICA]');
                if($count > 0){
                  //Texto entre condiciones
                  $sliceFisica = Str::after($string, '[SI_'.$parte.'_TIPO_PERSONA_FISICA]');
                  $sliceFisica = Str::before($sliceFisica, '[SI_');
                  $htmlA = Str::before($string, '[SI_'.$parte.'_TIPO_PERSONA_FISICA]');
                  $htmlB = Str::after($string, '[FIN_SI_'.$parte.'_TIPO_PERSONA]');
                  $string = $htmlA . $sliceFisica . $htmlB;
                }
                break;
              case 2: //moral
                $count = substr_count($string, '[SI_'.$parte.'_TIPO_PERSONA_MORAL]');
                if($count > 0){
                  $sliceMoral = Str::after($string, '[SI_'.$parte.'_TIPO_PERSONA_MORAL]');
                  $sliceMoral = Str::before($sliceMoral, '[FIN_SI');

                  $htmlA = Str::before($string, '[SI_');
                  $htmlB = Str::after($string, '[FIN_SI_'.$parte.'_TIPO_PERSONA]');
                  $string = $htmlA . $sliceMoral . $htmlB;
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
        $string = self::sustituyePlaceholdersConditionals($string, $vars);
        $vars_necesarias = [];
        if(preg_match_all('/\[(\w+)\]/',$string,$vars_necesarias) && isset($vars_necesarias[1])) {
            foreach ($vars_necesarias[1] as $varname) {
                if (!isset($vars[mb_strtolower($varname)])) {
                    $vars[mb_strtolower($varname)] = '<span style="color: red;">' . $varname . '</span>';
                }
            }
        }
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

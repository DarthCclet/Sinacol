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
            //dd($err);
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
        $string = preg_replace('/\[ESPACIO_FIRMA\]/','&nbsp;&nbsp;', $string);
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
        $countSolicitudRatificada = substr_count($string,'[SI_SOLICITUD_RATIFICADA]');
        $countPagosDiferidos = substr_count($string,'[SI_RESOLUCION_PAGO_DIFERIDO]');
        $countSolicitudVirtual = substr_count($string,'[SI_SOLICITUD_VIRTUAL]');
        if (isset($vars['resolucion_total_diferidos'])){
          if($countPagosDiferidos >0){
            for ($i=0; $i < $countPagosDiferidos; $i++) {
              if($vars['resolucion_total_diferidos'] > 0) { // Hay pagos diferidos
                // texto de pagos diferidos
                $sliceDiferido = Str::after($string, '[SI_RESOLUCION_PAGO_DIFERIDO]');
                $sliceDiferido = Str::before($sliceDiferido, '[SI_RESOLUCION_PAGO_NO_DIFERIDO]');
                $htmlA = Str::before($string, '[SI_RESOLUCION_PAGO');
                $htmlB = Str::after($string, '[FIN_SI_RESOLUCION_PAGO]');

                $string = $htmlA . $sliceDiferido . $htmlB;

              }else{//Sin pagos diferidos
                $sliceDiferido = Str::after($string, '[SI_RESOLUCION_PAGO_NO_DIFERIDO]');
                $sliceDiferido = Str::before($sliceDiferido, '[FIN_SI_RESOLUCION_PAGO]');

                $htmlA = Str::before($string, '[SI_RESOLUCION_PAGO_DIFERIDO');
                $htmlB = Str::after($string, '[FIN_SI_RESOLUCION_PAGO]');

                $string = $htmlA . $sliceDiferido . $htmlB;
                // break;
              }
            }
          }
        }
        if (isset($vars['solicitado_tipo_notificacion'])){
          if ($countTipoNotificacion >0 ){
            if($vars['solicitado_tipo_notificacion'] != null && $vars['solicitado_tipo_notificacion'] != "--"){
              for ($i=0; $i < $countTipoNotificacion; $i++) {
                $htmlA = Str::before($string, '[SI_SOLICITANTE_N');
                $htmlB = Str::after($string, '[FIN_SI_SOLICITANTE_NOTIFICA]');
                switch ($vars['solicitado_tipo_notificacion']) {
                  case 1: // El solicitante entrega citatorio a solicitados
                    // texto de notificacion por solicitante
                    $sliceNotificacion = Str::after($string, '[SI_SOLICITANTE_NOTIFICA]');
                    $sliceNotificacion = Str::before($sliceNotificacion, '[SI_NO_NOTIFICA]');

                    $string = $htmlA . $sliceNotificacion . $htmlB;
                  break;
                  default: //2 y 3
                  // case 2: //El actuario del centro entrega citatorio a solicitados
                    // texto de notificacion por actuario
                    $sliceNotificacion = Str::after($string, '[SI_NO_NOTIFICA]');
                    $sliceNotificacion = Str::before($sliceNotificacion, '[FIN_SI_SOLICITANTE_NOTIFICA]');
                    $string = $htmlA . $sliceNotificacion . $htmlB;
                  // default: // 3
                    // $string = $htmlA . $htmlB;
                  break;
                }
              }
            }else{
              $htmlA = Str::before($string, '[SI_SOLICITANTE_N');
              $htmlB = Str::after($string, '[FIN_SI_SOLICITANTE_NOTIFICA]');
              $sliceNotificacion = "Las partes se presentan de manera voluntaria ante esta autoridad conciliatoria.";
              $string = $htmlA . $sliceNotificacion . $htmlB;
            }
          }
        }
        if (isset($vars['audiencia_multiple'])){
          if($vars['audiencia_multiple'] != null && $countAudienciaSeparada > 0){
            for ($i=0; $i < $countAudienciaSeparada; $i++) {
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
                // break;
              }
            }
          }
        }
        if (isset($vars['solicitud_estatus_solicitud_id'])&& $countSolicitudRatificada > 0){
          for ($i=0; $i < $countSolicitudRatificada; $i++) {
            $htmlA = Str::before($string, '[SI_SOLICITUD_RATIFICADA');
            $htmlB = Str::after($string, '[FIN_SI_SOLICITUD_RATIFICADA]');
            if($vars['solicitud_estatus_solicitud_id'] != 1 ){ //solicitud ratificada o termindada
                // texto de datos de acceso a buzon
                $sliceRatificada = Str::after($string, '[SI_SOLICITUD_RATIFICADA]');
                $sliceRatificada = Str::before($sliceRatificada, '[FIN_SI_SOLICITUD_RATIFICADA]');

                $string = $htmlA . $sliceRatificada . $htmlB;
              }else{//solicitud no ratificada
                $string = $htmlA . $htmlB;
            }
          }
        }
        if (isset($vars['solicitud_virtual'])&& $countSolicitudVirtual > 0){
          for ($i=0; $i < $countSolicitudVirtual; $i++) {
            $htmlA = Str::before($string, '[SI_SOLICITUD_VIRTUAL');
            $htmlB = Str::after($string, '[FIN_SI_SOLICITUD_VIRTUAL]');
            if($vars['solicitud_virtual']){ //solicitud es virtual
                $sliceVirtual = Str::after($string, '[SI_SOLICITUD_VIRTUAL]');
                $sliceVirtual = Str::before($sliceRatificada, '[SI_SOLICITUD_NO_VIRTUAL]');

                $string = $htmlA . $sliceVirtual . $htmlB;
              }else{//solicitud no virtual
                $sliceVirtual = Str::after($string, '[SI_SOLICITUD_NO_VIRTUAL]');
                $sliceVirtual = Str::before($sliceRatificada, '[FIN_SI_SOLICITUD_VIRTUAL]');

                $string = $htmlA . $sliceVirtual . $htmlB;
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
          if(isset($vars[$parteL.'_genero_id']) && $vars[$parteL.'_genero_id'] != null && $countGenero >0){
            for ($i=0; $i < $countGenero; $i++) {
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
          }

          if(isset($vars[$parteL.'_tipo_persona_id']) && $vars[$parteL.'_tipo_persona_id'] != null  && ($countPersona >0) ){
            for ($i=0; $i < $countPersona; $i++) {
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

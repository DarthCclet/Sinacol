<?php
namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Blade;
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
        $string = preg_replace('/\[ESPACIO_FIRMA\]/','&nbsp;&nbsp;&nbsp;&nbsp;', $string);
        $blade = preg_replace('/<strong class="mceNonEditable" data-nombre="(\w+)">\[([\\p{L} &;]+)\]<\/strong>/i',
            '<strong>{!! \$$1 !!}</strong>', $string);


        return $blade;
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
        $blade = self::sustituyePlaceholders($string);
        return self::render($blade, $vars);
    }
}

<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Traits\ValidateRange;
use App\Http\Controllers\AudienciaController;
class RangoTiempoTest extends TestCase
{
    use ValidateRange;
    /**
     * test para provar las relaciones de la tabla salas
     *
     * @return void
     */
    public function testDisponibles(){
        /**
         * Casos a evaluar
         * Audiencia de 09:00:00 - 11:00:00
         * Case 01 | 09:00-11:00 09:00-11:00 |      false
         * Case 02 | 09:00-11:00 10:00-12:00 |      false
         * Case 03 | 10:00-12:00 09:00-11:00 |      false
         * Case 04 | 09:00-12:00 10:00-11:00 |      false
         * Case 05 | 10:00-11:00 09:00-12:00 |      false
         * Case 06 | 09:00-10:00 11:00-12:00 |      true
         * Case 07 | 11:00-12:00 09:00-10:00 |      true
         * Case 08 | 09:00-10:00 10:00-11:00 |      true
         * Case 09 | 10:00-11:00 09:00-10:00 |      true
         */
        
        ## Caso 1
        $horaInicioAudiencia="09:00:00";
        $horaFinAudiencia="11:00:00";
        $horaInicio="09:00:00";
        $horaFin="11:00:00";
        $this->assertFalse($this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin));
        
        ## Caso 2
        $horaInicioAudiencia="09:00:00";
        $horaFinAudiencia="11:00:00";
        $horaInicio="10:00:00";
        $horaFin="12:00:00";
        $this->assertFalse($this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin));
        
        ## Caso 3
        $horaInicioAudiencia="10:00:00";
        $horaFinAudiencia="12:00:00";
        $horaInicio="09:00:00";
        $horaFin="11:00:00";
        $this->assertFalse($this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin));
        
        ## Caso 4
        $horaInicioAudiencia="09:00:00";
        $horaFinAudiencia="12:00:00";
        $horaInicio="10:00:00";
        $horaFin="11:00:00";
        $this->assertFalse($this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin));
        
        ## Caso 5
        $horaInicioAudiencia="10:00:00";
        $horaFinAudiencia="11:00:00";
        $horaInicio="09:00:00";
        $horaFin="12:00:00";
        $this->assertFalse($this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin));
        
        ## Caso 6
        $horaInicioAudiencia="09:00:00";
        $horaFinAudiencia="10:00:00";
        $horaInicio="11:00:00";
        $horaFin="12:00:00";
        $this->assertTrue($this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin));
        
        ## Caso 7
        $horaInicioAudiencia="11:00:00";
        $horaFinAudiencia="12:00:00";
        $horaInicio="09:00:00";
        $horaFin="10:00:00";
        $this->assertTrue($this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin));
        
        ## Caso 8
        $horaInicioAudiencia="09:00:00";
        $horaFinAudiencia="10:00:00";
        $horaInicio="10:00:00";
        $horaFin="11:00:00";
        $this->assertTrue($this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin));
        
        ## Caso 9
        $horaInicioAudiencia="10:00:00";
        $horaFinAudiencia="11:00:00";
        $horaInicio="09:00:00";
        $horaFin="10:00:00";
        $this->assertTrue($this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin));
        
    }
}


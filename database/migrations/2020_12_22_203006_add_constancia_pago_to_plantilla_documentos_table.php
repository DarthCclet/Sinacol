<?php

use App\ClasificacionArchivo;
use App\PlantillaDocumento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConstanciaPagoToPlantillaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //plantilla para obtener header y footer
        $plantillaOrigen = PlantillaDocumento::find(1);
        //crear plantilla CONSTANCIA DE PAGO PARCIAL DE CONVENIO
        $plantillaDoc = PlantillaDocumento::create(
        [
            'nombre_plantilla'=> 'CONSTANCIA DE PAGO PARCIAL DE CONVENIO',
            'descripcion'=>'CONSTANCIA DE PAGO PARCIAL DE CONVENIO',
            'plantilla_header'=>$plantillaOrigen->plantilla_header,
            'plantilla_body'=>'<p>&nbsp;</p>
            <table style="border-collapse: collapse; width: 61.8489%; float: right;" border="1">
            <tbody>
            <tr>
            <td style="width: 28.6057%;"><strong>N&uacute;mero de identificaci&oacute;n &uacute;nico</strong></td>
            <td style="width: 28.884%;"><strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong><strong>&nbsp;</strong></td>
            </tr>
            <tr>
            <td style="width: 28.6057%;" rowspan="2"><strong>Buz&oacute;n electr&oacute;nico</strong></td>
            <td style="width: 28.884%;"><strong class="mceNonEditable" data-nombre="solicitante_correo_buzon">[SOLICITANTE_CORREO_BUZON]</strong><strong>&nbsp;</strong></td>
            </tr>
            <tr>
            <td style="width: 28.884%;"><strong class="mceNonEditable" data-nombre="solicitado_correo_buzon">[SOLICITADO_CORREO_BUZON]</strong><strong>&nbsp;</strong></td>
            </tr>
            <tr>
            <td style="width: 28.6057%;"><strong>Centro Federal de Conciliaci&oacute;n y Registro Laboral</strong></td>
            <td style="width: 28.884%;"><strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong>&nbsp;</td>
            </tr>
            </tbody>
            </table>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p style="text-align: center;"><strong>CONSTANCIA DE PAGO PARCIAL DE CONVENIO</strong></p>
            <p style="line-height: 14px;"><strong>Solicitante: <strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong>&nbsp; </strong></p>
            <p style="line-height: 14px;"><strong>Citado: <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong>&nbsp; </strong></p>
            <p style="line-height: 14px;"><strong>Funcionario(a) conciliador responsable: <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong></strong></p>
            <p style="line-height: 14px;"><strong>Objeto de la conciliaci&oacute;n: <strong class="mceNonEditable" data-nombre="solicitud_objeto_solicitudes">[SOLICITUD_OBJETO_SOLICITUDES]</strong></strong></p>
            <p style="line-height: 14px;"><strong>Fecha del conflicto: <strong class="mceNonEditable" data-nombre="solicitud_fecha_conflicto">[SOLICITUD_FECHA_CONFLICTO]</strong></strong></p>
            <p style="line-height: 14px;"><strong>Fecha de registro de la solicitud: <strong class="mceNonEditable" data-nombre="solicitud_fecha_recepcion">[SOLICITUD_FECHA_RECEPCION]</strong></strong></p>
            <p style="line-height: 14px;"><strong>Fecha y hora de audiencia: <strong class="mceNonEditable" data-nombre="audiencia_fecha_audiencia">[AUDIENCIA_FECHA_AUDIENCIA]</strong>&nbsp; <strong class="mceNonEditable" data-nombre="audiencia_hora_inicio">[AUDIENCIA_HORA_INICIO]</strong></strong></p>
            <p style="line-height: 14px;"><strong>Asistencia de las partes: Si.</strong></p>
            <p style="line-height: 14px;"><strong>Convenio conciliatorio: Si.</strong></p>
            <p style="line-height: 14px;">&nbsp;</p>
            <p><strong>Fundamento:</strong> Art&iacute;culos 684-E, fracci&oacute;n XIII, y 684-F, fracci&oacute;n VII, de la Ley Federal del Trabajo y art&iacute;culos 5 y 9, fracci&oacute;n I de la Ley Org&aacute;nica del Centro Federal de Conciliaci&oacute;n y Registro Laboral.</p>
            <p><strong>PRIMERA</strong>. Las <strong>PARTES</strong> han determinado, por as&iacute; convenir a sus intereses, dar por terminado el conflicto laboral por mutuo acuerdo, conforme a lo estipulado por el art&iacute;culo 33 de la Ley Federal del Trabajo.</p>
            <p><strong>SEGUNDA</strong>. La <strong>PARTES</strong>, conforme a su determinaci&oacute;n de dar por terminado el conflicto laboral, han suscrito el Convenio ante esta Autoridad Conciliadora.</p>
            <p><strong>TERCERA</strong>. La parte <strong>EMPLEADORA</strong> otorga en favor de la parte <strong>TRABAJADORA</strong> el pago parcial acordado en el citado Convenio, en fecha <strong class="mceNonEditable" data-nombre="fecha_actual">[FECHA_ACTUAL]</strong>, conforme a las disposiciones de la Ley Federal del Trabajo. As&iacute; mismo, la parte <strong>TRABAJADORA</strong> manifiesta su entera conformidad y la aceptaci&oacute;n de dicho pago.</p>
            <p><strong>Motivaci&oacute;n</strong>: De conformidad a la cl&aacute;usula Sexta del Convenio de Conciliaci&oacute;n suscrito entre el la parte <strong>TRABAJADORA</strong> y la parte <strong>EMPLEADORA</strong>, la parte <strong>EMPLEADORA</strong> se comprometi&oacute; a realizar el pago de la cantidad total del citado convenio en pagos diferidos. Al efectuar un pago diferido en fecha <strong class="mceNonEditable" data-nombre="fecha_actual">[FECHA_ACTUAL]</strong>, se emite la presente Constancia de Pago Parcial de Convenio. <strong>Doy fe</strong>.</p>
            <div id="contenedor-firma" class="mceNonEditable" style="text-align: center; border-bottom: thin solid black; width: 40%; margin: 0 auto;">
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <strong id="espacio-firma">[ESPACIO_FIRMA]</strong></div>
            <p style="text-align: center;"><strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp;</p>
            <p style="text-align: center;"><strong>PERSONAL CONCILIADOR</strong></p>
            ',
            'plantilla_footer' => $plantillaOrigen->plantilla_footer,
            'tipo_documento_id' => 1
            ]
        );

        $clasificacionArchivo = ClasificacionArchivo::create([
            'nombre' => 'Constancia de pago parcial de convenio',
            'tipo_archivo_id' => 4,
            'entidad_emisora_id' =>null
        ]);    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plantilla_documentos', function (Blueprint $table) {
            //
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAcuseV3ToPlantillaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('plantilla_documentos')->where('nombre_plantilla','ACUSE SOLICITUD')->update(
        [
            'plantilla_body'=>'<p>&nbsp;</p>
                <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;><strong>FECHA DE SOLICITUD: <strong class="mceNonEditable" data-nombre="solicitud_fecha_recepcion">[SOLICITUD_FECHA_RECEPCION]</strong>&nbsp; </strong></span></p>
                <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>SOLICITANTE(S): <strong class="mceNonEditable" data-nombre="solicitud_nombres_solicitantes">[SOLICITUD_NOMBRES_SOLICITANTES]</strong>&nbsp; &nbsp;</strong></span></p>
                <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>CITADO(S): <strong class="mceNonEditable" data-nombre="solicitud_nombres_solicitados">[SOLICITUD_NOMBRES_SOLICITADOS]</strong>&nbsp; </strong></span></p>
                <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>FECHA DE CONFLICTO: <strong class="mceNonEditable" data-nombre="solicitud_fecha_conflicto">[SOLICITUD_FECHA_CONFLICTO]</strong>&nbsp;</strong></span></p>
                <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>OBJETO DE LA SOLICITUD: <strong class="mceNonEditable" data-nombre="solicitud_objeto_solicitudes">[SOLICITUD_OBJETO_SOLICITUDES]</strong> </strong></span></p>
                <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>OFICINA ESTATAL DEL CFCRL: <strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong> </strong></span></p>
                <p>&nbsp;</p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Usted ha guardado exitosamente la solicitud de conciliaci&oacute;n con folio <strong class="mceNonEditable" data-nombre="centro_abreviatura">[CENTRO_ABREVIATURA]</strong>/<strong class="mceNonEditable" data-nombre="solicitud_folio">[SOLICITUD_FOLIO]</strong>/<strong class="mceNonEditable" data-nombre="solicitud_anio">[SOLICITUD_ANIO]</strong>. Debe acudir, con su identificaci&oacute;n oficial en forma original, a la Oficina Estatal del Centro Federal de Conciliaci&oacute;n y Registro Laboral <strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong> con domicilio en <strong class="mceNonEditable" data-nombre="centro_domicilio_completo">[CENTRO_DOMICILIO_COMPLETO]</strong> en un horario de <strong><strong class="mceNonEditable" data-nombre="centro_hora_inicio">[CENTRO_HORA_INICIO]</strong></strong> a <strong class="mceNonEditable" data-nombre="centro_hora_fin">[CENTRO_HORA_FIN]</strong> <strong>hrs</strong>. Conforme al Art&iacute;culo 735 de la Ley Federal del Trabajo tiene tres d&iacute;as h&aacute;biles a partir de este momento para ratificar la solicitud. Debes ratificar la solicitud como m&aacute;ximo el d&iacute;a <strong class="mceNonEditable" data-nombre="solicitud_fecha_maxima_ratificacion">[SOLICITUD_FECHA_MAXIMA_RATIFICACION]</strong>&nbsp; </span></p>
                <p style="text-align: justify;">&nbsp;</p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="">[SI_SOLICITUD_RATIFICADA]</strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Datos de acceso al buz&oacute;n:</span></p>
                <p style="text-align: justify;"><strong class="mceNonEditable" data-nombre="solicitante_correo_buzon">[SOLICITANTE_CORREO_BUZON]</strong> &nbsp;</p>
                <p style="text-align: justify;"><strong class="mceNonEditable" data-nombre="solicitante_password_buzon">[SOLICITANTE_PASSWORD_BUZON]</strong>&nbsp;</p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> <strong class="mceNonEditable" data-nombre="">[FIN_SI_SOLICITUD_RATIFICADA]</strong> &nbsp;</span></p>
                <p>&nbsp;</p>'
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

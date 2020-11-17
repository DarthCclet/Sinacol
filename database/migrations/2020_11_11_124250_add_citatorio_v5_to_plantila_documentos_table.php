<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCitatorioV5ToPlantilaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('plantilla_documentos')->where('nombre_plantilla','CITATORIO DE CONCILIACIÓN')->update(
            [
            'plantilla_body'=>'<p>&nbsp;</p>
            <p><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>ASUNTO</strong>: AUDIENCIA DE CONCILIACI&Oacute;N PREJUDICIAL<br /><strong>SOLICITANTE</strong>: <strong class="mceNonEditable" data-nombre="solicitud_nombres_solicitantes">[SOLICITUD_NOMBRES_SOLICITANTES]</strong>&nbsp; &nbsp;<br /><strong>CITADO</strong>: <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong>&nbsp; <br /><strong>FECHA</strong>: <strong class="mceNonEditable" data-nombre="fecha_actual">[FECHA_ACTUAL]</strong><br /><strong>N&Uacute;MERO DE IDENTIFICACI&Oacute;N &Uacute;NICO: <strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong></strong></span></p>
            <p><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>C. REPRESENTANTE LEGAL DE: <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong>&nbsp; <br />DOMICILIO<br /><strong class="mceNonEditable" data-nombre="solicitado_domicilios_vialidad">[SOLICITADO_DOMICILIOS_VIALIDAD]</strong>,&nbsp;<strong class="mceNonEditable" data-nombre="solicitado_domicilios_num_ext">[SOLICITADO_DOMICILIOS_NUM_EXT]</strong><br /><strong class="mceNonEditable" data-nombre="solicitado_domicilios_asentamiento">[SOLICITADO_DOMICILIOS_ASENTAMIENTO]</strong>&nbsp;<br /><strong class="mceNonEditable" data-nombre="solicitado_domicilios_municipio">[SOLICITADO_DOMICILIOS_MUNICIPIO]</strong>, <strong class="mceNonEditable" data-nombre="solicitado_domicilios_estado">[SOLICITADO_DOMICILIOS_ESTADO]</strong>, C.P. <strong class="mceNonEditable" data-nombre="solicitado_domicilios_cp">[SOLICITADO_DOMICILIOS_CP]</strong>&nbsp; </strong></span></p>
            <p><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>P R E S E N T E</strong></span></p>
            <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">En cumplimiento y observancia a la fracci&oacute;n XX, del art&iacute;culo 123 Constitucional, apartado A; as&iacute; como los de los Principios Procesales contenidos en los art&iacute;culos 684-E y 685 de la Ley Federal del Trabajo, que regulan el procedimiento obligatorio prejudicial conciliatorio; se le cita al <strong>C. REPRESENTANTE LEGAL DE:</strong> <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong> para que asista a la <strong>Audiencia de Conciliaci&oacute;n</strong> de fecha <strong class="mceNonEditable" data-nombre="audiencia_fecha_audiencia">[AUDIENCIA_FECHA_AUDIENCIA]</strong> a las <strong class="mceNonEditable" data-nombre="audiencia_hora_inicio">[AUDIENCIA_HORA_INICIO]</strong> horas, en la sala <strong class="mceNonEditable" data-nombre="sala_nombre">[SALA_NOMBRE]</strong>&nbsp;de la oficina de representaci&oacute;n <strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong> del Centro Federal de Conciliaci&oacute;n y Registro Laboral, con domicilio en <strong class="mceNonEditable" data-nombre="centro_domicilio_completo">[CENTRO_DOMICILIO_COMPLETO]</strong>.</span></p>
            <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><span style="font-family: Montserrat, sans-serif;">La audiencia ser&aacute; presidida por por un funcionario Conciliador del CFCRL, en cumplimiento al art&iacute;culo 684-H, manteniendo en todo momento los principios de conciliaci&oacute;n, imparcialidad, neutralidad, flexibilidad, legalidad, equidad, buena fe, informaci&oacute;n, honestidad, y confidencialidad. Este citatorio se notifica de manera personal conforme al art&iacute;culo 739 de la Ley Federal del Trabajo.</span><span style="font-family: Montserrat, sans-serif;">&nbsp;</span></span></p>
            <p style="text-align: justify;"><strong class="mceNonEditable" data-nombre="">[SI_SOLICITANTE_NOTIFICA]</strong></p>
            <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Con fundamento en el Art&iacute;culo 684-E, ante pen&uacute;ltimo p&aacute;rrafo, el presente citatorio es entregado por el solicitante.</span><br /><strong class="mceNonEditable" data-nombre="">[SI_NO_NOTIFICA]</strong></p>
            <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Con fundamento en el art&iacute;culo 684-E. Fracci&oacute;n IV, se apercibe al solicitado que de no comparecer por s&iacute; o por conducto de su representante legal, o bien por medio de apoderado con facultades suficientes, se le impondr&aacute; una multa entre 50 y 100 veces la Unidad de Medida y Actualizaci&oacute;n, y se le tendr&aacute; por inconforme con todo arreglo conciliatorio.</span><br /><strong class="mceNonEditable" data-nombre="">[FIN_SI_SOLICITANTE_NOTIFICA]</strong><span style="font-size: 10pt;">&nbsp;</span></p>
            <p style="text-align: justify;">&nbsp;</p>
            <p style="text-align: center;"><strong class="mceNonEditable" data-nombre="conciliador_qr_firma">[CONCILIADOR_QR_FIRMA]</strong>&nbsp; &nbsp;&nbsp;</p>
            <div id="contenedor-firma" class="mceNonEditable" style="text-align: center; border-bottom: thin solid black; width: 40%; margin: 0 auto;">
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <strong id="espacio-firma">[ESPACIO_FIRMA]</strong></div>
            <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong> &nbsp;</span></p>
            <p>&nbsp;</p>'
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plantila_documentos', function (Blueprint $table) {
            //
        });
    }
}

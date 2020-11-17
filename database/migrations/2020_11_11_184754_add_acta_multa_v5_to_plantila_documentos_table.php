<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddActaMultaV5ToPlantilaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('plantilla_documentos')->where('nombre_plantilla','ACTA DE MULTA')->update(
            [
                'plantilla_body'=>'<p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>CENTRO FEDERAL DE CONCILIACI&Oacute;N Y REGISTRO LABORAL&nbsp;</strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>CON SEDE EN <strong class="mceNonEditable" data-nombre="centro_domicilio_estado">[CENTRO_DOMICILIO_ESTADO]</strong></strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>N&Uacute;MERO IDENTIFICACI&Oacute;N &Uacute;NICO: <strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong>&nbsp; </strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">En <strong class="mceNonEditable" data-nombre="centro_domicilio_estado">[CENTRO_DOMICILIO_ESTADO]</strong> a <strong class="mceNonEditable" data-nombre="fecha_actual">[FECHA_ACTUAL]</strong></span>,<span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> el funcionario conciliador <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>,</span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp;adscrito al Centro Federal de Conciliaci&oacute;n y Registro Laboral, <strong>hace constar y certifica</strong> que la parte citada <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp;<strong>no compareci&oacute; a la audiencia de conciliaci&oacute;n</strong> prevista a las <strong class="mceNonEditable" data-nombre="audiencia_hora_inicio">[AUDIENCIA_HORA_INICIO]</strong> </span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> de esta misma fecha, a pesar de encontrase debidamente notificado(a) para tal efecto, circunstancia que se corrobora con la constancia de notificaci&oacute;n de<strong> <strong class="mceNonEditable" data-nombre="solicitado_fecha_notificacion">[SOLICITADO_FECHA_NOTIFICACION]</strong></strong>. <strong>Doy fe.</strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="centro_domicilio_estado">[CENTRO_DOMICILIO_ESTADO]</strong>, a <strong class="mceNonEditable" data-nombre="fecha_actual">[FECHA_ACTUAL]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">.</span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Vista la certificaci&oacute;n mencionada, se advierte que la parte citada <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">, <strong>no compareci&oacute; a la audiencia de conciliaci&oacute;n</strong> prevista a las <strong class="mceNonEditable" data-nombre="audiencia_hora_inicio">[AUDIENCIA_HORA_INICIO]</strong> horas de esta misma fecha, a pesar de encontrarse debidamente notificado(a) para tal efecto, circunstancia que se corrobora con la constancia de notificaci&oacute;n de <strong class="mceNonEditable" data-nombre="solicitado_fecha_notificacion">[SOLICITADO_FECHA_NOTIFICACION]</strong>.</span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Fundamentaci&oacute;n:&nbsp;</strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Art&iacute;culos 16, primer p&aacute;rrafo, de la Constituci&oacute;n Pol&iacute;tica de los Estados Unidos Mexicanos; 684-E, fracciones IV, X y 684-I, fracci&oacute;n II, de la Ley Federal del Trabajo; y 22, fracci&oacute;n II y V, de la Ley General de Protecci&oacute;n de Datos Personales en Posesi&oacute;n de Sujetos Obligados.</span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Acuerdo y/o motivaci&oacute;n:</strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">En atenci&oacute;n a lo anterior, se tiene a la parte citada <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong> </span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> por inconforme con todo arreglo conciliatorio; en consecuencia, exp&iacute;dase a la parte solicitante la constancia de haber agotado la etapa de conciliaci&oacute;n.</span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">En este acto, <strong>se hace efectivo el apercibimiento decretado</strong> en el citatorio notificado el <strong class="mceNonEditable" data-nombre="solicitado_fecha_notificacion">[SOLICITADO_FECHA_NOTIFICACION]</strong> y se impone a la parte citada <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong> </span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp;una multa m&iacute;nima por el monto de cincuenta veces la Unidad de Medida y Actualizaci&oacute;n.</span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Por tanto, g&iacute;rese atento oficio electr&oacute;nico al <strong>Servicio de Administraci&oacute;n Tributaria,</strong> para que haga efectivo el cobro de la multa impuesta a la parte citada <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp;con los datos de identificaci&oacute;n con los que se cuenta:</span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>1.</strong> <strong>Nombre o raz&oacute;n social <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong>&nbsp; &nbsp;</strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>2. CURP: <strong class="mceNonEditable" data-nombre="solicitado_curp">[SOLICITADO_CURP]</strong>&nbsp; </strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>3. RFC: <strong class="mceNonEditable" data-nombre="solicitado_rfc">[SOLICITADO_RFC]</strong>&nbsp; </strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>4. Domicilio: <strong class="mceNonEditable" data-nombre="solicitado_domicilios_vialidad">[SOLICITADO_DOMICILIOS_VIALIDAD]</strong>, No. <strong class="mceNonEditable" data-nombre="solicitado_domicilios_num_ext">[SOLICITADO_DOMICILIOS_NUM_EXT]</strong>, <strong class="mceNonEditable" data-nombre="solicitado_domicilios_municipio">[SOLICITADO_DOMICILIOS_MUNICIPIO]</strong>, <strong class="mceNonEditable" data-nombre="solicitado_domicilios_estado">[SOLICITADO_DOMICILIOS_ESTADO]</strong>&nbsp; </strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Notif&iacute;quese personalmente a la parte citada y por buz&oacute;n electr&oacute;nico a la parte solicitante.</strong></span></p>
                <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">As&iacute; lo provey&oacute; <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp; </span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">, funcionario conciliador adscrito al Centro Federal de Conciliaci&oacute;n y Registro Laboral. <strong>Doy fe.</strong></span></p>
                <p style="text-align: center;"><strong class="mceNonEditable" data-nombre="conciliador_qr_firma">[CONCILIADOR_QR_FIRMA]</strong>&nbsp;</p>
                <div id="contenedor-firma" class="mceNonEditable" style="text-align: justify; border-bottom: thin solid black; width: 40%; margin: 0px auto;">
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <strong id="espacio-firma">[ESPACIO_FIRMA]</strong></div>
                <p style="text-align: center;"><strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong></p>
                <p style="text-align: justify;">&nbsp;</p>'
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

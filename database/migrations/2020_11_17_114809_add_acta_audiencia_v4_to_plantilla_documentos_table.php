<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddActaAudienciaV4ToPlantillaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('plantilla_documentos')->where('nombre_plantilla','ACTA DE AUDIENCIA')->update(
            [
              'plantilla_body'=>'<p style="text-align: justify;">&nbsp;</p>
              <p style="text-align: justify; line-height: 8pt;"><span style="font-size: 12pt;"><strong><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">CENTRO FEDERAL DE CONCILIACI&Oacute;N Y REGISTRO LABORAL&nbsp;</span></strong></span></p>
              <p style="text-align: justify; line-height: 8pt;"><span style="font-size: 12pt;"><strong><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">OFICINA DE REPRESENTACI&Oacute;N <strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong>&nbsp; </span></strong></span></p>
              <p style="text-align: justify; line-height: 8pt;"><span style="font-size: 12pt;"><strong><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">N&Uacute;MERO IDENTIFICACI&Oacute;N &Uacute;NICO: <strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong>&nbsp;</span></strong></span></p>
              <p style="text-align: justify;">&nbsp;</p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>ACTA DE AUDIENCIA DE CONCILIACI&Oacute;N PREJUDICIAL</strong></span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">En la<span style="color: #000000;"> Ciudad de M&eacute;xico</span>, siendo las <strong class="mceNonEditable" data-nombre="audiencia_hora_inicio">[AUDIENCIA_HORA_INICIO]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp;del <strong class="mceNonEditable" data-nombre="audiencia_fecha_audiencia">[AUDIENCIA_FECHA_AUDIENCIA]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">, hora y d&iacute;a se&ntilde;alados para la celebraci&oacute;n de la audiencia de conciliaci&oacute;n prejudicial, relativa al n&uacute;mero de expediente electr&oacute;nico con n&uacute;mero de identificaci&oacute;n &uacute;nico <strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong> en la sala de audiencia <strong><strong class="mceNonEditable" data-nombre="sala_nombre">[SALA_NOMBRE]</strong></strong>&nbsp;y, en presencia de <strong><strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong></strong>, funcionario conciliador adscrito al Centro Federal de Conciliaci&oacute;n y Registro Laboral <strong><strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong></strong>&nbsp;con fundamento en los art&iacute;culos 684-A, 684-B, 684-C, 684-D, 684-E, fracci&oacute;n V, 684-F, 684-G y 684-I, de la Ley Federal del Trabajo de la Ley Federal del Trabajo, la <strong>declara abierta</strong> con la comparecencia de:&nbsp;</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">La parte <strong>solicitante</strong> <strong><strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong></strong>, parte quien se identifica con<strong>&nbsp;<strong class="mceNonEditable" data-nombre="solicitante_identificacion_documento">[SOLICITANTE_IDENTIFICACION_DOCUMENTO]</strong></strong>&nbsp;expedida a su favor por<strong> <strong class="mceNonEditable" data-nombre="solicitante_identificacion_expedida_por">[SOLICITANTE_IDENTIFICACION_EXPEDIDA_POR]</strong></strong>&nbsp;y, por la parte <strong>citada</strong>,<strong class="mceNonEditable" data-nombre="">[SI_SOLICITADO_TIPO_PERSONA_FISICA]</strong> <span class="mceNonEditable" style="font-weight: bolder;" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</span>, quien se identifica con <strong class="mceNonEditable" data-nombre="solicitado_identificacion_documento">[SOLICITADO_IDENTIFICACION_DOCUMENTO]</strong><span style="font-size: 0.75rem; letter-spacing: 0px;">, expedida a su favor por <strong class="mceNonEditable" data-nombre="solicitado_identificacion_expedida_por">[SOLICITADO_IDENTIFICACION_EXPEDIDA_POR]</strong></span><span style="font-weight: bolder;">,</span><strong class="mceNonEditable" data-nombre="">[SI_SOLICITADO_TIPO_PERSONA_MORAL]</strong> <strong class="mceNonEditable" data-nombre="solicitado_representante_legal_nombre_completo">[SOLICITADO_REPRESENTANTE_LEGAL_NOMBRE_COMPLETO]</strong> acude en su car&aacute;cter de representante legal de la empresa <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong> circunstancia que se acredita con el instrumento <strong class="mceNonEditable" data-nombre="solicitado_representante_legal_detalle_instrumento">[SOLICITADO_REPRESENTANTE_LEGAL_DETALLE_INSTRUMENTO]</strong><strong>, </strong>quien se identifica con <strong class="mceNonEditable" data-nombre="solicitado_representante_legal_identificacion_documento">[SOLICITADO_REPRESENTANTE_LEGAL_IDENTIFICACION_DOCUMENTO]</strong><strong>&nbsp;</strong>expedida a su favor por <strong class="mceNonEditable" data-nombre="solicitado_representante_legal_identificacion_expedida_por">[SOLICITADO_REPRESENTANTE_LEGAL_IDENTIFICACION_EXPEDIDA_POR]</strong><strong>, </strong><strong class="mceNonEditable" data-nombre="">[FIN_SI_SOLICITADO_TIPO_PERSONA]</strong> identificaciones que concuerdan fision&oacute;micamente con las partes y, que en este acto, se agrega copia certificada al expediente electr&oacute;nico para que conste como corresponda; documentos que les son devueltos por ser innecesaria su retenci&oacute;n.</span></p>
              <p style="text-align: justify;"><strong>&nbsp;</strong><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">La parte citada <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong> <strong class="mceNonEditable" data-nombre="">[SI_SOLICITANTE_NOTIFICA]</strong> fue notificada personalmente el <strong class="mceNonEditable" data-nombre="solicitado_fecha_notificacion">[SOLICITADO_FECHA_NOTIFICACION]</strong>.<strong class="mceNonEditable" data-nombre="">[SI_NO_NOTIFICA]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> fue notificada por la parte solicitante con citatorio emitido el <strong class="mceNonEditable" data-nombre="solicitud_fecha_ratificacion">[SOLICITUD_FECHA_RATIFICACION]</strong>, plazo legal indicado, en t&eacute;rminos de lo dispuesto por el art&iacute;culo 684-E, inciso IV, de la ley de la materia.<strong class="mceNonEditable" data-nombre="">[FIN_SI_SOLICITANTE_NOTIFICA]</strong>&nbsp;</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Por tanto, esta Autoridad Conciliadora, se encuentra en condiciones para desahogar la&nbsp; <strong>Audiencia de Conciliaci&oacute;n Prejudicial</strong>.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="">[SI_AUDIENCIA_POR_SEPARADO]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">La presente audiencia se desarrolla en dos salas de conciliaci&oacute;n con cada parte presente en una sala, debido a lo referido por la parte solicitante de circunstancias previstas en el Art&iacute;culo 684-E fracci&oacute;n XII. <strong class="mceNonEditable" data-nombre="">[FIN_SI_AUDIENCIA_POR_SEPARADO]</strong> </span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Se hace del conocimiento del trabajador(a) que podr&aacute; comparecer asistido por abogado(a) o persona de su confianza, pero no se reconocer&aacute; a &eacute;sta como apoderado, por tratarse de un procedimiento de conciliaci&oacute;n y no de un juicio; por lo que respecta al empleador, &eacute;ste podr&aacute; comparecer a trav&eacute;s de su representante, siempre y cuando cuente con las facultades suficientes para obligarse en su nombre y lo acredite ante esta instancia.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Asimismo, se les informa a las partes que las manifestaciones que realicen durante la audiencia, no podr&aacute;n constituir prueba o indicio en ning&uacute;n procedimiento administrativo o judicial ni el personal de las autoridades conciliadoras podr&aacute;n ser llamados a comparecer como testigos ante los Tribunales Laborales, de conformidad con los establecido en los art&iacute;culos 684-C, inciso V, segundo p&aacute;rrafo y 684-J de la Ley Federal del Trabajo.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">El proceso de conciliaci&oacute;n se realiza de conformidad con los principios constitucionales de legalidad, imparcialidad, confiabilidad, eficacia, objetividad, profesionalismo, transparencia y publicidad. Consecuentemente, es un proceso &aacute;gil, objetivo, imparcial, transparente y eficaz, en el que sus costos son menores en comparaci&oacute;n u procedimiento jurisdiccional, m&aacute;xime que en el procedimiento ni el patr&oacute;n ni el trabajador puede estar seguro de ganar el juicio, mientras que en la conciliaci&oacute;n se llega a un acuerdo en el que se benefician ambas partes.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">A continuaci&oacute;n, se cede el uso de la voz de manera ordenada y respetuosa a los presentes en esta audiencia, para manifestar en relaci&oacute;n al proceso de conciliaci&oacute;n:</span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp;</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="resolucion_primera_manifestacion">[RESOLUCION_PRIMERA_MANIFESTACION]</strong>&nbsp; </span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Asi, resulta procedente exponer a los presentes la propuesta de un acuerdo conciliatorio justo y equitativo que beneficie a ambas partes del conflicto; haciendo de su conocimiento que, en el caso de estar conformes con dicho acuerdo, se proceder&aacute; a realizar el convenio por escrito, mismo que deber&aacute; ratificarse en el presente acto y, posteriormente, se les entregar&aacute; copia certificada del mismo en el que conste su cumplimiento en t&eacute;rminos de los art&iacute;culos 684 I, 684-E, inciso XIV, de la ley Federal del Trabajo.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">La propuesta referida, se encuentra formulada en los t&eacute;rminos siguientes:</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="resolucion_propuesta_configurada">[RESOLUCION_PROPUESTA_CONFIGURADA]</strong>&nbsp; </span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">A efecto de conocer la opini&oacute;n de las partes, se cede el uso de la voz de manera ordenada y respetuosa a los presentes en esta audiencia, con la finalidad de escuchar lo que tengan que expresar en torno a la propuesta y sus alcances, <strong>haciendo de su conocimiento que no se podr&aacute;n negociar derechos y prestaciones irrenunciables en t&eacute;rminos de la Ley Federal del Trabajo</strong>, y respetando los adquiridos; de no estar de acuerdo se podr&aacute; solicitar una nueva audiencia que tendr&aacute; verificativo dentro de los cinco d&iacute;as siguientes al cierre de esta diligencia.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="resolucion_segunda_manifestacion">[RESOLUCION_SEGUNDA_MANIFESTACION]</strong>&nbsp; </span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Por tanto, una vez que las partes han expresado estar conformes con la propuesta sugerida, se procede a la celebraci&oacute;n del convenio respectivo, el cual tendr&aacute; valor de cosa juzgada y, tendr&aacute; la calidad de un t&iacute;tulo para iniciar acciones ejecutivas sin necesidad de ratificaci&oacute;n.&nbsp;</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">En caso de incumplimiento del convenio acordado, cualquiera de las partes podr&aacute; promover su cumplimiento mediante el proceso de ejecuci&oacute;n de sentencia establecido en la Ley Federal del Trabajo y ante los Tribunales Laborales competentes.&nbsp;</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Ahora bien, se hace del conocimiento de las partes que, la informaci&oacute;n aportada durante el procedimiento de conciliaci&oacute;n no podr&aacute; comunicarse a persona o autoridad alguna, a excepci&oacute;n de la constancia de no conciliaci&oacute;n y, en su caso, del convenio de conciliaci&oacute;n que se celebre.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">De igual modo, el tratamiento de los datos proporcionados por los interesados y los datos personales recabados por este Centro Federal de Conciliaci&oacute;n y Registro Laboral, ser&aacute;n protegidos, incorporados y tratados &uacute;nicamente por este Organismo Descentralizado de la Administraci&oacute;n P&uacute;blica Federal, como Sujeto Obligado ante la Ley General de Protecci&oacute;n de Datos Personales en Posesi&oacute;n de Sujetos Obligados y a la Ley General de Transparencia y Acceso a la Informaci&oacute;n P&uacute;blica.&nbsp;</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Asimismo, se informa que sus datos no podr&aacute;n ser difundidos sin el consentimiento expreso, salvo las excepciones previstas en ley.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">As&iacute; lo provey&oacute;, <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong><strong>,</strong> funcionario conciliador adscrito al Centro Federal de Conciliaci&oacute;n y Registro Laboral. <strong>Doy fe.</strong></span></p>
              <p style="text-align: justify;">&nbsp;</p>
              <table style="border-collapse: collapse; width: 90%; height: 129px; margin-left: auto; margin-right: auto;" border="0">
              <tbody>
              <tr>
              <td style="width: 50%;">
              <p>&nbsp;</p>
              <p style="text-align: center;"><strong class="mceNonEditable" data-nombre="solicitante_qr_firma">[SOLICITANTE_QR_FIRMA]</strong>&nbsp;</p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">_________________________________</span></p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>EL SOLICITANTE</strong></span></p>
              </td>
              <td style="width: 50%;">
              <p>&nbsp;</p>
              <p style="text-align: center;"><strong class="mceNonEditable" data-nombre="solicitado_qr_firma">[SOLICITADO_QR_FIRMA]</strong>&nbsp;</p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">___________________________________</span></p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>EL SOLICITADO</strong></span></p>
              </td>
              </tr>
              </tbody>
              </table>
              <p style="text-align: center;"><strong class="mceNonEditable" data-nombre="conciliador_qr_firma">[CONCILIADOR_QR_FIRMA]</strong>&nbsp;</p>
              <div id="contenedor-firma" class="mceNonEditable" style="text-align: justify; border-bottom: thin solid black; width: 40%; margin: 0px auto;">
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <strong id="espacio-firma">[ESPACIO_FIRMA]</strong></div>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><span style="font-size: 12pt;"><strong> &nbsp; <span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp;</span></strong></span></span></p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><span style="font-size: 12pt;"><strong><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">EL FUNCIONARIO CONCILIADOR</span></strong></span></span></p>',
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
        Schema::table('plantilla_documentos', function (Blueprint $table) {
            //
        });
    }
}

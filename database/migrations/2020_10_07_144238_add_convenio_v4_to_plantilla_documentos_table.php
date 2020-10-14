<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConvenioV4ToPlantillaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('plantilla_documentos')->where('nombre_plantilla','CONVENIO')->update(
            [
              'plantilla_body'=>'<p style="line-height: 8pt;">&nbsp;</p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>CENTRO FEDERAL DE CONCILIACI&Oacute;N Y REGISTRO LABORAL</strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>CON SEDE EN LA CIUDAD DE M&Eacute;XICO.</strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>OFICINA DE REPRESENTACI&Oacute;N <strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong>&nbsp; &nbsp;</strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>N&Uacute;MERO DE IDENTIFICACI&Oacute;N &Uacute;NICO <strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong>&nbsp;</strong></span></p>
              <p style="line-height: 8pt;">&nbsp;</p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Con fundamento en los art&iacute;culos 123, apartado A, fracci&oacute;n XXVII, inciso h) p&aacute;rrafo segundo, de la Constituci&oacute;n Pol&iacute;tica de los Estados Unidos Mexicanos; art&iacute;culo 33 y 684-E de la Ley Federal del Trabajo, se celebra el presente convenio por una parte <strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong> </span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">qui&eacute;n en los subsecuente se denominar&aacute; la parte &ldquo;<strong>TRABAJADORA</strong>&rdquo; y, por otro <strong class="mceNonEditable" data-nombre="resolucion_citados_convenio">[RESOLUCION_CITADOS_CONVENIO]</strong> a qui&eacute;n en lo subsecuente se le denominar&aacute; la parte &ldquo;<strong>EMPLEADORA</strong>&rdquo;, a quienes en lo sucesivo de forma conjunta se les denominar&aacute; <strong>&ldquo;LAS PARTES&rdquo;</strong>, quienes se someten y obligan en t&eacute;rminos de las siguientes declaraciones y cl&aacute;usulas:</span></p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>D E C L A R A C I O N E S:</strong></span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>PRIMERA</strong>. La parte <strong>TRABAJADORA </strong>se identifica con <strong class="mceNonEditable" data-nombre="solicitante_identificacion_documento">[SOLICITANTE_IDENTIFICACION_DOCUMENTO]</strong> y, declara ser una persona mayor de edad, por lo que tiene plenas capacidades de goce y ejercicio para convenir o transigir.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>SEGUNDA. </strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><span style="font-weight: bolder;"><span style="font-weight: 400;">Declara <strong class="mceNonEditable" data-nombre="resolucion_segunda_declaracion_convenio">[RESOLUCION_SEGUNDA_DECLARACION_CONVENIO]</strong></span></span><strong><br /></strong></span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>TERCERA</strong>. La parte <strong>TRABAJADORA</strong> y <strong>EMPLEADORA</strong> declaran que, el presente convenio se celebra con la finalidad de dar por terminado el procedimiento de conciliaci&oacute;n prejudicial, seguido ante el <strong>Centro Federal de Conciliaci&oacute;n y Registro Laboral, <strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong></strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">, bajo el n&uacute;mero de identificaci&oacute;n &uacute;nico <strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">.</span></p>
              <p><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>CUARTA</strong>. Declara la parte <strong>TRABAJADORA</strong>:&nbsp;</span></p>
              <p style="padding-left: 40px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">a)Que fue contratada por la parte <strong>EMPLEADORA</strong> desde el <strong><strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_fecha_ingreso">[SOLICITANTE_DATOS_LABORALES_FECHA_INGRESO]</strong></strong>, para prestar sus servicios como <strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_puesto">[SOLICITANTE_DATOS_LABORALES_PUESTO]</strong>, puesto en el que se desempe&ntilde;&oacute; hasta el d&iacute;a <strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_fecha_salida">[SOLICITANTE_DATOS_LABORALES_FECHA_SALIDA]</strong>&nbsp; que aleg&oacute; ser despedida por su empleador.</span></p>
              <p style="text-align: justify; padding-left: 40px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">b) Que por el desempe&ntilde;o de sus labores contaba con las siguientes prestaciones:</span></p>
              <p style="text-align: justify; padding-left: 80px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">- Salario mensual:<strong> $<strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_salario_mensual">[SOLICITANTE_DATOS_LABORALES_SALARIO_MENSUAL]</strong> </strong>(<strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_salario_mensual_letra">[SOLICITANTE_DATOS_LABORALES_SALARIO_MENSUAL_LETRA]</strong> moneda nacional).&nbsp;</span></p>
              <p style="text-align: justify; padding-left: 80px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">- D&iacute;as de descanso: <strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_dias_descanso">[SOLICITANTE_DATOS_LABORALES_DIAS_DESCANSO]</strong></span></p>
              <p style="text-align: justify; padding-left: 80px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">- Vacaciones: <strong><strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_dias_vacaciones">[SOLICITANTE_DATOS_LABORALES_DIAS_VACACIONES]</strong></strong>&nbsp;d&iacute;as al a&ntilde;o.</span></p>
              <p style="text-align: justify; padding-left: 80px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">- Aguinaldo:<strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_dias_aguinaldo">[SOLICITANTE_DATOS_LABORALES_DIAS_AGUINALDO]</strong> d&iacute;as al a&ntilde;o.</span></p>
              <p style="text-align: justify; padding-left: 80px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">- Otras prestaciones: <strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_prestaciones_adicionales">[SOLICITANTE_DATOS_LABORALES_PRESTACIONES_ADICIONALES]</strong>.</span></p>
              <p style="text-align: justify; padding-left: 40px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">c) Que desempe&ntilde;ana sus actividades laborales en las siguientes condiciones:&nbsp;</span></p>
              <p style="text-align: justify; padding-left: 80px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">- Horario: <strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_horario_laboral">[SOLICITANTE_DATOS_LABORALES_HORARIO_LABORAL]</strong></span></p>
              <p style="text-align: justify; padding-left: 80px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">- Horario de comida: de <strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_horario_comida">[SOLICITANTE_DATOS_LABORALES_HORARIO_COMIDA]</strong> <strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_comida_dentro">[SOLICITANTE_DATOS_LABORALES_COMIDA_DENTRO]</strong>&nbsp; de las instalaciones.</span></p>
              <p style="text-align: justify; padding-left: 80px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">- Domicilio : ubicado en la calle <strong class="mceNonEditable" data-nombre="solicitado_domicilios_completo">[SOLICITADO_DOMICILIOS_COMPLETO]</strong><strong>.</strong></span></p>
              <p style="text-align: justify; padding-left: 40px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">d) Que present&oacute; solicitud el d&iacute;a <strong><strong class="mceNonEditable" data-nombre="solicitud_fecha_ratificacion">[SOLICITUD_FECHA_RATIFICACION]</strong></strong> para iniciar el procedimiento de conciliaci&oacute;n prejudicial ante el Centro Federal de Conciliaci&oacute;n y Registro Laboral, con sede en Ciudad de M&eacute;xico, con objeto de <strong class="mceNonEditable" data-nombre="solicitud_objeto_solicitudes">[SOLICITUD_OBJETO_SOLICITUDES]</strong>.</span></p>
              <p style="text-align: justify; padding-left: 40px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">e) Que el Centro Federal, fij&oacute; la audiencia de conciliaci&oacute;n para el d&iacute;a&nbsp; <strong class="mceNonEditable" data-nombre="audiencia_fecha_audiencia">[AUDIENCIA_FECHA_AUDIENCIA]</strong>.</span></p>
              <p style="text-align: justify;"><br /><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>QUINTA</strong>. Declara la parte <strong>EMPLEADORA</strong>:</span></p>
              <p style="text-align: justify; padding-left: 40px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">a)Que la parte <strong>TRABAJADORA</strong>, fue contratada para laborar como <strong class="mceNonEditable" data-nombre="solicitante_datos_laborales_puesto">[SOLICITANTE_DATOS_LABORALES_PUESTO]</strong> en el domicilio ubicado en <strong><strong class="mceNonEditable" data-nombre="solicitado_domicilios_completo">[SOLICITADO_DOMICILIOS_COMPLETO]</strong>.</strong></span></p>
              <p style="text-align: justify; padding-left: 40px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">b)Que el d&iacute;a <strong class="mceNonEditable" data-nombre="solicitud_fecha_ratificacion">[SOLICITUD_FECHA_RATIFICACION]</strong>, en el domicilio ubicado en la calle <strong class="mceNonEditable" data-nombre="solicitado_domicilios_completo">[SOLICITADO_DOMICILIOS_COMPLETO]</strong>, se recibi&oacute; el citatorio para la celebraci&oacute;n de la audiencia de conciliaci&oacute;n de la parte <strong>TRABAJADORA</strong>, con motivo de <strong class="mceNonEditable" data-nombre="solicitud_objeto_solicitudes">[SOLICITUD_OBJETO_SOLICITUDES]</strong>.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>SEXTA</strong>. Declaran las <strong>PARTES</strong>:</span></p>
              <p style="text-align: justify; padding-left: 40px;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">a) Que el d&iacute;a <strong class="mceNonEditable" data-nombre="audiencia_fecha_audiencia">[AUDIENCIA_FECHA_AUDIENCIA]</strong>, se celebr&oacute; la audiencia de conciliaci&oacute;n y, que, por as&iacute; convenir a sus intereses, la <strong>TRABAJADORA</strong> y <strong>EMPLEADORA</strong> han llegado a un acuerdo para dirimir el conflicto suscitado, al tenor de las siguientes.</span></p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>C L &Aacute; U S U L A S:</strong></span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>PRIMERA</strong>. La parte <strong>TRABAJADORA</strong> y <strong>EMPLEADORA</strong>, han determinado que por as&iacute; convenir a sus intereses dan por concluida la relaci&oacute;n laboral por mutuo acuerdo, conforme a lo estipulado por el art&iacute;culo 53, fracci&oacute;n I, de la Ley Federal del Trabajo.&nbsp;</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>SEGUNDA</strong>. La parte <strong>TRABAJADORA</strong> manifiesta bajo protesta de decir verdad, que el v&iacute;nculo laboral lo mantuvo exclusivamente con la <strong>EMPLEADORA</strong>. Por lo anterior, expresa que no existi&oacute; relaci&oacute;n laboral alguna con otras personas, incluido el personal que fung&iacute;a como superior jer&aacute;rquico en el centro de trabajo donde la <strong>TRABAJADORA</strong> desempe&ntilde;aba sus labores.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>TERCERA</strong>. La <strong>EMPLEADORA</strong> otorga en favor de la <strong>TRABAJADORA</strong> el pago acordado conforme a las disposiciones de la Ley Federal del Trabajo y respetando los derechos consagrados en el mismo ordenamiento legal. Asimismo, La <strong>TRABAJADORA</strong> manifiesta su entera conformidad y la aceptaci&oacute;n de &eacute;ste, as&iacute; como la forma en que se obtuvieron los conceptos que se describen en la cl&aacute;usula QUINTA.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp;<strong>CUARTA</strong>. La <strong>TRABAJADORA</strong> manifiesta que durante el tiempo que labor&oacute; para la parte <strong>EMPLEADORA</strong>, se cubri&oacute; en tiempo y forma el pago su salario; cada una de las prestaciones ordinarias y extraordinarias y en especie que conforme a derecho le corresponden, as&iacute; mismo como cualquier riesgo o accidente de trabajo que haya sufrido. Por lo anterior, la parte <strong>EMPLEADORA</strong> no adeuda pago de concepto alguno.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>QUINTA</strong>. La <strong>TRABAJADORA</strong> recibir&aacute; por parte de la <strong>EMPLEADORA</strong> la cantidad de <strong>$<strong class="mceNonEditable" data-nombre="resolucion_total_percepciones">[RESOLUCION_TOTAL_PERCEPCIONES]</strong>&nbsp; </strong>(<strong class="mceNonEditable" data-nombre="resolucion_total_percepciones_letra">[RESOLUCION_TOTAL_PERCEPCIONES_LETRA]</strong> moneda nacional), conforme a los siguientes conceptos:</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="resolucion_propuesta_configurada">[RESOLUCION_PROPUESTA_CONFIGURADA]</strong></span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="">[SI_RESOLUCION_PAGO_DIFERIDO]</strong><strong>SEXTA.</strong>La &ldquo;EMPLEADORA&rdquo; pagar&aacute; en <strong class="mceNonEditable" data-nombre="resolucion_total_diferidos">[RESOLUCION_TOTAL_DIFERIDOS]</strong> exhibiciones, hasta culminar la cantidad de $<strong class="mceNonEditable" data-nombre="resolucion_total_percepciones">[RESOLUCION_TOTAL_PERCEPCIONES]</strong> (<strong class="mceNonEditable" data-nombre="resolucion_total_percepciones_letra">[RESOLUCION_TOTAL_PERCEPCIONES_LETRA]</strong> moneda nacional), tal como se muestra:</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="resolucion_pagos_diferidos">[RESOLUCION_PAGOS_DIFERIDOS]</strong>&nbsp; </span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="">[SI_RESOLUCION_PAGO_NO_DIFERIDO]</strong><span style="font-weight: bolder;">SEXTA.</span>&nbsp;La&nbsp;<span style="font-weight: bolder;">EMPLEADORA</span>&nbsp;manifiesta que pagar&aacute; a la&nbsp;<span style="font-weight: bolder;">TRABAJADORA</span>&nbsp;en una sola exhibici&oacute;n la cantidad de $<span class="mceNonEditable" style="font-weight: bolder;" data-nombre="resolucion_total_percepciones">[RESOLUCION_TOTAL_PERCEPCIONES]</span>&nbsp;(<span class="mceNonEditable" style="font-weight: bolder;" data-nombre="resolucion_total_percepciones_letra">[RESOLUCION_TOTAL_PERCEPCIONES_LETRA]</span>&nbsp;moneda nacional), el<span style="font-weight: bolder;">&nbsp;<span class="mceNonEditable" style="font-weight: bolder;" data-nombre="solicitud_fecha_ratificacion">[SOLICITUD_FECHA_RATIFICACION]</span></span>, en el domicilio que ocupa el Centro Federal de Conciliaci&oacute;n y Registro Laboral, con sede en Ciudad de M&eacute;xico, para que se certifique el cumplimiento de esta obligaci&oacute;n, de conformidad con lo establecido en el art&iacute;culo 684-E, fracci&oacute;n XIV, de la Ley Federal del Trabajo.<br /><strong class="mceNonEditable" data-nombre="">[FIN_SI_RESOLUCION_PAGO]</strong>&nbsp; </span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>S&Eacute;PTIMA</strong>. En caso de que la <strong>EMPLEADORA</strong> no cubra el pago de la cantidad estipulada y dentro del plazo determinado en la cl&aacute;usula <strong>SEXTA</strong>, deber&aacute; pagar a la <strong>TRABAJADORA</strong> el equivalente a un d&iacute;a de salario diario, el cual se fijar&aacute; en raz&oacute;n del salario que percib&iacute;a dicha parte antes de finalizar la relaci&oacute;n de trabajo. Esa cantidad se sumar&aacute; a la previamente pactada, por cada d&iacute;a que transcurra, sin que se d&eacute; cabal cumplimiento al convenio, con fundamento en el art&iacute;culo 684-E, fracci&oacute;n XIV, &uacute;ltimo p&aacute;rrafo, de la Ley Federal del Trabajo.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>OCTAVA</strong>. Las partes solicitan se apruebe y sancione este convenio, toda vez que se elabor&oacute; conforme a las disposiciones aplicables de la Ley Federal del Trabajo como resultado del di&aacute;logo de la conciliaci&oacute;n entre la <strong>TRABAJADORA</strong> y la <strong>EMPLEADORA</strong>. Asimismo, manifiestan que se encuentran conformes con el presente acuerdo por no contener cl&aacute;usula contraria a la costumbre, a la moral, ni renuncia a los derechos de las partes.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>NOVENA</strong>. <strong>LAS PARTES</strong>&nbsp;manifiestan que es su es su voluntad ratificar el presente convenio en todas y cada una de sus partes y la aprobaci&oacute;n de su contenido, por lo que no se reservan acci&oacute;n legal o derecho alguno para ejercitar con posterioridad a la firma del presente convenio.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>D&Eacute;CIMA</strong>. <strong>LAS PARTES</strong> solicitan ante el Centro Federal de Conciliaci&oacute;n y Registro Laboral que les sean expedidas las copias autorizadas del convenio, y en el momento que se haya cumplido totalmente, se les expida acta en la que conste el cumplimiento de &eacute;ste, en t&eacute;rminos del art&iacute;culo 684-E, fracci&oacute;n XIV, primer p&aacute;rrafo, de la Ley Federal del Trabajo.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>D&Eacute;CIMA PRIMERA.</strong> <strong>LAS PARTES</strong> manifiestan que, en la celebraci&oacute;n del presente convenio, no existi&oacute; violencia, mala fe, dolo, lesi&oacute;n o cualquier otro tipo de vicio del consentimiento que pudiera nulificarlo.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>D&Eacute;CIMA SEGUNDA.</strong> En caso de que no se cumpla los t&eacute;rminos de lo convenido en el presente instrumento, las partes deber&aacute;n acudir a los Tribunales Laborales a efecto de que se realice el procedimiento de ejecuci&oacute;n que la Ley Federal del Trabajo contempla.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Enteradas las partes del alcance legal del presente convenio que se eleva a cosa juzgada, conforme al art&iacute;culo 684-E fracci&oacute;n XIII, mismo que se firma en <strong>LUGAR a los <strong class="mceNonEditable" data-nombre="fecha_actual">[FECHA_ACTUAL]</strong></strong>, ante la fe de<strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong><strong>,</strong>&nbsp;funcionario conciliador, quien lo sanciona en este mismo acto. <strong>Doy fe.</strong></span></p>
              <table style="border-collapse: collapse; width: 100%;" border="0">
              <tbody>
              <tr>
              <td style="width: 50%; text-align: center;">
              <p>&nbsp;</p>
              <p><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">_________________________ &nbsp; &nbsp;</span></p>
              <p><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong>&nbsp; </span></p>
              <p><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>LA TRABAJADORA&nbsp;</strong></span></p>
              </td>
              <td style="width: 50%;">
              <p style="text-align: center;">&nbsp;</p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp;________________________</span></p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong>&nbsp; </span></p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>LA EMPLEADORA</strong></span></p>
              </td>
              </tr>
              </tbody>
              </table>
              <p style="text-align: center;">&nbsp;</p>
              <div id="contenedor-firma" class="mceNonEditable" style="text-align: center; border-bottom: thin solid black; width: 40%; margin: 0 auto;">
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <strong id="espacio-firma">[ESPACIO_FIRMA]</strong></div>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp; &nbsp;<strong><strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp; </strong></span></p>
              <p style="text-align: center;"><strong><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">FUNCIONARIO CONCILIADOR</span></strong></p>
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
        Schema::table('plantilla_documentos', function (Blueprint $table) {
            //
        });
    }
}

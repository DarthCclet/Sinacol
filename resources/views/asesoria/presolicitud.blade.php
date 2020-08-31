@extends('asesoria.default')
@include('includes.component.datatables')
@include('includes.component.pickers')
@section('content')
    <div class="align-middle align-center" style="margin-left: 10%; margin-right:10%; ">
        <div  data-parsley-validate="true" class="col-md-12 row DatosLaborales">
            <div class="col-md-12 mt-4">
                <h3>Datos laborales para cuantificaci&oacute;n</h3>
                <hr class="red">
            </div>
            <input type="hidden" id="dato_laboral_id">

            <div class="col-md-12 row">
                <div class="col-md-4">
                    <input class="form-control numero required" required data-parsley-type='number' id="remuneracion" max="99999999" placeholder="¿Cu&aacute;nto te pagan?" type="text" value="">
                    <p class="help-block needed">&iquest;Cu&aacute;nto te pagan?</p>
                </div>
                <div class="col-md-4">
                    {!! Form::select('periodicidad_id', isset($periodicidades) ? $periodicidades : [] , null, ['id'=>'periodicidad_id','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect required']);  !!}
                    {!! $errors->first('periodicidad_id', '<span class=text-danger>:message</span>') !!}
                    <p class="help-block needed">&iquest;Cada cuándo te pagan?</p>
                </div>
            </div>
            <div class="col-md-12 row">
                <div class="col-md-4 row" style="display:{{isset($origen) && $origen == '10101010' ? 'none' : 'block'}}">
                    <div class="col-md-6">
                        <span class="text-muted m-l-5 m-r-20" for='switch1'>Labora actualmente</span>
                    </div>
                    <div class="col-md-6" >
                        <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="labora_actualmente" name='labora_actualmente'/>
                    </div>
                </div>
                <div class="col-md-4">
                    <input class="form-control dateBirth required" required id="fecha_ingreso" placeholder="Fecha de ingreso" type="text" value="">
                    <p class="help-block needed">Fecha de ingreso</p>
                </div>
                <div class="col-md-4" id="divFechaSalida">
                    <input class="form-control dateBirth" id="fecha_salida" placeholder="Fecha salida" type="text" value="">
                    <p class="help-block needed">Fecha salida</p>
                </div>
            </div>
            <div class="col-md-6" >
                {!! Form::select('ocupacion_id', isset($ocupaciones) ? $ocupaciones : [] , null, ['id'=>'ocupacion_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                {!! $errors->first('ocupacion_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block ">En caso de desempeñar un oficio que cuenta con salario mínimo distinto al general, escoja del catálogo. Si no, deje vacío.</p>
            </div>
            <div>
                <a style="font-size: medium;" onclick="$('#modal-jornada').modal('show');"><i class="fa fa-question-circle"></i></a>
            </div>
        </div>
        <div>
            <button onclick="getDatosLaboralesParte()"  class="btn btn-primary DatosLaborales">Mostrar calculos</button>
            <button style="display: none; margin-top:2%;"  onclick="editarDatos()" class="btn btn-primary divPropuesta">Editar datos</button>
        </div>
    </div>
    <div  style="display: none; margin: 3% 15% 0 15%;" class="align-middle align-center divPropuesta">
        <input type="hidden" id="origen" value="{{$origen}}" />
        <input type="hidden" id="remuneracionDiaria" />
        <input type="hidden" id="salarioMinimo"/>
        <input type="hidden" id="antiguedad"/>
        <div>
            @if($origen == "10101010")
                <p>La propuesta completa reúne las indemnizaciones por despido, que incluyen la indemnización constitucional y la prima de antigüedad, al 100%. Se suman a esta propuesta el 100% de las prestaciones adquiridas de aguinaldo, vacaciones y prima vacacional. La propuesta de 45 días incluye la mitad de la indemnización constitucional, la mitad de la prima de antigüedad y al 100% de las prestaciones adquiridas. Generalmente en la pláticas y audiencias de conciliación, se arregla el conflicto de despido en una rango entre estas dos propuestas.</p>
            @elseif($origen == "10201010" || $origen == "10301010")
                <p>PRESTACIONES: Se muestran el cálculo las prestaciones de la Ley Federal del Trabajo, el aguinaldo, las vacaciones y la prima vacacional, proporcionales en cada caso al año en curso. En caso de que haya laborado 15 años o más, se muestra adicionalmente la prima de antigüedad porque ésta vuelve una prestación adquirida. El finiquito de ley en caso de que haya renunciado de manera voluntaria, incluye estas prestaciones (sin o con la prima de antigüedad dependiendo de no haber o haber cumplido 15 años de servicio) además de cualquier salario u otra prestación devengada (ejemplo: días laborados que no se pagaron, aguinaldo del año anterior, etc.)</p>
            @elseif($origen == "10401010")
                <p>RESCISIÓN: En el caso de la rescisión de la relación de trabajo por culpa de acciones del patrón y sin culpa del trabajador, el 100% de la compensación legal incluye indemnización constitucional de 90 días de salario, la prima de antigüedad y las prestaciones adquiridas aguinaldo, vacaciones y prima vacacional. Adicionalmente, el trabajador debe recibir 20 días por año laborado a su salario actual, lo que se llama Gratificación B en la tabla a continuación. Es importante recordar que aunque aquí le mostramos el cálculo del 100%, dada la incertidumbre, costo y tiempo de un juicio laboral, en la conciliación es recomendable considerar el arreglo del conflicto en una menor cantidad que la de un juicio ganado.</p>
            @endif
        </div>
        <div>
            <table class="table" id="divTablaCompleto" style="display: none;">
                <thead>
                    <tr>
                        <th>Prestaci&oacute;n</th><th>Propuesta completa</th><th>Propuesta 45 d&iacute;as</th>
                    </tr>
                </thead>
                <tbody id="tbodyPropuestaCompleto">
                </tbody>
            </table>
            <table class="table" id="divTablaAjuste" style="display: none;">
                <thead>
                    <tr>
                        <th>Prestaci&oacute;n</th><th>Propuesta completa</th>
                    </tr>
                </thead>
                <tbody id="tbodyPropuesta">
                </tbody>
            </table>
        </div>
    </div>

<!-- inicio Modal Domicilio-->

 <div class="modal" id="modal-jornada" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog ">
        <div class="modal-content">

            <div class="modal-body" >
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5>Para determinar tu tipo de jornada, debes considerar las primeras 8 horas que laboras en un día.</h5>
                <p style="font-size:large;">
                    <ol>
                        <li>Si estas 8 horas transcurren entre 6 am y 8 pm, es una jornada "DIURNA".</li>
                        <li>Si estas primeras 8 horas incluyen 3 horas o menos dentro del horario 8 pm - 6 am, es una jornada "MIXTA"</li>
                        <li>Si estas 8 horas incluyen 3.5 o más horas dentro del horario 8 pm - 6 am, es una jornada NOCTURNA. </li>
                        <li>En caso de que tengas algunas jornadas diurnas y otras mixtas o nocturnas, debes poner una jornada "MIXTA".</li>
                    </ol>
                </p>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-primary btn-sm" class="close" data-dismiss="modal" aria-hidden="true" ><i class="fa fa-times"></i> Aceptar</a>
                </div>
            </div>
        </div>
    </div>
</div>
    <a href="/asesoria/101010101010" class="btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
<!-- Fin Modal de Domicilio-->

@push('scripts')

<script>
    var listaPropuestas={};
    $(".catSelect").select2({width: '100%'});
    $("#labora_actualmente").change(function(){
        if($("#labora_actualmente").is(":checked")){
            $("#divFechaSalida").hide();
            $("#fecha_salida").removeAttr("required");
            $("#fecha_salida").removeClass("required");
            $("#fecha_salida").val("");
        }else{
            $("#fecha_salida").addClass("required");
            $("#divFechaSalida").show();
        }
    });

    function getDatosLaboralesParte(){
        var validate = true;
        $(".required").each(function(){
            if($(this).val() == ""){
                validate = false;
            }
        });
        if(validate){
            $.ajax({
                url:"/api/conceptos-resolucion/getLaboralesConceptosPre",
                type:"POST",
                dataType:"json",
                data:{
                    periodicidad_id:$("#periodicidad_id").val(),
                    origen:$("#origen").val(),
                    remuneracion:$("#remuneracion").val(),
                    ocupacion_id:$("#ocupacion_id").val(),
                    fecha_ingreso:dateFormat($("#fecha_ingreso").val()),
                    fecha_salida:dateFormat($("#fecha_salida").val()),
                    labora_actualmente:$("#labora_actualmente").is(":checked"),
                },
                success:function(datos){
                    var datosLaborales = {};
                    datosLaborales.periodicidad_id=$("#periodicidad_id").val();
                    datosLaborales.remuneracion=$("#remuneracion").val();
                    datosLaborales.ocupacion_id=$("#ocupacion_id").val();
                    datosLaborales.fecha_ingreso=dateFormat($("#fecha_ingreso").val());
                    datosLaborales.fecha_salida=dateFormat($("#fecha_salida").val());
                    datosLaborales.labora_actualmente=$("#labora_actualmente").is(":checked");
                    localStorage.setItem("datos_laborales",JSON.stringify(datosLaborales))
                    let dato = datos.data;
                    listaPropuestas[dato.idParte]= [];
                    listaPropuestas[dato.idParte]['completa'] = [];
                    listaPropuestas[dato.idParte]['al50'] = [];
                    $.each(dato.propuestaCompleta,function(index,propuesta){
                        listaPropuestas[propuesta.idSolicitante]['completa'].push({
                            'idSolicitante':propuesta.idSolicitante,
                            'concepto_pago_resoluciones_id':propuesta.concepto_pago_resoluciones_id,
                            'dias':propuesta.dias,
                            'monto':propuesta.monto,
                            'otro':''
                        });
                    });
                    $.each(dato.propuestaAl50,function(index,propuesta){
                        listaPropuestas[propuesta.idSolicitante]['al50'].push({
                            'idSolicitante':propuesta.idSolicitante,
                            'concepto_pago_resoluciones_id':propuesta.concepto_pago_resoluciones_id,
                            'dias':propuesta.dias,
                            'monto':propuesta.monto,
                            'otro':''
                        });
                    });

                    $('#remuneracionDiaria').val(dato.remuneracionDiaria);
                    $('#salarioMinimo').val(dato.salarioMinimo);
                    $('#antiguedad').val(dato.antiguedad);
                    if($("#origen").val() == "10101010"){
                        let table = "";
                        table+=" <tr>";
                        table+=' <th>Indemnización constitucional</th><td class="amount"> $'+ (dato.completa.indemnizacion).toLocaleString("en-US")+'</td><td class="amount" > $'+ (dato.al50.indemnizacion).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Aguinaldo</th><td class="amount"> $'+ (dato.completa.aguinaldo ).toLocaleString("en-US") +'</td><td class="amount"> $'+ (dato.al50.aguinaldo).toLocaleString("en-US").toLocaleString("en-US") +"</td>";
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Vacaciones</th><td class="amount"> $'+ (dato.completa.vacaciones).toLocaleString("en-US") +'</td><td class="amount"> $'+ (dato.al50.vacaciones).toLocaleString("en-US").toLocaleString("en-US") +"</td>";
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Prima vacacional</th><td class="amount"> $'+ (dato.completa.prima_vacacional ).toLocaleString("en-US") +'</td><td class="amount"> $'+ (dato.al50.prima_vacacional).toLocaleString("en-US") +"</td>";
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Prima antigüedad</th><td class="amount"> $'+ (dato.completa.prima_antiguedad ).toLocaleString("en-US") +'</td><td class="amount"> $'+ (dato.al50.prima_antiguedad).toLocaleString("en-US") +"</td>";
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th style=> TOTAL PRESTACIONES LEGALES</th><td class="amount"> $'+ (dato.completa.total ).toLocaleString("en-US") +'</td><td class="amount"> $'+ (dato.al50.total).toLocaleString("en-US") +"</td>";
                        table+=" </tr>";
                        $('#tbodyPropuestaCompleto').html(table);
                        $('#divTablaCompleto').show();
                        $('#divTablaAjuste').hide();
                    }else if($("#origen").val() == "10201010" || $("#origen").val() == "10301010"){
                        var total = dato.completa.aguinaldo + dato.completa.vacaciones +dato.completa.prima_vacacional;
                        let table = "";
                        table+=" <tr>";
                        table+=' <th>Aguinaldo</th><td class="amount"> $'+ (dato.completa.aguinaldo ).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Vacaciones</th><td class="amount"> $'+ (dato.completa.vacaciones).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Prima vacacional</th><td class="amount"> $'+ (dato.completa.prima_vacacional ).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        if(dato.anios_antiguedad >= 15){
                            table+=" <tr>";
                            table+=' <th>Prima antigüedad</th><td class="amount"> $'+ (dato.completa.prima_antiguedad ).toLocaleString("en-US") +'</td>';
                            table+=" </tr>";
                            total = total + dato.completa.prima_antiguedad;
                        }
                        table+=" <tr>";
                        table+=' <th style=> TOTAL PRESTACIONES LEGALES</th><td class="amount"> $'+ (total ).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        $('#tbodyPropuesta').html(table);
                        $('#divTablaAjuste').show();
                        $('#divTablaCompleto').hide();
                    }else if($("#origen").val() == "10401010"){
                        var total = dato.completa.aguinaldo + dato.completa.vacaciones +dato.completa.prima_vacacional+dato.completa.prima_antiguedad+dato.completa.gratificacion_b;
                        let table = "";
                        table+=" <tr>";
                        table+=' <th>Indemnización constitucional</th><td class="amount"> $'+ (dato.completa.indemnizacion).toLocaleString("en-US")+'</td>';
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Aguinaldo</th><td class="amount"> $'+ (dato.completa.aguinaldo ).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Vacaciones</th><td class="amount"> $'+ (dato.completa.vacaciones).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Prima vacacional</th><td class="amount"> $'+ (dato.completa.prima_vacacional ).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Prima antigüedad</th><td class="amount"> $'+ (dato.completa.prima_antiguedad ).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th>Gratificaci&oacute;n B (20 d&iacute;as)</th><td class="amount"> $'+ (dato.completa.gratificacion_b ).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        table+=" <tr>";
                        table+=' <th style=> TOTAL PRESTACIONES LEGALES</th><td class="amount"> $'+ (total ).toLocaleString("en-US") +'</td>';
                        table+=" </tr>";
                        $('#tbodyPropuesta').html(table);
                        $('#divTablaAjuste').show();
                        $('#divTablaCompleto').hide();
                    }
                }
            });
            $(".divPropuesta").show();
            $(".editarDatos").show();
            $(".DatosLaborales").hide();
        }else{
            $(".divPropuesta").hide();
            $(".editarDatos").hide();
            $(".DatosLaborales").show();
            $('#tbodyPropuesta').html("");
            $('#divTablaAjuste').hide();
            $('#divTablaCompleto').hide();
            swal({
                title: 'Error',
                text: 'Es necesario capturar todos los campos requeridos',
                icon: 'error',
            });
        }
    }
    function editarDatos(){
        $(".divPropuesta").hide();
        $(".editarDatos").hide();
        $(".DatosLaborales").show();
        $('#tbodyPropuesta').html("");
        $('#divTablaAjuste').hide();
        $('#divTablaCompleto').hide();
    }
    $(".dateBirth").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "c-80:",
        format:'dd/mm/yyyy',
    });
</script>
@endpush

@endsection

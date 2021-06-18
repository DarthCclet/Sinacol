@extends('layouts.default', ['paceTop' => true])

@section('title', 'Reportes')

@include('includes.component.daterangepicker')

@section('content')
<style>
    .select2-selection__clear {
        margin-right: 10px;
    }
</style>
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Administración</a></li>
        <li class="breadcrumb-item active">Reportes</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Consultar reportes</h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Seleccione los parámetros deseados</h4>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            {!! Form::open(['route' => 'reportes.reporte','method'=>'GET']) !!}

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Periodo o rango de consulta</label>
                <div class="col-lg-8">
                    <div id="advance-daterange" class="btn btn-default btn-block text-left">
                        <i class="fa fa-caret-down pull-right m-t-2"></i>
                        <span></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Tipo Reporte</label>
                <div class="col-lg-8">
                    {!! Form::select('tipo_reporte', ['agregado' => 'Agregado','desagregado' => 'Desagregado', 'operativo'=>'Reporte operativo'] ,'agregado' , ['id'=>'tipo_reporte', 'class' => 'form-control']);  !!}
                </div>
            </div>

            <div class="form-group row reportes-agregado-desagregado">
                <label class="col-lg-4 col-form-label">Centro</label>
                <div class="col-lg-8">
                    {!! Form::select('centro[]', isset($centros) ? $centros : [] ,null , ['id'=>'centro', 'class' => 'form-control select2', 'multiple'=>'multiple']);  !!}
                </div>
            </div>

            <div class="form-group row reportes-agregado-desagregado">
                <label class="col-lg-4 col-form-label">Conciliador</label>
                <div class="col-lg-8">
                    {!! Form::select('conciliadores[]', isset($conciliadores) ? $conciliadores : [] ,null , ['id'=>'conciliadores', 'class' => 'form-control select2', 'multiple'=>'multiple']);  !!}
                </div>
            </div>

            <div class="form-group row reportes-agregado-desagregado">
                <label class="col-lg-4 col-form-label">Tipo solicitud</label>
                <div class="col-lg-8">
                    {!! Form::select('tipo_solicitud_id', [1 => 'Trabajador individual',2 => 'Patrón individual'] ,null , ['id'=>'tipo_solicitud_id', 'placeholder' => 'Seleccione una opción', 'class' => 'form-control']);  !!}
                </div>
            </div>

            <div class="form-group row reportes-agregado-desagregado">
                <label class="col-lg-4 col-form-label">Objeto de la solicitud</label>
                <div class="col-lg-8">
                    {!! Form::select('objeto_solicitud_id[]', [] ,null , ['id'=>'objeto_id', 'class' => 'form-control select2', 'multiple'=>'multiple']);  !!}
                </div>
            </div>

            <div class="form-group row reportes-agregado-desagregado">
                <label class="col-lg-4 col-form-label">Industria</label>
                <div class="col-lg-8">
                    {!! Form::select('giro_id[]', isset($tipoIndustria) ? $tipoIndustria : [] ,null , ['id'=>'giro_id', 'class' => 'form-control select2', 'multiple'=>'multiple']);  !!}
                </div>
            </div>

            <div class="form-group row reportes-agregado-desagregado">
                <label class="col-lg-4 col-form-label">Género</label>
                <div class="col-lg-8">
                    {!! Form::select('genero_id', [2 => 'Femenino',1 => 'Masculino'] ,null , ['id'=>'genero_id', 'placeholder' => 'Seleccione una opción', 'class' => 'form-control select2']);  !!}
                </div>
            </div>

            <div class="form-group row reportes-agregado-desagregado">
                <label class="col-lg-4 col-form-label">Grupo etario</label>
                <div class="col-lg-8">
                    {!! Form::select('grupo_id[]', isset($grupo_etario) ? $grupo_etario : []   ,null , ['id'=>'grupo_id', 'class' => 'form-control select2', 'multiple'=>'multiple']);  !!}
                </div>
            </div>

            <div class="form-group row reportes-agregado-desagregado">
                <label class="col-lg-4 col-form-label">Tipo Persona</label>
                <div class="col-lg-8">
                    {!! Form::select('tipo_persona_id', [1 => 'Física',2 => 'Moral'] ,null , ['id'=>'tipo_persona_id', 'placeholder' => 'Seleccione una opción', 'class' => 'form-control select2']);  !!}
                </div>
            </div>


            <input type="hidden" name="fecha_inicial" id="fecha_inicial">
            <input type="hidden" name="fecha_final" id="fecha_final">
            <input type="hidden" name="edad_inicial" id="edad_inicial">
            <input type="hidden" name="edad_final" id="edad_final">

            <div class="panel-footer text-right" id="div-reporte-operativo" style="display: none;">
                <a href="{!! route('reportes.forma') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
                <button class="btn btn-primary btn-sm m-l-5" type="button" id="btn-reporte-operativo"><i class="fa fa-file-excel"></i> Generar reporte</button>
            </div>

            <div class="panel-footer text-right" id="div-reportes">
                <a href="{!! route('reportes.forma') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
                <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-file-excel"></i> Generar reporte</button>
            </div>

                @if(isset($querys) && $querys)
                    <input type="hidden" id="querys" name="querys" value="1"/>
                @endif
            {!! Form::close() !!}
            <input type="hidden" id="lista-conciliadores" name="lista_conciliadores" value="{{$conciliadoresJson}}"/>
            <input type="hidden" id="lista-objetos" name="lista_objetos" value="{{$tipoObjetosJson}}"/>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('#advance-daterange span').html((moment().subtract(1, 'month').startOf('month').format('MMMM D, YYYY','es') + ' - ' + moment().subtract(1, 'month').endOf('month').format('MMMM D, YYYY','es')).toUpperCase());
            $('#fecha_inicial').val(moment().subtract(1, 'month').startOf('month').format('YYYY-MM-DD'));
            $('#fecha_final').val(moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD'));
            $('#advance-daterange').daterangepicker({
                format: 'MM/DD/YYYY',
                startDate: moment().subtract(29, 'days'),
                endDate: moment(),
                minDate: '18/11/2020',
                dateLimit: { days: 365 },
                showDropdowns: true,
                showWeekNumbers: false,
                timePicker: false,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                ranges: {
                    'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Este mes': [moment().startOf('month'), moment().endOf('month')],
                },
                opens: 'right',
                drops: 'down',
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-primary',
                cancelClass: 'btn-default',
                separator: ' a ',
                locale: {
                    applyLabel: 'OK',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'Desde',
                    toLabel: 'Hasta',
                    customRangeLabel: 'Seleccionar',
                    daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sá'],
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    firstDay: 1
                }
            }, function(start, end, label) {
                $('#advance-daterange span').html((start.format('MMMM D, YYYY','es') + ' - ' + end.format('MMMM D, YYYY','es')).toUpperCase());
                $('#fecha_inicial').val(start.format('YYYY-MM-DD'));
                $('#fecha_final').val(end.format('YYYY-MM-DD'));
            });

            //Para los selectores genéricos sin mayor interactividad:
            $(".select2").select2({width: '100%', placeholder: 'Seleccione una opción',  allowClear: true});

            $("#tipo_reporte_id").select2({width: '100%', placeholder: 'Seleccione una opción'});

            $("#tipo_solicitud_id").select2({width: '100%', placeholder: 'Seleccione una opción', allowClear: true});


            // Lista de objetos de la conciliación

            let objetos = JSON.parse($('#lista-objetos').val());
            let listaObjetos = {"results":[]};
            for(const tipo in objetos) {
                let contipo = [];
                for (const idtipo in objetos[tipo]) {
                    contipo.push({
                        "id": objetos[tipo][idtipo].id,
                        "text": objetos[tipo][idtipo].nombre,
                    })
                }

                listaObjetos.results.push({
                    "id": tipo,
                    "text": tipo,
                    "children": contipo
                });
            }

            $("#objeto_id").select2({width: '100%', placeholder: 'Seleccione una opción', allowClear: true, data: listaObjetos.results});

            // Lista de conciliadores para mostrar agrupado por centro en el selector
            let conciliadores = JSON.parse($('#lista-conciliadores').val());
            let listaConciliadores = {"results":[]};
            for(const centro in conciliadores) {
                let concentro = [];
                for (const idcon in conciliadores[centro]) {
                        concentro.push({
                            "id": conciliadores[centro][idcon].id,
                            "text": conciliadores[centro][idcon].nombre,
                        })
                }
                listaConciliadores.results.push({
                    "id": centro,
                    "text": centro,
                    "children": concentro
                });
            }

            $("#conciliadores").select2({width: '100%', placeholder: 'Seleccione una opción', allowClear: true, data: listaConciliadores.results});

            //Cuando cambia el selector de centros hacemos cambios en el selector ceonciliadores a corde a lo seleccionado en centros.
            $("#centro").select2().on('change', function (e) {
                let centros = $(this).val();
                let listaConciliadoresFiltrados = [];
                // Si el selector de centros está vacío entonces ponemos todos los conciliadores en el selector de conciliadores.
                if(centros.length === 0) {
                    listaConciliadoresFiltrados = listaConciliadores.results;
                    $("#conciliadores").select2('destroy').empty().select2({width: '100%', placeholder: 'Seleccione una opción', allowClear: true, data: listaConciliadoresFiltrados});
                }
                //De lo contrario filtramos los conciliadores pertenecientes a los centros seleccionados
                for(const centro in centros){
                    for(const cid in listaConciliadores.results) {
                        if(listaConciliadores.results[cid].id !== centros[centro]) continue;
                        listaConciliadoresFiltrados.push(listaConciliadores.results[cid])
                    }
                }
                $("#conciliadores").select2('destroy').empty().select2({width: '100%', placeholder: 'Seleccione una opción', allowClear: true, data: listaConciliadoresFiltrados});
            });

            //Cuando cambia el tipo de solicitud se debe actualizar el selector de objetos acorde al tipo de objeto seleccionado
            let tiposObjetos = {"1": "Trabajador individual", "2": "Patron Individual"};
            $("#tipo_solicitud_id").select2().on('change', function (e) {
                let tipo_solicitud_id = $(this).val();
                let listaObjetosFiltrados = [];
                // Si el selector de tipo de solicitud está vacío o es nulo
                if(!tipo_solicitud_id) {
                    listaObjetosFiltrados = listaObjetos.results;
                    $("#objeto_id").select2('destroy').empty().select2({width: '100%', placeholder: 'Seleccione una opción', allowClear: true, data: listaObjetosFiltrados});
                }
                //De lo contrario filtramos los objetos pertenecientes al tipo de objeto seleccionado
                for(const oid in listaObjetos.results) {
                    console.log(listaObjetos.results[oid].id + " => "+ tiposObjetos[tipo_solicitud_id]);
                    if(listaObjetos.results[oid].id !== tiposObjetos[tipo_solicitud_id]) continue;
                    listaObjetosFiltrados.push(listaObjetos.results[oid])
                }
                $("#objeto_id").select2('destroy').empty().select2({width: '100%', placeholder: 'Seleccione una opción', allowClear: true, data: listaObjetosFiltrados});
            });

            $("#tipo_reporte").select2().on('change', function (e) {
                console.log('hola:'+$(this).val());
                let tipo_reporte_id = $(this).val();
                if(tipo_reporte_id != 'operativo'){
                    $("#div-reporte-operativo").hide();
                    $("#div-reportes").show();
                    $(".reportes-agregado-desagregado").show();

                }else{
                    $("#div-reporte-operativo").show();
                    $("#div-reportes").hide();
                    $(".reportes-agregado-desagregado").hide();
                }
            });

            $("#btn-reporte-operativo").on('click', function (e) {
                let fecha_inicial = $("#fecha_inicial").val();
                let fecha_final = $("#fecha_final").val();
                let querys = '';
                if($('#querys').length) {
                    querys = '&querys=1';
                }
                window.location.href = '/reportes/reporte-operativo?fecha_inicial='+fecha_inicial+'&fecha_final='+fecha_final+querys;
            });


        });
    </script>
@endpush

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
                <label class="col-lg-4 col-form-label">Centro</label>
                <div class="col-lg-8">
                    {!! Form::select('centro[]', isset($centros) ? $centros : [] ,null , ['id'=>'centro', 'class' => 'form-control select2', 'multiple'=>'multiple']);  !!}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Tipo solicitud</label>
                <div class="col-lg-8">
                    {!! Form::select('tipo_solicitud_id', [1 => 'Trabajador individual',2 => 'Patrón individual'] ,null , ['id'=>'tipo_solicitud_id', 'placeholder' => 'Seleccione una opción', 'class' => 'form-control']);  !!}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Objeto de la solicitud</label>
                <div class="col-lg-8">
                    {!! Form::select('objeto_solicitud_id[]', isset($tipoObjetos) ? $tipoObjetos : [] ,null , ['id'=>'objeto_id', 'class' => 'form-control select2', 'multiple'=>'multiple']);  !!}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Industria</label>
                <div class="col-lg-8">
                    {!! Form::select('giro_id', isset($tipoIndustria) ? $tipoIndustria : [] ,null , ['id'=>'giro_id', 'class' => 'form-control select2', 'multiple'=>'multiple']);  !!}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Género</label>
                <div class="col-lg-8">
                    {!! Form::select('genero_id', [2 => 'Femenino',1 => 'Masculino'] ,null , ['id'=>'genero_id', 'placeholder' => 'Seleccione una opción', 'class' => 'form-control select2']);  !!}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Grupo etario</label>
                <div class="col-lg-8">
                    {!! Form::select('grupo_id[]', isset($grupo_etario) ? $grupo_etario : []   ,null , ['id'=>'grupo_id', 'class' => 'form-control select2', 'multiple'=>'multiple']);  !!}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Tipo Persona</label>
                <div class="col-lg-8">
                    {!! Form::select('tipo_persona_id', [1 => 'Física',2 => 'Moral'] ,null , ['id'=>'tipo_persona_id', 'placeholder' => 'Seleccione una opción', 'class' => 'form-control select2']);  !!}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-4 col-form-label">Tipo Reporte</label>
                <div class="col-lg-8">
                    {!! Form::select('tipo_reporte', ['agregado' => 'Agregado','desagregado' => 'Desagregado'] ,'agregado' , ['id'=>'tipo_reporte', 'class' => 'form-control']);  !!}
                </div>
            </div>

            <input type="hidden" name="fecha_inicial" id="fecha_inicial">
            <input type="hidden" name="fecha_final" id="fecha_final">
            <input type="hidden" name="edad_inicial" id="edad_inicial">
            <input type="hidden" name="edad_final" id="edad_final">

            <div class="panel-footer text-right">
                <a href="{!! route('reportes.forma') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
                <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-file-excel"></i> Generar reporte</button>
            </div>

            {!! Form::close() !!}
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

            // TODO: contextualizar el campo de tipo de objeto según el tipo de solicitud que se seleccione
            $("#tipo_solicitud_id").on('change.select2', function (e) {
                let tipo_solicitud_id = $(this).val();
                //Si el tipo de solicitude es trabajador individual entonces ocultamos las opciones de
                //patrón individual
                if(tipo_solicitud_id == 1) {

                }
            });
        });
    </script>
@endpush

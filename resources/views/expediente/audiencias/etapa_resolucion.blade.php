@extends('layouts.default')

@section('title', 'Calendar')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')
@push('styles')
<style>
    .fc-event{
        height:60px !important;
    }
</style>
@endpush
@section('content')
<!-- begin breadcrumb -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
    <li class="breadcrumb-item"><a href="javascript:;">Audiencias</a></li>
    <li class="breadcrumb-item active">Guia Audiencia</li>
</ol>
<!-- end breadcrumb -->
<!-- begin page-header -->
<h1 class="page-header">Gu&iacute;a Resoluci&oacute;n <small>pasos para cumplir la audiencia</small></h1>
<!-- end page-header -->
<input type="hidden" id="audiencia_id" name="audiencia_id" value="{{$audiencia->id}}" />
<!-- begin timeline -->
<ul class="timeline">
    @foreach($etapa_resolucion as $etapa)
        @if($etapa->id == 1)
        <li style="" id="step{{$etapa->id}}">
        @else
        <li style="display:none;" id="step{{$etapa->id}}">
        @endif
            <!-- begin timeline-time -->
            <div class="timeline-time">
                <span class="date"></span>
            <span class="time">{{$etapa->id}}.  {{$etapa->nombre}}</span>
            </div>
            <!-- end timeline-time -->
            <!-- begin timeline-icon -->
            <div class="timeline-icon">
            <a href="javascript:;" id="icon{{$etapa->id}}">&nbsp;</a>
            </div>
            <!-- end timeline-icon -->
            <!-- begin timeline-body -->
        <div class="timeline-body" style="border: 1px solid black;">
                <div class="timeline-header">
                <span class="username"><a href="javascript:;">{{$etapa->descripcion}}</a> <small></small></span>
                </div>
            <div class="timeline-content" id="contentStep{{$etapa->id}}">
                    <p>
                        @switch($etapa->id)
                            @case(1)
                                <p>Comparecientes</p>
                                <input type="text" id="evidencia{{$etapa->id}}" />
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Continuar </button>
                                @break
                                @case(2)
                                <input type="hidden" id="evidencia{{$etapa->id}}" value="true" />
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Continuar </button>
                            @break
                            @case(3)
                                <input type="text" id="evidencia{{$etapa->id}}" />
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Continuar </button>
                            @break
                            @case(4)
                                <input type="text" id="evidencia{{$etapa->id}}" />
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Continuar </button>
                            @break
                            @case(5)
                                <input type="text" id="evidencia{{$etapa->id}}" />
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Continuar </button>
                            @break
                            @case(6)
                                <input type="text" id="evidencia{{$etapa->id}}" />
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Continuar </button>
                            @break
                            @default
                                
                        @endswitch
                    </p>
                </div>
                <div class="timeline-footer">
                </div>
            </div>
            <!-- end timeline-body -->
        </li>
    @endforeach
</ul>
<!-- end timeline -->
@endsection
@push('scripts')
<script>
    
    function nextStep(pasoActual){
        var siguiente = pasoActual+1;
        $("#icon"+pasoActual).css("background","lightgreen");
        $("#contentStep"+pasoActual).hide();
        $("#step"+siguiente).show();
        guardarEvidenciaEtapa(pasoActual);

    }

    function guardarEvidenciaEtapa(etapa){
        $.ajax({
            url:'/api/etapa_resolucion_audiencia',
            type:"POST",
            dataType:"json",
            async:false,
            data:{
                etapa_resolucion_id:etapa,
                audiencia_id:$("#audiencia_id").val(),
                evidencia: $("#evidencia"+etapa).val()
            },
            success:function(data){
                try{
                    
                }catch(error){
                    console.log(error);
                }
            }
        });
    }

    function getEtapasAudiencia(){
        $.ajax({
            url:'/api/etapa_resolucion_audiencia/audiencia/'+$("#audiencia_id").val(),
            type:"GET",
            dataType:"json",
            async:false,
            data:{
            },
            success:function(data){
                try{
                    setPasosAudiencia(data)
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
    function setPasosAudiencia(etapas){
        $.each(etapas, function (key, value) {
            var pasoActual = value.etapa_resolucion_id;
            var siguiente = pasoActual+1;
            $("#evidencia"+pasoActual).val(value.evidencia)
            $("#icon"+pasoActual).css("background","lightgreen");
            // $("#contentStep"+pasoActual).hide();
            $("#step"+siguiente).show();
        });
    }
    getEtapasAudiencia();

</script>
<script src="/assets/js/demo/timeline.demo.js"></script>
@endpush
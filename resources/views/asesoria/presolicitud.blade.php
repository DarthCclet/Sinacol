@extends('asesoria.default')
@include('includes.component.datatables')
@include('includes.component.pickers')
@section('content')
    <div class="align-middle align-center" style="margin-left: 10%; margin-right:10%; ">
        <div  data-parsley-validate="true"  class="col-md-12 row">
            <div class="col-md-12 mt-4">
                <h3>Presolicitud</h3>
                <hr class="red">
            </div>
            <input type="hidden" id="dato_laboral_id">
            <div class="col-md-12 row">
                <div class="col-md-6">
                    <input class="form-control upper" id="puesto" placeholder="Puesto" type="text" value="">
                    <p class="help-block ">Puesto</p>
                </div>
                <div class="col-md-6" >
                    {!! Form::select('ocupacion_id', isset($ocupaciones) ? $ocupaciones : [] , null, ['id'=>'ocupacion_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                    {!! $errors->first('ocupacion_id', '<span class=text-danger>:message</span>') !!}
                    <p class="help-block ">&iquest;En caso de desempeñar un oficio que cuenta con salario mínimo distinto al general, escoja del catálogo. Si no, deja vacío.</p>
                </div>
                {{-- <div class="col-md-4">
                    <input class="form-control numero" data-parsley-type='integer' id="no_issste" placeholder="No. ISSSTE"  type="text" value="">
                    <p class="help-block">No. ISSSTE</p>
                </div> --}}
            </div>
            <div class="col-md-12 row">
                <div class="col-md-4">
                    <input class="form-control numero " required data-parsley-type='number' id="remuneracion" max="99999999" placeholder="¿Cu&aacute;nto te pagan?" type="text" value="">
                    <p class="help-block needed">&iquest;Cu&aacute;nto te pagan?</p>
                </div>
                <div class="col-md-4">
                    {!! Form::select('periodicidad_id', isset($periodicidades) ? $periodicidades : [] , null, ['id'=>'periodicidad_id','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect']);  !!}
                    {!! $errors->first('periodicidad_id', '<span class=text-danger>:message</span>') !!}
                    <p class="help-block needed">&iquest;Cada cuándo te pagan?</p>
                </div>
                <div class="col-md-4">
                    <input class="form-control numero" required data-parsley-type='integer' id="horas_semanales" placeholder="Horas semanales" type="text" value="">
                    <p class="help-block needed">Horas semanales</p>
                </div>
            </div>
            <div class="col-md-12 row">

                <div class="col-md-2">
                    <span class="text-muted m-l-5 m-r-20" for='switch1'>Labora actualmente</span>
                </div>
                <div class="col-md-2">
                    <input type="hidden" />
                    <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="labora_actualmente" name='labora_actualmente'/>
                </div>
                <div class="col-md-4">
                    <input class="form-control dateBirth" required id="fecha_ingreso" placeholder="Fecha de ingreso" type="text" value="">
                    <p class="help-block needed">Fecha de ingreso</p>
                </div>
                <div class="col-md-4" id="divFechaSalida">
                    <input class="form-control dateBirth" required id="fecha_salida" placeholder="Fecha salida" type="text" value="">
                    <p class="help-block needed">Fecha salida</p>
                </div>
            </div>
            <div class="col-md-4">
                {{-- <select id="jornada_id" required="" class="form-control catSelect" name="jornada_id" data-select2-id="jornada_id" tabindex="-1" aria-hidden="true">
                    <option selected="selected" value="" data-select2-id="19">Seleccione una opción</option>
                    @foreach($jornadas as $jornada)
                        <option value="{{$jornada->id}}" > {{$jornada->nombre}} </option>
                    @endforeach
                </select> --}}
                {!! Form::select('jornada_id', isset($jornadas) ? $jornadas : [] , null, ['id'=>'jornada_id','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect']);  !!}
                {!! $errors->first('jornada_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block needed">Jornada</p>
            </div>
            <div>
                <a style="font-size: medium;" onclick="$('#modal-jornada').modal('show');"><i class="fa fa-question-circle"></i></a>
            </div>
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
<!-- Fin Modal de Domicilio-->

@push('scripts')

<script>
    $(".catSelect").select2({width: '100%'});
    $("#labora_actualmente").change(function(){
        if($("#labora_actualmente").is(":checked")){
            $("#divFechaSalida").hide();
            $("#fecha_salida").removeAttr("required");
        }else{
            $("#fecha_salida").attr("required","");
            $("#divFechaSalida").show();
        }
    });
</script>
@endpush

@endsection
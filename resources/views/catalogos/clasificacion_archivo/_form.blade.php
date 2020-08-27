<div class="row">
    <div class="col-md-offset-3 col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Nombre de la clasificación</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($clasificacion) ? $clasificacion->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre de la clasificación', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará la clasificación</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Tipo de Archivo</label>
            <div class="col-sm-10">
                {!! Form::select('tipo_archivo_id', isset($tiposArchivos) ? $tiposArchivos : [] ,  isset($clasificacion)? $clasificacion->tipo_archivo_id: null , ['id'=>'tipo_archivo_id','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect']);  !!}
                {!! $errors->first('tipo_archivo_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Tipo de archivo al que pertence</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Entidad emisora</label>
            <div class="col-sm-10">
                {!! Form::select('entidad_emisora_id', isset($entidades) ? $entidades : [] ,  isset($clasificacion)? $clasificacion->entidad_emisora_id: null , ['id'=>'entidad_emisora_id','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect']);  !!}
                {!! $errors->first('entidad_emisora_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es la entidad que emite el documento</p>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $(".catSelect").select2();
    });
</script>
@endpush
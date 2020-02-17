<div class="row">
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Nombre del centro</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($centro) ? $centro->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Centro', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('centro.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará el centro</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Duración de la audiencia</label>
            <div class="col-sm-10">
                {!! Form::text('duracionAudiencia', isset($centro) ? $centro->duracionAudiencia : null, ['class'=>'form-control', 'id'=>'duracionAudiencia', 'placeholder'=>'Duración', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('centro.duracionAudiencia', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es la duración promedio de una audiencia</p>
            </div>
        </div>
    </div>
</div>

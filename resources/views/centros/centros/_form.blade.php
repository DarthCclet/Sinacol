<div class="row">
    <div class=" col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Nombre del centro</label>
            {!! Form::text('centro[nombre]', isset($centro) ? $centro->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Centro', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true]) !!}
            {!! $errors->first('centro.nombre', '<span class=text-danger>:message</span>') !!}
            <p class="help-block">Es el nombre con el que se identificará el centro</p>
        </div>
    </div>
    <div class=" col-md-4 ">
        <div class="form-group">
            <label for="abreviatura" class="control-label">Abreviatura</label>
            {!! Form::text('centro[abreviatura]', isset($centro) ? $centro->abreviatura : null, ['class'=>'form-control', 'id'=>'abreviatura', 'placeholder'=>'Abreviatura', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true]) !!}
            {!! $errors->first('centro.abreviatura', '<span class=text-danger>:message</span>') !!}
            <p class="help-block">Son las siglas con las que se identifica el centro</p>
        </div>
    </div>
    <div class=" col-md-4 ">
        <div class="form-group">
            <label for="nombre" class="control-label">Duración de la audiencia</label>
            {!! Form::text('centro[duracionAudiencia]', isset($centro) ? $centro->duracionAudiencia : null, ['class'=>'form-control', 'id'=>'duracionAudiencia', 'placeholder'=>'Duración', 'maxlength'=>'30', 'size'=>'10', 'autofocus'=>true]) !!}
            {!! $errors->first('centro.duracionAudiencia', '<span class=text-danger>:message</span>') !!}
            <p class="help-block">Es la duración promedio de una audiencia</p>
        </div>
    </div>
    @include('includes.component.map',['identificador' => '', 'instancia' => '1','domicilio'=>isset($centro) ? $centro->domicilios[0] : null])
</div>

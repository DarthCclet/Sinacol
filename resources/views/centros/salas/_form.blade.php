<div class="row">
    <h2>Salas</h2>
    <div class="col-md-offset-3 col-md-6 ">



        <div class="form-group">
            <label for="sala" class="col-sm-3 control-label">Nombre sala</label>
            <div class="col-sm-10">
                {!! Form::text('sala', null, ['class'=>'form-control', 'id'=>'sala', 'placeholder'=>'Nombre sala', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('sala', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará la sala</p>
            </div>
        </div>

        <div class="form-group">
            <label for="centro_id" class="col-sm-6 control-label">Centro de conciliación</label>
            <div class="col-sm-10">
                  {!! Form::text('centro_id', null, ['class'=>'form-control', 'id'=>'centro_id', 'placeholder'=>'Centro de conciliación', 'maxlength'=>'10','size'=>'10']) !!}
                {!! $errors->first('centro_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Centro de conciliación al que pertenece la sala.</p>
            </div>
        </div>

    </div>
</div>

<hr/>


<div class="form-group">
    <label for="apepat" class="col-sm-2 control-label">Estatus</label>
    <div class="col-sm-10">
        <div class="radio">
            <label>
                {!! Form::radio('activo', 1, isset($sala) ? $sala->activo : null, ['class'=>'px']) !!}
                <span class="lbl">Activo</span>
            </label>
        </div> <!-- / .radio -->
        <div class="radio">
            <label>
                {!! Form::radio('activo', 0, isset($sala) ? $sala->activo : 1, ['class'=>'px']) !!}
                <span class="lbl">Inactivo</span>
            </label>
        </div> <!-- / .radio -->

        {!! $errors->first('salas.activo', '<span class=text-danger>:message</span>') !!}
        <p class="help-block">Indica si la sala está activa o inactiva</p>
    </div>
</div>

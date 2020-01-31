<div class="row">
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="sala" class="col-sm-3 control-label">Nombre sala</label>
            <div class="col-sm-10">
                {!! Form::text('sala', null, ['class'=>'form-control', 'id'=>'sala', 'placeholder'=>'Nombre sala', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                <p class="help-block">Es el nombre con el que se identificará la sala</p>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-6 ">
        <div class="form-group">
            <label for="centro_id" class="col-sm-6 control-label">Centro de conciliación</label>
            <div class="col-sm-10">
                <select id="centro_id" class="form-control">
                    <option value="">-- Selecciona un centro</option>
                </select>
            </div>
        </div>
    </div>
</div>
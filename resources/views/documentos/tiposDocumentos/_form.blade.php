<div class="row">
    <h2>Tipo Documento</h2>
    <div class="col-md-offset-3 col-md-6 ">



        <div class="form-group">
            <label for="nombre" class="col-sm-6 control-label">Nombre tipo documento</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($tipoDocumento) ? $tipoDocumento->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre del tipo documento', 'maxlength'=>'60', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('tipo_documentos.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará el tipo de documento</p>
            </div>

            <label for="nombre" class="col-sm-6 control-label">Datos requeridos:</label>
            <div class="col-sm-10">

              @foreach($objetoDocumento as $objeto)

                <input type="checkbox" name="objetoD[]" value="{{ $objeto['objeto'] }}" {{ $objeto['checked']}}> {{ $objeto["nombre"] }}<br>
              @endforeach
            </div>
        </div>
    </div>
</div>

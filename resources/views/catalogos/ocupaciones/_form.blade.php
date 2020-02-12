
@include('includes.component.pickers')

<div class="row">
    <h2>Ocupaciones Laborales</h2>
    <div class="col-md-offset-3 col-md-6 ">



        <div class="form-group">
            <label for="nombre" class="col-sm-6 control-label">Nombre ocupación</label>
            <div class="col-sm-10">
                {!! Form::text('nombre', isset($ocupacion) ? $ocupacion->nombre : null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre de la ocupacion', 'maxlength'=>'60', 'size'=>'10', 'autofocus'=>true, 'data-parsley-required'=>true ]) !!}
                {!! $errors->first('ocupacion.nombre', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Es el nombre con el que se identificará la ocupacion</p>
            </div>
            <div class="col-md-12 row">
              <div class="col-md-6">
                <label for="salario_zona_libre" class="control-label">Salario Zona Libre </label>
                  {!! Form::text('salario_zona_libre', isset($ocupacion) ? $ocupacion->salario_zona_libre : null, ['class'=>'form-control', 'id'=>'salario_libre', 'placeholder'=>'Salario minimo en zona libre', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                  {!! $errors->first('ocupacion.salario_zona_libre', '<span class=text-danger>:message</span>') !!}
                  <!-- <p class="help-block">Vigencia desde</p> -->
              </div>
              <div class="col-md-6">
                <label for="salario_resto_del_pais" class="control-label">Salario Resto del País </label>
                  {!! Form::text('salario_resto_del_pais', isset($ocupacion) ? $ocupacion->salario_resto_del_pais : null, ['class'=>'form-control ', 'id'=>'salario_resto', 'placeholder'=>'Salario minimo en resto del pais', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true, 'data-parsley-required'=>'true' ]) !!}
                  {!! $errors->first('ocupacion.salario_resto_del_pais', '<span class=text-danger>:message</span>') !!}
                  <!-- <p class="help-block">Vigencia hasta</p> -->
              </div>
            </div><br>

            <div class="col-md-12 row">
              <div class="col-md-6">
                <label for="vigencia_de" class="control-label">Fecha inicio de vigencia </label>
                  {!! Form::text('vigencia_de', isset($ocupacion) ? $ocupacion->vigencia_de : null, ['class'=>'form-control date', 'id'=>'vigencia_de', 'placeholder'=>'Fecha de inicio de vigencia', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true, 'readonly' ]) !!}
                  {!! $errors->first('ocupacion.vigencia_de', '<span class=text-danger>:message</span>') !!}
                  <!-- <p class="help-block">Vigencia desde</p> -->
              </div>
              <div class="col-md-6">
                <label for="vigencia_a" class="control-label">Fecha fin de vigencia </label>
                  {!! Form::text('vigencia_a', isset($ocupacion) ? $ocupacion->vigencia_a : null, ['class'=>'form-control date', 'id'=>'vigencia_a', 'placeholder'=>'Fecha de fin de vigencia', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true, 'readonly' ]) !!}
                  {!! $errors->first('ocupacion.vigencia_a', '<span class=text-danger>:message</span>') !!}
                  <!-- <p class="help-block">Vigencia hasta</p> -->
              </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.date').datetimepicker({
              useCurrent: false,
              format:'DD/MM/YYYY',
              ignoreReadonly: true,
              locale:'es'
            });
            $("#vigencia_de").on("dp.change", function (e) {
                $('#vigencia_a').data("DateTimePicker").minDate(e.date);
            });
            $("#vigencia_a").on("dp.change", function (e) {
                $('#vigencia_de').data("DateTimePicker").maxDate(e.date);
            });

        });
    </script>
@endpush

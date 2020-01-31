<div class="row">
    <div class="col-md-offset-3 col-md-12">
        <div class="form-group">
                <center><h1>Solicitud de conciliacion</h1></center>
            <div class="col-md-12">
                <div>
                    <div>
                        <h2>Solicitante</h2>
                    </div>
                    <div style="margin-left:5%; margin-bottom:3%; ">
                        <label >Tipo Persona</label>
                        <div class="row">
                            <div class="col-md-offset-6">
                                {!! Form::radio('solicitante[tipo_persona_id]', 1 , true); !!}
                                <label for="radioFisicaSolicitante">Fisica</label>
                            </div>
                            <div class="col-md-offset-6">
                                {!! Form::radio('solicitante[tipo_persona_id]', 2 , false); !!}
                                <label for="radioMoralSolicitante   ">Moral</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-4" style="display:none;">
                            {!! Form::text('solicitante[id]', isset($solicitud->solicitante) ? $solicitud->solicitante->id : null, ['class'=>'form-control', 'id'=>'idsolicitante', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('solicitante[nombre]', isset($solicitud->solicitante) ? $solicitud->solicitante->nombre : null, ['class'=>'form-control', 'id'=>'idSolicitanteNombre','placeholder'=>'Nombre del solicitante', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            
                            {!! $errors->first('solicitante.nombre', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Nombre del solicitante</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('solicitante[primer_apellido]', isset($solicitud->solicitante) ? $solicitud->solicitante->primer_apellido : null, ['class'=>'form-control', 'id'=>'solicitantePrimerApellido', 'placeholder'=>'Primer apellido del solicitante', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitante.primer_apellido', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Primer apellido</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('solicitante[segundo_apellido]', isset($solicitud->solicitante) ? $solicitud->solicitante->segundo_apellido : null, ['class'=>'form-control', 'id'=>'solicitanteSegundoApellido', 'placeholder'=>'Segundo apellido del solicitante', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitante.segundo_apellido', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Segundo apellido</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-8">
                            {!! Form::text('solicitante[nombre_comercial]', isset($solicitud->solicitante) ? $solicitud->solicitante->nombre_comercial : null, ['class'=>'form-control', 'id'=>'solicitanteNombreComercial', 'placeholder'=>'Nombre comercial del solicitante', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitante.nombre_comercial', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Nombre comercia</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            {!! Form::text('solicitante[fecha_nacimiento]', isset($solicitud->solicitante) ? $solicitud->solicitante->fecha_nacimiento : null, ['class'=>'form-control date', 'id'=>'solicitantefechaNacimiento', 'placeholder'=>'Fecha de nacimeinto del solicitante', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitante.fecha_nacimiento', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Fecha de nacimiento</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('solicitante[edad]', isset($solicitud->solicitante) ? $solicitud->solicitante->edad : null, ['class'=>'form-control', 'id'=>'solicitanteEdad', 'placeholder'=>'Edad del solicitante', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitante.edad', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Edad del solicitante</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('solicitante[rfc]', isset($solicitud->solicitante) ? $solicitud->solicitante->rfc : null, ['class'=>'form-control', 'id'=>'solicitanteRfc', 'placeholder'=>'Rfc del solicitante', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitante.rfc', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Rfc del solicitante</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-8">
                            {!! Form::text('solicitante[curp]', isset($solicitud->solicitante) ? $solicitud->solicitante->curp : null, ['class'=>'form-control', 'id'=>'solicitanteCURP', 'placeholder'=>'CURP del solicitante', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitante.curp', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">CURP del solicitante</p>
                        </div>
                    </div>
                    <div style="margin-left:5%; margin-bottom:3%; ">
                        <label >Presenta Abogado</label>
                        <div class="row">
                            <div class="col-md-offset-6">
                                {{-- <input type="radio" name="age" value="1" checked=""> --}}
                                {!! Form::radio('solicitud[presenta_abogado]', 0 , true); !!}
                                <label for="solicitud[presenta_abogado]">Si</label>
                            </div>
                            <div class="col-md-offset-6">
                                {!! Form::radio('solicitud[presenta_abogado]', 1 , false); !!}
                                <label for="solicitud[presenta_abogado]   ">No</label>
                            </div>
                        </div>
                    </div>
                    <div >
                        <h2>Abogado</h2>
                        <div class="col-md-12 row">
                            <div class="col-md-4" style="display:none;">
                                {!! Form::text('abogado[id]', isset($solicitud->abogado) ? $solicitud->abogado->id : null, ['class'=>'form-control', 'id'=>'idAbogado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            </div>
                            <div class="col-md-4">
                                {!! Form::text('abogado[nombre]', isset($solicitud->abogado) ? $solicitud->abogado->nombre : null, ['class'=>'form-control', 'id'=>'solicitanteNombre', 'placeholder'=>'Nombre del abogado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                                {!! $errors->first('abogado.nombre', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Nombre del abogado</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::text('abogado[primer_apellido]', isset($solicitud->abogado) ? $solicitud->abogado->primer_apellido : null, ['class'=>'form-control', 'id'=>'solicitantePrimerApellido', 'placeholder'=>'Primer apellido del abogado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                                {!! $errors->first('abogado.primer_apellido', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Primer apellido</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::text('abogado[segundo_apellido]', isset($solicitud->abogado) ? $solicitud->abogado->segundo_apellido : null, ['class'=>'form-control', 'id'=>'solicitanteSegundoApellido', 'placeholder'=>'Segundo apellido del abogado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                                {!! $errors->first('abogado.segundo_apellido', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Segundo apellido</p>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4">
                                {!! Form::text('abogado[cedula_profesional]', isset($solicitud->abogado) ? $solicitud->abogado->cedula_profesional : null, ['class'=>'form-control', 'id'=>'solicitanteNombre', 'placeholder'=>'Cedula profesional abogado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                                {!! $errors->first('abogado.cedula_profesional', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Cedula profesional</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::text('abogado[numero_empleado]', isset($solicitud->abogado) ? $solicitud->abogado->numero_empleado : null, ['class'=>'form-control', 'id'=>'solicitantePrimerApellido', 'placeholder'=>'Numero de empleado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                                {!! $errors->first('abogado.numero_empleado', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Numero empleado</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::text('abogado[email]', isset($solicitud->abogado) ? $solicitud->abogado->email : null, ['class'=>'form-control', 'id'=>'solicitanteSegundoApellido', 'placeholder'=>'Correo', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                                {!! $errors->first('abogado.email', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Correo</p>
                            </div>
                            <div class="">
                                {!! Form::checkbox('abogado[profedet]', '1', isset($solicitud->abogado->profedet) ? $solicitud->abogado->profedet : null)  !!}
                                <label for="solicitud.abogado.profedet">Profedet</label>
                            </div>
                            
                        </div>
                    </div>
                    {{-- <div>
                        <h2>Domicilio</h2>
                    </div> --}}
                </div>
                <div>
                    <h2>Solicitado</h2>
                    <div style="margin-left:5%; margin-bottom:3%; ">
                        <label >Tipo Persona</label>
                        <div class="row">
                            <div class="col-md-offset-6">
                                {!! Form::radio('solicitado[tipo_persona_id]', 1 , true); !!}
                                <label for="radioFisicaSolicitado">Fisica</label>
                            </div>
                            <div class="col-md-offset-6">
                                {!! Form::radio('solicitado[tipo_persona_id]', 2 , false); !!}
                                <label for="radioMoralSolicitado">Moral</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-4" style="display:none;">
                            {!! Form::text('solicitado[id]', isset($solicitud->solicitado) ? $solicitud->solicitado->id : null, ['class'=>'form-control', 'id'=>'idAbogado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('solicitado[nombre]', isset($solicitud->solicitado) ? $solicitud->solicitado->nombre : null, ['class'=>'form-control', 'id'=>'solicitadoNombre', 'placeholder'=>'Nombre del solicitado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitado.nombre', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Nombre del solicitado</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('solicitado[primer_apellido]', isset($solicitud->solicitado) ? $solicitud->solicitado->primer_apellido : null, ['class'=>'form-control', 'id'=>'solicitadoPrimerApellido', 'placeholder'=>'Primer apellido del solicitado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitado.primer_apellido', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Primer apellido</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('solicitado[segundo_apellido]', isset($solicitud->solicitado) ? $solicitud->solicitado->segundo_apellido : null, ['class'=>'form-control', 'id'=>'solicitadoSegundoApellido', 'placeholder'=>'Segundo apellido del solicitado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitado.segundo_apellido', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Segundo apellido</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-8">
                            {!! Form::text('solicitado[nombre_comercial]', isset($solicitud->solicitado) ? $solicitud->solicitado->nombre_comercial : null, ['class'=>'form-control', 'id'=>'solicitadoNombreComercial', 'placeholder'=>'Nombre comercial del solicitado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitado.nombre_comercial', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Nombre comercial</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            {!! Form::text('solicitado[fecha_nacimiento]', isset($solicitud->solicitado) ? $solicitud->solicitado->fecha_nacimiento : null, ['class'=>'form-control', 'id'=>'solicitadofechaNacimiento', 'placeholder'=>'Fecha de nacimeinto del solicitado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitado.fecha_nacimiento', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Segundo apellido</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('solicitado[edad]', isset($solicitud->solicitado) ? $solicitud->solicitado->edad : null, ['class'=>'form-control', 'id'=>'solicitadoEdad', 'placeholder'=>'Edad del solicitado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitado.edad', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Edad del solicitado</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('solicitado[rfc]', isset($solicitud->solicitado) ? $solicitud->solicitado->rfc : null, ['class'=>'form-control', 'id'=>'solicitadoRfc', 'placeholder'=>'Rfc del solicitado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitado.rfc', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Rfc del solicitado</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-8">
                            {!! Form::text('solicitado[curp]', isset($solicitud->solicitado) ? $solicitud->solicitado->curp : null, ['class'=>'form-control', 'id'=>'solicitadoCURP', 'placeholder'=>'CURP del solicitado', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                            {!! $errors->first('solicitado.curp', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">CURP del solicitado</p>
                        </div>
                    </div>
                    {{-- <div>
                        <h2>Domicilio</h2>
                    </div> --}}
                </div>
                {!! Form::text('solicitud[observaciones]', isset($solicitud->observaciones)?$solicitud->observaciones:null, ['class'=>'form-control', 'id'=>'observaciones', 'placeholder'=>'Observaciones de la solicitud', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('solicitud.observaciones', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Observaciones de la solicitud</p>
                
                {!! Form::select('solicitud[estatus_solicitud_id]', isset($estatus_solicitudes) ? $estatus_solicitudes : [] , isset($solicitud->estatus_solicitud_id)?$solicitud->estatus_solicitud_id:null, ['placeholder' => 'Seleccione una opcion', 'class' => 'form-control col-md-4']);  !!}
                {!! $errors->first('solicitud.estatus_solicitud_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Estatus de la solicitud</p>

                {!! Form::select('solicitud[motivo_solicitud_id]', isset($motivo_solicitudes) ? $motivo_solicitudes : [] , isset($solicitud->motivo_solicitud_id)?$solicitud->motivo_solicitud_id:null, ['placeholder' => 'Seleccione una opcion', 'class' => 'form-control col-md-4']);  !!}
                {!! $errors->first('solicitud.motivo_solicitud_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Motivo de la solicitud</p>

                {!! Form::select('solicitud[centro_id]', isset($centros) ? $centros : [] , isset($solicitud->centro_id)?$solicitud->centro_id:null, ['placeholder' => 'Seleccione una opcion', 'class' => 'form-control col-md-4']);  !!}
                {!! $errors->first('solicitud.centro_di', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Motivo de la solicitud</p>

                <div class="">
                    {!! Form::checkbox('solicitud[ratificada]', '1', isset($solicitud->ratificada) ? $solicitud->ratificada : null)  !!}
                    <label for="solicitud.ratificada">Ratificada</label>
                </div>
                {!! Form::text('solicitud[fecha_ratificacion]', isset($solicitud->fecha_ratificacion)?$solicitud->fecha_ratificacion:null, ['class'=>'form-control date', 'id'=>'fechaRatificacion', 'placeholder'=>'Fecha de ratificacion', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('solicitud.fecha_ratificacion', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Fecha de Ratificaci&oacute;n</p>
                {!! Form::text('solicitud[fecha_recepcion]', isset($solicitud->fecha_recepcion)?$solicitud->fecha_recepcion:null, ['class'=>'form-control date', 'id'=>'fechaRecepcion', 'placeholder'=>'Fecha de Recepcion', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('solicitud.fecha_recepcion', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Fecha de Recepci&oacute;n</p>
                {!! Form::text('solicitud[fecha_conflicto]', isset($solicitud->fecha_conflicto)?$solicitud->fecha_conflicto:null, ['class'=>'form-control date', 'id'=>'fechaConflicto', 'placeholder'=>'Fecha de Conflicto', 'maxlength'=>'50', 'size'=>'10', 'autofocus'=>true]) !!}
                {!! $errors->first('solicitud.fecha_conflicto', '<span class=text-danger>:message</span>') !!}
                <p class="help-block">Fecha de Conflicto</p>
                
                
            </div>
        </div>
    </div>
</div>
<hr/>

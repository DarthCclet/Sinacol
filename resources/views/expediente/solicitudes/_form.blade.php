
<style>
    .inputError {
        border: 1px red solid;
    }
</style>

<div>

</div>
<div id="wizard" >
    <!-- begin wizard-step -->
    <ul>
        <li>
            <a href="#step-1">
                <span class="number">1</span> 
                <span class="info">
                    Solicitante
                    <small>Información del solicitante</small>
                </span>
            </a>
        </li>
        <li>
            <a href="#step-2">
                <span class="number">2</span> 
                <span class="info">
                    Solicitado
                    <small>Información del solicitado</small>
                </span>
            </a>
        </li>
        <li>
            <a href="#step-3">
                <span class="number">3</span>
                <span class="info">
                    Solicitud
                    <small>Información general de la solicitud</small>
                </span>
            </a>
        </li>
        <!-- El paso 4 solo se muestra cuando se selecciona excepcion de conciliacion -->
        <li class="step-4">
            <a href="#step-4">
                <span class="number">4</span>
                <span class="info">
                    Excepci&oacute;n
                    <small>Excepcion de conciliacion</small>
                </span>
            </a>
        </li>
    </ul>
    <!-- end wizard-step -->
    <!-- begin wizard-content -->
    <div>
        <!-- begin step-1 -->
        <div id="step-1" data-parsley-validate="true">
            <!-- begin fieldset -->
            <fieldset>
                <!-- begin row -->
                <div class="row" id="form" >
                    <div class="col-xl-10 offset-xl-1">
                        <div>
                            <center>  <h1>Solicitante</h1></center>
                        </div>
                        <div style="margin-left:5%; margin-bottom:3%; ">
                            <label>Tipo Persona</label>
                            <div class="row">
                                <div class="col-md-offset-6">
                                    <input checked="checked" id="tipo_persona_fisica_solicitante" name="tipo_persona_solicitante" type="radio" value="1">
                                    <label for="tipo_persona_fisica_solicitante">Fisica</label>
                                </div>
                                <div class="col-md-offset-6">
                                    <input name="tipo_persona_solicitante" id="tipo_persona_moral_solicitante" type="radio" value="2">
                                    <label for="tipo_persona_moral_solicitante">Moral</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4" style="display:none;">
                                <input class="form-control" id="idsolicitante" maxlength="50" autofocus="" name="solicitante[id]" type="text" value="253">
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control" id="idNombreSolicitante" required placeholder="Nombre del solicitante" maxlength="50"   autofocus="" name="solicitante[nombre]" type="text" value="">
                                <p class="help-block">Nombre del solicitante</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control" id="idPrimerASolicitante" required placeholder="Primer apellido del solicitante" maxlength="50"   autofocus="" name="solicitante[primer_apellido]" type="text" value="">
                                
                                <p class="help-block">Primer apellido</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control" id="idSegundoASolicitante" placeholder="Segundo apellido del solicitante" maxlength="50"   autofocus="" name="solicitante[segundo_apellido]" type="text" value="">
                                
                                <p class="help-block">Segundo apellido</p>
                            </div>
                        </div>
                        <div class="col-md-12 row personaMoralSolicitante">
                            <div class="col-md-8">
                                <input class="form-control" id="idNombreCSolicitante" placeholder="Nombre comercial del solicitante" maxlength="50"   autofocus="" name="solicitante[nombre_comercial]" type="text" value="">
                                <p class="help-block">Nombre comercia</p>
                            </div>
                        </div>
                        <div class="col-md-12 row ">
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control date" id="idFechaNacimientoSolicitante" placeholder="Fecha de nacimeinto del solicitante" maxlength="50"   autofocus="" name="solicitante[fecha_nacimiento]" type="text" value="">
                                <p class="help-block">Fecha de nacimiento</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control numero" required data-parsley-type='integer' id="idEdadSolicitante" placeholder="Edad del solicitante" maxlength="50"   autofocus="" name="solicitante[edad]" type="text" value="">
                                <p class="help-block">Edad del solicitante</p>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" required id="idSolicitanteRfc" placeholder="Rfc del solicitante" maxlength="50"   autofocus="" name="solicitante[rfc]" type="text" value="">
                                <p class="help-block">Rfc del solicitante</p>
                            </div>
                        </div>
                        <div class="col-md-12 row personaFisicaSolicitante">
                            <div class="col-md-4">
                                {!! Form::select('genero_id_solicitante', isset($generos) ? $generos : [] , null, ['id'=>'genero_id_solicitante','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('genero_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Genero</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::select('giro_comercial_solicitante', isset($giros_comerciales) ? $giros_comerciales : [] , null, ['id'=>'giro_comercial_solicitante','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('giro_comercial_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Giro Comercial</p>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control" id="idSolicitanteCURP" placeholder="CURP del solicitante" maxlength="50"   autofocus="" name="solicitante[curp]" type="text" value="">
                                <p class="help-block">CURP del solicitante</p>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4">
                                {!! Form::select('nacionalidad_id_solicitante', isset($nacionalidades) ? $nacionalidades : [] , null, ['id'=>'nacionalidad_id_solicitante','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('nacionalidad_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Nacionalidad</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::select('entidad_nacimiento_id_solicitante', isset($estados) ? $estados : [] , null, ['id'=>'entidad_nacimiento_id_solicitante','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('entidad_nacimiento_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Estados</p>
                            </div>
                        </div>
                        <!-- seccion de domicilios solicitante -->
                        <div style="margin-top: 1%;" >
                            <div class="row">
                                <h4>Domicilio(s)</h4>
                                <a style="font-size:large; margin-left:1%; color:#49b6d6;" onclick="$('#modal-domicilio').modal('show'); $('#tipoParteDomicilio').val(0);"  > <i class="fa fa-plus-circle"></i></a>
                            </div>
                            <div class="col-md-10 offset-md-1" >
                                <table class="table table-bordered" >
                                    <thead>
                                        <tr>
                                            <th>Domicilio</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDomicilioSolicitante">
                                    </tbody>
                                </table>  
                            </div>
                        </div>
                        <!-- end seccion de domicilios solicitante -->
                        <!-- Seccion de Datos laborales -->
                        <div>
                            <h4>Datos Laborales</h4>
                            <div class="col-md-12">
                                <input class="form-control" required id="nombre_jefe_directo" placeholder="Nombre del jefe directo" maxlength="50"   autofocus="" type="text" value="">
                                <p class="help-block">Nombre del Jefe directo</p>
                            </div>
                            <div class="col-md-12 row">
                                <div class="col-md-4">
                                    <input class="form-control" required id="puesto" placeholder="Puesto" maxlength="50"   autofocus="" type="text" value="">
                                    <p class="help-block">Puesto</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero" data-parsley-type='integer' required id="nss" placeholder="No. servicio social" maxlength="50"   autofocus="" type="text" value="">
                                    <p class="help-block">Numero de seguro social</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero" data-parsley-type='integer'  required id="no_issste" placeholder="No del ISSSTE" maxlength="50"   autofocus="" type="text" value="">
                                    <p class="help-block">No. ISSSTE</p>
                                </div>
                            </div>
                            <div class="col-md-12 row">
                                <div class="col-md-4">
                                    <input class="form-control numero" required data-parsley-type='integer' id="no_afore" placeholder="No afore" maxlength="50"   autofocus="" type="text" value="">
                                    <p class="help-block">No. afore</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" required data-parsley-type='digits' id="percepcion_mensual_neta" placeholder="Percepcion neta mensual" maxlength="50"   autofocus="" type="text" value="">
                                    <p class="help-block">Percepcion neta mensual</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" required data-parsley-type='digits' id="percepcion_mensual_bruta" placeholder="Percepci&oacute;n mensual bruta" maxlength="50"   autofocus="" type="text" value="">
                                    <p class="help-block">Percepci&oacute;n mensual bruta</p>
                                </div>
                            </div>
                            <div class="col-md-12 row">
                                
                                <div class="col-md-4">
                                    <input class="form-control date" required id="fecha_ingreso" placeholder="Fecha de ingreso" maxlength="50"   autofocus="" type="text" value="">
                                    <p class="help-block">Fecha de ingreso</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control date" required id="fecha_salida" placeholder="Fecha salida" maxlength="50"   autofocus="" type="text" value="">
                                    <p class="help-block">Fecha salida</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero" required data-parsley-type='integer' id="horas_semanales" placeholder="Horas semanales" maxlength="50"   autofocus="" type="text" value="">
                                    <p class="help-block">Horas semanales</p>
                                </div>
                            </div>
                            {!! Form::select('jornada_id', isset($jornadas) ? $jornadas : [] , null, ['id'=>'jornada_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control col-md-4 catSelect']);  !!}
                            {!! $errors->first('jornada_id', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block">Jornada</p>
                            <div class="col-md-4">
                                <input id="labora_actualmente" type="checkbox" value="1">
                                <label for="labora_actualmente">Labora actualmente</label>
                            </div>
                        </div>
                        <!-- end Seccion de Datos laborales -->

                        <hr style="margin-top:5%;">
                        <div>
                            <button class="btn btn-info" type="button" id="agregarSolicitante" > <i class="fa fa-plus-circle"></i> Agregar solicitante</button>
                        </div>
                        <div class="col-md-10 offset-md-1" style="margin-top: 3%;" >
                            <table class="table table-bordered" >
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Curp</th>
                                        <th>RFC</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodySolicitante">
                                </tbody>
                            </table>  
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </fieldset>
            <!-- end fieldset -->
        </div>
        <!-- end step-1 -->
        <!-- begin step-2 -->
        <div id="step-2" data-parsley-validate="true">
            <!-- begin fieldset -->
            <fieldset>
                <!-- begin row -->
                <div class="row">
                    <div class="col-xl-10 offset-xl-1">
                        <div>
                            <center>  <h1>Solicitado</h1></center>
                        </div>
                        <div style="margin-left:5%; margin-bottom:3%; ">
                            <label>Tipo Persona</label>
                            <div class="row">
                                <div class="col-md-offset-6">
                                    <input checked="checked" name="tipo_persona_solicitado" id="tipo_persona_fisica_solicitado" type="radio" value="1">
                                    <label for="tipo_persona_fisica_solicitado">Fisica</label>
                                </div>
                                <div class="col-md-offset-6">
                                    <input name="tipo_persona_solicitado" id="tipo_persona_moral_solicitado" type="radio" value="2">
                                    <label for="tipo_persona_moral_solicitado">Moral</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4" style="display:none;">
                                <input class="form-control" id="idsolicitado" maxlength="50"   autofocus="" name="solicitado[id]" type="text" value="253">
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control" id="idNombreSolicitado" placeholder="Nombre del solicitado" maxlength="50"   autofocus="" name="solicitado[nombre]" type="text" value="">
                                <p class="help-block">Nombre del solicitado</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control" id="idPrimerASolicitado" placeholder="Primer apellido del solicitado" maxlength="50"   autofocus="" name="solicitado[primer_apellido]" type="text" value="">
                                
                                <p class="help-block">Primer apellido</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control" id="idSegundoASolicitado" placeholder="Segundo apellido del solicitado" maxlength="50"   autofocus="" name="solicitado[segundo_apellido]" type="text" value="">
                                
                                <p class="help-block">Segundo apellido</p>
                            </div>
                        </div>
                        <div class="col-md-12 row personaMoralSolicitado">
                            <div class="col-md-8">
                                <input class="form-control" id="idNombreCSolicitado" placeholder="Nombre comercial del solicitado" maxlength="50"   autofocus="" name="solicitado[nombre_comercial]" type="text" value="">
                                <p class="help-block">Nombre comercia</p>
                            </div>
                        </div>
                        <div class="col-md-12 row ">
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control date" id="idFechaNacimientoSolicitado" placeholder="Fecha de nacimeinto del solicitado" maxlength="50"   autofocus="" name="solicitado[fecha_nacimiento]" type="text" value="">
                                <p class="help-block">Fecha de nacimiento</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control" id="idEdadSolicitado" placeholder="Edad del solicitado" maxlength="50"   autofocus="" name="solicitado[edad]" type="text" value="">
                                <p class="help-block">Edad del solicitado</p>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" id="idSolicitadoRfc" placeholder="Rfc del solicitado" maxlength="50"   autofocus="" name="solicitado[rfc]" type="text" value="">
                                <p class="help-block">Rfc del solicitado</p>
                            </div>
                        </div>
                        <div class="col-md-12 row personaFisicaSolicitado">
                            <div class="col-md-4">
                                {!! Form::select('genero_id_solicitado', isset($generos) ? $generos : [] , null, ['id'=>'genero_id_solicitado','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('genero_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Genero</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::select('giro_comercial_solicitado', isset($giros_comerciales) ? $giros_comerciales : [] , null, ['id'=>'giro_comercial_solicitado','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('giro_comercial_solicitando', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Giro Comercial</p>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control" id="idSolicitadoCURP" placeholder="CURP del solicitado" maxlength="50"   autofocus="" name="solicitado[curp]" type="text" value="">
                                <p class="help-block">CURP del solicitado</p>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4">
                                {!! Form::select('nacionalidad_id_solicitado', isset($nacionalidades) ? $nacionalidades : [] , null, ['id'=>'nacionalidad_id_solicitado','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('nacionalidad_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Nacionalidad</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::select('entidad_nacimiento_id_solicitado', isset($estados) ? $estados : [] , null, ['id'=>'entidad_nacimiento_id_solicitado','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('entidad_nacimiento_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Estado de nacimiento</p>
                            </div>
                        </div>
                        <!-- seccion de domicilios solicitado -->
                        <div >
                            <div class="row">
                                <h4>Domicilio(s)</h4>
                                <a style="font-size:large; margin-left:1%; color:#49b6d6;" onclick="$('#modal-domicilio').modal('show'); $('#tipoParteDomicilio').val(1);"  > <i class="fa fa-plus-circle"></i></a>
                            </div>
                            <div class="col-md-10 offset-md-1" style="margin-top: 3%;" >
                                <table class="table table-bordered" >
                                    <thead>
                                        <tr>
                                            <th>Domicilio</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDomicilioSolicitado">
                                    </tbody>
                                </table>  
                            </div>
                        </div>
                        <!-- end seccion de domicilios solicitado -->
                        <hr style="margin-top:5%;">
                        <div>
                            <button class="btn btn-info" type="button" id="agregarSolicitado" > <i class="fa fa-plus-circle"></i> Agregar solicitado</button>
                        </div>

                        <div class="col-md-10 offset-md-1" style="margin-top: 3%;" >
                            <table class="table table-bordered" >
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Curp</th>
                                        <th>RFC</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodySolicitado">
                                </tbody>
                            </table>  
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </fieldset>
            <!-- end fieldset -->
        </div>
        <!-- begin step-3 -->
        <div id="step-3" data-parsley-validate="true">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                    <center>  <h1>Solicitado</h1></center>
                <div class="col-md-12 row">

                    <div class="col-md-4">
                        {!! Form::select('estatus_solicitud_id', isset($estatus_solicitudes) ? $estatus_solicitudes : [] , null, ['id'=>'estatus_solicitud_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('estatus_solicitud_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Estatus de la solicitud</p>
                    </div>
                        
                    <div class="col-md-4">
                        {!! Form::select('objeto_solicitud_id', isset($objeto_solicitudes) ? $objeto_solicitudes : [] , null, ['id'=>'objeto_solicitud_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('objeto_solicitud_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Objeto de la solicitud</p>
                    </div>
                        
                    <div class="col-md-4">
                        {!! Form::select('centro_id', isset($centros) ? $centros : [] , null, ['id'=>'centro_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('centro_di', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Centro de la solicitud</p>
                    </div>
                    
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-4">
                        <input class="form-control date" id="fechaRatificacion" placeholder="Fecha de ratificacion" maxlength="50"   autofocus="" name="solicitud[fecha_ratificacion]" type="text" value="">
                        <p class="help-block">Fecha de Ratificación</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control date" id="fechaRecepcion" placeholder="Fecha de Recepcion" maxlength="50"   autofocus="" name="solicitud[fecha_recepcion]" type="text" value="">                
                        <p class="help-block">Fecha de Recepción</p>    
                    </div>
                    <div class="col-md-4">
                        <input class="form-control date" id="fechaConflicto" placeholder="Fecha de Conflicto" maxlength="50"   autofocus="" name="solicitud[fecha_conflicto]" type="text" value="">
                        <p class="help-block">Fecha de Conflicto</p>
                    </div>
                </div>
                <div>
                    <div class="">
                        <input checked="checked" id="ratificada" type="checkbox" value="1">
                        <label for="ratificada">Ratificada</label>
                    </div>
                    <div class="">
                        <input checked="checked" id="solicita_excepcion" type="checkbox" value="1">
                        <label for="solicita_excepcion">Solicita excepcion</label>
                    </div>
                </div>

                <textarea rows="4" class="form-control" id="observaciones" data-parsley-maxlength='250'></textarea>
                <p class="help-block">Observaciones de la solicitud</p>
                
            </div>
        </div>
        </div>
        <!-- end step-3 -->
        <!-- begin step-4 -->
        <div id="step-4" class="step-4">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                
                </div>
            </div>
        </div>
        <!-- end step-4 -->
    </div>
    <!-- end wizard-content -->
</div>
<!-- end wizard -->

<!-- inicio Modal de Domicilio-->
<div class="modal" id="modal-domicilio" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Domicilio</h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 row">
                    <input type="hidden" id="tipoParteDomicilio">
                    <div class="col-md-4" style="display:none;">
                        <input class="form-control" id="idDomicilio" maxlength="50"   autofocus="" name="id" type="text" value="">
                    </div>
                    <div>    
                        {!! Form::select('tipo_vialidad_id', isset($tipos_vialidades) ? $tipos_vialidades : [] , null, ['id'=>'tipo_vialidad_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('tipo_vialidad_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Tipo de vialidad</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="num_ext" placeholder="Num Exterior" maxlength="50"   autofocus="" name="abogado[primer_apellido]" type="text" value="">
                        
                        <p class="help-block">Numero exterior</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="num_int" placeholder="Num Interior" maxlength="50"   autofocus="" name="abogado[primer_apellido]" type="text" value="">
                        
                        <p class="help-block">Numero interior</p>
                    </div>
                    <div>    
                        {!! Form::select('tipo_asentamiento_id', isset($tipos_asentamientos) ? $tipos_asentamientos : [] , null, ['id'=>'tipo_asentamiento_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('tipo_asentamiento_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Tipo de asentamiento</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="asentamiento" placeholder="Asentamiento" maxlength="50"   autofocus="" name="abogado[primer_apellido]" type="text" value="">
                        
                        <p class="help-block">Nombre asentamiento</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="municipio" placeholder="Municipio" maxlength="50"   autofocus="" name="abogado[primer_apellido]" type="text" value="">
                        <p class="help-block">Nombre del municipio</p>
                    </div>
                    <div>    
                        {!! Form::select('estado_id', isset($estados) ? $estados : [] , null, ['id'=>'estado_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('estado_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Estado</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="cp" placeholder="Codigo Postal" maxlength="50"   autofocus="" name="abogado[primer_apellido]" type="text" value="">
                        
                        <p class="help-block">Codigo postal</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="referencias" placeholder="Referencias" maxlength="50"   autofocus="" name="abogado[segundo_apellido]" type="text" value="">
                        <p class="help-block">Referencias</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="entre_calle1" placeholder="Entre calle" maxlength="50"   autofocus="" name="abogado[segundo_apellido]" type="text" value="">
                        
                        <p class="help-block">Entre calle 1</p>
                    </div>
                    y
                    <div class="col-md-4">
                        <input class="form-control" id="entre_calle2" placeholder="Entre calle 2" maxlength="50"   autofocus="" name="abogado[segundo_apellido]" type="text" value="">
                        <p class="help-block">Entre calle 2</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" onclick="agregarDomicilio()"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Domicilio-->

@push('scripts')
<script>
    // Se declaran las variables globales
    var arraySolicitados = new Array(); //Lista de solicitados
    var arraySolicitantes = new Array(); //Lista de solicitantes
    var arrayDomiciliosSolicitante = new Array(); // Array de domicilios para el solicitante
    var arrayDomiciliosSolicitado = new Array(); // Array de domicilios para el solicitado
    // var rutaApiStore = {{route('solicitudes.store')}};

    $(document).ready(function() {
        $(".personaMoralSolicitado").hide();
        $(".personaMoralSolicitante").hide();
        $(".step-4").hide();

        $("#agregarSolicitante").click(function(){
            //Informacion de solicitante
            if($('#step-1').parsley().validate()){
                
                var solicitante = {};
                if($("input[name='tipo_persona_solicitante']:checked").val() == 1){
                    solicitante.nombre = $("#idNombreSolicitante").val();
                    solicitante.primer_apellido = $("#idPrimerASolicitante").val();
                    solicitante.segundo_apellido = $("#idSegundoASolicitante").val();
                    solicitante.fecha_nacimiento = dateFormat($("#idFechaNacimientoSolicitante").val());
                    solicitante.curp = $("#idSolicitanteCURP").val();    
                    solicitante.genero_id = $("#genero_id_solicitante").val();    
                    solicitante.nacionalidad_id = $("#nacionalidad_id_solicitante").val();    
                    solicitante.entidad_nacimiento_id = $("#entidad_nacimiento_id_solicitante").val();    
                }else{
                    solicitante.nombre_comercial = $("#idNombreCSolicitante").val();
                }
                solicitante.tipo_persona_id = $("input[name='tipo_persona_solicitante']:checked").val()
                solicitante.giro_comercial_id = $("#giro_comercial_solicitante").val()
                solicitante.tipo_parte_id = 1;
                solicitante.rfc = $("#idSolicitanteRfc").val();
                // datos laborales en la solicitante
                var dato_laboral = {};
                dato_laboral.nombre_jefe_directo = $("#nombre_jefe_directo").val();
                dato_laboral.puesto = $("#puesto").val();
                dato_laboral.nss = $("#nss").val();
                dato_laboral.no_issste = $("#no_issste").val();
                dato_laboral.no_afore = $("#no_afore").val();
                dato_laboral.percepcion_mensual_neta = $("#percepcion_mensual_neta").val();
                dato_laboral.percepcion_mensual_bruta = $("#percepcion_mensual_bruta").val();
                dato_laboral.labora_actualmente = $("#labora_actualmente").is(":checked");
                dato_laboral.fecha_ingreso = dateFormat($("#fecha_ingreso").val());
                dato_laboral.fecha_salida = dateFormat($("#fecha_salida").val());
                dato_laboral.jornada_id = $("#jornada_id").val();
                dato_laboral.horas_semanales = $("#horas_semanales").val();
                solicitante.datos_laborales = dato_laboral;

                //domicilio del solicitante
                solicitante.domicilios = arrayDomiciliosSolicitante;
                //domicilio

                arraySolicitantes.push(solicitante);
                limpiarSolicitante();
                formarTablaSolicitante();
            }
        });

        /**
        * Funcion para agregar solicitado a lista de solicitados
        */
        $("#agregarSolicitado").click(function(){
            var solicitado = {};
            // Si tipo persona es fisica o moral llena diferentes campos
            if($("input[name='tipo_persona_solicitado']:checked").val() == 1){
                solicitado.nombre = $("#idNombreSolicitado").val();
                solicitado.primer_apellido = $("#idPrimerASolicitado").val();
                solicitado.segundo_apellido = $("#idSegundoASolicitado").val();
                solicitado.fecha_nacimiento = dateFormat($("#idFechaNacimientoSolicitado").val());
                solicitado.curp = $("#idSolicitadoCURP").val();    
                solicitado.genero_id = $("#genero_id_solicitado").val();    
                solicitado.nacionalidad_id = $("#nacionalidad_id_solicitado").val();    
                solicitado.entidad_nacimiento_id = $("#entidad_nacimiento_id_solicitado").val();    
            }else{
                solicitado.nombre_comercial = $("#idNombreCSolicitado").val();
            }
            solicitado.tipo_persona_id = $("input[name='tipo_persona_solicitado']:checked").val();
            solicitado.giro_comercial_id = $("#giro_comercial_solicitado").val()
            solicitado.tipo_parte_id = 2;
            solicitado.rfc = $("#idSolicitadoRfc").val();
            
            arraySolicitados.push(solicitado);
            formarTablaSolicitado();
        });

        /**
        *Funcion para limpiar campos de solicitante
        */
        function limpiarSolicitante(){
            $("#idNombreSolicitante").val("");
            $("#idPrimerASolicitante").val("");
            $("#idSegundoASolicitante").val("");
            $("#idFechaNacimientoSolicitante").val("");
            $("#idEdadSolicitante").val("");
            $("#idSolicitanteCURP").val("");    
            $("#idNombreCSolicitante").val("");
            $("#tipo_persona_fisica_solicitante").prop("checked", true);
            $(".personaMoralSolicitante").hide();
            $(".personaFisicaSolicitante").show();
            $("#idSolicitanteRfc").val("");
            $("#nombre_jefe_directo").val("");
            $("#puesto").val("");
            $("#nss").val("");
            $("#no_issste").val("");
            $("#no_afore").val("");
            $("#percepcion_mensual_neta").val("");
            $("#percepcion_mensual_bruta").val("");
            $("#labora_actualmente").prop("checked", false);
            $("#fecha_ingreso").val("");
            $("#fecha_salida").val("");
            $("#jornada_id").val("");
            $("#horas_semanales").val("");
            $("#genero_id_solicitante").val("");    
            $("#nacionalidad_id_solicitante").val("");    
            $("#entidad_nacimiento_id_solicitante").val("");    
        }

        /**
        *Funcion para limpiar campos de solicitante
        */
        function limpiarSolicitado(){
            $("#idNombreSolicitado").val("");
            $("#idPrimerASolicitado").val("");
            $("#idSegundoASolicitado").val("");
            $("#idFechaNacimientoSolicitado").val("");
            $("#idEdadSolicitado").val("");
            $("#idSolicitadoCURP").val("");    
            $("#idNombreCSolicitado").val("");
            $("#tipo_persona_fisica_solicitado").prop("checked", true);
            $(".personaMoralSolicitado").hide();
            $(".personaFisicaSolicitado").show();
            $("#idSolicitadoRfc").val("");
            $("#genero_id_solicitado").val("");    
            $("#nacionalidad_id_solicitado").val("");    
            $("#entidad_nacimiento_id_solicitado").val("");    
        }

        /**
        * Funcion para limpiar domicilios
        */
        function limpiarDomicilios(){
            $("#num_ext").val("");
            $("#num_int").val("");
            $("#asentamiento").val("");
            $("#municipio").val("");
            $("#cp").val("");
            $("#entre_calle1").val("");
            $("#entre_calle2").val("");
            $("#referencias").val("");
            $("#tipo_vialidad_id").val("");
            $("#tipo_asentamiento_id").val("");
            $("#estado_id").val();
        }

        /**
        * Funcion para conocer si el tipo persona del solicitante es moral o fisica
        */
        $("input[name='tipo_persona_solicitante']").change(function(){
            if($("input[name='tipo_persona_solicitante']:checked").val() == 1){
                $(".personaMoralSolicitante").hide();
                $(".personaFisicaSolicitante").show();
            }else{
                $(".personaMoralSolicitante").show();
                $(".personaFisicaSolicitante").hide();
            }
        });

        /**
        * Funcion para conocer si el tipo persona del solicitado es moral o fisica
        */
        $("input[name='tipo_persona_solicitado']").change(function(){
            if($("input[name='tipo_persona_solicitado']:checked").val() == 1){
                $(".personaMoralSolicitado").hide();
                $(".personaFisicaSolicitado").show();
            }else{
                $(".personaMoralSolicitado").show();
                $(".personaFisicaSolicitado").hide();
            }
        });
    });
    
    /**
    * Funcion para generar tabla a partir de array de solicitantes
    */
    function formarTablaSolicitante(){
        var html = "";
        
        $("#tbodySolicitante").html("");
        
        $.each(arraySolicitantes, function (key, value) {
            console.log(value);
            html += "<tr>";
            if(value.tipo_persona == 0){
                html += "<td>" + value.nombre + " " + value.primer_apellido + " " + value.segundo_apellido + "</td>";
            }else{
                html += "<td> " + value.nombre_comercial + " </td>";    
            }
            
            html += "<td> " + value.rfc + " </td>";
            if(value.tipo_persona == 1){
                html += "<td> " + value.curp + " </td>";
            }else{
                html += "<td></td>";    
            }
            
            html += "<td><a class='btn btn-xs btn-info'><i class='fa fa-pencil-alt'></i> </a> <a class='btn btn-xs btn-warning' onclick='eliminarSolicitante("+key+")' ><i class='fa fa-trash'></i></a></td>";
            html += "</tr>";
            $("#tbodySolicitante").html(html);
        });
    }

    /**
    * Funcion para generar tabla a partir de array de solicitados
    */
    function formarTablaSolicitado(){
        var html = "";
        
        $("#tbodySolicitado").html("");
        
        $.each(arraySolicitados, function (key, value) {
            console.log(value);
            html += "<tr>";
            if(value.tipo_persona == 1){
                html += "<td>" + value.nombre + " " + value.primer_apellido + " " + value.segundo_apellido + "</td>";
            }else{
                html += "<td> " + value.nombre_comercial + " </td>";    
            }
            
            html += "<td> " + value.rfc + " </td>";
            if(value.tipo_persona == 1){
                html += "<td> " + value.curp + " </td>";
            }else{
                html += "<td></td>";    
            }
            
            html += "<td><a class='btn btn-xs btn-info'><i class='fa fa-pencil-alt'></i> </a> <a class='btn btn-xs btn-warning' onclick='eliminarSolicitante("+key+")' ><i class='fa fa-trash'></i></a></td>";
            html += "</tr>";
            $("#tbodySolicitado").html(html);
        });
    }

    /**
    * Funcion para generar tabla a partir de array domicilios solicitantes
    */
    function formarTablaDomiciliosSolicitante(){
        var html = "";
        
        $("#tbodyDomicilioSolicitante").html("");
        
        $.each(arrayDomiciliosSolicitante, function (key, value) {
            console.log(value);
            html += "<tr>";
            html += "<td>" + value.asentamiento + " " + value.cp + "</td>";
            html += "<td><a class='btn btn-xs btn-info'><i class='fa fa-pencil-alt'></i> </a> <a class='btn btn-xs btn-warning' onclick='eliminarDomicilio("+key+")' ><i class='fa fa-trash'></i></a></td>";
            html += "</tr>";
            
        });
        $("#tbodyDomicilioSolicitante").html(html);
        
    }
    /**
    * Funcion para generar tabla a partir de array domicilios solicitados
    */
    function formarTablaDomiciliosSolicitado(){
        var html = "";
        
        $("#tbodySolicitado").html("");
        
        $.each(arrayDomiciliosSolicitado, function (key, value) {
            html += "<tr>";
            html += "<td>" + value.asentamiento + " " + value.cp + "</td>";
            html += "<td><a class='btn btn-xs btn-info'><i class='fa fa-pencil-alt'></i> </a> <a class='btn btn-xs btn-warning' onclick='eliminarDomicilio("+key+")' ><i class='fa fa-trash'></i></a></td>";
            html += "</tr>";
        });
        $("#tbodyDomicilioSolicitado").html(html);
    }

    /**
    * Funcion para agregar Domicilio de solicitante y solicitado 
    */
    function agregarDomicilio(){
        var domicilio = {};
        // tipoParteDomicilio 0 es solicitante 1 es solicitud 
        domicilio.tipoParteDomicilio = $("#tipoParteDomicilio").val();
        domicilio.num_ext = $("#num_ext").val();
        domicilio.num_int = $("#num_int").val();
        domicilio.asentamiento = $("#asentamiento").val();
        domicilio.municipio = $("#municipio").val();
        domicilio.cp = $("#cp").val();
        domicilio.entre_calle1 = $("#entre_calle1").val();
        domicilio.entre_calle2 = $("#entre_calle2").val();
        domicilio.referencias = $("#referencias").val();
        domicilio.tipo_vialidad_id = $("#tipo_vialidad_id").val();
        domicilio.tipo_asentamiento_id = $("#tipo_asentamiento_id").val();
        domicilio.estado_id = $("#estado_id").val();
        if($("#tipoParteDomicilio").val() == 0){
            arrayDomiciliosSolicitante.push(domicilio);
            formarTablaDomiciliosSolicitante();
        }else{
            arrayDomiciliosSolicitado.push(domicilio);
            formarTablaDomiciliosSolicitado();
        }
        $('#modal-domicilio').modal('hide');
        limpiarDomicilios();
    }

    /**
    * Funcion para guardar solicitud
    */
    function guardarSolicitud(){
        //funcion para obtener informacion de la solicitud
        var solicitud = getSolicitud();
        //funcion para obtener informacion de la excepcion
        var excepcion = getExcepcion();
        //Se llama api para guardar solicitud
        $.ajax({
            url:'https://192.168.10.10/solicitudes',
            type:"POST",
            dataType:"json",
            async:false,
            data:{
                solicitados:arraySolicitados,
                solicitantes:arraySolicitantes,
                solicitud:solicitud,
                excepcion:excepcion,
                _token:$("input[name=_token]").val()

            },
            success:function(data){
                console.log(data);
                // if(data != null){
                //     $("#id").val(data.id);
                //     $("#nombreCentro").text(data.nombre);
                //     limpiarModal();
                //     $.each(data.disponibilidades,function(index,data){
                //         var elm = $("#switch"+data.dia);
                //         $(elm).trigger('click');
                //         $(elm).prev().val(data.id);
                //         $(elm).parent().next().children().next().val(data.hora_inicio);
                //         $(elm).parent().next().next().children().next().val(data.hora_fin);
                //     });
                //     $("#modal-disponinbilidad").modal("show");
                // }
            }
        });
    }

    //funcion para obtener informacion de la solicitud
    function getSolicitud(){
        var solicitud = {};
        solicitud.observaciones = $("#observaciones").val();
        solicitud.estatus_solicitud_id = $("#estatus_solicitud_id").val();
        solicitud.objeto_solicitud_id = $("#objeto_solicitud_id").val();
        solicitud.centro_id = $("#centro_id").val();
        solicitud.ratificada = $("#ratificada").is(":checked");
        solicitud.solicita_excepcion = $("#solicita_excepcion").is(":checked");
        solicitud.fecha_ratificacion = dateFormat($("#fechaRatificacion").val());
        solicitud.fecha_recepcion = dateFormat($("#fechaRecepcion").val());
        solicitud.fecha_conflicto = dateFormat($("#fechaConflicto").val());
        return solicitud;
    }

    //funcion para obtener informacion de la excepcion
    function getExcepcion(){
        var excepcion = {};
        return excepcion;
    }
    $(".catSelect").select2();
    $(".date").datetimepicker({useCurrent: false,format:'DD/MM/YYYY'});
    $(".date").keypress(function(event){
        event.preventDefault();
    });

    (function (a) {
        a.fn.limitKeyPress = function (b) {
            a(this).on({keypress: function (a) {
                    var c = a.which, d = a.keyCode, e = String.fromCharCode(c).toLowerCase(), f = b;
                    (-1 != f.indexOf(e) || 9 == d || 37 != c && 37 == d || 39 == d && 39 != c || 8 == d || 46 == d && 46 != c) && 161 != c || a.preventDefault()
                }})
        }
    })(jQuery);

    $(".numero").limitKeyPress('1234567890');

    function dateFormat(fecha){
        if(fecha != ""){
            var vecFecha = fecha.split("/");
            var formatedDate = vecFecha[2] + "-" + vecFecha[1] + "-" + vecFecha[0];
            return formatedDate;
        }
    }
    

</script>
<script src="/assets/plugins/parsleyjs/dist/parsley.min.js"></script>
<script src="/assets/plugins/highlight.js/highlight.min.js"></script>
<script src="/assets/plugins/highlight.js/es.js"></script>
@endpush
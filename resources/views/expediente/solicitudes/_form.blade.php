
<style>
    .inputError {
        border: 1px red solid;
    }
    .needed:after {
      color:darkred;
      content: " (*)";
   }
   .widget-maps{
        min-height: 350px;
        position: relative;
        border: thin solid #c0c0c0;
        width:100%;
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
                            <input type="hidden" id="solicitante_id">
                            <input type="hidden" id="edit_key">
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
                        <div class="col-md-8 personaFisicaSolicitante">
                            <input class="form-control" id="idSolicitanteCURP" placeholder="CURP del solicitante"  autofocus="" type="text" value="">
                            <p class="help-block">CURP del solicitante</p>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4" style="display:none;">
                                <input class="form-control" id="idsolicitante" type="text" value="253">
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control" id="idNombreSolicitante" required placeholder="Nombre del solicitante" type="text" value="">
                                <p class="help-block needed">Nombre del solicitante</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control" id="idPrimerASolicitante" required placeholder="Primer apellido del solicitante" type="text" value="">
                                
                                <p class="help-block needed">Primer apellido</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control" id="idSegundoASolicitante" placeholder="Segundo apellido del solicitante" type="text" value="">
                                
                                <p class="help-block">Segundo apellido</p>
                            </div>
                        </div>
                        <div class="col-md-12 row personaMoralSolicitante">
                            <input class="form-control" id="idNombreCSolicitante" placeholder="Nombre comercial" type="text" value="">
                            <p class="help-block">Nombre comercia</p>
                        </div>
                        <div class="col-md-12 row ">
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control date" required id="idFechaNacimientoSolicitante" placeholder="Fecha de nacimeinto del solicitante" type="text" value="">
                                <p class="help-block needed">Fecha de nacimiento</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control numero" required data-parsley-type='integer' id="idEdadSolicitante" placeholder="Edad del solicitante" type="text" value="">
                                <p class="help-block needed">Edad del solicitante</p>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" required id="idSolicitanteRfc" placeholder="Rfc del solicitante" type="text" value="">
                                <p class="help-block needed">Rfc del solicitante</p>
                            </div>
                        </div>
                        <div class="col-md-12 row personaFisicaSolicitante">
                            <div class="col-md-4">
                                {!! Form::select('genero_id_solicitante', isset($generos) ? $generos : [] , null, ['id'=>'genero_id_solicitante','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('genero_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Genero</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::select('giro_comercial_solicitante', isset($giros_comerciales) ? $giros_comerciales : [] , null, ['id'=>'giro_comercial_solicitante','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('giro_comercial_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Giro Comercial</p>
                            </div>
                            
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4">
                                {!! Form::select('nacionalidad_id_solicitante', isset($nacionalidades) ? $nacionalidades : [] , null, ['id'=>'nacionalidad_id_solicitante','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('nacionalidad_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Nacionalidad</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::select('entidad_nacimiento_id_solicitante', isset($estados) ? $estados : [] , null, ['id'=>'entidad_nacimiento_id_solicitante','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('entidad_nacimiento_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Estado de nacimiento</p>
                            </div>
                        </div>
                        <!-- seccion de domicilios solicitante -->
                        <div style="margin-top: 1%;" >
                            <h4>Domicilios</h4>
                            <div class="col-md-12 row">
                                <input type="hidden" id="domicilio_solicitante_id">
                                <input type="hidden" id="direccion_marker">
                                <input type="hidden" id="latitud_solicitante">
                                <input type="hidden" id="longitud_solicitante">
                                <div class="col-md-10">
                                    <input id="autocomplete"
                                    class="form-control"
                                    onfocus="geolocate()"
                                    placeholder="Escriba la dirección y seleccione la opción correcta o más cercana."
                                    type="text"/>
                                    <p class="help-block needed">Escriba la dirección y seleccione la opción correcta o más cercana.</p>
                                </div>
                                <div class="col-md-4">    
                                    {!! Form::select('estado_id_solicitante', isset($estados) ? $estados : [] , null, ['id'=>'estado_id_solicitante','required','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect direccionUpd']);  !!}
                                    {!! $errors->first('estado_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block needed">Estado</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control direccionUpd" id="municipio_solicitante" required placeholder="Municipio" type="text" value="">
                                    <p class="help-block needed">Nombre del municipio</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero" id="cp_solicitante" required placeholder="Codigo Postal" maxlength="5" type="text" value="">
                                    
                                    <p class="help-block needed">Codigo postal</p>
                                </div>
                                <div class="col-md-4">    
                                    {!! Form::select('tipo_asentamiento_id_solicitante', isset($tipos_asentamientos) ? $tipos_asentamientos : [] , null, ['id'=>'tipo_asentamiento_id_solicitante','required','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                    {!! $errors->first('tipo_asentamiento_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block">Tipo de asentamiento</p>
                                </div>
                                <div class="col-md-4"   >    
                                    {!! Form::select('tipo_vialidad_id_solicitante', isset($tipos_vialidades) ? $tipos_vialidades : [] , null, ['id'=>'tipo_vialidad_id_solicitante','required','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect direccionUpd']);  !!}
                                    {!! $errors->first('tipo_vialidad_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block">Tipo de vialidad</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control direccionUpd" id="vialidad_solicitante" placeholder="Vialidad" required type="text" value="">
                                    <p class="help-block">Vialidad</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero direccionUpd" id="num_ext_solicitante" placeholder="Num Exterior" required type="text" value="">
                                    <p class="help-block">Numero exterior</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero" id="num_int_solicitante" placeholder="Num Interior" required type="text" value="">
                                    
                                    <p class="help-block">Numero interior</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control direccionUpd" id="asentamiento_solicitante" placeholder="Asentamiento" required type="text" value="">
                                    <p class="help-block">Nombre asentamiento</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" id="referencias_solicitante" placeholder="Referencias" required type="text" value="">
                                    <p class="help-block">Referencias</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" id="entre_calle1_solicitante" placeholder="Entre calle" required type="text" value="">
                                    
                                    <p class="help-block">Entre calle</p>
                                </div>
                                y
                                <div class="col-md-4">
                                    <input class="form-control" id="entre_calle2_solicitante" placeholder="Entre calle 2" required type="text" value="">
                                    <p class="help-block">Entre calle</p>
                                </div>
                            </div>
                            <div class="widget-maps" id="widget-maps"></div>
                        
                                  
                        
                        </div>
                        <!-- end seccion de domicilios solicitante -->
                        <!-- Seccion de Datos laborales -->
                        <div>
                            <h4>Datos Laborales</h4>
                            <input type="hidden" id="dato_laboral_id">
                            <div class="col-md-12">
                                <input class="form-control" required id="nombre_jefe_directo" placeholder="Nombre del jefe directo" type="text" value="">
                                <p class="help-block needed">Nombre del Jefe directo</p>
                            </div>
                            <div class="col-md-12 row">
                                <div class="col-md-4"   >    
                                    {!! Form::select('ocupacion_id', isset($ocupaciones) ? $ocupaciones : [] , null, ['id'=>'ocupacion_id','required','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                    {!! $errors->first('ocupacion_id', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block needed">Ocupaci&oacute;n</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero" data-parsley-type='integer' required id="nss" placeholder="No. servicio social"  type="text" value="">
                                    <p class="help-block needed">Numero de seguro social</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero" data-parsley-type='integer' id="no_issste" placeholder="No del ISSSTE"  type="text" value="">
                                    <p class="help-block">No. ISSSTE</p>
                                </div>
                            </div>
                            <div class="col-md-12 row">
                                <div class="col-md-4">
                                    <input class="form-control numero" data-parsley-type='integer' id="no_afore" placeholder="No afore" type="text" value="">
                                    <p class="help-block">No. afore</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero " required data-parsley-type='number' id="percepcion_mensual_neta" placeholder="Percepcion neta mensual" type="text" value="">
                                    <p class="help-block needed">Percepcion neta mensual</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero" required data-parsley-type='number' id="percepcion_mensual_bruta" placeholder="Percepci&oacute;n mensual bruta" type="text" value="">
                                    <p class="help-block needed">Percepci&oacute;n mensual bruta</p>
                                </div>
                            </div>
                            <div class="col-md-12 row">
                                
                                <div class="col-md-4">
                                    <input class="form-control date" required id="fecha_ingreso" placeholder="Fecha de ingreso" type="text" value="">
                                    <p class="help-block needed">Fecha de ingreso</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control date" required id="fecha_salida" placeholder="Fecha salida" type="text" value="">
                                    <p class="help-block needed">Fecha salida</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control numero" required data-parsley-type='integer' id="horas_semanales" placeholder="Horas semanales" type="text" value="">
                                    <p class="help-block">Horas semanales</p>
                                </div>
                            </div>
                            {!! Form::select('jornada_id', isset($jornadas) ? $jornadas : [] , null, ['id'=>'jornada_id','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control col-md-4 catSelect']);  !!}
                            {!! $errors->first('jornada_id', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block needed">Jornada</p>
                            <div class="col-md-4">
                                <input id="labora_actualmente" type="checkbox" value="1">
                                <label for="labora_actualmente">Labora actualmente</label>
                            </div>
                        </div>
                        <!-- end Seccion de Datos laborales -->

                        <hr style="margin-top:5%;">
                        <div>
                            <button class="btn btn-info" type="button" id="agregarSolicitante" > <i class="fa fa-plus-circle"></i> Agregar solicitante</button>
                            <button class="btn btn-warning" type="button" onclick="limpiarSolicitante()"> <i class="fa fa-eraser"></i> Limpiar campos</button>
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
                            <input type="hidden" id="solicitado_id">
                            <input type="hidden" id="solicitado_key">
                            <div class="row">
                                <div class="col-md-offset-6">
                                    <input checked="checked" id="tipo_persona_fisica_solicitado" name="tipo_persona_solicitado" type="radio" value="1">
                                    <label for="tipo_persona_fisica_solicitado">Fisica</label>
                                </div>
                                <div class="col-md-offset-6">
                                    <input id="tipo_persona_moral_solicitado" name="tipo_persona_solicitado" type="radio" value="2">
                                    <label for="tipo_persona_moral_solicitado">Moral</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 personaFisicaSolicitado">
                            <input class="form-control" required id="idSolicitadoCURP" placeholder="CURP del solicitado" type="text" value="">
                            <p class="help-block">CURP del solicitado</p>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4" style="display:none;">
                                <input class="form-control" id="idsolicitado" type="text" value="253">
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control" required id="idNombreSolicitado" placeholder="Nombre del solicitado" type="text" value="">
                                <p class="help-block needed">Nombre del solicitado</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control" required id="idPrimerASolicitado" placeholder="Primer apellido del solicitado" type="text" value="">
                                
                                <p class="help-block needed">Primer apellido</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control" id="idSegundoASolicitado" placeholder="Segundo apellido del solicitado" type="text" value="">
                                
                                <p class="help-block">Segundo apellido</p>
                            </div>
                        </div>
                        <div class="col-md-12 row personaMoralSolicitado">
                            <div class="col-md-8">
                                <input class="form-control" id="idNombreCSolicitado" required placeholder="Nombre comercial del solicitado" type="text" value="">
                                <p class="help-block needed">Nombre comercia</p>
                            </div>
                        </div>
                        <div class="col-md-12 row ">
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control date" id="idFechaNacimientoSolicitado" placeholder="Fecha de nacimeinto del solicitado" type="text" value="">
                                <p class="help-block needed">Fecha de nacimiento</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control numero" required id="idEdadSolicitado" placeholder="Edad del solicitado" type="text" value="">
                                <p class="help-block needed">Edad del solicitado</p>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" required id="idSolicitadoRfc" placeholder="Rfc del solicitado" type="text" value="">
                                <p class="help-block needed">Rfc del solicitado</p>
                            </div>
                        </div>
                        <div class="col-md-12 row personaFisicaSolicitado">
                            <div class="col-md-4">
                                {!! Form::select('genero_id_solicitado', isset($generos) ? $generos : [] , null, ['id'=>'genero_id_solicitado','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('genero_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Genero</p>
                            </div>
                            <div class="col-md-4">
                                {!! Form::select('giro_comercial_solicitado', isset($giros_comerciales) ? $giros_comerciales : [] , null, ['id'=>'giro_comercial_solicitado','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('giro_comercial_solicitando', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Giro Comercial</p>
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
                                <a style="font-size:large; margin-left:1%; color:#49b6d6;" onclick="$('#modal-domicilio').modal('show');"  > <i class="fa fa-plus-circle"></i></a>
                            </div>
                            <div class="col-md-10 offset-md-1" style="margin-top: 3%;" >
                                <table class="table table-bordered" >
                                    <thead>
                                        <tr>
                                            <th>Domicilio</th>
                                            <th>Accion</th>
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
                    <center>  <h1>Solicitud</h1></center>
                <div class="col-md-12 row">
                    <input type="hidden" id="solicitud_id" value="<?php echo isset($solicitud) ? $solicitud->id : ""; ?>">
                    <div class="col-md-4">
                        {!! Form::select('estatus_solicitud_id', isset($estatus_solicitudes) ? $estatus_solicitudes : [] , null, ['id'=>'estatus_solicitud_id','required','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('estatus_solicitud_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block needed">Estatus de la solicitud</p>
                    </div>    
                    <div class="col-md-4">
                        {!! Form::select('centro_id', isset($centros) ? $centros : [] , null, ['id'=>'centro_id','required','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('centro_di', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block needed">Centro de la solicitud</p>
                    </div>
                    
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-4">
                        <input class="form-control dateTime" required id="fechaRatificacion" placeholder="Fecha de ratificacion" type="text" value="">
                        <p class="help-block needed">Fecha de Ratificación</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control dateTime" required id="fechaRecepcion" placeholder="Fecha de Recepcion" type="text" value="">                
                        <p class="help-block needed">Fecha de Recepción</p>    
                    </div>
                    <div class="col-md-4">
                        <input class="form-control date" required id="fechaConflicto" placeholder="Fecha de Conflicto" type="text" value="">
                        <p class="help-block needed">Fecha de Conflicto</p>
                    </div>
                </div>
                <div>
                    <div class="col-md-4">
                        {!! Form::select('objeto_solicitud_id', isset($objeto_solicitudes) ? $objeto_solicitudes : [] , null, ['id'=>'objeto_solicitud_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('objeto_solicitud_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block needed">Objeto de la solicitud</p>
                    </div>
                    <button class="btn btn-primary btn-sm m-l-5" onclick="agregarObjetoSol()"><i class="fa fa-save"></i> Agregar objeto</button>
                    <div class="col-md-10 offset-md-1" style="margin-top: 3%;" >
                        <table class="table table-bordered" >
                            <thead>
                                <tr>
                                    <th>Objeto</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyObjetoSol">
                            </tbody>
                        </table>  
                    </div>
                </div>
                <div>
                    <div class="">
                        <input id="ratificada" type="checkbox" value="1">
                        <label for="ratificada">Ratificada</label>
                    </div>
                    <div class="">
                        <input id="solicita_excepcion" type="checkbox" value="1">
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

<!-- inicio Modal Domicilio-->
<div class="modal" id="modal-domicilio" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Domicilio</h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 row">
                    <input type="hidden" id="domicilio_id_modal">
                    <input type="hidden" id="domicilio_key">
                    <div>    
                        {!! Form::select('tipo_vialidad_id', isset($tipos_vialidades) ? $tipos_vialidades : [] , null, ['id'=>'tipo_vialidad_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('tipo_vialidad_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Tipo de vialidad</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control numero" id="num_ext" placeholder="Num Exterior" type="text" value="">
                        
                        <p class="help-block">Numero exterior</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control numero" id="num_int" placeholder="Num Interior" type="text" value="">
                        
                        <p class="help-block">Numero interior</p>
                    </div>
                    <div>    
                        {!! Form::select('tipo_asentamiento_id', isset($tipos_asentamientos) ? $tipos_asentamientos : [] , null, ['id'=>'tipo_asentamiento_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('tipo_asentamiento_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Tipo de asentamiento</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="asentamiento" placeholder="Asentamiento" type="text" value="">
                        
                        <p class="help-block">Nombre asentamiento</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="municipio" placeholder="Municipio" type="text" value="">
                        <p class="help-block">Nombre del municipio</p>
                    </div>
                    <div>    
                        {!! Form::select('estado_id', isset($estados) ? $estados : [] , null, ['id'=>'estado_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('estado_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Estado</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control numero" id="cp" placeholder="Codigo Postal" maxlength="5" type="text" value="">
                        <p class="help-block">Codigo postal</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="referencias" placeholder="Referencias" type="text" value="">
                        <p class="help-block">Referencias</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" id="entre_calle1" placeholder="Entre calle" type="text" value="">
                        
                        <p class="help-block">Entre calle 1</p>
                    </div>
                    y
                    <div class="col-md-4">
                        <input class="form-control" id="entre_calle2" placeholder="Entre calle 2" type="text" value="">
                        <p class="help-block">Entre calle 2</p>
                    </div>
                    <div class="widget-maps" id="widget-maps2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal" onclick="limpiarDomicilios()"><i class="fa fa-times"></i> Cancelar</a>
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
    var arrayObjetoSolicitudes = new Array(); // Array de objeto_solicitude para el solicitado

    $(document).ready(function() {
        $(".personaMoralSolicitado").hide();
        $(".personaMoralSolicitante").hide();
        $(".personaFisicaSolicitante input").attr("required","");
        $(".personaFisicaSolicitado input").attr("required","");
        $(".personaMoralSolicitante input").removeAttr("required");
        $(".personaMoralSolicitado input").removeAttr("required");
        $(".step-4").hide();

        $("#agregarSolicitante").click(function(){
            //Informacion de solicitante
            var key = $("#edit_key").val();
            if($('#step-1').parsley().validate()){
                
                var solicitante = {};
                solicitante.id = $("#solicitante_id").val();
                if($("input[name='tipo_persona_solicitante']:checked").val() == 1){
                    solicitante.nombre = $("#idNombreSolicitante").val();
                    solicitante.primer_apellido = $("#idPrimerASolicitante").val();
                    solicitante.segundo_apellido = $("#idSegundoASolicitante").val();
                    solicitante.fecha_nacimiento = dateFormat($("#idFechaNacimientoSolicitante").val());
                    solicitante.curp = $("#idSolicitanteCURP").val();    
                    solicitante.edad = $("#idEdadSolicitante").val();
                    solicitante.genero_id = $("#genero_id_solicitante").val();    
                    solicitante.nacionalidad_id = $("#nacionalidad_id_solicitante").val();    
                    solicitante.entidad_nacimiento_id = $("#entidad_nacimiento_id_solicitante").val();    
                }else{
                    solicitante.nombre_comercial = $("#idNombreCSolicitante").val();
                }
                solicitante.tipo_persona_id = $("input[name='tipo_persona_solicitante']:checked").val()
                solicitante.giro_comercial_id = $("#giro_comercial_solicitante").val()
                solicitante.tipo_parte_id = 1;
                solicitante.activo = 1;
                solicitante.rfc = $("#idSolicitanteRfc").val();
                // datos laborales en la solicitante
                var dato_laboral = {};
                dato_laboral.id = $("#dato_laboral_id").val();
                dato_laboral.nombre_jefe_directo = $("#nombre_jefe_directo").val();
                dato_laboral.ocupacion_id = $("#ocupacion_id").val();
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
                solicitante.dato_laboral = dato_laboral;

                //domicilio del solicitante
                var domicilio = {};
                domicilio.id = $("#domicilio_solicitante_id").val();
                domicilio.num_ext = $("#num_ext_solicitante").val();
                domicilio.num_int = $("#num_int_solicitante").val();
                domicilio.asentamiento = $("#asentamiento_solicitante").val();
                domicilio.municipio = $("#municipio_solicitante").val();
                domicilio.cp = $("#cp_solicitante").val();
                domicilio.entre_calle1 = $("#entre_calle1_solicitante").val();
                domicilio.entre_calle2 = $("#entre_calle2_solicitante").val();
                domicilio.referencias = $("#referencias_solicitante").val();
                domicilio.tipo_vialidad_id = $("#tipo_vialidad_id_solicitante").val();
                domicilio.vialidad = $("#vialidad_solicitante").val();
                domicilio.tipo_asentamiento_id = $("#tipo_asentamiento_id_solicitante").val();
                domicilio.estado_id = $("#estado_id_solicitante").val();
                solicitante.domicilios = [domicilio];
                //domicilio
                if(key == ""){
                    arraySolicitantes.push(solicitante);
                }else{
                    
                    arraySolicitantes[key] = solicitante;
                }
                
                limpiarSolicitante();
                formarTablaSolicitante();
            }
        });

        /**
        * Funcion para agregar solicitado a lista de solicitados
        */
        $("#agregarSolicitado").click(function(){
            if($('#step-2').parsley().validate()){
                var solicitado = {};
                key = $("#solicitado_key").val();
                solicitado.id = $("#solicitado_id").val();
                // Si tipo persona es fisica o moral llena diferentes campos
                if($("input[name='tipo_persona_solicitado']:checked").val() == 1){
                    solicitado.nombre = $("#idNombreSolicitado").val();
                    solicitado.primer_apellido = $("#idPrimerASolicitado").val();
                    solicitado.segundo_apellido = $("#idSegundoASolicitado").val();
                    solicitado.fecha_nacimiento = dateFormat($("#idFechaNacimientoSolicitado").val());
                    solicitado.curp = $("#idSolicitadoCURP").val();    
                    solicitado.edad = $("#idEdadSolicitado").val();    
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
                solicitado.activo = 1;
                solicitado.domicilios = arrayDomiciliosSolicitado;
                
                if(key == ""){
                    arraySolicitados.push(solicitado);
                }else{
                    
                    arraySolicitados[key] = solicitado;
                }
                formarTablaSolicitado();
                limpiarSolicitado();
                arrayDomiciliosSolicitado = new Array();
                formarTablaDomiciliosSolicitado();  
            }
        });

        

        /**
        * Funcion para conocer si el tipo persona del solicitante es moral o fisica
        */
        $("input[name='tipo_persona_solicitante']").change(function(){
            if($("input[name='tipo_persona_solicitante']:checked").val() == 1){
                $(".personaFisicaSolicitante input").attr("required","");
                $(".personaMoralSolicitante input").removeAttr("required");
                $(".personaMoralSolicitante").hide();
                $(".personaFisicaSolicitante").show();
            }else{
                $(".personaFisicaSolicitante input").removeAttr("required");
                $(".personaMoralSolicitante input").attr("required","");
                $(".personaMoralSolicitante").show();
                $(".personaFisicaSolicitante").hide();
            }
        });

        /**
        * Funcion para conocer si el tipo persona del solicitado es moral o fisica
        */
        $("input[name='tipo_persona_solicitado']").change(function(){
            if($("input[name='tipo_persona_solicitado']:checked").val() == 1){
                $(".personaFisicaSolicitado input").attr("required","");
                $(".personaMoralSolicitado input").removeAttr("required");
                $(".personaMoralSolicitado").hide();
                $(".personaFisicaSolicitado").show();
            }else{
                $(".personaFisicaSolicitado input").removeAttr("required");
                $(".personaMoralSolicitado input").attr("required","");
                $(".personaMoralSolicitado").show();
                $(".personaFisicaSolicitado").hide();
            }
        });

        function getSolicitudFromBD(){
            $.ajax({
            url:'/api/solicitudes/'+$("#solicitud_id").val(),
            type:"GET",
            dataType:"json",
            async:false,
            data:{},
            success:function(data){
                arraySolicitados = Object.values(data.solicitados);
                formarTablaSolicitado();
                arraySolicitantes = Object.values(data.solicitantes);
                formarTablaSolicitante();
                
                $.each(data.objeto_solicitudes, function (key, value) {
                    var objeto_solicitud = {};
                    objeto_solicitud.id = value.id;
                    objeto_solicitud.objeto_solicitud_id = value.pivot.objeto_solicitud_id.toString();
                    objeto_solicitud.activo = 1;
                    arrayObjetoSolicitudes.push(objeto_solicitud);
                });
                // arrayObjetoSolicitudes = data.objeto_solicitudes;
                formarTablaObjetoSol();
                $("#observaciones").val(data.observaciones);
                $("#estatus_solicitud_id").val(data.estatus_solicitud_id);
                $("#centro_id").val(data.centro_id);
                if(data.ratificada){
                    $("#ratificada").prop("checked",true);
                }
                if(data.solicita_excepcion){
                    $("#solicita_excepcion").prop("checked",true);
                }
                $("#fechaRatificacion").val(dateFormat(data.fecha_ratificacion,2));
                $("#fechaRecepcion").val(dateFormat(data.fecha_recepcion,2));
                $("#fechaConflicto").val(dateFormat(data.fecha_conflicto,0));
                
            }
            });
        }
        if(edit){
            getSolicitudFromBD();
        }
        
    });

    /**
        *Funcion para limpiar campos de solicitante
        */
        function limpiarSolicitante(){
            $("#edit_key").val("");
            $("#domicilio_solicitante_id").val("");
            $("#dato_laboral_id").val("");
            $("#solicitante_id").val("");
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
            $("#ocupacion_id").val("");
            $("#giro_comercial_solicitante").val("");
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
            $("#num_ext_solicitante").val("");
            $("#num_int_solicitante").val("");
            $("#asentamiento_solicitante").val("");
            $("#municipio_solicitante").val("");
            $("#cp_solicitante").val("");
            $("#entre_calle1_solicitante").val("");
            $("#entre_calle2_solicitante").val("");
            $("#referencias_solicitante").val("");
            $("#tipo_vialidad_id_solicitante").val("");
            $("#vialidad_solicitante").val("");
            $("#tipo_asentamiento_id_solicitante").val("");
            $("#estado_id_solicitante").val("");    
            $('#step-1').parsley().reset();
            $('.catSelect').trigger('change');
        }

        /**
        *Funcion para limpiar campos de solicitante
        */
        function limpiarSolicitado(){
            $("#solicitado_id").val("");
            $("#solicitado_key").val("");
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
            $('.catSelect').trigger('change');  
            limpiarDomicilios();
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
            $("#estado_id").val("");
            $("#domicilio_id_modal").val("");
            $("#domicilio_key").val("");
            $('.catSelect').trigger('change');
        }
    
    /**
    * Funcion para generar tabla a partir de array de solicitantes
    */
    function formarTablaObjetoSol(){
        
        var html = "";
        
        $("#tbodyObjetoSol").html("");
        
        $.each(arrayObjetoSolicitudes, function (key, value) {
            if(value.activo == "1" || value.id != ""){
                html += "<tr>";
                $("#objeto_solicitud_id").val(value.objeto_solicitud_id);
                html += "<td> " + $("#objeto_solicitud_id :selected").text(); + " </td>";
                html += "<td><a class='btn btn-xs btn-warning' onclick='eliminarObjetoSol("+key+")' ><i class='fa fa-trash'></i></a></td>";
                html += "</tr>";
            }
        });
        $("#objeto_solicitud_id").val("");
        $("#tbodyObjetoSol").html(html);
    }
    /**
    * Funcion para generar tabla a partir de array de solicitantes
    */
    function formarTablaSolicitante(){
        
        var html = "";
        
        $("#tbodySolicitante").html("");
        
        $.each(arraySolicitantes, function (key, value) {
            if(value.activo == "1"){
                html += "<tr>";
                if(value.tipo_persona_id == 1){
                    html += "<td>" + value.nombre + " " + value.primer_apellido + " " + value.segundo_apellido + "</td>";
                }else{
                    html += "<td> " + value.nombre_comercial + " </td>";    
                }
                
                html += "<td> " + value.rfc + " </td>";
                if(value.tipo_persona_id == 1){
                    html += "<td> " + value.curp + " </td>";
                }else{
                    html += "<td></td>";    
                }
                
                html += "<td><a class='btn btn-xs btn-info' onclick='cargarEditarSolicitante("+key+")'><i class='fa fa-pencil-alt'></i></a> ";
                html += "<a class='btn btn-xs btn-warning' onclick='eliminarSolicitante("+key+")' ><i class='fa fa-trash'></i></a></td>";
                html += "</tr>";
            }
        });
        $("#tbodySolicitante").html(html);
    }

    /**
    * Funcion para generar tabla a partir de array de solicitados
    */
    function formarTablaSolicitado(){
        var html = "";
        
        $("#tbodySolicitado").html("");
        
        $.each(arraySolicitados, function (key, value) {
            if(value.activo == "1"){
                html += "<tr>";
                if(value.tipo_persona_id == 1){
                    html += "<td>" + value.nombre + " " + value.primer_apellido + " " + value.segundo_apellido + "</td>";
                }else{
                    html += "<td> " + value.nombre_comercial + " </td>";    
                }
                
                html += "<td> " + value.rfc + " </td>";
                if(value.tipo_persona_id == 1){
                    html += "<td> " + value.curp + " </td>";
                }else{
                    html += "<td></td>";    
                }
                
                html += "<td><a class='btn btn-xs btn-info' onclick='cargarEditarSolicitado("+key+")'><i class='fa fa-pencil-alt'></i></a> ";
                html += "<a class='btn btn-xs btn-warning' onclick='eliminarSolicitado("+key+")' ><i class='fa fa-trash'></i></a></td>";
                html += "</tr>";
            }
        });
        $("#tbodySolicitado").html(html);
    }

    /**
    * Funcion para eliminar el solicitante
    *@argument key posicion de array a eliminar
    */
    function eliminarSolicitante(key){
        if(arraySolicitantes[key].id == ""){
            arraySolicitantes = arraySolicitantes.splice(1,key);
        }else{
            arraySolicitantes[key].activo = 0;
        }
        formarTablaSolicitante();
    }
    /**
    * Funcion para eliminar el domicilio
    *@argument key posicion de array a eliminar
    */
    function eliminarDomicilio(key){
        if(arrayDomiciliosSolicitado[key].id == ""){
            arrayDomiciliosSolicitado = arrayDomiciliosSolicitado.splice(1,key);
        }else{
            arrayDomiciliosSolicitado[key].activo = 0;
        }
        formarTablaSolicitante();
    }

    /**
    * Funcion para eliminar el objetos de la solicitud
    *@argument key posicion de array a eliminar
    */
    function eliminarObjetoSol(key){
        if(arrayObjetoSolicitudes[key].id == ""){
            arrayObjetoSolicitudes = arrayObjetoSolicitudes.splice(1,key);
        }else{
            arrayObjetoSolicitudes[key].activo = 0;
        }
        formarTablaSolicitante();
    }

    /**
    * Funcion para eliminar el solicitado
    * @argument key posicion de array a eliminar
    */
    function eliminarSolicitado(key){
        if(arraySolicitados[key].id == ""){
            arraySolicitados.splice(1,key);
        }else{
            arraySolicitados[key].activo = 0;
        }
        formarTablaSolicitado();
    }

    /**
    * Funcion para editar el solicitante
    *@argument key posicion de array a editar
    */
    function cargarEditarSolicitante(key){
            $("#edit_key").val(key);
            $("#solicitante_id").val(arraySolicitantes[key].id);
            if(arraySolicitantes[key].tipo_persona_id == 1){
                $("#idNombreSolicitante").val(arraySolicitantes[key].nombre);
                $("#idPrimerASolicitante").val(arraySolicitantes[key].primer_apellido);
                $("#idSegundoASolicitante").val(arraySolicitantes[key].segundo_apellido);
                $("#idFechaNacimientoSolicitante").val(dateFormat(arraySolicitantes[key].fecha_nacimiento,0));
                $("#idSolicitanteCURP").val(arraySolicitantes[key].curp);    
                $("#genero_id_solicitante").val(arraySolicitantes[key].genero_id);    
                $("#idEdadSolicitante").val(arraySolicitantes[key].edad);    
                $("#nacionalidad_id_solicitante").val(arraySolicitantes[key].nacionalidad_id);    
                $("#entidad_nacimiento_id_solicitante").val(arraySolicitantes[key].entidad_nacimiento_id);
                $("#tipo_persona_fisica_solicitante").prop("checked", true);
                $(".personaMoralSolicitante").hide();
                $(".personaFisicaSolicitante").show();
            }else{
                $(".personaMoralSolicitante").show();
                $(".personaFisicaSolicitante").hide();
                $("#tipo_persona_moral_solicitante").prop("checked", true);
                $("#idNombreCSolicitante").val(arraySolicitantes[key].nombre_comercial);
            }
            $("#giro_comercial_solicitante").val(arraySolicitantes[key].giro_comercial_id);
            $("#idSolicitanteRfc").val(arraySolicitantes[key].rfc);
            // datos laborales en la solicitante
            $("#dato_laboral_id").val(arraySolicitantes[key].dato_laboral.id);
            $("#nombre_jefe_directo").val(arraySolicitantes[key].dato_laboral.nombre_jefe_directo);
            $("#ocupacion_id").val(arraySolicitantes[key].dato_laboral.ocupacion_id);
            $("#nss").val(arraySolicitantes[key].dato_laboral.nss);
            $("#no_issste").val(arraySolicitantes[key].dato_laboral.no_issste);
            $("#no_afore").val(arraySolicitantes[key].dato_laboral.no_afore);
            $("#percepcion_mensual_neta").val(arraySolicitantes[key].dato_laboral.percepcion_mensual_neta);
            $("#percepcion_mensual_bruta").val(arraySolicitantes[key].dato_laboral.percepcion_mensual_bruta);
            $("#labora_actualmente").is(":checked"); arraySolicitantes[key].dato_laboral.labora_actualmente
            $("#fecha_ingreso").val(dateFormat(arraySolicitantes[key].dato_laboral.fecha_ingreso,0));
            $("#fecha_salida").val(dateFormat(arraySolicitantes[key].dato_laboral.fecha_salida,0));
            $("#jornada_id").val(arraySolicitantes[key].dato_laboral.jornada_id);
            $("#horas_semanales").val(arraySolicitantes[key].dato_laboral.horas_semanales);

            //domicilio del solicitante
            $("#domicilio_solicitante_id").val(arraySolicitantes[key].domicilios[0].id);
            $("#num_ext_solicitante").val(arraySolicitantes[key].domicilios[0].num_ext);
            $("#num_int_solicitante").val(arraySolicitantes[key].domicilios[0].num_int);
            $("#asentamiento_solicitante").val(arraySolicitantes[key].domicilios[0].asentamiento);
            $("#municipio_solicitante").val(arraySolicitantes[key].domicilios[0].municipio);
            $("#cp_solicitante").val(arraySolicitantes[key].domicilios[0].cp);
            $("#entre_calle1_solicitante").val(arraySolicitantes[key].domicilios[0].entre_calle1);
            $("#entre_calle2_solicitante").val(arraySolicitantes[key].domicilios[0].entre_calle2);
            $("#referencias_solicitante").val(arraySolicitantes[key].domicilios[0].referencias);
            $("#tipo_vialidad_id_solicitante").val(arraySolicitantes[key].domicilios[0].tipo_vialidad_id);
            $("#vialidad_solicitante").val(arraySolicitantes[key].domicilios[0].vialidad);
            $("#tipo_asentamiento_id_solicitante").val(arraySolicitantes[key].domicilios[0].tipo_asentamiento_id);
            $("#estado_id_solicitante").val(arraySolicitantes[key].domicilios[0].estado_id);
            $('.catSelect').trigger('change');

    }

    /**
    * Funcion para editar el solicitante
    *@argument key posicion de array a editar
    */
    function cargarEditarSolicitado(key){
        $("#solicitado_key").val(key);
        $("#solicitado_id").val(arraySolicitados[key].id);
        // Si tipo persona es fisica o moral llena diferentes campos
        if(arraySolicitados[key].tipo_persona_id == 1){
            $("#idNombreSolicitado").val(arraySolicitados[key].nombre);
            $("#idPrimerASolicitado").val(arraySolicitados[key].primer_apellido);
            $("#idSegundoASolicitado").val(arraySolicitados[key].segundo_apellido);
            $("#idFechaNacimientoSolicitado").val(dateFormat(arraySolicitados[key].fecha_nacimiento,0));
            $("#idSolicitadoCURP").val(arraySolicitados[key].curp);
            $("#idEdadSolicitado").val(arraySolicitados[key].edad);
            $("#genero_id_solicitado").val(arraySolicitados[key].genero_id);
            $("#nacionalidad_id_solicitado").val(arraySolicitados[key].nacionalidad_id);
            $("#entidad_nacimiento_id_solicitado").val(arraySolicitados[key].entidad_nacimiento_id);
            $("#tipo_persona_fisica_solicitado").prop("checked", true);
            $(".personaMoralSolicitado").hide();
            $(".personaFisicaSolicitado").show();
        }else{
            $(".personaMoralSolicitado").show();
            $(".personaFisicaSolicitado").hide();
            $("#idNombreCSolicitado").val(arraySolicitados[key].nombre_comercial);
            $("#tipo_persona_moral_solicitado").prop("checked", true);
        }
        $("#giro_comercial_solicitado").val(arraySolicitados[key].giro_comercial_id);
        $("#idSolicitadoRfc").val(arraySolicitados[key].rfc);
        arrayDomiciliosSolicitado = arraySolicitados[key].domicilios;
        formarTablaDomiciliosSolicitado();
        $('.catSelect').trigger('change');
    }
    
    /**
    * Funcion para editar el domicilio del solicitante
    *@argument key posicion de array a editar
    */
    function cargarEditarDomicilioSolicitado(key){
        $("#domicilio_key").val(key);
        $("#domicilio_id_modal").val(arrayDomiciliosSolicitado[key].id);
        $("#num_ext").val(arrayDomiciliosSolicitado[key].num_ext);
        $("#num_int").val(arrayDomiciliosSolicitado[key].num_int);
        $("#asentamiento").val(arrayDomiciliosSolicitado[key].asentamiento);
        $("#municipio").val(arrayDomiciliosSolicitado[key].municipio);
        $("#cp").val(arrayDomiciliosSolicitado[key].cp);
        $("#entre_calle1").val(arrayDomiciliosSolicitado[key].entre_calle1);
        $("#entre_calle2").val(arrayDomiciliosSolicitado[key].entre_calle2);
        $("#referencias").val(arrayDomiciliosSolicitado[key].referencias);
        $("#tipo_vialidad_id").val(arrayDomiciliosSolicitado[key].tipo_vialidad_id);
        $("#tipo_asentamiento_id").val(arrayDomiciliosSolicitado[key].tipo_asentamiento_id);
        $("#estado_id").val(arrayDomiciliosSolicitado[key].estado_id);
        $('#modal-domicilio').modal('show');
        $('.catSelect').trigger('change');
    }

    
    /**
    * Funcion para generar tabla a partir de array domicilios solicitados
    */
    function formarTablaDomiciliosSolicitado(){
        var html = "";
        
        $("#tbodyDomicilioSolicitado").html("");
        
        $.each(arrayDomiciliosSolicitado, function (key, value) {
            html += "<tr>";
            html += "<td>" + value.asentamiento + " " + value.cp + "</td>";
            html += "<td><a class='btn btn-xs btn-info' onclick='cargarEditarDomicilioSolicitado("+key+")' ><i class='fa fa-pencil-alt'></i> </a> <a class='btn btn-xs btn-warning' onclick='eliminarDomicilio("+key+")' ><i class='fa fa-trash'></i></a></td>";
            html += "</tr>";
        });
        $("#tbodyDomicilioSolicitado").html(html);
    }

    /**
    * Funcion para agregar Domicilio de solicitante y solicitado 
    */
    function agregarDomicilio(){
        var domicilio = {};
        key = $("#domicilio_key").val();
        domicilio.id = $("#domicilio_id_modal").val();
        // tipoParteDomicilio 0 es solicitante 1 es solicitud 
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
        if(key == ""){
            arrayDomiciliosSolicitado.push(domicilio);
        }else{
            
            arrayDomiciliosSolicitado[key] = domicilio;
        }
        
        formarTablaDomiciliosSolicitado();
        $('#modal-domicilio').modal('hide');
        limpiarDomicilios();
    }
    /**
    * Funcion para agregar Domicilio de solicitante y solicitado 
    */
    function agregarObjetoSol(){
        
        if($("#objeto_solicitud_id").val() != ""){
            var objeto_solicitud = {};
            objeto_solicitud.id = "";
            objeto_solicitud.objeto_solicitud_id = $("#objeto_solicitud_id").val();
            objeto_solicitud.activo = 1;
            arrayObjetoSolicitudes.push(objeto_solicitud);
            $("#objeto_solicitud_id :selected").prop("disabled",true);
            formarTablaObjetoSol();
            $("#objeto_solicitud_id").val("").trigger('change');
        }
        
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
        if($('#step-3').parsley().validate() && arraySolicitados.length > 0 && arraySolicitantes.length > 0){
            var upd = "";
            if($("#solicitud_id").val() == ""){
                method = "POST";
            }else{
                method = "PUT";
                upd = "/"+$("#solicitud_id").val();
            }
            $.ajax({
                url:'/api/solicitudes'+upd,
                type:method,
                dataType:"json",
                async:false,
                data:{
                    solicitados:arraySolicitados,
                    solicitantes:arraySolicitantes,
                    solicitud:solicitud,
                    objeto_solicitudes:arrayObjetoSolicitudes,
                    excepcion:excepcion,
                    _token:$("input[name=_token]").val()

                },
                success:function(data){
                    if(data.success){
                        swal({
                            title: 'Correcto',
                            text: 'Solicitud guardada correctamente',
                            icon: 'success',
                            
                        });
                        setTimeout('', 5000);
                        location.href='{{ route('solicitudes.index')  }}'
                    }
                }
            });
        }
    }

    //funcion para obtener informacion de la solicitud
    function getSolicitud(){
        var solicitud = {};
        solicitud.id = $("#solicitud_id").val();
        solicitud.observaciones = $("#observaciones").val();
        solicitud.estatus_solicitud_id = $("#estatus_solicitud_id").val();
        solicitud.centro_id = $("#centro_id").val();
        solicitud.ratificada = $("#ratificada").is(":checked");
        solicitud.solicita_excepcion = $("#solicita_excepcion").is(":checked");
        solicitud.fecha_ratificacion = dateFormat($("#fechaRatificacion").val(),3);
        solicitud.fecha_recepcion = dateFormat($("#fechaRecepcion").val(),3);
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
    $(".dateTime").datetimepicker({useCurrent: false,format:'DD/MM/YYYY HH:mm:ss'});
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

    $(".numero").limitKeyPress('1234567890.');

    function dateFormat(fecha,tipo = 1){
        if(fecha != ""){
            if(tipo == 1){
                var vecFecha = fecha.split("/");
                var formatedDate = vecFecha[2] + "-" + vecFecha[1] + "-" + vecFecha[0];
                return formatedDate;
            }else if(tipo == 2){
                var vecFechaHora = fecha.split(" ");
                var vecFecha = vecFechaHora[0].split("-");
                var formatedDate = vecFecha[2] + "/" + vecFecha[1] + "/" + vecFecha[0] + " " + vecFechaHora[1];
                return formatedDate;
            }else if(tipo == 3){
                var vecFechaHora = fecha.split(" ");
                var vecFecha = vecFechaHora[0].split("/");
                var formatedDate = vecFecha[2] + "-" + vecFecha[1] + "-" + vecFecha[0] + " " + vecFechaHora[1];
                return formatedDate;
            }else{
                var vecFecha = fecha.split("-");
                var formatedDate = vecFecha[2] + "/" + vecFecha[1] + "/" + vecFecha[0];
                return formatedDate;
            }
            
        }
    }
    var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        sublocality_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
    };

    var campos = {
        street_number: 'num_ext',
        route: 'calle',
        locality: 'municipio_solicitante',
        sublocality_level_1: 'vialidad_solicitante',
        administrative_area_level_1: 'estado',
        country: 'pais',
        postal_code: 'cp_solicitante'
    }
    var map;
    var marker;
    // var map2;
    function initMap() {
      var lat = $('#latitud_solicitante').val() ? $('#latitud_solicitante').val() : "19.398606";
      var lon = $('#longitud_solicitante').val() ? $('#longitud_solicitante').val() : "-99.158581";
      
      map = new google.maps.Map(document.getElementById('widget-maps'), {
          zoom: 15,
          center: {lat: parseFloat(lat), lng: parseFloat(lon)},
          zoomControl: true,
          mapTypeId: google.maps.MapTypeId.ROADMAP
      });
      map.panorama = map.getStreetView();
      console.log("Al iniciar el mapa latlon %s %s confirmada: %s", lat,lon);
      map.panorama.addListener('visible_changed', function() {
        if(!this.visible){
            console.log("entro");
            seteaMarker(map, this.position);
        }
       
    //   map2 = new google.maps.Map(document.getElementById('widget-maps2'), {
    //       zoom: 15,
    //       center: {lat: parseFloat(lat), lng: parseFloat(lon)},
    //       zoomControl: true,
    //       mapTypeId: google.maps.MapTypeId.ROADMAP
    //   });
      if($("#direccion_marker").val() == ""){
          seteaMarker(map, {lat: parseFloat(lat), lng: parseFloat(lon)});
        //   seteaMarker(map2, {lat: parseFloat(lat), lng: parseFloat(lon)});
      }
      else{
          geocodeAddress(map);
      }
    });

        //Autocomplete section
        autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('autocomplete'), 
            {types: ['geocode']}
        );
        autocomplete.setFields(['address_component']);
        autocomplete.addListener('place_changed', fillInAddress);
    }

    function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                var circle = new google.maps.Circle(
                    {center: geolocation, radius: position.coords.accuracy});
                autocomplete.setBounds(circle.getBounds());
            });
        }
    }

    function fillInAddress() {
        var place = autocomplete.getPlace();
        console.log(place);
        for (var component in componentForm) {
            console.log(campos[component]);
            document.getElementById(campos[component]).value = '';
            document.getElementById(campos[component]).disabled = false;
        }

        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];

                document.getElementById(campos[addressType]).value = val;
            }
        }
    }

    function geocodeAddress(resultsMap) {
        var geocoder = new google.maps.Geocoder();
        var address = $("#direccion_marker").val();
        console.log(address);
        geocoder.geocode({'address': address}, function(results, status) {
            if (status === 'OK') {
                resultsMap.setCenter(results[0].geometry.location);
                seteaMarker(resultsMap, results[0].geometry.location);
                $('#btn-confirmar-direccion').removeClass('disabled');
            } else {
                console.log('No se pudo completar el geocoding: %s', status);
            }
        });
    }
    function seteaNuevaPosicionManual(ev){
        console.log("Punto donde se suelta el cursor: Latitud: %s Longitud: %s", ev.latLng.lat(), ev.latLng.lng());
        $('#latitud_solicitante').val(ev.latLng.lat());
        $('#longitud_solicitante').val(ev.latLng.lng());
        $('#btn-confirmar-direccion').removeClass('disabled');
    }

    var seteaMarker = function (resultsMap, coords) {
        if(marker) borraMarker();
         marker = new google.maps.Marker({
             map: resultsMap,
             draggable: true,
             animation: google.maps.Animation.DROP,
             position: coords
         });
        //  marker = new google.maps.Marker({
        //      map2: resultsMap,
        //      draggable: true,
        //      animation: google.maps.Animation.DROP,
        //      position: coords
        //  });
         $('#latitud_solicitante').val(coords.lat);
        $('#longitud_solicitante').val(coords.lng);
        $('#btn-confirmar-direccion').removeClass('disabled');
        marker.addListener('dragend', seteaNuevaPosicionManual);
     };
     function borraMarker(){
            marker.setMap(null);
        }
     $(".direccionUpd").blur(function(){
         if($("#tipo_vialidad_id_solicitante").val() != "" && $("#vialidad_solicitante").val() != "" && $("#num_ext_solicitante").val() != "" && $("#asentamiento_solicitante").val() && $("#municipio_solicitante").val() != "" && $("#estado_id_solicitante").val() != "" ){
             var direccion = $("#tipo_vialidad_id_solicitante :selected").text() + "," + $("#vialidad_solicitante").val() + "," + $("#num_ext_solicitante").val() + "," + $("#asentamiento_solicitante").val() + "," + $("#municipio_solicitante").val() + "." + $("#estado_id_solicitante :selected").text();
            $("#direccion_marker").val(direccion);
            geocodeAddress(map);
         }
     });
    

</script>

<script src="https://maps.googleapis.com/maps/api/js?callback=initMap&libraries=places&key=AIzaSyBx0RdMGMOYgE_eLXfCblBP9RhYDQXjrqY"></script>
<script src="/assets/plugins/parsleyjs/dist/parsley.min.js"></script>
<script src="/assets/plugins/highlight.js/highlight.min.js"></script>
<script src="/assets/plugins/highlight.js/es.js"></script>
@endpush

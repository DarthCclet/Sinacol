
<style>
    .inputError {
        border: 1px red solid;
    }
    .needed:after {
      color:darkred;
      content: " (*)";
   }
    .upper{
        text-transform: uppercase;
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
        <li >
            <a href="#step-3">
                <span class="number">3</span>
                <span class="info">
                    Solicitud
                    <small>Información general de la solicitud</small>
                </span>
            </a>
        </li>
        <li class="step-4">
            <a href="#step-4">
                <span class="number">4</span>
                <span class="info">
                    Documentos
                    <small>Documentos del expediente solicitud</small>
                </span>
            </a>
        </li>
        
        <!-- El paso 5 Es para asignar Audiencias -->
        <li class="step-5">
            <a href="#step-5">
                <span class="number">5</span>
                <span class="info">
                    Audiencias
                    <small>Audiencias de conciliación</small>
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
                <div class="row" id="form">
                    <div class="col-xl-10 offset-xl-1">
                        <div>
                            <center>  <h1>Solicitante</h1></center>
                        </div>
                        <div style="margin-left:5%; margin-bottom:3%; ">
                            <input type="hidden" id="solicitante_id">
                            <input type="hidden" id="edit_key">
                            <label>Tipo Persona</label>
                            <div class="row">
                                <div class="radio radio-css radio-inline">
                                    <input checked="checked" name="tipo_persona_solicitante" type="radio" id="tipo_persona_fisica_solicitante" value="1"/>
                                    <label for="tipo_persona_fisica_solicitante">Fisica</label>
                                </div>
                                <div class="radio radio-css radio-inline">
                                    <input name="tipo_persona_solicitante" type="radio" id="tipo_persona_moral_solicitante" value="2"/>
                                    <label for="tipo_persona_moral_solicitante">Moral</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 personaFisicaSolicitante">
                            <input class="form-control upper" id="idSolicitanteCURP" placeholder="CURP del solicitante" maxlength="18" onblur="validaCURP(this.value);" autofocus="" type="text" value="">
                            <p class="help-block">CURP del solicitante</p>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4" style="display:none;">
                                <input class="form-control" id="idsolicitante" type="text" value="253">
                            </div>
                        <div class="col-md-12 row">
                            <input class="form-control" id="idsolicitante" type="hidden" value="253">
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
                            <div class="col-md-12 personaMoralSolicitante">
                                <input class="form-control" id="idNombreCSolicitante" placeholder="Nombre comercial" type="text" value="">
                                <p class="help-block">Nombre comercial</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control date" required id="idFechaNacimientoSolicitante" placeholder="Fecha de nacimeinto del solicitante" type="text" value="">
                                <p class="help-block needed">Fecha de nacimiento</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                <input class="form-control numero" disabled required data-parsley-type='integer' id="idEdadSolicitante" placeholder="Edad del solicitante" type="text" value="">
                                <p class="help-block needed">Edad del solicitante</p>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control upper" required id="idSolicitanteRfc" placeholder="Rfc del solicitante" type="text" value="">
                                <p class="help-block needed">Rfc del solicitante</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                {!! Form::select('genero_id_solicitante', isset($generos) ? $generos : [] , null, ['id'=>'genero_id_solicitante','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('genero_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Genero</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                {!! Form::select('nacionalidad_id_solicitante', isset($nacionalidades) ? $nacionalidades : [] , null, ['id'=>'nacionalidad_id_solicitante','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('nacionalidad_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Nacionalidad</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitante">
                                {!! Form::select('entidad_nacimiento_id_solicitante', isset($estados) ? $estados : [] , null, ['id'=>'entidad_nacimiento_id_solicitante','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('entidad_nacimiento_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Estado de nacimiento</p>
                            </div>
                            
                            
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4">
                                <div >
                                    <span class="text-muted m-l-5 m-r-20" for='switch1'>Solicita traductor</span>
                                </div>
                                <div >
                                    <input type="hidden" />
                                    <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="solicita_traductor_solicitante" name='solicita_traductor_solicitante'/>
                                </div>
                            </div>
                            
                            <div class="col-md-4" id="selectIndigenaSolicitante" style="display:none;">
                                {!! Form::select('lengua_indigena_id_solicitante', isset($lengua_indigena) ? $lengua_indigena : [] , null, ['id'=>'lengua_indigena_id_solicitante','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('lengua_indigena_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Lengua Indigena</p>
                            </div>
                        </div>
                        {{-- Seccion de contactos solicitados --}}
                        <div class="col-md-12 row">
                            <div class="col-md-12" align="center">
                                <h4>Contacto</h4>
                            </div>
                            <input type="hidden" id="contacto_id_solicitante">
                            <div class="col-md-4">
                                {!! Form::select('tipo_contacto_id_solicitante', isset($tipo_contacto) ? $tipo_contacto : [] , null, ['id'=>'tipo_contacto_id_solicitante','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('tipo_contacto_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Tipo de contacto</p>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" id="contacto_solicitante" placeholder="Contacto"  type="text" value="">
                                <p class="help-block needed">Contacto</p>
                            </div>
                            <div class="col-md-4">
                            <button class="btn btn-info" type="button" onclick="agregarContactoSolicitante();" > <i class="fa fa-plus-circle"></i> Agregar Contacto</button>
                            </div>
                            <div class="col-md-10 offset-md-1" >
                                <table class="table table-bordered" >
                                    <thead>
                                        <tr>
                                            <th style="width:80%;">Tipo</th>
                                            <th style="width:80%;">Contacto</th>
                                            <th style="width:20%; text-align: center;">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyContactoSolicitante">
                                    </tbody>
                                </table>  
                            </div>
                        </div>
                        {{-- end seccion de contactos solicitados --}}
                        <!-- seccion de domicilios solicitante -->
                        @include('includes.component.map',['identificador' => 'solicitante', 'instancia' => '1'])

                        <!-- end seccion de domicilios solicitante -->
                        <!-- Seccion de Datos laborales -->
                        <div class="col-md-12 row">
                            <div class="col-md-12" align="center">
                                <h4>Datos Laborales</h4>
                            </div>
                            <input type="hidden" id="dato_laboral_id">
                            <div class="col-md-12">
                                <input class="form-control" required id="nombre_jefe_directo" placeholder="Nombre del jefe directo" type="text" value="">
                                <p class="help-block needed">Nombre del Jefe directo</p>
                            </div>
                            <div class="col-md-12 row">
                                <input type="hidden" id="giro_comercial_solicitante">      
                                <div class="col-md-8" id="divNivel1solicitante" >
                                    <select id="girosNivel1solicitante" nextLevel="2" tipo="solicitante" class="form-control giroNivel">
                                    </select>
                                </div>
                                <div class="col-md-8" id="divNivel2solicitante" style="display:none">
                                    <select id="girosNivel2solicitante" nextLevel="3" tipo="solicitante" class="form-control giroNivel">
                                    </select>
                                </div>
                                <div class="col-md-8" id="divNivel3solicitante" style="display:none">
                                    <select id="girosNivel3solicitante" nextLevel="" tipo="solicitante"  class="form-control giroNivel">
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <p class="help-block needed">Giro comercial</p>
                                    <label id="giro_solicitante"></label>
                                </div>
                            </div>
                            <div class="col-md-12 row">
                                <div class="col-md-4">
                                    {!! Form::select('ocupacion_id', isset($ocupaciones) ? $ocupaciones : [] , null, ['id'=>'ocupacion_id','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
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
                                    <input class="form-control numero" data-parsley-type='integer' required id="no_afore" placeholder="No afore" type="text" value="">
                                    <p class="help-block needed">No. afore</p>
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
                                    <p class="help-block needed">Horas semanales</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! Form::select('jornada_id', isset($jornadas) ? $jornadas : [] , null, ['id'=>'jornada_id','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('jornada_id', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Jornada</p>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted m-l-5 m-r-20" for='switch1'>Labora actualmente</span>
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" />
                                <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="labora_actualmente" name='labora_actualmente'/>
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
                            <center><h1>Solicitado</h1></center>
                        </div>
                        <div style="margin-left:5%; margin-bottom:3%; ">
                            <label>Tipo Persona</label>
                            <input type="hidden" id="solicitado_id">
                            <input type="hidden" id="solicitado_key">
                            <div class="row">
                                <div class="radio radio-css radio-inline">
                                    <input checked="checked" name="tipo_persona_solicitado" type="radio" id="tipo_persona_fisica_solicitado" value="1"/>
                                    <label for="tipo_persona_fisica_solicitado">Fisica</label>
                                </div>
                                <div class="radio radio-css radio-inline">
                                    <input name="tipo_persona_solicitado" type="radio" id="tipo_persona_moral_solicitado" value="2"/>
                                    <label for="tipo_persona_moral_solicitado">Moral</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 personaFisicaSolicitado">
                            <input class="form-control upper" required id="idSolicitadoCURP" maxlength="18" onblur="validaCURP(this.value);" placeholder="CURP del solicitado" type="text" value="">
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
                            <div class="col-md-8 personaMoralSolicitado">
                                <input class="form-control" id="idNombreCSolicitado" required placeholder="Nombre comercial del solicitado" type="text" value="">
                                <p class="help-block needed">Nombre comercia</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control date" id="idFechaNacimientoSolicitado" placeholder="Fecha de nacimeinto del solicitado" type="text" value="">
                                <p class="help-block needed">Fecha de nacimiento</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                <input class="form-control numero" required disabled id="idEdadSolicitado" placeholder="Edad del solicitado" type="text" value="">
                                <p class="help-block needed">Edad del solicitado</p>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control upper" required id="idSolicitadoRfc" placeholder="Rfc del solicitado" type="text" value="">
                                <p class="help-block needed">Rfc del solicitado</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                {!! Form::select('genero_id_solicitado', isset($generos) ? $generos : [] , null, ['id'=>'genero_id_solicitado','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('genero_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Genero</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                {!! Form::select('nacionalidad_id_solicitado', isset($nacionalidades) ? $nacionalidades : [] , null, ['id'=>'nacionalidad_id_solicitado','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('nacionalidad_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Nacionalidad</p>
                            </div>
                            <div class="col-md-4 personaFisicaSolicitado">
                                {!! Form::select('entidad_nacimiento_id_solicitado', isset($estados) ? $estados : [] , null, ['id'=>'entidad_nacimiento_id_solicitado','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('entidad_nacimiento_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Estado de nacimiento</p>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-4">
                                <div  >
                                    <span class="text-muted m-l-5 m-r-20" for='switch1'>Solicita traductor</span>
                                </div>
                                <div >
                                    <input type="hidden" />
                                    <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="solicita_traductor_solicitado" name='solicita_traductor_solicitado'/>
                                </div>
                            </div>
                            <div class="col-md-4" id="selectIndigenaSolicitado" style="display:none">
                                {!! Form::select('lengua_indigena_id_solicitado', isset($lengua_indigena) ? $lengua_indigena : [] , null, ['id'=>'lengua_indigena_id_solicitado','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('lengua_indigena_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Lengua Indigena</p>
                            </div>
                        </div>
                        {{-- Seccion de contactos solicitados --}}
                        <div class="col-md-12 row">
                            <div class="col-md-12" align="center">
                                <h4>Contacto</h4>
                            </div>
                            <input type="hidden" id="contacto_id_solicitado">
                            <div class="col-md-4">
                                {!! Form::select('tipo_contacto_id_solicitado', isset($tipo_contacto) ? $tipo_contacto : [] , null, ['id'=>'tipo_contacto_id_solicitado','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('tipo_contacto_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Tipo de contacto</p>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" id="contacto_solicitado" placeholder="Contacto"  type="text" value="">
                                <p class="help-block needed">Contacto</p>
                            </div>
                            <div class="col-md-4">
                            <button class="btn btn-info" type="button" onclick="agregarContactoSolicitado();" > <i class="fa fa-plus-circle"></i> Agregar Contacto</button>
                            </div>
                            <div class="col-md-10 offset-md-1" >
                                <table class="table table-bordered" >
                                    <thead>
                                        <tr>
                                            <th style="width:80%;">Tipo</th>
                                            <th style="width:80%;">Contacto</th>
                                            <th style="width:20%; text-align: center;">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyContactoSolicitado">
                                    </tbody>
                                </table>  
                            </div>
                        </div>
                        {{-- end seccion de contactos solicitados --}}
                        <!-- seccion de domicilios solicitado -->
                        <div class="col-md-12 row">
                            <div class="row">
                                <h4>Domicilio(s)</h4>
                                <a style="font-size:large; margin-left:1%; color:#49b6d6;" onclick="$('#modal-domicilio').modal('show');"> <i class="fa fa-plus-circle"></i></a>
                            </div>
                            <div class="col-md-10 offset-md-1" >
                                <table class="table table-bordered" >
                                    <thead>
                                        <tr>
                                            <th style="width:80%;">Domicilio</th>
                                            <th style="width:20%; text-align: center;">Accion</th>
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
                                        <th style="width:15%; text-align: center;">Accion</th>
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
                    <input type="hidden" id="solicitud_id">
                    <div class="col-md-4">
                        <input class="form-control dateTime" id="fechaRatificacion" disabled placeholder="Fecha de ratificacion" type="text" value="">
                        <p class="help-block">Fecha de Ratificación</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control dateTime" required id="fechaRecepcion" placeholder="Fecha de Recepcion" type="text" value="">                
                        <p class="help-block needed">Fecha de Recepción</p>    
                    </div>
                    <div class="col-md-4">
                        <input class="form-control date" required id="fechaConflicto" placeholder="Fecha de Conflicto" type="text" value="">
                        <p class="help-block needed">Fecha de Conflicto</p>
                    </div>
                    <div class="col-md-4 estatusSolicitud">
                        {!! Form::select('estatus_solicitud_id', isset($estatus_solicitudes) ? $estatus_solicitudes : [] , isset($solicitud->estatus_solicitud_id) ?  $solicitud->estatus_solicitud_id : null, ['id'=>'estatus_solicitud_id','disabled','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('estatus_solicitud_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block needed">Estatus de la solicitud</p>
                    </div>
                    <div class="col-md-4">
                        {!! Form::select('objeto_solicitud_id', isset($objeto_solicitudes) ? $objeto_solicitudes : [] , null, ['id'=>'objeto_solicitud_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('objeto_solicitud_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block needed">Objeto de la solicitud</p>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-sm m-l-5" onclick="agregarObjetoSol()"><i class="fa fa-save"></i> Agregar objeto</button>
                    </div>
                    <div class="col-md-10 offset-md-1" style="margin-top: 3%;" >
                        <table class="table table-bordered" >
                            <thead>
                                <tr>
                                    <th>Objeto</th>
                                    <th style="width:15%; text-align: center;">Accion</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyObjetoSol">
                            </tbody>
                        </table>  
                    </div>
                    <br>
                    <br>
                    <div class="col-md-12 form-group">
                        <textarea rows="4" class="form-control" id="observaciones" data-parsley-maxlength='250'></textarea>
                        <p class="help-block">Observaciones de la solicitud</p>
                    </div>
                </div>
            </div>
            
            @if(isset($solicitud->estatus_solicitud_id) && $solicitud->estatus_solicitud_id == 1)
                <div class="form-group">
                    <button class="btn btn-info btn-sm m-l-5" id="btnRatificarSolicitud"><i class="fa fa-check"></i> Ratificar Solicitud</button>
                </div>
            @endif
        </div>
        </div>
        <!-- end step-3 -->
        <!-- begin step-4 -->
        <div id="step-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-right">
                        <button class="btn btn-primary btn-sm m-l-5" id='btnAgregarArchivo'><i class="fa fa-plus"></i> Agregar documento</button>
                    </div>
                </div>
                <div class="col-md-12">
                    <div id="gallery" class="gallery row"></div>
                    <!--<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">-->
                </div>

                <!-- The template to display files available for upload -->
                <script id="template-upload" type="text/x-tmpl">
                    {% for (var i=0, file; file=o.files[i]; i++) { %}
                        <tr class="template-upload fade show">
                            <td>
                                <span class="preview"></span>
                            </td>
                            <td>
                                <div class="bg-light rounded p-10 mb-2">
                                    <dl class="m-b-0">
                                        <dt class="text-inverse">File Name:</dt>
                                        <dd class="name">{%=file.name%}</dd>
                                        <dt class="text-inverse m-t-10">File Size:</dt>
                                        <dd class="size">Processing...</dd>
                                    </dl>
                                </div>
                                <strong class="error text-danger h-auto d-block text-left"></strong>
                            </td>
                            <td>
                                <select class="form-control tipo_documento" name="tipo_documento_id[]">
                                    <option value="1">Audiencia 1</option>
                                    <option value="2">Audiencia 2</option>
                                </select>
                            </td>
                            <td>
                                <dl>
                                    <dt class="text-inverse m-t-3">Progress:</dt>
                                    <dd class="m-t-5">
                                        <div class="progress progress-sm progress-striped active rounded-corner"><div class="progress-bar progress-bar-primary" style="width:0%; min-width: 40px;">0%</div></div>
                                    </dd>
                                </dl>
                            </td>
                            <td nowrap>
                                {% if (!i && !o.options.autoUpload) { %}
                                    <button class="btn btn-primary start width-100 p-r-20 m-r-3" disabled>
                                        <i class="fa fa-upload fa-fw text-inverse"></i>
                                        <span>Start</span>
                                    </button>
                                {% } %}
                                {% if (!i) { %}
                                    <button class="btn btn-default cancel width-100 p-r-20">
                                        <i class="fa fa-trash fa-fw text-muted"></i>
                                        <span>Cancel</span>
                                    </button>
                                {% } %}
                            </td>
                        </tr>
                    {% } %}
                </script>
                <!-- The template to display files available for download -->
                <script id="template-download" type="text/x-tmpl">
                    {% for (var i=0, file; file=o.files[i]; i++) { %}
                        <tr class="template-download fade show">
                            <td width="1%">
                                <span class="preview">
                                    {% if (file.thumbnailUrl) { %}
                                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}" class="rounded"></a>
                                    {% } else { %}
                                        <div class="bg-light text-center f-s-20" style="width: 80px; height: 80px; line-height: 80px; border-radius: 6px;">
                                            <i class="fa fa-file-image fa-lg text-muted"></i>
                                        </div>
                                    {% } %}
                                </span>
                            </td>
                            <td>
                                <div class="bg-light p-10 mb-2">
                                    <dl class="m-b-0">
                                        <dt class="text-inverse">File Name:</dt>
                                        <dd class="name">
                                            {% if (file.url) { %}
                                                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                                            {% } else { %}
                                                <span>{%=file.name%}</span>
                                            {% } %}
                                        </dd>
                                        <dt class="text-inverse m-t-10">File Size:</dt>
                                        <dd class="size">{%=o.formatFileSize(file.size)%}</dd>
                                    </dl>
                                    {% if (file.error) { %}
                                        <div><span class="label label-danger">ERROR</span> {%=file.error%}</div>
                                    {% } %}
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                            <td>
                                {% if (file.deleteUrl) { %}
                                    <button class="btn btn-danger delete width-100 m-r-3 p-r-20" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                                        <i class="fa fa-trash pull-left fa-fw text-inverse m-t-2"></i>
                                        <span>Delete</span>
                                    </button>
                                    <input type="checkbox" name="delete" value="1" class="toggle">
                                {% } else { %}
                                    <button class="btn btn-default cancel width-100 m-r-3 p-r-20">
                                        <i class="fa fa-trash pull-left fa-fw text-muted m-t-2"></i>
                                        <span>Cancel</span>
                                    </button>
                                {% } %}
                            </td>
                        </tr>
                    {% } %}
                </script>
            </div>
        </div>
        <!-- end step-4 -->
        <!-- begin step-5 -->
        <div id="step-5">
            @if (isset($audiencias))
            <div class="row">
                <div class="col-md-12">
                    @if(Count($audiencias) > 0)
                        @include('expediente.audiencias._list',$audiencias)
                    @else
                        @include('expediente.audiencias.calendarioWizard')
                    @endif
                </div>
            </div>
            @else
            <div> <h1> Audiencia disponible despues de Ratificaci&oacute;n </h1> </div>
            @endif

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
                <input type="hidden" id="domicilio_edit">
                
                @include('includes.component.map',['identificador' => 'solicitado', 'instancia' => 2])
                
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal" onclick="domicilioObj2.limpiarDomicilios()"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" onclick="agregarDomicilio()"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div> 
<!-- Fin Modal de Domicilio-->
<!-- inicio Modal cargar archivos-->
<div class="modal" id="modal-archivos" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Archivos de Audiencia</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form id="fileupload" action="/api/documentos/solicitud" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="solicitud_id[]" id='solicitud_id_modal'/>
                    <div class="row fileupload-buttonbar">
                        <div class="col-xl-12">
                                <span class="btn btn-primary fileinput-button m-r-3">
                                        <i class="fa fa-fw fa-plus"></i>
                                        <span>Agregar...</span>
                                        <input type="file" name="files[]" multiple>
                                </span>
                                <button type="submit" class="btn btn-primary start m-r-3">
                                        <i class="fa fa-fw fa-upload"></i>
                                        <span>Cargar</span>
                                </button>
                                <button type="reset" class="btn btn-default cancel m-r-3" id="btnCancelFiles">
                                        <i class="fa fa-fw fa-ban"></i>
                                        <span>Cancelar</span>
                                </button>
                                <!-- The global file processing state -->
                                <span class="fileupload-process"></span>
                        </div>
                        <!-- The global progress state -->
                        <div class="col-xl-5 fileupload-progress fade d-none d-xl-block">
                                <!-- The global progress bar -->
                                <div class="progress progress-striped active m-b-0">
                                        <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                </div>
                                <!-- The extended global progress state -->
                                <div class="progress-extended">&nbsp;</div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th width="10%">VISTA PREVIA</th>
                                    <th>INFORMACION</th>
                                    <th>TIPO DE DOCUMENTO</th>
                                    <th>PROGRESO</th>
                                    <th width="1%"></th>
                                </tr>
                            </thead>
                            <tbody class="files">
                                <tr data-id="empty">
                                    <td colspan="4" class="text-center text-muted p-t-30 p-b-30">
                                        <div class="m-b-10"><i class="fa fa-file fa-3x"></i></div>
                                        <div>Sin documentos</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-sign-out"></i> Cerrar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de cargar archivos-->
<div class="modal" id="modal-visor" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div id="bodyArchivo">
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="expediente_id">
@push('scripts')

<script>
    // Se declaran las variables globales
    var arraySolicitados = new Array(); //Lista de solicitados
    var arraySolicitantes = new Array(); //Lista de solicitantes
    var arrayDomiciliosSolicitante = new Array(); // Array de domicilios para el solicitante
    var arrayDomiciliosSolicitado = new Array(); // Array de domicilios para el solicitado
    var arrayObjetoSolicitudes = new Array(); // Array de objeto_solicitude para el solicitado
    var arrayContactoSolicitantes = new Array(); // Array de objeto_solicitude para el solicitado
    var arrayContactoSolicitados = new Array(); // Array de objeto_solicitude para el solicitado

    $(document).ready(function() {
        $('#wizard').smartWizard({
            selected: 0,
            keyNavigation: false,
            theme: 'default',
            transitionEffect: 'fade',
            showStepURLhash: false,
            anchorSettings: {
                anchorClickable: true, // Enable/Disable anchor navigation
                enableAllAnchors: true, // Activates all anchors clickable all times
                markDoneStep: true, // add done css
                enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
            },
            lang: { next: 'Siguiente', previous: 'Anterior' }
        });
        if(edit){
            $(".estatusSolicitud").show();
            var solicitud='{{ $solicitud->id ?? ""}}';
            FormMultipleUpload.init();
            Gallery.init();
            $('#wizard').smartWizard("stepState", [3], "show");
            $(".step-4").show();
        }else{
            $(".step-4").hide();
            $(".step-5").hide();
            $('#wizard').smartWizard("stepState", [3], "hide");
            $('#wizard').smartWizard("stepState", [4], "hide");
            $(".estatusSolicitud").hide();
        }

        $(".personaMoralSolicitado").hide();
        $(".personaMoralSolicitante").hide();
        $(".personaFisicaSolicitante input").attr("required","");
        $(".personaFisicaSolicitado input").attr("required","");
        $(".personaMoralSolicitante input").removeAttr("required");
        $(".personaMoralSolicitado input").removeAttr("required");

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
                    solicitante.lengua_indigena_id = $("#lengua_indigena_id_solicitante").val();    
                }else{
                    solicitante.nombre_comercial = $("#idNombreCSolicitante").val();
                    
                }
                solicitante.solicita_traductor = $("input[name='solicita_traductor_solicitante']:checked").val()
                solicitante.tipo_persona_id = $("input[name='tipo_persona_solicitante']:checked").val()
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
                dato_laboral.giro_comercial_id = $("#giro_comercial_solicitante").val()
                solicitante.dato_laboral = dato_laboral;

                //domicilio del solicitante
                
                var domicilio = {};
                domicilio = domicilioObj.getDomicilio();
                
                
                solicitante.domicilios = [domicilio];
                
                //domicilio

                //contactos del solicitante
                    solicitante.contactos = arrayContactoSolicitantes;
                //contactos
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
                    solicitado.lengua_indigena_id = $("#lengua_indigena_id_solicitado").val();    
                }else{
                    solicitado.nombre_comercial = $("#idNombreCSolicitado").val();
                }
                solicitado.solicita_traductor = $("input[name='solicita_traductor_solicitado']:checked").val();
                solicitado.tipo_persona_id = $("input[name='tipo_persona_solicitado']:checked").val();
                solicitado.tipo_parte_id = 2;
                solicitado.rfc = $("#idSolicitadoRfc").val();
                solicitado.activo = 1;
                solicitado.domicilios = arrayDomiciliosSolicitado;
                //contactos del solicitado
                solicitado.contactos = arrayContactoSolicitados;
                //contactos
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
                $(".personaMoralSolicitante select").removeAttr("required");
                $(".personaMoralSolicitante").hide();
                $(".personaFisicaSolicitante").show();
            }else{
                $(".personaMoralSolicitante input").attr("required","");
                $(".personaMoralSolicitante select").attr("required","");
                $(".personaFisicaSolicitante input").removeAttr("required");
                $(".personaFisicaSolicitante select").removeAttr("required");
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
                $(".personaFisicaSolicitado select").attr("required","");
                $(".personaMoralSolicitado select").removeAttr("required");
                $(".personaMoralSolicitado").hide();
                $(".personaFisicaSolicitado").show();
            }else{
                $(".personaFisicaSolicitado input").removeAttr("required");
                $(".personaMoralSolicitado input").attr("required","");
                $(".personaFisicaSolicitado select").removeAttr("required");
                $(".personaMoralSolicitado select").attr("required","");
                $(".personaMoralSolicitado").show();
                $(".personaFisicaSolicitado").hide();
            }
        });

        
        if(edit){
            $("#solicitud_id").val(solicitud);
            $("#solicitud_id_modal").val(solicitud);
            cargarDocumentos();
            getSolicitudFromBD(solicitud);
        }
        getGironivel("",1,"girosNivel1solicitante");
        
    });
    function getSolicitudFromBD(solicitud){
        $.ajax({
            url:'/api/solicitudes/'+solicitud,
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
                console.log(data.ratificada);
                if(data.ratificada){
                    $("#ratificada").prop("checked",true);
                    $('#wizard').smartWizard("stepState", [4], "show");
                    $(".step-5").show();
                    $("#btnRatificarSolicitud").hide();
                    $("#expediente_id").val(data.expediente.id);
                }else{
                    $('#wizard').smartWizard("stepState", [4], "hide");
                    $(".step-5").hide();
                    $("#btnRatificarSolicitud").show();
                    $("#expediente_id").val("");
                }
                if(data.solicita_excepcion){
                    $("#solicita_excepcion").prop("checked",true);
                }
                if(data.estatus_solicitud_id == 2){
                    $("#btnRatificarSolicitud").hide();
                }

                $("#fechaRatificacion").val(dateFormat(data.fecha_ratificacion,2));
                $("#fechaRecepcion").val(dateFormat(data.fecha_recepcion,2));
                $("#fechaConflicto").val(dateFormat(data.fecha_conflicto,0));
            }
        });
    }
    /**
        *Funcion para limpiar campos de solicitante
        */
        function limpiarSolicitante(){
            $("#edit_key").val("");
            
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
            $("#lengua_indigena_id_solicitante").val("");
            if($("#solicita_traductor_solicitante").is(":checked")){
                $("#solicita_traductor_solicitante").trigger('click');
            }
            $("#agregarSolicitante").html('<i class="fa fa-plus-circle"></i> Agregar solicitante');
            $("#giro_comercial_solicitante").val("");
            getGironivel("",1,"girosNivel1solicitante");
            $("#girosNivel1solicitante").trigger("change");
            $("#giro_solicitante").html("");
            $("input[name='tipo_persona_solicitante']").trigger("change")
            arrayContactoSolicitantes = new Array();
            formarTablaContacto(true);
            $('.catSelect').trigger('change');
            domicilioObj.limpiarDomicilios();
            $('#step-1').parsley().reset();
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
            $("#lengua_indigena_id_solicitado").val("");
            if($("#solicita_traductor_solicitado").is(":checked")){
                $("#solicita_traductor_solicitado").trigger('click');
            }

            $("#agregarSolicitado").html('<i class="fa fa-plus-circle"></i> Agregar solicitado');
            arrayContactoSolicitados = new Array();;
            formarTablaContacto();
            $('.catSelect').trigger('change');  
            domicilioObj2.limpiarDomicilios();
            $('#step-2').parsley().reset();
        }
    
    function agregarContactoSolicitante(){  
        var contacto = {};
        idContacto = $("#contacto_id_solicitante").val();
        contacto.id = idContacto;
        contacto.activo = 1;
        contacto.contacto = $("#contacto_solicitante").val();
        contacto.tipo_contacto_id = $("#tipo_contacto_id_solicitante").val();
        if(idContacto == ""){
            arrayContactoSolicitantes.push(contacto);
        }else{
            
            arrayContactoSolicitantes[idContacto] = contacto;
        }

        formarTablaContacto(true);
        limpiarContactoSolicitante();
    }
    function limpiarContactoSolicitante(){
        $("#tipo_contacto_id_solicitante").val("");
        $("#tipo_contacto_id_solicitante").trigger('change');
        $("#contacto_solicitante").val("");
    }

    function agregarContactoSolicitado(){
        var contacto = {};
        idContacto = $("#contacto_id_solicitado").val();
        contacto.id = idContacto;
        contacto.activo = 1;
        contacto.contacto = $("#contacto_solicitado").val();
        contacto.tipo_contacto_id = $("#tipo_contacto_id_solicitado").val();
        if(idContacto == ""){
            arrayContactoSolicitados.push(contacto);
        }else{
            
            arrayContactoSolicitados[idContacto] = contacto;
        }
        
        formarTablaContacto();
    }

    function limpiarContactoSolicitado(){
        $("#tipo_contacto_id_solicitado").val("");
        $("#tipo_contacto_id_solicitado").trigger('change');
        $("#contacto_solicitado").val("");
    }

    /**
    * Funcion para generar tabla a partir de array de solicitantes
    */
    function formarTablaContacto(solicitante=false){
        var arrayS = [];
        if(solicitante){
            arrayS = arrayContactoSolicitantes;
            $("#tbodyContactoSolicitante").html("");
        }else{
            arrayS = arrayContactoSolicitados;
            $("#tbodyContactoSolicitado").html("");
        }
        var html = "";
        
        $.each(arrayS, function (key, value) {
            if(value.activo == "1" || (value.id != "" && typeof value.activo == "undefined")){
                html += "<tr>";
                $("#tipo_contacto_id_solicitante").val(value.tipo_contacto_id);
                html += "<td> " + $("#tipo_contacto_id_solicitante :selected").text(); + " </td>";
                html += "<td> " + value.contacto + " </td>";
                html += "<td style='text-align: center;'><a class='btn btn-xs btn-warning' onclick='eliminarContactoSol("+key+","+solicitante+")' ><i class='fa fa-trash' style='color:white;'></i></a></td>";
                html += "</tr>";
            }
        });
        $("#tipo_contacto_id_solicitante").val("");
        if(solicitante){
            $("#tbodyContactoSolicitante").html(html);
        }else{
            $("#tbodyContactoSolicitado").html(html);
        }
    }

    function eliminarContactoSol(key, solicitante){
        if(solicitante){
            if(arrayContactoSolicitantes[key].id == ""){
                arrayContactoSolicitantes = arrayContactoSolicitantes.splice(1,key);
            }else{
                arrayContactoSolicitantes[key].activo = 0;
            }
        }else{
            if(arrayContactoSolicitados[key].id == ""){
                arrayContactoSolicitados = arrayContactoSolicitados.splice(1,key);
            }else{
                arrayContactoSolicitados    [key].activo = 0;
            }
        }
        
        formarTablaContacto(solicitante);
    }
    
    /**
    * Funcion para generar tabla a partir de array de solicitantes
    */
    function formarTablaObjetoSol(){
        
        var html = "";
        
        $("#tbodyObjetoSol").html("");
        
        $.each(arrayObjetoSolicitudes, function (key, value) {
            if(value.activo == "1" || (value.id != "" && typeof value.activo == "undefined" )){
                html += "<tr>";
                $("#objeto_solicitud_id").val(value.objeto_solicitud_id);
                html += "<td> " + $("#objeto_solicitud_id :selected").text(); + " </td>";
                html += "<td style='text-align: center;'><a class='btn btn-xs btn-warning' onclick='eliminarObjetoSol("+key+")' ><i class='fa fa-trash' style='color:white;'></i></a></td>";
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
                
                if(value.tipo_persona_id == 1){
                    html += "<td> " + value.curp + " </td>";
                }else{
                    html += "<td></td>";    
                }
                html += "<td> " + value.rfc + " </td>";
                
                html += "<td style='text-align: center;'><a class='btn btn-xs btn-info' onclick='cargarEditarSolicitante("+key+")'><i class='fa fa-pencil-alt' style='color:white;'></i></a> ";
                html += "<a class='btn btn-xs btn-warning' onclick='eliminarSolicitante("+key+")' ><i class='fa fa-trash' style='color:white;'></i></a></td>";
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
                
                if(value.tipo_persona_id == 1){
                    html += "<td> " + value.curp + " </td>";
                }else{
                    html += "<td></td>";    
                }
                html += "<td> " + value.rfc + " </td>";
                
                html += "<td style='text-align: center;'><a class='btn btn-xs btn-info' onclick='cargarEditarSolicitado("+key+")'><i class='fa fa-pencil-alt' style='color:white;'></i></a> ";
                html += "<a class='btn btn-xs btn-warning' onclick='eliminarSolicitado("+key+")' ><i class='fa fa-trash' style='color:white;'></i></a></td>";
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
        formarTablaDomiciliosSolicitado();
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
        formarTablaObjetoSol();
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
        $("#agregarSolicitante").html('<i class="fa fa-edit"></i> Editar solicitante');
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
            $("#lengua_indigena_id_solicitante").val(arraySolicitantes[key].lengua_indigena_id);
            if(arraySolicitantes[key].solicita_traductor == 1){
                if(!$("#solicita_traductor_solicitante").is(":checked")){
                    $("#solicita_traductor_solicitante").trigger('click');
                }
            }else{
                if($("#solicita_traductor_solicitante").is(":checked")){
                    $("#solicita_traductor_solicitante").trigger('click');
                }
            }
            $("#tipo_persona_fisica_solicitante").prop("checked", true);
            $(".personaMoralSolicitante").hide();
            $(".personaFisicaSolicitante").show();
        }else{
            $(".personaMoralSolicitante").show();
            $(".personaFisicaSolicitante").hide();
            $("#tipo_persona_moral_solicitante").prop("checked", true);
            $("#idNombreCSolicitante").val(arraySolicitantes[key].nombre_comercial);
        }
        $("#idSolicitanteRfc").val(arraySolicitantes[key].rfc);
        // datos laborales en la solicitante
        $("#dato_laboral_id").val(arraySolicitantes[key].dato_laboral.id);
        $("#giro_comercial_solicitante").val(arraySolicitantes[key].dato_laboral.giro_comercial_id);
        getGiroEditar("solicitante");
        $("#nombre_jefe_directo").val(arraySolicitantes[key].dato_laboral.nombre_jefe_directo);
        $("#ocupacion_id").val(arraySolicitantes[key].dato_laboral.ocupacion_id);
        $("#nss").val(arraySolicitantes[key].dato_laboral.nss);
        $("#no_issste").val(arraySolicitantes[key].dato_laboral.no_issste);
        $("#no_afore").val(arraySolicitantes[key].dato_laboral.no_afore);
        $("#percepcion_mensual_neta").val(arraySolicitantes[key].dato_laboral.percepcion_mensual_neta);
        $("#percepcion_mensual_bruta").val(arraySolicitantes[key].dato_laboral.percepcion_mensual_bruta);
        if(arraySolicitantes[key].dato_laboral.labora_actualmente != $("#labora_actualmente").is(":checked")){
            $("#labora_actualmente").prop("checked", true);;
        }
        $("input[name='tipo_persona_solicitante']").trigger("change");
        $("#fecha_ingreso").val(dateFormat(arraySolicitantes[key].dato_laboral.fecha_ingreso,0));
        $("#fecha_salida").val(dateFormat(arraySolicitantes[key].dato_laboral.fecha_salida,0));
        $("#jornada_id").val(arraySolicitantes[key].dato_laboral.jornada_id);
        $("#horas_semanales").val(arraySolicitantes[key].dato_laboral.horas_semanales);
        arrayContactoSolicitantes = arraySolicitantes[key].contactos ? arraySolicitantes[key].contactos : new Array();
        formarTablaContacto(true);
        //domicilio del solicitante
        domicilioObj.cargarDomicilio(arraySolicitantes[key].domicilios[0]);
        $('.catSelect').trigger('change');
    }

    /**
    * Funcion para editar el solicitante
    *@argument key posicion de array a editar
    */
    function cargarEditarSolicitado(key){
        $("#agregarSolicitado").html('<i class="fa fa-edit"></i> Editar solicitado');
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
            $("#lengua_indigena_id_solicitado").val(arraySolicitados[key].lengua_indigena_id);
            if(arraySolicitados[key].solicita_traductor == 1){
                if(!$("#solicita_traductor_solicitado").is(":checked")){
                    $("#solicita_traductor_solicitado").trigger('click');
                }
            }else{
                if($("#solicita_traductor_solicitado").is(":checked")){
                    $("#solicita_traductor_solicitado").trigger('click');
                }
            }
            $("#tipo_persona_fisica_solicitado").prop("checked", true);
            $(".personaMoralSolicitado").hide();
            $(".personaFisicaSolicitado").show();
        }else{
            $(".personaMoralSolicitado").show();
            $(".personaFisicaSolicitado").hide();
            $("#idNombreCSolicitado").val(arraySolicitados[key].nombre_comercial);
            $("#tipo_persona_moral_solicitado").prop("checked", true);
        }
        $("#idSolicitadoRfc").val(arraySolicitados[key].rfc);
        $("input[name='tipo_persona_solicitado']").trigger("change");
        arrayContactoSolicitados = arraySolicitados[key].contactos ? arraySolicitados[key].contactos : new Array();
        formarTablaContacto();
        // arrayContactoSolicitados = arraySolicitados[key].contactos;
        arrayDomiciliosSolicitado = arraySolicitados[key].domicilios;
        formarTablaDomiciliosSolicitado();
        $('.catSelect').trigger('change');
    }
    
    /**
    * Funcion para editar el domicilio del solicitante
    *@argument key posicion de array a editar
    */
    function cargarEditarDomicilioSolicitado(key){
        $("#domicilio_edit").val(key)
        domicilioObj2.cargarDomicilio(arrayDomiciliosSolicitado[key]);
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
            if(value.activo == "1" || value.id != "" && typeof value.activo == "undefined"){
                html += "<tr>";
                html += "<td>" + value.asentamiento + " " + value.cp + "</td>";
                html += "<td style='text-align: center;'><a class='btn btn-xs btn-info' onclick='cargarEditarDomicilioSolicitado("+key+")' ><i class='fa fa-pencil-alt' style='color:white;'></i> </a> <a class='btn btn-xs btn-warning' onclick='eliminarDomicilio("+key+")' ><i class='fa fa-trash' style='color:white;'></i></a></td>";
                html += "</tr>";
            }
            
        });
        $("#tbodyDomicilioSolicitado").html(html);
    }

    /**
    * Funcion para agregar Domicilio de solicitante y solicitado 
    */
    function agregarDomicilio(){
        key = $("#domicilio_edit").val();
        if(key == ""){
            arrayDomiciliosSolicitado.push(domicilioObj2.getDomicilio());
        }else{
            arrayDomiciliosSolicitado[key] = domicilioObj2.getDomicilio();
        }

        formarTablaDomiciliosSolicitado();
        $('#modal-domicilio').modal('hide');
        domicilioObj2.limpiarDomicilios();
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
                    }else{
                        
                    }
                    
                },error:function(data){
                    $.each(data.responseJSON.errors, function (key, value) {
                        console.log(value);
                    });
                }
            });
        }
    }

    //funcion para obtener informacion de la solicitud
    function getSolicitud(){
        var solicitud = {};
        solicitud.id = $("#solicitud_id").val();
        solicitud.observaciones = $("#observaciones").val();
        solicitud.ratificada = $("#ratificada").is(":checked");
        solicitud.solicita_excepcion = $("#solicita_excepcion").is(":checked");
        solicitud.fecha_ratificacion = dateFormat($("#fechaRatificacion").val(),3);
        solicitud.fecha_recepcion = dateFormat($("#fechaRecepcion").val(),3);
        solicitud.fecha_conflicto = dateFormat($("#fechaConflicto").val());
        return solicitud;
    }
    
    // Funcion para ratificar solicitudes
    $("#btnRatificarSolicitud").on("click",function(){
        if($('#step-3').parsley().validate() && arraySolicitados.length > 0 && arraySolicitantes.length > 0){
            swal({
                title: '¿Estas seguro?',
                text: 'Al oprimir aceptar se creará un expediente y se podrán agendar audiencias para conciliación',
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: 'Cancelar',
                        value: null,
                        visible: true,
                        className: 'btn btn-default',
                        closeModal: true,
                    },
                    confirm: {
                        text: 'Aceptar',
                        value: true,
                        visible: true,
                        className: 'btn btn-warning',
                        closeModal: true
                    }
                }
            }).then(function(isConfirm){
                if(isConfirm){
                    $.ajax({
                        url:'/api/solicitud/ratificar',
                        type:'POST',
                        dataType:"json",
                        async:true,
                        data:{
                            id:$("#solicitud_id").val()
                        },
                        success:function(data){
                            if(data != null && data != ""){
                                swal({
                                    title: 'Correcto',
                                    text: 'Solicitud ratificada correctamente',
                                    icon: 'success'
                                });
                                location.reload(); 
                                //getSolicitudFromBD($("#solicitud_id").val());
                            }else{
                                swal({
                                    title: 'Error',
                                    text: 'No se pudo ratificar',
                                    icon: 'error'
                                });
                            }
                        }
                    });
                }
            });
        }else{
            swal({
                title: 'Error',
                text: 'Llena todos los campos',
                icon: 'warning'
            });
        }
    });

    //funcion para obtener informacion de la excepcion
    function getExcepcion(){
        var excepcion = {};
        return excepcion;
    }

    function getGironivel(id,nivel,select){
        var tieneHijos = false;
        $.ajax({
            url:"/api/giros_comerciales/getGirosComercialesByNivelId",
            type:"POST",
            dataType:"json",
            async:false,
            data:{
                id:id,
                nivel:nivel
            },
            success:function(json){
                if(json != null && json.data != ""){
                    $("#"+select).html("<option value=''>Selecciona una opcion</option>");
                    $.each(json.data,function(index,element){
                        $("#"+select).append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                    });
                    tieneHijos = true;
                }else{
                    $("#"+select).html("<option value=''>No hay opciones a mostrar</option>");
                }
                
            }
        });
        return tieneHijos;
    }

    function getGiroEditar(tipoParte){
        var idGiro = $("#giro_comercial_"+tipoParte).val();
        var ultimoNivel = "";
        $.ajax({
            url:'/api/giros_comerciales/getAncestors',
            type:"POST",
            dataType:"json",
            async:false,
            data:{
                id: idGiro
            },
            success:function(json){
                $.each(json.data, function(index,element){
                    if(element.depth == 0){
                        var nivelSiguente = element.depth;
                        getGironivel("",1,"girosNivel1"+tipoParte);
                    }else{
                        // getGironivel(elemnt.id,1,"girosNivel1solicitado");
                        var nivelSiguente = element.depth;
                        $("#girosNivel"+nivelSiguente+tipoParte).val(element.id);
                        $("#girosNivel"+nivelSiguente+tipoParte).trigger("change");
                        
                    }
                    ultimoNivel = element.depth+1;
                    
                });
            }
        });
        $("#girosNivel"+ultimoNivel+tipoParte).val(idGiro);
        $("#girosNivel"+ultimoNivel+tipoParte).trigger("change");
    }

    $(".giroNivel").select2({
        placeholder:"Seleccione una opcion",
        language: "es"
    });
    $(".giroNivel").on("change",function(){
        var tipoParte = $(this).attr("tipo");
        
        $("#giro_comercial_"+tipoParte).val($(this).val());
        $("#giro_"+tipoParte).html("* Giro comercial seleccionado: <b>"+$(this)[0].selectedOptions[0].text+"</b>");
        if($(this).attr("id") == "girosNivel1"+tipoParte){
            $("#divNivel2"+tipoParte).hide();
            $("#divNivel3"+tipoParte).hide();
            $('#divNivel2'+tipoParte+' option').remove();
            $('#divNivel3'+tipoParte+' option').remove();
        }
        var tieneHijos = false;
        if($(this).attr("nextLevel") != ""){
            tieneHijos = getGironivel($(this).val(),$(this).attr("nextLevel"),"girosNivel"+$(this).attr("nextLevel")+tipoParte);
        }
        
        if(!tieneHijos){
            $("#divNivel"+$(this).attr("nextLevel")+tipoParte).hide();
        }else{
            $("#divNivel"+$(this).attr("nextLevel")+tipoParte).show();
        }
    });

    

    $("#solicita_traductor_solicitado").change(function(){
        if($("#solicita_traductor_solicitado").is(":checked")){
            $("#selectIndigenaSolicitado").show();
        }else{
            $("#selectIndigenaSolicitado").hide();
            
        }
    });

    $("#idFechaNacimientoSolicitante").blur(function(){
        $("#idEdadSolicitante").val(Edad($("#idFechaNacimientoSolicitante").val()))
    });
    $("#idFechaNacimientoSolicitado").blur(function(){
        $("#idEdadSolicitado").val(Edad($("#idFechaNacimientoSolicitado").val()))
    });
    $("#solicita_traductor_solicitante").change(function(){
        if($("#solicita_traductor_solicitante").is(":checked")){
            $("#selectIndigenaSolicitante").show();
        }else{
            $("#selectIndigenaSolicitante").hide();
        }
    });
    $(".catSelect").select2({width: '100%'});
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
        if(fecha != "" && fecha != null){
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
    $('.upper').on('keyup', function () {
        var valor = $(this).val();
        $(this).val(valor.toUpperCase());
    });

    //validacion curp
     
var CURP = (function () {

    var modulo = {};

    /**
     * Valida que la cadena proporcionada se apegue a la definición de la CURP
     */
    modulo.valida = function (curp) {
    var reg = "";
    if(curp.length == 18)
    {
        var digito = this.verifica(curp);

        reg = /[A-Z]{4}\d{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9][0-9]/;

        if(curp.search(reg))
        {
        return false;
        }

        if(!(parseInt(digito) == parseInt(curp.substring(17,18))))
        {
        return false;
        }
        return true;
    }
    else
    {
        return false;
    }
    };

    /**
     * Comprueba el dígito verificador mediante el algoritmo de LUHN "tropicalizado"
     */
    modulo.verifica = function(curp){
    var segRaiz      = curp.substring(0,17);
    var chrCaracter  = "0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
    var intFactor    = new Array(17);
    var lngSuma      = 0.0;
    var lngDigito    = 0.0;

    for(var i=0; i<17; i++)
    {
        for(var j=0;j<37; j++)
        {
            
        if(segRaiz.substring(i,i+1)==chrCaracter.substring(j,j+1))
        {
            intFactor[i]=j;
        }
        }
    }
    for(var k = 0; k < 17; k++)
    {
        lngSuma= lngSuma + ((intFactor[k]) * (18 - k));
    }
    lngDigito= (10 - (lngSuma % 10));

    if(lngDigito==10)
    {
        lngDigito=0;
    }

    return lngDigito;
    };

    return modulo;
}());

function validaCURP(curp){
    if(curp.length == 0){
        return;
    }
    if(CURP.valida(curp)){
    //   document.getElementById('msg').innerText = 'CURP Válida';
    return;
    }

    swal({title: 'Error',text: 'La curp no es valida',icon: 'warning',});   
}

/**
  *  Funcion para calcular edad
**/
function Edad(FechaNacimiento) {
    var fechaNace = new Date(dateFormat(FechaNacimiento));
    var fechaActual = new Date()

    var mes = fechaActual.getMonth();
    var dia = fechaActual.getDate();
    var año = fechaActual.getFullYear();

    fechaActual.setDate(dia);
    fechaActual.setMonth(mes);
    fechaActual.setFullYear(año);
    edad = Math.floor(((fechaActual - fechaNace) / (1000 * 60 * 60 * 24) / 365));

    return edad;
}

/**
*  Aqui comienzan las funciones para carga de documentos de la solicitud
*/
    $("#btnAgregarArchivo").on("click",function(){
        $("#btnCancelFiles").click();
        $("#modal-archivos").modal("show");
    });
    function cargarDocumentos(){
        $.ajax({
            url:"/api/solicitudes/documentos/"+$("#solicitud_id").val(),
            type:"GET",
            dataType:"json",
            async:true,
            success:function(data){
                if(data != null && data != ""){
                    var table = "";
                    var div = "";
                    $.each(data, function(index,element){
                        div += '<div class="image gallery-group-1">';
                        div += '    <div class="image-inner" style="position: relative;">';
                        if(element.tipo == 'pdf' || element.tipo == 'PDF'){
                            div += '            <a href="/api/documentos/getFile/'+element.id+'" data-toggle="iframe" data-gallery="example-gallery-pdf" data-type="url">';
                            div += '                <div class="img" align="center">';
                            div += '                    <i class="fa fa-file-pdf fa-4x" style="color:black;margin: 0;position: absolute;top: 50%;transform: translateX(-50%);"></i>';
                            div += '                </div>';
                            div += '            </a>';
                        }else{
                            div += '            <a href="/api/documentos/getFile/'+element.id+'" data-toggle="lightbox" data-gallery="example-gallery" data-type="image">';
                            div += '                <div class="img" style="background-image: url(\'/api/documentos/getFile/'+element.id+'\')"></div>';
                            div += '            </a>';
                        }
                        div += '            <p class="image-caption">';
                        div += '                '+element.longitud+' kb';
                        div += '            </p>';
                        div += '    </div>';
                        div += '    <div class="image-info">';
                        div += '            <h5 class="title">'+element.nombre_original+'</h5>';
                        div += '            <div class="desc">';
                        div += '                <strong>Documento: </strong>'+element.clasificacionArchivo.nombre;
                        div +=                  element.descripcion+'<br>';
                        div += '            </div>';
                        div += '    </div>';
                        div += '</div>';
                    });
                    $("#gallery").html(div);
                }else{
//                    swal({
//                        title: 'Algo salio mal',
//                        text: 'No se guardo el registro',
//                        icon: 'warning'
//                    });
                }
            }
        });       
    }
    var handleJqueryFileUpload = function() {
        // Initialize the jQuery File Upload widget:
        $('#fileupload').fileupload({
            autoUpload: false,
            disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf)$/i,
            stop: function(e,data){
              cargarDocumentos();
              $("#modal-archivos").modal("hide");
            }
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCCOLOR_REDentials: true},                
        });

        // Enable iframe cross-domain access via COLOR_REDirect option:
        $('#fileupload').fileupload(
            'option',
            'COLOR_REDirect',
            window.location.href.replace(
                    /\/[^\/]*$/,
                    '/cors/result.html?%s'
            )
        );

        // hide empty row text
        $('#fileupload').bind('fileuploadadd', function(e, data) {
            $('#fileupload [data-id="empty"]').hide();
            $(".tipo_documento").select2();
        });
        $('#fileupload').bind('fileuploaddone', function(e, data) {
            console.log("add");
        });

        // show empty row text
        $('#fileupload').bind('fileuploadfail', function(e, data) {
            var rowLeft = (data['originalFiles']) ? data['originalFiles'].length : 0;
            if (rowLeft === 0) {
                    $('#fileupload [data-id="empty"]').show();
            } else {
                    $('#fileupload [data-id="empty"]').hide();
            }
        });

        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
                $.ajax({
                        type: 'HEAD'
                }).fail(function () {
                        $('<div class="alert alert-danger"/>').text('Upload server currently unavailable - ' + new Date()).appendTo('#fileupload');
                });
        }

        // Load & display existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCCOLOR_REDentials: true},
                url: $('#fileupload').fileupload('option', 'url'),
                dataType: 'json',
                context: $('#fileupload')[0]
        }).always(function () {
                $(this).removeClass('fileupload-processing');
        }).done(function (result) {
                $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });
    };
    var handleIsotopesGallery = function() {
        var container = $('#gallery');
        $(window).on('resize', function() {
            var dividerValue = calculateDivider();
            var containerWidth = $(container).width();
            var columnWidth = containerWidth / dividerValue;
            $(container).isotope({
                masonry: { 
                    columnWidth: columnWidth 
                }
            });
        });
    };
    function calculateDivider() {
        var dividerValue = 4;
        if ($(this).width() <= 576) {
            dividerValue = 1;
        } else if ($(this).width() <= 992) {
            dividerValue = 2;
        } else if ($(this).width() <= 1200) {
            dividerValue = 3;
        }
        return dividerValue;
    }
    var FormMultipleUpload = function () {
        "use strict";
        return {
            //main function
            init: function () {
                handleJqueryFileUpload();
            }
        };
    }();
    var Gallery = function () {
        "use strict";
        return {
            //main function
            init: function () {
                handleIsotopesGallery();
            }
        };
    }();
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox({
            alwaysShowClose: false,
            onShown: function() {
                console.log('Checking our the events huh?');
            },
            onNavigate: function(direction, itemIndex){
                console.log('Navigating '+direction+'. Current item: '+itemIndex);
            }
        }); 
    });

    $(document).on('click', '[data-toggle="iframe"]',function(event){
        event.preventDefault();
        var pdf_link = $(this).attr('href');
        var iframe = "";
        iframe +='    <div id="Iframe-Cicis-Menu-To-Go" class="set-margin-cicis-menu-to-go set-padding-cicis-menu-to-go set-border-cicis-menu-to-go set-box-shadow-cicis-menu-to-go center-block-horiz">';
        iframe +='        <div class="responsive-wrapper responsive-wrapper-padding-bottom-90pct" style="-webkit-overflow-scrolling: touch; overflow: auto;">';
        iframe +='            <iframe src="'+pdf_link+'"></iframe>';
        iframe +='        </div>';
        iframe +='    </div>';

        $("#bodyArchivo").html(iframe);
        $("#modal-visor").modal("show");

        return false;
    });
</script>

<script src="/assets/plugins/parsleyjs/dist/parsley.min.js"></script>
<script src="/assets/plugins/highlight.js/highlight.min.js"></script>

@endpush

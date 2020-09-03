
<style>
    .inputError {
        border: 1px red solid;
    }
    .needed:after {
      color:darkred;
      content: " (*)";
   }
    .highlighted{
        background-color: #FFFF00;
        color: #000 !important;
    }
    .select2-results__option--highlighted{
        background: #348fe2 !important;
        color: #fff !important;
    }
    .select2-results__options {
        max-height: 400px;
    }
    .wizard-steps li.active, .wizard-steps li.current, .wizard-steps li.success {
        background-color: #9D2449;
        color: #fff;
        height: 70px !important;
        top: 0;
    }
    .loading-results {
        background-image: url('/assets/img/spinner.gif');
        background-repeat: no-repeat;
        padding-left: 10px;
        background-position: 120px 50%;
    }
    .upper{
        text-transform: uppercase;
    }

</style>
@if(auth()->user())
    <input type="hidden" id="externo" value="0">
@else
    <input type="hidden" id="externo" value="1">
@endif
<div id="wizard" class="col-md-12" >
    <!-- begin wizard-step -->
    <ul class="wizard-steps">
        <li>
            <a id="paso1" href="#step-1">

                <span class="">
                    Solicitante
                    <small>Información del solicitante</small>
                </span>
            </a>
        </li>
        <li>
            <a id="paso2" href="#step-2">

                <span class="">
                    Citado
                    <small>Información del citado</small>
                </span>
            </a>
        </li>
        <li >
            <a id="paso3" href="#step-3">

                <span class="">
                    Solicitud
                    <small>Información de la solicitud</small>
                </span>
            </a>
        </li>
        <li id="paso4" class="step-4">
            <a href="#step-4">

                <span class="">
                    Excepci&oacute;n
                    <small>Casos de excepci&oacute;n</small>
                </span>
            </a>
        </li>

        <!-- El paso 5 Es para asignar Audiencias -->
        <li class="step-5">
            <a id="paso5" href="#step-5">

                <span class="">
                    Audiencias
                    <small>Audiencias de conciliación</small>
                </span>
            </a>
        </li>

        <!-- El paso 5 Es para asignar Audiencias -->
        <li class="step-6">
            <a id="paso6" href="#step-6">

                <span class="">
                    Historial
                    <small>Historial de acciones</small>
                </span>
            </a>
        </li>
        <!-- El paso 5 Es para asignar Audiencias -->
        <li id="paso7" class="step-7">
            <a href="#step-7">

                <span class="">
                    Documentos
                    <small>Documentos del expediente</small>
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

                        <div class="col-md-12 mt-4">
                            <h2>Datos generales de la solicitud</h2>
                            <hr class="red">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control date" required id="fechaConflicto" placeholder="Fecha de Conflicto" type="text" value="">
                            <p class="help-block needed">Fecha de Conflicto</p>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                {!! Form::select('objeto_solicitud_id', isset($objeto_solicitudes) ? $objeto_solicitudes : [] , null, ['id'=>'objeto_solicitud_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                {!! $errors->first('objeto_solicitud_id', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block needed">Objeto de la solicitud</p>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-primary" type="button" onclick="agregarObjetoSol()" id="btnObjetoSol" > <i class="fa fa-plus-circle"></i> Agregar Objeto</button>
                            </div>
                        </div>

                        <div class="col-md-10 offset-md-1" style="margin-top: 3%;" >
                            <table class="table table-bordered" >
                                <thead>
                                    <tr>
                                        <th>Objeto</th>
                                        <th>Acci&oacute;n</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyObjetoSol">
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 form-group row">
                            <input type="hidden" id="term">
                            <div class="col-md-12 " title="Escribe la actividad y escoge de la opciones que se despliegan" data-toggle="tooltip" data-placement="top" >
                                <select name="giro_comercial_solicitante" placeholder="Seleccione" id="giro_comercial_solicitante" class="form-control"></select>
                            </div>
                            <div class="col-md-12">
                                <p class="help-block "><span class="needed">&iquest;Qué es la actividad principal de tu patrón?</span> <br> Ejemplos: comercio de productos al por menor, construcción, servicios médicos...</p>
                            <label id="giro_solicitante"></label>
                            </div>
                        </div>
                        {!! Form::select('giro_comercial_hidden', isset($giros_comerciales) ? $giros_comerciales : [] , null, ['id'=>'giro_comercial_hidden','placeholder' => 'Seleccione una opción','style'=>'display:none;']);  !!}

                    </div>
                    <div class="col-xl-10 offset-xl-1">
                        <div>
                            <center><h1>Solicitante <span id="labelTipoSolicitante"></span></h1></center>
                            <div id="editandoSolicitante"></div>
                        </div>
                        <div id="divSolicitante">
                            <div id="datosIdentificacionSolicitante" data-parsley-validate="true">
                                <div class="col-md-12 mt-4">
                                    <h4>Datos de identificaci&oacute;n</h4>
                                    <hr class="red">
                                </div>
                                <div style="margin-left:5%; margin-bottom:3%; " id="divTipoPersona">
                                    <input type="hidden" id="solicitante_id">
                                    <input type="hidden" id="edit_key">
                                    <label>Tipo Persona</label>
                                    <div class="row">
                                        <div class="radio radio-css radio-inline">
                                            <input checked="checked" name="tipo_persona_solicitante" type="radio" id="tipo_persona_fisica_solicitante" value="1"/>
                                            <label for="tipo_persona_fisica_solicitante">Física</label>
                                        </div>
                                        <div class="radio radio-css radio-inline">
                                            <input name="tipo_persona_solicitante" type="radio" id="tipo_persona_moral_solicitante" value="2"/>
                                            <label for="tipo_persona_moral_solicitante">Moral</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 personaFisicaSolicitante">
                                    <input class="form-control upper" id="idSolicitanteCURP" placeholder="CURP del solicitante" maxlength="18" onblur="validaCURP(this.value);" type="text" value="">
                                    <p class="help-block needed">CURP del solicitante</p>
                                </div>
                                <div class="col-md-12 row">
                                    <div class="col-md-4" style="display:none;">
                                        <input class="form-control" id="idsolicitante" type="text" value="253">
                                    </div>
                                </div>
                                <div class="col-md-12 row">
                                    <input class="form-control" id="idsolicitante" type="hidden" value="253">
                                    <div class="col-md-4 personaFisicaSolicitante">
                                        <input class="form-control upper" id="idNombreSolicitante" required placeholder="Nombre del solicitante" type="text" value="">
                                        <p class="help-block needed">Nombre del solicitante</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitante">
                                        <input class="form-control upper" id="idPrimerASolicitante" required placeholder="Primer apellido del solicitante" type="text" value="">
                                        <p class="help-block needed">Primer apellido</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitanteNO">
                                        <input class="form-control upper" id="idSegundoASolicitante" placeholder="Segundo apellido del solicitante" type="text" value="">
                                        <p class="help-block">Segundo apellido</p>
                                    </div>
                                    <div class="col-md-12 personaMoralSolicitante">
                                        <input class="form-control upper" id="idNombreCSolicitante" placeholder="Raz&oacute;n social" type="text" value="">
                                        <p class="help-block needed">Raz&oacute;n Social</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitante">
                                        <input class="form-control dateBirth" required id="idFechaNacimientoSolicitante" placeholder="Fecha de nacimiento del solicitante" type="text" value="">
                                        <p class="help-block needed">Fecha de nacimiento</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitante">
                                        <input class="form-control numero" disabled required data-parsley-type='integer' id="idEdadSolicitante" placeholder="Edad del solicitante" type="text" value="">
                                        <p class="help-block needed">Edad del solicitante</p>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control upper" id="idSolicitanteRfc" onblur="validaRFC(this.value);" placeholder="RFC del solicitante" type="text" value="">
                                        <p class="help-block">RFC del solicitante</p>
                                    </div>
                                    
                                    <div class="col-md-4 sindicato" style="display: none;">
                                        <input class="form-control upper " id="registro_sindical" placeholder="Registro sindical" type="text" value="">
                                        <p class="help-block needed">Registro sindical</p>
                                    </div>
                                    
                                    <div class="col-md-4 sindicato" style="display: none;">
                                        <input class="form-control upper" id="contrato_colectivo" placeholder="Contrato Colectivo" type="text" value="">
                                        <p class="help-block">Contrato colectivo</p>
                                    </div>
                                    
                                    <div class="col-md-4 personaFisicaSolicitante">
                                        {!! Form::select('genero_id_solicitante', isset($generos) ? $generos : [] , null, ['id'=>'genero_id_solicitante','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                        {!! $errors->first('genero_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block needed">Género</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitante">
                                        {!! Form::select('nacionalidad_id_solicitante', isset($nacionalidades) ? $nacionalidades : [] , null, ['id'=>'nacionalidad_id_solicitante','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect']);  !!}
                                        {!! $errors->first('nacionalidad_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block needed">Nacionalidad</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitante">
                                        {!! Form::select('entidad_nacimiento_id_solicitante', isset($estados) ? $estados : [] , null, ['id'=>'entidad_nacimiento_id_solicitante','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect']);  !!}
                                        {!! $errors->first('entidad_nacimiento_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block needed">Estado de nacimiento</p>
                                    </div>
                                </div>
                                <div class="col-md-12 row personaFisicaSolicitanteNO">
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
                                        {!! Form::select('lengua_indigena_id_solicitante', isset($lengua_indigena) ? $lengua_indigena : [] , null, ['id'=>'lengua_indigena_id_solicitante','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                        {!! $errors->first('lengua_indigena_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block needed">Lengua Indigena</p>
                                    </div>
                                </div>
                                <div  class="col-md-12 pasoSolicitante" id="continuar1">
                                    <button style="float: right;" class="btn btn-primary" onclick="pasoSolicitante(1)" type="button" > Validar <i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                            {{-- <div class="col-md-12 row">
                                <div class="col-md-4 personaFisicaSolicitanteNO">
                                    {!! Form::select('motivo_excepciones_id_solicitante', isset($motivo_excepciones) ? $motivo_excepciones : [] , null, ['id'=>'motivo_excepciones_id_solicitante','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                    {!! $errors->first('motivo_excepciones_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block needed">Motivo de excepcion</p>
                                </div>
                                <div class="col-md-4 personaFisicaSolicitanteNO" id="divGrupoPrioritario" style="display: none;">
                                    {!! Form::select('grupo_prioritario_id_solicitante', isset($grupo_prioritario) ? $grupo_prioritario : [] , null, ['id'=>'grupo_prioritario_id_solicitante','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                    {!! $errors->first('grupo_prioritario_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block needed">Grupo Vulnerable</p>
                                </div>
                            </div> --}}
                            {{-- Seccion de contactos solicitantes --}}
                            <div id="divContactoSolicitante" data-parsley-validate="true" style="display:none" class="col-md-12 row">
                                <div class="col-md-12 mt-4">
                                    <h4>Contacto</h4>
                                    <hr class="red">
                                </div>
                                <input type="hidden" id="contacto_id_solicitante">
                                <div class="col-md-4">
                                    {!! Form::select('tipo_contacto_id_solicitante', isset($tipo_contacto) ? $tipo_contacto : [] , null, ['id'=>'tipo_contacto_id_solicitante','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                    {!! $errors->first('tipo_contacto_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block needed">Tipo de contacto</p>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" id="contacto_solicitante" placeholder="Contacto"  type="text" value="">
                                    <p class="help-block needed">Contacto</p>
                                </div>
                                <div class="col-md-4">
                                <button class="btn btn-primary" type="button" onclick="agregarContactoSolicitante();" > <i class="fa fa-plus-circle"></i> Agregar Contacto</button>
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
                                <div  class="col-md-12 pasoSolicitante" id="continuar2">
                                    <button style="float: right;" class="btn btn-primary" onclick="pasoSolicitante(2)" type="button" > Validar <i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                            {{-- end seccion de contactos citados --}}
                            <!-- seccion de domicilios solicitante -->
                            <div id="divMapaSolicitante" data-parsley-validate="true" style="display:none">
                                @include('includes.component.map',['identificador' => 'solicitante','needsMaps'=>"false", 'instancia' => '1'])
                                <div class="col-md-12 pasoSolicitante"id="continuar3">
                                    <button style="float: right;" class="btn btn-primary" onclick="pasoSolicitante(3)" type="button" > Validar <i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>

                            <!-- end seccion de domicilios solicitante -->
                            <!-- Seccion de Datos laborales -->
                            <div id="divDatoLaboralSolicitante" style="display: none;"  class="col-md-12 row">
                                <div class="col-md-12 mt-4">
                                    <h4>Datos Laborales</h4>
                                    <hr class="red">
                                </div>
                                <input type="hidden" id="dato_laboral_id">
                                <div class="col-md-6">
                                    <input class="form-control upper" id="nombre_jefe_directo" placeholder="Nombre del jefe directo" type="text" value="">
                                    <p class="help-block">Nombre del Jefe directo</p>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-control upper" id="nombre_contrato" placeholder="Nombre de quien te contrato" type="text" value="">
                                    <p class="help-block">&iquest;Quien te contrato?</p>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-control upper" id="nombre_paga" placeholder="Nombre quien te paga" type="text" value="">
                                    <p class="help-block">&iquest;Quien te paga?</p>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-control upper" id="nombre_prestas_servicio" placeholder="Nombre de a quien le prestas tus servicios" type="text" value="">
                                    <p class="help-block">&iquest;A quien prestas el servicio?</p>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-control numero" maxlength="11" minlength="11" length="11" data-parsley-type='integer' id="nss" placeholder="N&uacute;mero de seguro social"  type="text" value="">
                                    <p class="help-block ">N&uacute;mero de seguro social</p>
                                </div>
                                <div class="col-md-12 row">
                                    <div class="col-md-6">
                                        <input class="form-control upper" id="puesto" placeholder="Puesto" type="text" value="">
                                        <p class="help-block ">Puesto</p>
                                    </div>
                                    <div class="col-md-6" >
                                        {!! Form::select('ocupacion_id', isset($ocupaciones) ? $ocupaciones : [] , null, ['id'=>'ocupacion_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                        {!! $errors->first('ocupacion_id', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block ">&iquest;En caso de desempeñar un oficio que cuenta con salario mínimo distinto al general, escoja del catálogo. Si no, deja vacío.</p>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <input class="form-control numero" data-parsley-type='integer' id="no_issste" placeholder="No. ISSSTE"  type="text" value="">
                                        <p class="help-block">No. ISSSTE</p>
                                    </div> --}}
                                </div>
                                <div class="col-md-12 row">
                                    <div class="col-md-4">
                                        <input class="form-control numero requiredLaboral" required data-parsley-type='number' id="remuneracion" max="99999999" placeholder="¿Cu&aacute;nto te pagan?" type="text" value="">
                                        <p class="help-block needed">&iquest;Cu&aacute;nto te pagan?</p>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::select('periodicidad_id', isset($periodicidades) ? $periodicidades : [] , null, ['id'=>'periodicidad_id','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect requiredLaboral']);  !!}
                                        {!! $errors->first('periodicidad_id', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block needed">&iquest;Cada cuándo te pagan?</p>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control numero requiredLaboral" required data-parsley-type='integer' id="horas_semanales" placeholder="Horas semanales" type="text" value="">
                                        <p class="help-block needed">Horas semanales</p>
                                    </div>
                                </div>
                                <div class="col-md-12 row">

                                    <div class="col-md-2">
                                        <span class="text-muted m-l-5 m-r-20" for='switch1'>Labora actualmente</span>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="hidden" />
                                        <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="labora_actualmente" name='labora_actualmente'/>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control dateBirth requiredLaboral" required id="fecha_ingreso" placeholder="Fecha de ingreso" type="text" value="">
                                        <p class="help-block needed">Fecha de ingreso</p>
                                    </div>
                                    <div class="col-md-4" id="divFechaSalida">
                                        <input class="form-control dateBirth requiredLaboral" required id="fecha_salida" placeholder="Fecha salida" type="text" value="">
                                        <p class="help-block needed">Fecha salida</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {{-- <select id="jornada_id" required="" class="form-control catSelect" name="jornada_id" data-select2-id="jornada_id" tabindex="-1" aria-hidden="true">
                                        <option selected="selected" value="" data-select2-id="19">Seleccione una opción</option>
                                        @foreach($jornadas as $jornada)
                                            <option value="{{$jornada->id}}" > {{$jornada->nombre}} </option>
                                        @endforeach
                                    </select> --}}
                                    {!! Form::select('jornada_id', isset($jornadas) ? $jornadas : [] , null, ['id'=>'jornada_id','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect requiredLaboral']);  !!}
                                    {!! $errors->first('jornada_id', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block needed">Jornada</p>
                                </div>
                                <div>
                                    <a style="font-size: medium;" onclick="$('#modal-jornada').modal('show');"><i class="fa fa-question-circle"></i></a>
                                </div>
                            </div>
                            <!-- end Seccion de Datos laborales -->
                            <hr style="margin-top:5%;">
                            <div id="divBotonesSolicitante" style="display:none">
                                <button class="btn btn-danger" type="button" onclick="limpiarSolicitante()"> <i class="fa fa-eraser"></i> Limpiar campos</button>
                                <button class="btn btn-primary" style="float: right;" type="button" id="agregarSolicitante" > <i class="fa fa-plus-circle"></i> Validar y agregar solicitante</button>
                            </div>
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
                            <center><h1>Citado</h1></center>
                            <div id="editandoSolicitado"></div>
                        </div>
                        <div  id="divSolicitado">
                            <div id="datosIdentificacionSolicitado" data-parsley-validate="true">

                                <div style="margin-left:5%; margin-bottom:3%; ">
                                    <label>Tipo Persona</label>
                                    <input type="hidden" id="solicitado_id">
                                    <input type="hidden" id="solicitado_key">
                                    <div class="row">
                                        <div class="radio radio-css radio-inline">
                                            <input checked="checked" name="tipo_persona_solicitado" type="radio" id="tipo_persona_fisica_solicitado" value="1"/>
                                            <label for="tipo_persona_fisica_solicitado">Física</label>
                                        </div>
                                        <div class="radio radio-css radio-inline">
                                            <input name="tipo_persona_solicitado" type="radio" id="tipo_persona_moral_solicitado" value="2"/>
                                            <label for="tipo_persona_moral_solicitado">Moral</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 personaFisicaSolicitadoNO">
                                    <input class="form-control upper" id="idSolicitadoCURP" maxlength="18" onblur="validaCURP(this.value);" placeholder="CURP del citado" type="text" value="">
                                    <p class="help-block">CURP del citado</p>
                                </div>
                                <div class="col-md-12 row">
                                    <div class="col-md-4" style="display:none;">
                                        <input class="form-control" id="idsolicitado" type="text" value="253">
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitado">
                                        <input class="form-control upper" required id="idNombreSolicitado" placeholder="Nombre del citado" type="text" value="">
                                        <p class="help-block needed">Nombre del citado</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitado">
                                        <input class="form-control upper" required id="idPrimerASolicitado" placeholder="Primer apellido del citado" type="text" value="">

                                        <p class="help-block needed">Primer apellido</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitadoNO">
                                        <input class="form-control upper" id="idSegundoASolicitado" placeholder="Segundo apellido del citado" type="text" value="">

                                        <p class="help-block">Segundo apellido</p>
                                    </div>
                                    <div class="col-md-8 personaMoralSolicitado">
                                        <input class="form-control upper" id="idNombreCSolicitado" required placeholder="Raz&oacute;n social del citado" type="text" value="">
                                        <p class="help-block needed">Raz&oacute;n Social</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitadoNO">
                                        <input class="form-control dateBirth" id="idFechaNacimientoSolicitado" placeholder="Fecha de nacimiento del citado" type="text" value="">
                                        <p class="help-block">Fecha de nacimiento</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitadoNO">
                                        <input class="form-control numero" disabled id="idEdadSolicitado" placeholder="Edad del citado" type="text" value="">
                                        <p class="help-block">Edad del citado</p>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control upper" id="idSolicitadoRfc" onblur="validaRFC(this.value);" placeholder="RFC del citado" type="text" value="">
                                        <p class="help-block">RFC del citado</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitadoNO">
                                        {!! Form::select('genero_id_solicitado', isset($generos) ? $generos : [] , null, ['id'=>'genero_id_solicitado','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                        {!! $errors->first('genero_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block">Género</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitadoNO">
                                        {!! Form::select('nacionalidad_id_solicitado', isset($nacionalidades) ? $nacionalidades : [] , null, ['id'=>'nacionalidad_id_solicitado','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                        {!! $errors->first('nacionalidad_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block">Nacionalidad</p>
                                    </div>
                                    <div class="col-md-4 personaFisicaSolicitadoNO">
                                        {!! Form::select('entidad_nacimiento_id_solicitado', isset($estados) ? $estados : [] , null, ['id'=>'entidad_nacimiento_id_solicitado','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                        {!! $errors->first('entidad_nacimiento_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block">Estado de nacimiento</p>
                                    </div>
                                </div>
                                <div class="col-md-12 row personaFisicaSolicitadoNO">
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
                                        {!! Form::select('lengua_indigena_id_solicitado', isset($lengua_indigena) ? $lengua_indigena : [] , null, ['id'=>'lengua_indigena_id_solicitado','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                        {!! $errors->first('lengua_indigena_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block needed">Lengua Indigena</p>
                                    </div>
                                </div>
                                <div class="col-md-12 pasoSolicitado" id="continuarSolicitado1">
                                    <button style="float: right;" class="btn btn-primary" onclick="pasoSolicitado(1)" type="button" > Validar <i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                            {{-- Seccion de contactos solicitados --}}
                            <div id="divContactoSolicitado" data-parsley-validate="true" style="display:none;">
                                <div  class="col-md-12 row">
                                    <div class="col-md-12 mt-4">
                                        <h4>Contacto</h4>
                                        <hr class="red">
                                    </div>
                                    <input type="hidden" id="contacto_id_solicitado">
                                    <div class="alert alert-warning p-10">En caso de contar con datos de contacto de la persona citada, es muy importante llenar esta informaci&oacute;n para facilitar la conciliaci&oacute;n efectiva</div>
                                    <div class="col-md-4">
                                        {!! Form::select('tipo_contacto_id_solicitado', isset($tipo_contacto) ? $tipo_contacto : [] , null, ['id'=>'tipo_contacto_id_solicitado','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                        {!! $errors->first('tipo_contacto_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                        <p class="help-block needed">Tipo de contacto</p>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control text-lowercase" id="contacto_solicitado" placeholder="Contacto"  type="text" value="">
                                        <p class="help-block needed">Contacto</p>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary" type="button" onclick="agregarContactoSolicitado();" > <i class="fa fa-plus-circle"></i> Agregar Contacto</button>
                                    </div>
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
                                    <div class="col-md-12 pasoSolicitado" id="continuarSolicitado2">
                                        <button style="float: right;" class="btn btn-primary" onclick="pasoSolicitado(2)" type="button" > Validar <i class="fa fa-arrow-right"></i></button>
                                    </div>
                            </div>
                            {{-- end seccion de contactos solicitados --}}
                            <!-- seccion de domicilios citado -->
                            <div id="divMapaSolicitado" data-parsley-validate="true" style="display: none;">
                                <div  class="col-md-12 row">
                                    <div class="row">
                                        <h4>Domicilio(s)</h4>
                                        <hr class="red">
                                        {{-- <a style="margin-left:1%;" onclick="$('#modal-domicilio').modal('show');"> <i style="font-size:large; color:#49b6d6;" class="fa fa-plus-circle"></i> Oprima + para llenar los datos del domicilio y visualizar el mapa</a> --}}
                                    </div>
                                    @include('includes.component.map',['identificador' => 'solicitado','needsMaps'=>"true", 'instancia' => 2])
                                    <div style="margin-top: 2%;" class="col-md-12">

                                        {{-- <button class="btn btn-primary btn-sm m-l-5" onclick="agregarDomicilio()"><i class="fa fa-save"></i> Guardar Domicilio</button> --}}
                                        {{-- <div class="col-md-10 offset-md-1" >
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
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="col-md-12 pasoSolicitado" id="continuarSolicitado3">
                                    <button style="float: right;" class="btn btn-primary" onclick="pasoSolicitado(3)" type="button" > Validar <i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                                <!-- end seccion de domicilios citado -->
                            <hr style="margin-top:5%;">
                            <div id="divBotonesSolicitado" style="display: none;">
                                <button class="btn btn-primary" type="button" id="agregarSolicitado" > <i class="fa fa-plus-circle"></i> Agregar citado</button>
                                <button class="btn btn-danger" type="button" onclick="limpiarSolicitado()"> <i class="fa fa-eraser"></i> Limpiar campos</button>
                            </div>

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
                    <input type="hidden" id="tipo_solicitud_id" value="{{$tipo_solicitud_id}}">
                    <div class="col-md-4 showEdit" >
                        <input class="form-control dateTime" id="fechaRatificacion" disabled placeholder="Fecha de ratificación" type="text" value="">
                        <p class="help-block">Fecha de Ratificación</p>
                    </div>
                    <div class="col-md-4 showEdit">
                        <input class="form-control dateTime" id="fechaRecepcion" disabled placeholder="Fecha de Recepción" type="text" value="">
                        <p class="help-block needed">Fecha de Recepción</p>
                    </div>
                    <div class="col-md-4 estatusSolicitud">
                        {!! Form::select('estatus_solicitud_id', isset($estatus_solicitudes) ? $estatus_solicitudes : [] , isset($solicitud->estatus_solicitud_id) ?  $solicitud->estatus_solicitud_id : null, ['id'=>'estatus_solicitud_id','disabled','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('estatus_solicitud_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block needed">Estatus de la solicitud</p>
                    </div>
                    <div class="col-md-12">

                        <div><h4>Objeto de la solicitud</h4></div>
                        <div class="col-md-10 offset-md-1" style="margin-top: 3%;" >
                            <table class="table table-bordered" >
                                <thead>
                                    <tr>
                                        <th>Objeto</th>
                                        <th>Acci&oacute;n</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyObjetoSolRevision">
                                </tbody>
                            </table>
                        </div>
                        <div><h4>Solicitantes</h4></div>
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
                                <tbody id="tbodySolicitanteRevision">
                                </tbody>
                            </table>
                        </div>
                        <div><h4>Citados</h4></div>
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
                                <tbody id="tbodySolicitadoRevision">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <br>
                    <br>
                    <div class="col-md-12 form-group">
                        <textarea rows="4" class="form-control" id="observaciones" onkeyup="validarPalabras(this)"></textarea>
                        <p class="help-block">Descripci&oacute;n de los hechos motivo de la solicitud (<label id="numeroPalabras">0</label> de 200 palabras)</p>
                        <input type="hidden" id="countObservaciones" />
                    </div>
                </div>
            </div>

            @if(isset($solicitud->estatus_solicitud_id) && $solicitud->estatus_solicitud_id == 1)
                <div class="form-group">
                    <button class="btn btn-primary btn-sm m-l-5" id="btnRatificarSolicitud"><i class="fa fa-check"></i> Ratificar Solicitud</button>
                </div>
            @endif
            <div class="col-md-12" id="btnGuardar">
                <button style="float: right;" class="btn btn-primary pull-right btn-lg m-l-5" onclick="guardarSolicitud()"><i class="fa fa-save" ></i> Guardar</button>
            </div>
            <div class="col-md-12" id="btnGetAcuse" style="display: none;">
                <a id="btnAcuse" href="/api/documentos/getFile/" class="btn btn-primary pull-right btn-lg m-l-5" target="_blank"><i class="fa fa-file" ></i> Descargar Acuse</a>
            </div>

        </div>
        </div>
        <!-- end step-3 -->
        <!-- begin step-4 -->
        <div id="step-4">
            <div class="row">
                @if (isset($audiencias))
                <div id="divGruposPrioritarios" class="col-md-12" >
                    <div class="col-md-12" style="text-align: center;">
                        <h1 >Excepci&oacute;n a la conciliaci&oacute;n</h1>
                    </div>
                    <div class="col-md-12" style="margin-top: 2%;">
                        <div class="solicitudTerminada">
                            <form action="/solicitud/excepcion" id="excepcionForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="solicitud_id_excepcion" id="solicitud_id_excepcion">
                                <div class="col-md-5">
                                    {!! Form::select('conciliador_excepcion_id', isset($conciliadores) ? $conciliadores : [] , null, ['id'=>'conciliador_excepcion_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                    {!! $errors->first('conciliador_excepcion_id', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block needed">Conciliador</p>
                                </div>
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th>Solicitante</th>
                                            <th>Grupo Vulnerable</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyGruposPrioritarios">

                                    </tbody>
                                </table>
                                <button class="btn btn-primary " >Excepci&oacute;n de Audiencia</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

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
                        @include('expediente.audiencias.calendarioWizard',$partes)
                    @endif
                </div>
            </div>
            @else
            <div> <h1> Audiencia disponible despues de Ratificaci&oacute;n </h1> </div>
            @endif

        </div>
        <!-- end step-5 -->
        <!-- begin step-6 -->
        <div id="step-6">
            <ul class="timeline">
                @if(isset($audits))
                    @foreach($audits as $audit)
                        <li>
                            <!-- begin timeline-time -->
                            <div class="timeline-time">
                                <span >{{\Carbon\Carbon::parse($audit["created_at"])->diffForHumans()}}</span>
                                <!--<span >04:20</span>-->
                            </div>
                            <!-- end timeline-time -->
                            <!-- begin timeline-icon -->
                            <div class="timeline-icon">
                                <a href="javascript:;">&nbsp;</a>
                            </div>
                            <!-- end timeline-icon -->
                            <!-- begin timeline-body -->
                            <div class="timeline-body">
                                <div class="timeline-header">
                                    <span class="userimage"><i class="fa fa-user fa-x3"></i></span>
                                    <span class="username">
                                        <a href="javascript:;">{{$audit["user"]}}</a>
                                        <small></small>
                                    </span>
                                </div>
                                <div class="timeline-content">
                                    @if($audit["elemento"] == 'Solicitud')
                                        @if($audit["event"] == "Modificación")
                                            <p>Se realizaron los siguientes cambios a la solicitud</p>
                                            <p>
                                                @foreach($audit["cambios"] as $key => $value)
                                                    {{$key}} cambio de valor de {{$value["old"]}} a {{$value["new"]}}<br>
                                                @endforeach
                                            </p>
                                        @elseif($audit["event"] == "Inserción")
                                            <p>Se creo la solicitud</p>
                                        @endif
                                    @elseif($audit["elemento"] == 'Parte')
                                        @if($audit["event"] == "Modificación")
                                            <p>Se realizaron los siguientes cambios a {{$audit["extra"]}}</p>
                                            <p>
                                                @foreach($audit["cambios"] as $key => $value)
                                                    {{$key}} cambio de valor de <b>{{isset($value["old"]) ? $value["old"]:""}}</b> a {{isset($value["new"])?$value["new"]:""}}<br>
                                                @endforeach
                                            </p>
                                        @elseif($audit["event"] == "Inserción")
                                            <p>Se creo la parte {{$audit["extra"]}}</p>
                                        @endif
                                    @elseif($audit["elemento"] == 'Expediente')
                                        @if($audit["event"] == "Modificación")
                                            <p>Se realizaron los siguientes cambios al expediente {{$audit["extra"]}}</p>
                                            <p>
                                                @foreach($audit["cambios"] as $key => $value)
                                                    {{$key}} cambio de valor de {{$value["old"]}} a {{$value["new"]}}<br>
                                                @endforeach
                                            </p>
                                        @elseif($audit["event"] == "Inserción")
                                            <p>Se ratificó la solicitud y se creo el expediente {{$audit["extra"]}}</p>
                                        @endif
                                    @elseif($audit["elemento"] == 'Audiencia')
                                        @if($audit["event"] == "Modificación")
                                            <p>Se realizaron los siguientes cambios a la audiencia {{$audit["extra"]}}</p>
                                            <p>
                                                @foreach($audit["cambios"] as $key => $value)
                                                    {{$key}} cambio de valor de {{$value["old"]}} a {{$value["new"]}}<br>
                                                @endforeach
                                            </p>
                                        @elseif($audit["event"] == "Inserción")
                                            <p>Se creo la audiencia {{$audit["extra"]}}</p>
                                            <p>
                                                @foreach($audit["cambios"] as $key => $value)
                                                    @if($key == "fecha_audiencia")
                                                        Fecha: {{\Carbon\Carbon::parse($value["new"])->isoFormat('LL')}}<br>
                                                    @elseif($key == "hora_inicio")
                                                        Hora de inicio: {{\Carbon\Carbon::parse($value["new"])->format('h:i:s')}}<br>
                                                    @elseif($key == "hora_fin")
                                                        Hora de termino: {{\Carbon\Carbon::parse($value["new"])->format('h:i:s')}}<br>
                                                    @endif
                                                @endforeach
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <!-- end timeline-body -->
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
        <!-- end step-6 -->
        <!-- begin step-7 -->
        <div id="step-7">

            <div class="text-right">
                <button class="btn btn-primary btn-sm m-l-5" id='btnAgregarArchivo'><i class="fa fa-plus"></i> Agregar documento</button>
            </div>
            <div class="col-md-12">
                <div id="gallery" class="gallery row"></div>
                <!--<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">-->
            </div>

            <!-- The template to display files available for upload -->
            <script id="template-upload" type="text/x-tmpl">
                @if(isset($solicitud))
                {% for (var i=0, file; file=o.files[i]; i++) { %}
                    <tr class="template-upload fade show">
                        <td>
                            <span class="preview"></span>
                        </td>
                        <td>
                            <div class="bg-light rounded p-10 mb-2">
                                <dl class="m-b-0">
                                    <dt class="text-inverse">Nombre del documento:</dt>
                                    <dd class="name">{%=file.name%}</dd>
                                    <dt class="text-inverse m-t-10">File Size:</dt>
                                    <dd class="size">Processing...</dd>
                                </dl>
                            </div>
                            <strong class="error text-danger h-auto d-block text-left"></strong>
                        </td>
                        <td>
                            <select class="form-control catSelectFile" name="tipo_documento_id[]">
                                <option value="">Seleccione una opci&oacute;n</option>
                                @if(isset($clasificacion_archivo))
                                    @foreach($clasificacion_archivo as $clasificacion)
                                        @if($clasificacion->tipo_archivo_id == 1)
                                        <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td>
                            <select class="form-control catSelectFile parteClass" name="parte[]">
                                <option value="">Seleccione una opci&oacute;n</option>
                                @if(isset($solicitud))
                                    @foreach($solicitud->partes as $parte)
                                        @if(($parte->tipo_parte_id == 1 || $parte->tipo_parte_id == 3) && $parte->tipo_persona_id == 1 )
                                            <option value="{{$parte->id}}">{{$parte->nombre_comercial}}{{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td>
                            <dl>
                                <dt class="text-inverse m-t-3">Progress:</dt>
                                <dd class="m-t-5">
                                    <div class="progress progress-sm progress-striped active rounded-corner"><div class="progress-bar progress-bar-primary" style="width:0%; min-width: 0px;">0%</div></div>
                                </dd>
                            </dl>
                        </td>
                        <td nowrap>
                            {% if (!i && !o.options.autoUpload) { %}
                                <button class="btn btn-primary start width-100 p-r-20 m-r-3" disabled>
                                    <i class="fa fa-upload fa-fw text-inverse"></i>
                                    <span>Guardar</span>
                                </button>
                            {% } %}
                        </td>
                        <td nowrap>
                            {% if (!i) { %}
                                <button class="btn btn-default cancel width-100 p-r-20">
                                    <i class="fa fa-trash fa-fw text-muted"></i>
                                    <span>Cancel</span>
                                </button>
                            {% } %}
                        </td>
                    </tr>
                {% } %}
                @endif
            </script>
            <!-- The template to display files available for download -->
            <script id="template-download" type="text/x-tmpl">
                @if(isset($solicitud))
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
                                    <dt class="text-inverse">Nombre del archivo:</dt>
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
                                {% if (file.success) { %}
                                    <div><span class="label label-success">Correcto</span> {%=file.success%}</div>
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
                @endif
            </script>

        </div>
        <!-- end step-7 -->
    </div>
    <!-- end wizard-content -->
</div>
<!-- end wizard -->

<!-- inicio Modal Domicilio-->

 <div class="modal" id="modal-jornada" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog ">
        <div class="modal-content">

            <div class="modal-body" >
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5>Para determinar tu tipo de jornada, debes considerar las primeras 8 horas que laboras en un día.</h5>
                <p style="font-size:large;">
                    <ol>
                        <li>Si estas 8 horas transcurren entre 6 am y 8 pm, es una jornada "DIURNA".</li>
                        <li>Si estas primeras 8 horas incluyen 3 horas o menos dentro del horario 8 pm - 6 am, es una jornada "MIXTA"</li>
                        <li>Si estas 8 horas incluyen 3.5 o más horas dentro del horario 8 pm - 6 am, es una jornada NOCTURNA. </li>
                        <li>En caso de que tengas algunas jornadas diurnas y otras mixtas o nocturnas, debes poner una jornada "MIXTA".</li>
                    </ol>
                </p>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-primary btn-sm" class="close" data-dismiss="modal" aria-hidden="true" ><i class="fa fa-times"></i> Aceptar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Domicilio-->
<!-- inicio Modal cargar archivos-->
<div class="modal" id="modal-archivos" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Documentos de identificaci&oacute;n</h4>
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
                                {{-- <button type="submit" class="btn btn-primary start m-r-3">
                                        <i class="fa fa-fw fa-upload"></i>
                                        <span>Cargar</span>
                                </button> --}}
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
                                    <th>PARTE RELACIONADA</th>
                                    <th>PROGRESO</th>
                                    <th width="1%"></th>
                                    <th width="1%"></th>
                                </tr>
                            </thead>
                            <tbody class="files">
                                <tr data-id="empty">
                                    <td colspan="5" class="text-center text-muted p-t-30 p-b-30">
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
                    <a class="btn btn-primary btn-sm" data-dismiss="modal" onclick="continuarRatificacion()"><i class="fa fa-sign-out"></i> Continuar a ratificaci&oacute;n</a>
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-sign-out"></i> Cerrar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de cargar archivos-->
{{-- Modal confirma falta de correo --}}
<div class="modal" id="modal_valida_correo" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5>No capturo correo electronico, tome en cuenta que el correo electronico es muy importante para el seguimiento del proceso de conciliaci&oacute;n</h5>
                <div>
                    <label for="sin_correo">Seleccione si no tiene correo electr&oacute;nico</label>
                    <input type="checkbox" value="1" onchange="if($('#sin_correo').is(':checked')){ $('#btnContinuarCorreo').removeAttr('disabled'); }else{ $('#btnContinuarCorreo').attr('disabled', true);  }" data-render="switchery" data-theme="default" id="sin_correo" />
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal" ><i class="fa fa-times"></i> Capturar correo</a>
                    <button class="btn btn-primary btn-sm m-l-5" disabled data-dismiss="modal" onclick="$('#divMapaSolicitante').show();$('#continuar2').hide();"  id='btnContinuarCorreo'><i class="fa fa-save"></i> Continuar</button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Modal confirma falta de correo --}}

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
 <div class="modal" id="modalRatificacion" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ratificaci&oacute;n</h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div id="divNeedRepresentante" style="display: none;">
                        <h5>Representantes legales</h5>
                        <hr class="red">
                        <div class="alert alert-muted" style="display: none;" id="menorAlert" >
                            <strong>Menor de edad:</strong> Detectamos que al menos un solicitante no es mayor de edad, para poder continuar con la solicitud es necesario agregar al representante legal del menor y la identificación oficial de dicho representante.
                        </div>
                        <input type="hidden" id="parte_id" />
                        <input type="hidden" id="parte_representada_id">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td>Solicitante</td>
                                    <td>Accion</td>
                                </tr>
                            </thead>
                            <tbody id="tbodyRepresentante">
                            <tbody>
                        </table>
                    </div>
                    <h5>Identificaciones</h5>
                    <hr class="red">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <td>Solicitante</td>
                                <td>Documento</td>
                            </tr>
                        </thead>
                        <tbody id="tbodyRatificacion">
                        <tbody>
                    </table>
                </div>
                <div style="margin: 2%;">
                    <a class="btn btn-primary btn-sm" style="float: right;" data-dismiss="modal" onclick="$('#modal-archivos').modal('show');" ><i class="fa fa-plus"></i> Agregar Documentos</a>
                </div>
                <br>

            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal" ><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id='btnGuardarRatificar'><i class="fa fa-save"></i> Ratificar</button>
                    <button class="btn btn-success btn-sm m-l-5" id='btnGuardarConvenio'><i class="fa fa-save"></i> Ratificar con convenio</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--inicio modal para representante legal-->
<div class="modal" id="modal-representante" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Representante legal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h5>Datos del Representante legal</h5>
                <div class="col-md-12 row">
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="curp" class="control-label">CURP</label>
                            <input type="text" id="curp" maxlength="18" onblur="validaCURP(this.value);" class="form-control upper" placeholder="CURP del representante legal">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="nombre" class="control-label">Nombre</label>
                            <input type="text" id="nombre" class="form-control" placeholder="Nombre del representante legal">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="primer_apellido" class="control-label">Primer apellido</label>
                            <input type="text" id="primer_apellido" class="form-control" placeholder="Primer apellido del representante">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="segundo_apellido" class="control-label">Segundo apellido</label>
                            <input type="text" id="segundo_apellido" class="form-control" placeholder="Segundo apellido representante">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="fecha_nacimiento" class="control-label">Fecha de nacimiento</label>
                            <input type="text" id="fecha_nacimiento" class="form-control fecha" placeholder="Fecha de nacimiento del representante">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="genero_id" class="col-sm-6 control-label">Género</label>
                        <select id="genero_id" class="form-control select-element">
                            <option value="">-- Selecciona un género</option>
                        </select>
                    </div>
                </div>
                <hr>
                <div id="representanteMoral" style="display: none;">

                    <h5>Datos de comprobante como representante legal</h5>
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clasificacion_archivo_id_representante" class="control-label">Instrumento</label>
                                <select id="clasificacion_archivo_id_representante" class="form-control select-element">
                                    <option value="">-- Selecciona un instrumento</option>
                                    @foreach($clasificacion_archivos_Representante as $clasificacion)
                                    <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="feha_instrumento" class="control-label">Fecha de instrumento</label>
                                <input type="text" id="feha_instrumento" class="form-control fecha" placeholder="Fecha en que se extiende el instrumento">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="detalle_instrumento" class="control-label">Detalle del instrumento notarial</label>
                                <textarea type="text" id="detalle_instrumento" class="form-control" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <h5>Datos de contacto</h5>
                <div class="col-md-12 row">
                    <div class="col-md-5">
                        <label for="tipo_contacto_id" class="col-sm-6 control-label">Tipo de contacto</label>
                        <select id="tipo_contacto_id" class="form-control select-element">
                            <option value="">-- Selecciona un tipo de contacto</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="contacto" class="control-label">Contacto</label>
                            <input type="text" id="contacto" class="form-control" placeholder="Información de contacto">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" type="button" id="btnAgregarContacto">
                            <i class="fa fa-plus-circle"></i> Agregar
                        </button>
                    </div>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered" >
                        <thead>
                            <tr>
                                <th style="width:80%;">Tipo</th>
                                <th style="width:80%;">Contacto</th>
                                <th style="width:20%; text-align: center;">Accion</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyContacto">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarRepresentante"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal-registro-correos" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Representante legal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    - Para ingresar al buzón electronico se debe registrar una dirección de correo, los siguientes solicitantes no registraron una cuenta, indica su correo o solicita un acceso del sistema
                </div>
                <table class="table table-bordered table-striped table-hover" id="tableSolicitantesCorreo">
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th></th>
                            <th>Correo electrónico</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarCorreos"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modal-aviso-resolucion-inmediata" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ratificación inmediata</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    <p>
                        Usted está a punto de ratificar la solicitud para que se de resolución inmediatamente, las indicaciones para esta resolución son las siguientes.<br><br>
                        <ul>
                            <li>Debido a que no se requiere sala para realizar la audiencia, se asignará una sala virtual y el conciliador será asignado de acuerdo a la disponibilidad</li>
                            <li>Debido a que ya hay un convenio entré las partes, la unica labor del conciliador será dar fe de lo acordado</li>
                            <li>Se deberá acceder a la guia de audiencia donde se llenarán los datos requerido para extender la ratificación y el documento que de esta resulte</li>
                            <li>Si desea continuar con el proceso de ratificación inmediata presione ratificar</li>
                            <li>Si desea agendar una audiencia para conciliar, presione cancelar</li>
                        </ul>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnRatificarInmediata"><i class="fa fa-arrow-right"></i> Continuar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Fin de modal de representante legal-->
<input type="hidden" id="expediente_id">
<!--</div>-->
@push('scripts')

<script>
    // Se declaran las variables globales
    var arraySolicitados = []; //Lista de citados
    var arraySolicitantes = []; //Lista de solicitantes
    var arrayDomiciliosSolicitante = []; // Array de domicilios para el solicitante
    var arrayDomiciliosSolicitado = []; // Array de domicilios para el citado
    var arrayObjetoSolicitudes = []; // Array de objeto_solicitude para el citado
    var arrayContactoSolicitantes = []; // Array de objeto_solicitude para el citado
    var arrayContactoSolicitados = []; // Array de objeto_solicitude para el citado
    var arraySolicitanteExcepcion = {}; // Array de solicitante excepción
    var ratifican = false;; // Array de solicitante excepción
    var listaContactos=[];

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
        $('.sw-btn-prev').hide();
        $('.sw-btn-next').hide();
        if(edit){
            $(".estatusSolicitud").show();
            $(".showEdit").show();
            var solicitud='{{ $solicitud->id ?? ""}}';
            FormMultipleUpload.init();
            Gallery.init();
        }else{
            $(".showEdit").hide();
            $(".step-4").hide();
            $(".step-5").hide();
            $(".step-6").hide();
            $(".step-7").hide();
            $('#wizard').smartWizard("stepState", [4], "hide");
            $('#wizard').smartWizard("stepState", [5], "hide");
            $('#wizard').smartWizard("stepState", [6], "hide");
            $('#wizard').smartWizard("stepState", [7], "hide");
            $(".estatusSolicitud").hide();
        }
        $(".fecha").datetimepicker({format:"DD/MM/YYYY"});
        $(".select-element").select2();

        $(".personaMoralSolicitado").hide();
        $(".personaMoralSolicitante").hide();
        $(".personaFisicaSolicitante input").attr("required","");
        $(".personaFisicaSolicitado input").attr("required","");
        $(".personaMoralSolicitante input").removeAttr("required");
        $(".personaMoralSolicitado input").removeAttr("required");

        $("#agregarSolicitante").click(function(){
            try{
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
                        solicitante.grupo_prioritario_id = "";//$("#grupo_prioritario_id_solicitante").val();
                        solicitante.motivo_excepciones_id = "";//$("#motivo_excepciones_id_solicitante").val();
                        solicitante.solicita_traductor = $("input[name='solicita_traductor_solicitante']:checked").val()
                    }else{
                        solicitante.nombre_comercial = $("#idNombreCSolicitante").val();
                        if($("#tipo_solicitud_id").val() == "4"){
                            solicitante.registro_sindical = $("#registro_sindical").val();
                            solicitante.contrato_colectivo = $("#contrato_colectivo").val();
                        }

                    }
                    solicitante.tipo_persona_id = $("input[name='tipo_persona_solicitante']:checked").val()
                    solicitante.tipo_parte_id = 1;
                    solicitante.activo = 1;
                    solicitante.rfc = $("#idSolicitanteRfc").val();
                    // datos laborales en la solicitante
                    if($("#tipo_solicitud_id").val() == "1"){
                        var dato_laboral = {};
                        dato_laboral.id = $("#dato_laboral_id").val();
                        dato_laboral.nombre_jefe_directo = $("#nombre_jefe_directo").val();
                        dato_laboral.nombre_prestas_servicio = $("#nombre_prestas_servicio").val();
                        dato_laboral.nombre_paga = $("#nombre_paga").val();
                        dato_laboral.nombre_contrato = $("#nombre_contrato").val();
                        dato_laboral.ocupacion_id = $("#ocupacion_id").val();
                        dato_laboral.puesto = $("#puesto").val();
                        dato_laboral.nss = $("#nss").val();
                        dato_laboral.no_issste = "";//$("#no_issste").val();
                        dato_laboral.remuneracion = $("#remuneracion").val();
                        dato_laboral.periodicidad_id = $("#periodicidad_id").val();
                        dato_laboral.labora_actualmente = $("#labora_actualmente").is(":checked");
                        dato_laboral.fecha_ingreso = dateFormat($("#fecha_ingreso").val());
                        dato_laboral.fecha_salida = dateFormat($("#fecha_salida").val());
                        dato_laboral.jornada_id = $("#jornada_id").val();
                        dato_laboral.horas_semanales = $("#horas_semanales").val();
                        dato_laboral.giro_comercial_id = $("#giro_comercial_hidden").val();
                        dato_laboral.resolucion = false;
                        solicitante.dato_laboral = dato_laboral;
                    }

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

                    $('#divContactoSolicitante').hide();
                    $('#divMapaSolicitante').hide();
                    $('#divDatoLaboralSolicitante').hide();
                    $('#divBotonesSolicitante').hide();
                    $(".pasoSolicitante").show();
                    swal({
                        title: '¿Quieres seguir capturando solicitante(s) o proceder a capturar citado(s)?',
                        text: '',
                        icon: '',
                        buttons: {
                            cancel: {
                                text: 'Capturar otro solicitante',
                                value: null,
                                visible: true,
                                className: 'btn btn-primary',
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Capturar Citado(s)',
                                value: true,
                                visible: true,
                                className: 'btn btn-primary',
                                closeModal: true
                            }
                        }
                    }).then(function(isConfirm){
                        if(isConfirm){
                            $('#paso2').click();
                        }else{
                            divSolicitante
                            $('html,body').animate({
                                scrollTop: $("#divSolicitante").offset().top
                            }, 'slow');
                        }
                    });
                }else{
                    swal({
                        title: 'Error',
                        text: 'Revisa que los campos requeridos esten correctos',
                        icon: 'error',
                    });
                }
            }catch(error){
                console.log(error);
            }
        });


        /**
        * Funcion para agregar citado a lista de citados
        */
        $("#agregarSolicitado").click(function(){
            try{
                if($('#step-2').parsley().validate()  ){
                    agregarDomicilio();
                    if(arrayDomiciliosSolicitado.length > 0 ){

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
                        arrayDomiciliosSolicitado = [];
                        formarTablaDomiciliosSolicitado();
                        $('#divContactoSolicitado').hide();
                        $('#divMapaSolicitado').hide();
                        $('#divBotonesSolicitado').hide();
                        $(".pasoSolicitado").show();
                        swal({
                            title: '¿Quieres seguir capturando citados?',
                            text: '',
                            icon: '',
                            buttons: {
                                cancel: {
                                    text: 'Capturar otro citado',
                                    value: null,
                                    visible: true,
                                    className: 'btn btn-primary',
                                    closeModal: true,
                                },
                                confirm: {
                                    text: 'Continuar',
                                    value: true,
                                    visible: true,
                                    className: 'btn btn-primary',
                                    closeModal: true
                                }
                            }
                        }).then(function(isConfirm){
                            if(isConfirm){
                                $('#paso3').click();
                            }else{
                                divSolicitante
                                $('html,body').animate({
                                    scrollTop: $("#divSolicitado").offset().top
                                }, 'slow');
                            }
                        });
                    }else{
                    swal({
                        title: 'Error',
                        text: 'Es necesario llenar todos los campos obligatorios y al menos una dirección del citado',
                        icon: 'error',

                    });
                }
                }else{
                    swal({
                        title: 'Error',
                        text: 'Es necesario llenar todos los campos obligatorios y al menos una dirección del citado',
                        icon: 'error',

                    });
                }
            }catch(error){
                console.log(error);
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
                $(".personaFisicaSolicitanteNO").show();
            }else{
                $(".personaMoralSolicitante input").attr("required","");
                $(".personaMoralSolicitante select").attr("required","");
                $(".personaFisicaSolicitante input").removeAttr("required");
                $(".personaFisicaSolicitante select").removeAttr("required");
                $(".personaMoralSolicitante").show();
                $(".personaFisicaSolicitante").hide();
                $(".personaFisicaSolicitanteNO").hide();
                $(".divGrupoPrioritario").hide();
            }
        });
        $("#labora_actualmente").change(function(){
            if($("#labora_actualmente").is(":checked")){
                $("#divFechaSalida").hide();
                $("#fecha_salida").removeAttr("required");
            }else{
                $("#fecha_salida").attr("required","");
                $("#divFechaSalida").show();
            }
        });
        // $("#motivo_excepciones_id_solicitante").change(function(){
        //     if($(this).val() == 3){
        //         $("#divGrupoPrioritario").show();
        //     }else{
        //         $("#divGrupoPrioritario").hide();
        //         $("#grupo_prioritario_id_solicitante").val("").trigger("change");
        //     }
        // });

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
                $(".personaFisicaSolicitadoNO").show();
            }else{
                $(".personaFisicaSolicitado input").removeAttr("required");
                $(".personaMoralSolicitado input").attr("required","");
                $(".personaFisicaSolicitado select").removeAttr("required");
                $(".personaMoralSolicitado select").attr("required","");
                $(".personaMoralSolicitado").show();
                $(".personaFisicaSolicitado").hide();
                $(".personaFisicaSolicitadoNO").hide();
            }
        });


        if(edit){
            $("#solicitud_id").val(solicitud);
            $("#solicitud_id_modal").val(solicitud);
            $("#solicitud_id_excepcion").val(solicitud);
            cargarDocumentos();
            getSolicitudFromBD(solicitud);
        }else{
            if(localStorage.getItem("datos_laborales")){
                var datos_laborales_storage = localStorage.getItem("datos_laborales");
                datos_laborales_storage = JSON.parse(datos_laborales_storage);
                $("#periodicidad_id").val(datos_laborales_storage.periodicidad_id).trigger('change');
                $("#remuneracion").val(datos_laborales_storage.remuneracion);
                $("#ocupacion_id").val(datos_laborales_storage.ocupacion_id).trigger('change');
                $("#fecha_ingreso").val(datos_laborales_storage.fecha_ingreso);
                if(datos_laborales_storage.labora_actualmente != $("#labora_actualmente").is(":checked")){
                    $("#labora_actualmente").click();
                }
                $("#fecha_salida").val(datos_laborales_storage.fecha_salida);
            }
        }
        // getGironivel("",1,"girosNivel1solicitante");
        if($("#tipo_solicitud_id").val() == 4){
            $("#labelTipoSolicitante").text("(Sindicato)")
            $("#divTipoPersona").hide();
            $("#tipo_persona_moral_solicitante").prop("checked", true).trigger('change');
            $(".sindicato").show();
            $("#registro_sindical").attr("required",true);
        }else if($("#tipo_solicitud_id").val() == 3){
            $("#labelTipoSolicitante").text("(Patron colectiva)")
        }else if($("#tipo_solicitud_id").val() == 2){
            $("#labelTipoSolicitante").text("(Patron individual)")
        }else if($("#tipo_solicitud_id").val() == 1){
            $("#labelTipoSolicitante").text("(Trabajador)")
        }
    });
    function exepcionConciliacion(){
        var formData = new FormData();

        $.ajax({
            url:'/solicitud/excepcion',
            type:'POST',
            dataType:"json",
            contentType: false,
            processData: false,
            data:{
                arraySolicitanteExcepcion:arraySolicitanteExcepcion,
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
                swal({
                    title: 'Error',
                    text: ' Error al guardar excepción',
                    icon: 'error'
                });
            }
        });
    }

    function fortarTablaGrupoPrioritario(){
        var html = "";
        // $.each(arraySolicitantes, function (key, value) {
        //     if(value.grupo_prioritario_id != null && value.tipo_persona_id == 1){
        //         html += "<tr>";
        //             console.log(value);
        //         html += "<td>"+value.nombre + " " + value.primer_apellido + " " + value.segundo_apellido+"</td>";
        //         $("#grupo_prioritario_id_solicitante").val(value.grupo_prioritario_id);
        //         html += "<td>"+$("#grupo_prioritario_id_solicitante option:selected").text()+"</td>";
        //         $("#grupo_prioritario_id_solicitante").val("");
        //         html += "<td> <span class='btn btn-primary fileinput-button m-r-3'><i class='fa fa-fw fa-plus'></i><span>Agregar...</span><input type='file' accept='.pdf' name='"+value.id+"' class='fileGrupoVulnerable' idsolicitante='"+value.id+"' id='fileGrupoPrioritario"+value.id+"' multiple></span><label id='fileName"+value.id+"'></label></td>";
        //         html += "</tr>";
        //     }
        // });
        $("#tbodyGruposPrioritarios").html(html);
        $(".fileGrupoVulnerable").change(function(e){
            var id = $(this).attr("idsolicitante");
            $("#fileName"+id).html(e.target.files[0].name);
            var solicitanteExcepcion = {};
            solicitanteExcepcion.file = e.target.files[0];
            solicitanteExcepcion.id = $(this).attr("idsolicitante");
            solicitanteExcepcion.conciliador_id = $("#conciliador_excepcion_id").val();
            arraySolicitanteExcepcion[$(this).attr("idsolicitante")] = solicitanteExcepcion;
        });
    }
    function getSolicitudFromBD(solicitud){
        $.ajax({
            url:'/solicitudes/'+solicitud,
            type:"GET",
            dataType:"json",
            async:false,
            data:{},
            success:function(data){
                try{

                    arraySolicitados = Object.values(data.solicitados);
                    formarTablaSolicitado();
                    arraySolicitantes = Object.values(data.solicitantes);
                    $.each(arraySolicitantes ,function(key,value){
                        if($.isArray(arraySolicitantes[key].dato_laboral)){
                            arraySolicitantes[key].dato_laboral = arraySolicitantes[key].dato_laboral[0];
                        }
                    })
                    formarTablaSolicitante();
                    fortarTablaGrupoPrioritario()

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

                    if(data.solicita_excepcion){
                        $("#solicita_excepcion").prop("checked",true);
                    }
                    if(data.estatus_solicitud_id == 2){
                        $("#btnRatificarSolicitud").hide();
                    }
                    if(data.estatus_solicitud_id == 3){
                        $(".solicitudTerminada").hide();
                    }

                    $("#fechaRatificacion").val(dateFormat(data.fecha_ratificacion,2));
                    $("#fechaRecepcion").val(dateFormat(data.fecha_recepcion,2));
                    $("#fechaConflicto").val(dateFormat(data.fecha_conflicto,4));
                    // var excepcion = false;
                    // $.each(arraySolicitantes,function(key,value){
                    //     if(value.grupo_prioritario_id != null){
                    //         excepcion = true;
                    //     }
                    // }) ;
                    // console.log(excepcion);
                    $(".step-6").show();
                    $('#wizard').smartWizard("stepState", [5], "show");
                    if(data.ratificada){
                        $("#ratificada").prop("checked",true);
                        $("#btnRatificarSolicitud").hide();
                        $("#expediente_id").val(data.expediente.id);
                        $(".step-5").show();
                        $('#wizard').smartWizard("stepState", [4], "show");
                        // if(excepcion){
                        //     $(".step-4").show();
                        //     $('#wizard').smartWizard("stepState", [3], "show");
                        // }else{
                        $(".step-4").hide();
                        $('#wizard').smartWizard("stepState", [3], "hide");
                        // }
                    }else{
                        $('#wizard').smartWizard("stepState", [3], "hide");
                        $('#wizard').smartWizard("stepState", [4], "hide");
                        $(".step-5").hide();
                        $(".step-4").hide();
                        $("#btnRatificarSolicitud").show();
                        $("#expediente_id").val("");
                    }
                    cargarGeneros();
                    cargarTipoContactos();
                }catch(error){
                    console.log(error);
                }
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
            $("#nombre_prestas_servicio").val("");
            $("#nombre_paga").val("");
            $("#nombre_contrato").val("");
            $("#ocupacion_id").val("");
            $("#puesto").val("");
            $("#nss").val("");
            $("#no_issste").val("");
            $("#remuneracion").val("");
            $("#periodicidad_id").val("");
            $("#labora_actualmente").prop("checked", false);
            $("#fecha_ingreso").val("");
            $("#fecha_salida").val("");
            $("#jornada_id").val("");
            $("#horas_semanales").val("");
            $("#genero_id_solicitante").val("");
            $("#nacionalidad_id_solicitante").val("");
            $("#entidad_nacimiento_id_solicitante").val("");
            $("#lengua_indigena_id_solicitante").val("");
            // $("#motivo_excepciones_id_solicitante").val("");
            if($("#solicita_traductor_solicitante").is(":checked")){
                $("#solicita_traductor_solicitante").trigger('click');
            }
            $("#agregarSolicitante").html('<i class="fa fa-plus-circle"></i> Agregar solicitante');
            // getGironivel("",1,"girosNivel1solicitante");
            $("#giro_comercial_solicitante").val("").trigger("change");
            // $("#girosNivel1solicitante").trigger("change");
            $("#giro_solicitante").html("");
            $("input[name='tipo_persona_solicitante']").trigger("change")
            arrayContactoSolicitantes = [];
            formarTablaContacto(true);
            $('.catSelect').trigger('change');
            domicilioObj.limpiarDomicilios();
            $('#step-1').parsley().reset();
            $("#editandoSolicitante").html("");
            $("#botonAgregarSolicitante").show();
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

            $("#agregarSolicitado").html('<i class="fa fa-plus-circle"></i> Agregar citado');
            arrayContactoSolicitados = [];;
            arrayDomiciliosSolicitado = [];
            formarTablaDomiciliosSolicitado();
            formarTablaContacto();
            $('.catSelect').trigger('change');
            domicilioObj2.limpiarDomicilios();
            $('#step-2').parsley().reset();
            $("#editandoSolicitado").html("");
            $("#botonAgregarSolicitado").show();
        }

    function agregarContactoSolicitante(){
        if($("#contacto_solicitante").val() != "" && $("#tipo_contacto_id_solicitante").val() != ""){
            var contactoVal = $("#contacto_solicitante").val();
            if($("#tipo_contacto_id_solicitante").val() == 3){

                if(!validateEmail(contactoVal)){
                    swal({
                        title: 'Error',
                        text: 'El correo no tiene la estructura correcta',
                        icon: 'error',

                    });
                    return false;
                }
            }else{
                if(!/^[0-9]{10}$/.test(contactoVal)){
                    swal({
                        title: 'Error',
                        text: 'El contacto debe tener 10 digitos de tipo numero',
                        icon: 'error',

                    });
                    return false;
                }
            }
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
        }else{
            swal({
                title: 'Error',
                text: 'Los campos Tipo de contacto y Contacto son obligatorios',
                icon: 'error',

            });
        }
    }
    function limpiarContactoSolicitante(){
        $("#tipo_contacto_id_solicitante").val("");
        $("#tipo_contacto_id_solicitante").trigger('change');
        $("#contacto_solicitante").val("");
    }


    function agregarContactoSolicitado(){
        if($("#contacto_solicitado").val() != "" && $("#tipo_contacto_id_solicitado").val() != ""){
            var contactoVal = $("#contacto_solicitado").val();
            if($("#tipo_contacto_id_solicitado").val() == 3){

                if(!validateEmail(contactoVal)){
                    swal({
                        title: 'Error',
                        text: 'El correo no tiene la estructura correcta',
                        icon: 'error',

                    });
                    return false;
                }
            }else{
                if(!/^[0-9]{10}$/.test(contactoVal)){
                    swal({
                        title: 'Error',
                        text: 'El contacto debe tener 10 digitos de tipo numero',
                        icon: 'error',

                    });
                    return false;
                }
            }

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
            limpiarContactoSolicitado();
        }else{
            swal({
                title: 'Error',
                text: 'Los campos Tipo de contact y Contacto son obligatorios',
                icon: 'error',

            });
        }

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
        try{
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
                    html += "<td style='text-align: center;'><a class='btn btn-xs btn-danger' onclick='eliminarContactoSol("+key+","+solicitante+")' ><i class='fa fa-trash'></i></a></td>";
                    html += "</tr>";
                }
            });
            $("#tipo_contacto_id_solicitante").val("");
            if(solicitante){
                $("#tbodyContactoSolicitante").html(html);
            }else{
                $("#tbodyContactoSolicitado").html(html);
            }
        }catch(error){
            console.log(error);
        }
    }

    function eliminarContactoSol(key, solicitante){
        try{

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
        }catch(error){
            console.log(error);
        }
    }

    /**
    * Funcion para generar tabla a partir de array de solicitantes
    */
    function formarTablaObjetoSol(){

        var html = "";

        $("#tbodyObjetoSol").html("");
        $("#tbodyObjetoSolRevision").html("");

        $.each(arrayObjetoSolicitudes, function (key, value) {
            if(value.activo == "1" || (value.id != "" && typeof value.activo == "undefined" )){
                html += "<tr>";
                $("#objeto_solicitud_id").val(value.objeto_solicitud_id);
                html += "<td> " + $("#objeto_solicitud_id :selected").text(); + " </td>";
                html += "<td style='text-align: center;'><a class='btn btn-xs btn-danger' onclick='eliminarObjetoSol("+key+")' ><i class='fa fa-trash'></i></a></td>";
                html += "</tr>";
            }
        });
        $("#objeto_solicitud_id").val("");
        $("#tbodyObjetoSol").html(html);
        $("#tbodyObjetoSolRevision").html(html);
    }
    /**
    * Funcion para generar tabla a partir de array de solicitantes
    */
    function formarTablaSolicitante(){

        var html = "";

        $("#tbodySolicitante").html("");
        $("#tbodySolicitanteRevision").html("");

        $.each(arraySolicitantes, function (key, value) {
            if(value.activo == "1"){
                html += "<tr>";
                if(value.tipo_persona_id == 1){
                    html += "<td>" + value.nombre + " " + value.primer_apellido + " " + (value.segundo_apellido|| "") + "</td>";
                }else{
                    html += "<td> " + value.nombre_comercial + " </td>";
                }

                if(value.tipo_persona_id == 1){
                    html += "<td> " + value.curp + " </td>";
                }else{
                    html += "<td></td>";
                }
                if(value.rfc){
                    html += "<td> " + value.rfc + " </td>";
                }else{
                    html += "<td></td>";
                }

                html += "<td style='text-align: center;'><a class='btn btn-xs btn-primary' onclick='cargarEditarSolicitante("+key+")'><i class='fa fa-pencil-alt'></i></a> ";
                html += "<a class='btn btn-xs btn-danger' onclick='eliminarSolicitante("+key+")' ><i class='fa fa-trash'></i></a></td>";
                html += "</tr>";
            }
        });
        $("#tbodySolicitante").html(html);
        $("#tbodySolicitanteRevision").html(html);
    }

    /**
    * Funcion para generar tabla a partir de array de solicitados
    */
    function formarTablaSolicitado(){
        var html = "";

        $("#tbodySolicitado").html("");
        $("#tbodySolicitadoRevision").html("");

        $.each(arraySolicitados, function (key, value) {
            if(value.activo == "1"){
                html += "<tr>";
                if(value.tipo_persona_id == 1){
                    html += "<td>" + value.nombre + " " + value.primer_apellido + " " + (value.segundo_apellido || "") + "</td>";
                }else{
                    html += "<td> " + value.nombre_comercial + " </td>";
                }

                if(value.tipo_persona_id == 1){
                    html += "<td> " + (value.curp || "") + " </td>";
                }else{
                    html += "<td></td>";
                }
                if(value.rfc){
                    html += "<td> " + value.rfc + " </td>";
                }else{
                    html += "<td></td>";
                }

                html += "<td style='text-align: center;'><a class='btn btn-xs btn-primary' onclick='cargarEditarSolicitado("+key+")'><i class='fa fa-pencil-alt'></i></a> ";
                html += "<a class='btn btn-xs btn-danger' onclick='eliminarSolicitado("+key+")' ><i class='fa fa-trash'></i></a></td>";
                html += "</tr>";
            }
        });
        $("#tbodySolicitado").html(html);
        $("#tbodySolicitadoRevision").html(html);
    }

    /**
    * Funcion para eliminar el solicitante
    *@argument key posicion de array a eliminar
    */
    function eliminarSolicitante(key){
        $("#paso1").click();
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
        $("#paso2").click();
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
        $('#divContactoSolicitante').show();
        $('#divMapaSolicitante').show();
        $('#divBotonesSolicitante').show();
        $("#paso1").click();
        $("#agregarSolicitante").html('<i class="fa fa-edit"></i> Validar y Editar solicitante');
        $("#edit_key").val(key);
        $("#solicitante_id").val(arraySolicitantes[key].id);
        if(arraySolicitantes[key].tipo_persona_id == 1){
            $("#editandoSolicitante").html("<center><h3>Editando a "+ arraySolicitantes[key].nombre+" "+arraySolicitantes[key].primer_apellido+" "+(arraySolicitantes[key].segundo_apellido|| "")+ "</h3></center>");
            $("#idNombreSolicitante").val(arraySolicitantes[key].nombre);
            $("#idPrimerASolicitante").val(arraySolicitantes[key].primer_apellido);
            $("#idSegundoASolicitante").val((arraySolicitantes[key].segundo_apellido|| ""));
            $("#idFechaNacimientoSolicitante").val(dateFormat(arraySolicitantes[key].fecha_nacimiento,4));
            $("#idSolicitanteCURP").val(arraySolicitantes[key].curp);
            $("#genero_id_solicitante").val(arraySolicitantes[key].genero_id);
            $("#idEdadSolicitante").val(arraySolicitantes[key].edad);
            $("#nacionalidad_id_solicitante").val(arraySolicitantes[key].nacionalidad_id);
            $("#entidad_nacimiento_id_solicitante").val(arraySolicitantes[key].entidad_nacimiento_id);
            $("#lengua_indigena_id_solicitante").val(arraySolicitantes[key].lengua_indigena_id);
            // $("#grupo_prioritario_id_solicitante").val(arraySolicitantes[key].grupo_prioritario_id);
            // $("#motivo_excepciones_id_solicitante").val(arraySolicitantes[key].motivo_excepciones_id);
            if(arraySolicitantes[key].solicita_traductor == 1){
                if(!$("#solicita_traductor_solicitante").is(":checked")){
                    $("#solicita_traductor_solicitante").trigger('click');
                }
            }else{
                if($("#solicita_traductor_solicitante").is(":checked")){
                    $("#solicita_traductor_solicitante").trigger('click');
                }
            }
            $("#tipo_persona_fisica_solicitante").prop("checked", true).trigger('change');
            $(".personaMoralSolicitante").hide();
            $(".personaFisicaSolicitante").show();
        }else{
            alert();
            $("#editandoSolicitante").html("<center><h3>Editando a "+ arraySolicitantes[key].nombre_comercial+ "</h3></center>");
            $(".personaMoralSolicitante").show();
            $(".personaFisicaSolicitante").hide();
            $("#tipo_persona_moral_solicitante").prop("checked", true).trigger('change');
            $("#idNombreCSolicitante").val(arraySolicitantes[key].nombre_comercial);
        }
        $("#idSolicitanteRfc").val(arraySolicitantes[key].rfc);
        // datos laborales en la solicitante
        if(arraySolicitantes[key].dato_laboral != undefined){
            if($.isArray(arraySolicitantes[key].dato_laboral)){
                arraySolicitantes[key].dato_laboral = arraySolicitantes[key].dato_laboral[0];
            }
            $("#dato_laboral_id").val(arraySolicitantes[key].dato_laboral.id);
            $('#divDatoLaboralSolicitante').show();
            // $("#giro_comercial_solicitante").val(arraySolicitantes[key].dato_laboral.giro_comercial_id).trigger("change");
            $("#giro_comercial_hidden").val(arraySolicitantes[key].dato_laboral.giro_comercial_id)
            $("#giro_solicitante").html("<b> *"+$("#giro_comercial_hidden :selected").text() + "</b>");
            // getGiroEditar("solicitante");
            $("#nombre_jefe_directo").val(arraySolicitantes[key].dato_laboral.nombre_jefe_directo);
            $("#nombre_prestas_servicio").val(arraySolicitantes[key].dato_laboral.nombre_prestas_servicio);
            $("#nombre_paga").val(arraySolicitantes[key].dato_laboral.nombre_paga);
            $("#nombre_contrato").val(arraySolicitantes[key].dato_laboral.nombre_contrato);
            $("#ocupacion_id").val(arraySolicitantes[key].dato_laboral.ocupacion_id);
            $("#puesto").val(arraySolicitantes[key].dato_laboral.puesto);
            $("#nss").val(arraySolicitantes[key].dato_laboral.nss);
            $("#no_issste").val(arraySolicitantes[key].dato_laboral.no_issste);
            $("#remuneracion").val(arraySolicitantes[key].dato_laboral.remuneracion);
            $("#periodicidad_id").val(arraySolicitantes[key].dato_laboral.periodicidad_id);
            if(arraySolicitantes[key].dato_laboral.labora_actualmente != $("#labora_actualmente").is(":checked")){
                $("#labora_actualmente").click();
            }
            $("input[name='tipo_persona_solicitante']").trigger("change");
            $("#fecha_ingreso").val(dateFormat(arraySolicitantes[key].dato_laboral.fecha_ingreso,4));
            $("#fecha_salida").val(dateFormat(arraySolicitantes[key].dato_laboral.fecha_salida,4));
            $("#jornada_id").val(arraySolicitantes[key].dato_laboral.jornada_id);
            $("#horas_semanales").val(arraySolicitantes[key].dato_laboral.horas_semanales);
        }else{
            $(".requiredLaboral").removeAttr('required');
        }
        arrayContactoSolicitantes = arraySolicitantes[key].contactos ? arraySolicitantes[key].contactos : [];
        formarTablaContacto(true);
        //domicilio del solicitante
        domicilioObj.cargarDomicilio(arraySolicitantes[key].domicilios[0]);
        $('.catSelect').trigger('change');
        $("#botonAgregarSolicitante").hide();
    }

    /**
    * Funcion para editar el solicitante
    *@argument key posicion de array a editar
    */
    function cargarEditarSolicitado(key){
        $('#divContactoSolicitado').show();
        $('#divMapaSolicitado').show();
        $('#divBotonesSolicitado').show();
        $("#paso2").click();
        $("#agregarSolicitado").html('<i class="fa fa-edit"></i> Editar citado');
        $("#solicitado_key").val(key);
        $("#solicitado_id").val(arraySolicitados[key].id);
        // Si tipo persona es fisica o moral llena diferentes campos
        if(arraySolicitados[key].tipo_persona_id == 1){
            $("#editandoSolicitado").html("<center><h3>Editando a "+ arraySolicitados[key].nombre+" "+arraySolicitados[key].primer_apellido+" "+(arraySolicitados[key].segundo_apellido|| "")+ "</h3></center>");
            $("#idNombreSolicitado").val(arraySolicitados[key].nombre);
            $("#idPrimerASolicitado").val(arraySolicitados[key].primer_apellido);
            $("#idSegundoASolicitado").val((arraySolicitados[key].segundo_apellido|| ""));
            $("#idFechaNacimientoSolicitado").val(dateFormat(arraySolicitados[key].fecha_nacimiento,4));
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
            $("#editandoSolicitado").html("<center><h3>Editando a "+ arraySolicitados[key].nombre_comercial+ "</h3></center>");
            $(".personaMoralSolicitado").show();
            $(".personaFisicaSolicitado").hide();
            $("#idNombreCSolicitado").val(arraySolicitados[key].nombre_comercial);
            $("#tipo_persona_moral_solicitado").prop("checked", true);
        }
        $("#idSolicitadoRfc").val(arraySolicitados[key].rfc);
        $("input[name='tipo_persona_solicitado']").trigger("change");
        arrayContactoSolicitados = arraySolicitados[key].contactos ? arraySolicitados[key].contactos : [];
        formarTablaContacto();
        // arrayContactoSolicitados = arraySolicitados[key].contactos;
        arrayDomiciliosSolicitado = arraySolicitados[key].domicilios;
        cargarEditarDomicilioSolicitado(0);
        formarTablaDomiciliosSolicitado();
        $('.catSelect').trigger('change');
        $("#botonAgregarSolicitado").hide();
    }

    /**
    * Funcion para editar el domicilio del solicitante
    *@argument key posicion de array a editar
    */
    function cargarEditarDomicilioSolicitado(key){
        $("#domicilio_edit").val(key)
        domicilioObj2.cargarDomicilio(arrayDomiciliosSolicitado[key]);
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
                html += "<td style='text-align: center;'><a class='btn btn-xs btn-primary' onclick='cargarEditarDomicilioSolicitado("+key+")' ><i class='fa fa-pencil-alt'></i> </a> <a class='btn btn-xs btn-danger' onclick='eliminarDomicilio("+key+")' ><i class='fa fa-trash btn-danger'></i></a></td>";
                html += "</tr>";
            }

        });
        $("#tbodyDomicilioSolicitado").html(html);
    }

    /**
    * Funcion para agregar Domicilio de solicitante y solicitado
    */
    function agregarDomicilio(){
        if($("#estado_idsolicitado").val() != "" && $("#municipiosolicitado").val() != "" && $("#cpsolicitado").val() != "" && $("#asentamientosolicitado").val() != "" && $("#tipo_vialidad_idsolicitado").val() != "" && $("#vialidadsolicitado").val() != "" && $("#num_extsolicitado").val() != ""){
            key = $("#domicilio_edit").val();

            // if(key == ""){
            //     arrayDomiciliosSolicitado.push(domicilioObj2.getDomicilio());
            // }else{
                arrayDomiciliosSolicitado[0] = domicilioObj2.getDomicilio();
            // }

            formarTablaDomiciliosSolicitado();
            $('#modal-domicilio').modal('hide');
            domicilioObj2.limpiarDomicilios();
        }else{
            swal({
                title: 'Error',
                text: 'Es necesario llenar los campos obligatorios',
                icon: 'error'
            });
        }
    }
    /**
    * Funcion para agregar Domicilio de solicitante y solicitado
    */
    function agregarObjetoSol(){
        var objeto = $("#objeto_solicitud_id").val();
        $("#objeto_solicitud_id").val("").trigger("change");
        if(objeto != ""){
            registrado = false;
            $.each(arrayObjetoSolicitudes,function(index,value){
                if(value.objeto_solicitud_id == objeto){
                    swal({
                        title: 'Error',
                        text: 'El objeto ya esta registrado',
                        icon: 'error'
                    });
                    registrado = true;
                }
            });
            if(!registrado){

                var objeto_solicitud = {};
                objeto_solicitud.id = "";
                objeto_solicitud.objeto_solicitud_id = objeto;
                objeto_solicitud.activo = 1;
                arrayObjetoSolicitudes.push(objeto_solicitud);
                formarTablaObjetoSol();
            }
        }
    }

    /**
    * Funcion para guardar solicitud
    */
    function guardarSolicitud(){
        try{
            //funcion para obtener informacion de la solicitud
            var solicitud = getSolicitud();
            //funcion para obtener informacion de la excepcion
            var excepcion = getExcepcion();
            //Se llama api para guardar solicitud
            if($('#step-3').parsley().validate() && arraySolicitados.length > 0 && arraySolicitantes.length > 0 && $("#countObservaciones").val() <= 200 ){

                var upd = "";
                if($("#solicitud_id").val() == ""){
                    method = "POST";
                }else{
                    method = "PUT";
                    upd = "/"+$("#solicitud_id").val();
                }
                var externo = "";
                if($("#externo").val() == 1){
                    externo = "/store-public";
                }

                $.ajax({
                    url:'/solicitudes'+externo+upd,
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
                            // setTimeout('', 5000);
                            // location.href='{{ route('solicitudes.index')  }}'
                            $("#solicitud_id").val(data.data.id);
                            getDocumentoAcuse();
                        }else{

                        }

                    },error:function(data){
                        var mensajes = "";
                        $.each(data.responseJSON.errors, function (key, value) {
                            var origen = key.split(".");

                            mensajes += "- "+value[0]+ " del "+origen[0].slice(0,-1)+" "+(parseInt(origen[1])+1)+" \n";
                        });
                        if(mensajes != ""){
                            swal({
                                title: 'Error',
                                text: 'Es necesario validar los siguientes campos \n'+mensajes,
                                icon: 'error'
                            });
                        }else{
                            swal({
                                title: 'Error',
                                text: ' Error al capturar la solicitud',
                                icon: 'error'
                            });
                        }
                    }
                });
            }else{
                if($("#countObservaciones").val() > 200){
                    swal({
                        title: 'Error',
                        text: 'La descripción de los hechos debe tener menos de 200 palabras',
                        icon: 'error'
                    });
                }else{
                    swal({
                        title: 'Error',
                        text: 'Es necesario capturar al menos un solicitante, un citado y datos de la solicitud',
                        icon: 'error'
                    });
                }
            }
        }catch(error){
            console.log(error);
        }
    }

    //funcion para obtener informacion de la solicitud
    function getSolicitud(){
        try{
            var solicitud = {};
            solicitud.id = $("#solicitud_id").val();
            solicitud.observaciones = $("#observaciones").val();
            solicitud.ratificada = $("#ratificada").is(":checked");
            solicitud.solicita_excepcion = $("#solicita_excepcion").is(":checked");
            solicitud.fecha_ratificacion = dateFormat($("#fechaRatificacion").val(),3);
            solicitud.fecha_recepcion = dateFormat($("#fechaRecepcion").val(),3);
            solicitud.fecha_conflicto = dateFormat($("#fechaConflicto").val());
            solicitud.tipo_solicitud_id = $("#tipo_solicitud_id").val();
            return solicitud;
        }catch(error){
            console.log(error);
        }
    }

    // Funcion para ratificar solicitudes
    $("#btnRatificarSolicitud").on("click",function(){
        try{
            cargarDocumentos();
            var solicitanteMenor = arraySolicitantes.filter(x=>x.edad <= 16).filter(x=>x.edad != null);
            var solicitanteMoral = arraySolicitantes.filter(x=>x.tipo_persona_id == "2");
            if(solicitanteMenor.length > 0 || solicitanteMoral.length > 0){
                $("#divNeedRepresentante").show();
                var html = "";
                console.log(solicitanteMenor);
                $.each(solicitanteMenor,function(key,parte){
                    html += "<tr>";
                    html += "<td>"+parte.nombre + " " + parte.primer_apellido + " " + (parte.segundo_apellido|| "")+"</td>";
                    html += "<td><button class='btn btn-primary' type='button' onclick='AgregarRepresentante("+parte.id+",1)' id='btnaddRep"+parte.id+"' > <i class='fa fa-plus-circle'></i> Agregar Representante</button> <span style='color:green; font-size:Large;' id='tieneRepresentante"+parte.id+"'></span></td>";
                    html += "</tr>";
                });
                $.each(solicitanteMoral,function(key,parte){
                    html += "<tr>";
                    html += "<td>"+parte.nombre_comercial +"</td>";
                    html += "<td><button class='btn btn-primary' type='button' onclick='AgregarRepresentante("+parte.id+",0)' id='btnaddRep"+parte.id+"' > <i class='fa fa-plus-circle'></i> Agregar Representante</button> <span style='color:green; font-size:Large;' id='tieneRepresentante"+parte.id+"'></span></td>";
                    html += "</tr>";
                });
                $("#tbodyRepresentante").html(html);
            }else{
                    $("#divNeedRepresentante").hide();
            }
            $("#modalRatificacion").modal("show");
//
        }catch(error){
            console.log(error);
        }
    });

    $("#btnGuardarRatificar").on("click",function(){
        if(ratifican){
            $.ajax({
                url:'/solicitud/correos/'+$("#solicitud_id").val(),
                type:'GET',
                dataType:"json",
                async:true,
                success:function(data){
                    if(data == null || data == ""){
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
                                    className: 'btn btn-danger',
                                    closeModal: true
                                }
                            }
                        }).then(function(isConfirm){
                            if(isConfirm){
                                $.ajax({
                                    url:'/solicitud/ratificar',
                                    type:'POST',
                                    dataType:"json",
                                    async:true,
                                    data:{
                                        id:$("#solicitud_id").val(),
                                        _token:"{{ csrf_token() }}"
                                    },
                                    success:function(data){
                                        if(data != null && data != ""){
                                            $("#modalRatificacion").modal("hide");
                                            swal({
                                                title: 'Correcto',
                                                text: 'Solicitud ratificada correctamente',
                                                icon: 'success'
                                            });
                                            location.reload();
                                        }else{
                                            swal({
                                                title: 'Error',
                                                text: 'No se pudo ratificar',
                                                icon: 'error'
                                            });
                                        }
                                    },error:function(data){
                                        swal({
                                            title: 'Error',
                                            text: ' Error al ratificar la solicitud',
                                            icon: 'error'
                                        });
                                    }
                                });
                            }
                        });
                    }else{
                        var tableSolicitantes = '';
                        $.each(data, function(index,element){
                            tableSolicitantes +='<tr>';
                            if(element.tipo_persona_id == 1){
                                tableSolicitantes +='<td>'+element.nombre+' '+element.primer_apellido+' '+(element.segundo_apellido|| "")+'</td>';
                            }else{
                                tableSolicitantes +='<td>'+element.nombre_comercial+'</td>';
                            }
                            tableSolicitantes += '  <td>';
                            tableSolicitantes += '      <div class="col-md-12">';
                            tableSolicitantes += '          <span class="text-muted m-l-5 m-r-20" for="checkCorreo'+element.id+'">Proporcionar accesos</span>';
                            tableSolicitantes += '          <input type="checkbox" class="checkCorreo" data-id="'+element.id+'" checked="checked" id="checkCorreo'+element.id+'" name="checkCorreo'+element.id+'" onclick="checkCorreo('+element.id+')"/>';
                            tableSolicitantes += '      </div>';
                            tableSolicitantes += '  </td>';
                            tableSolicitantes += '  <td>';
                            tableSolicitantes += '      <input type="text" class="form-control" disabled="disabled" id="correoValidar'+element.id+'">';
                            tableSolicitantes += '  </td>';
                            tableSolicitantes +='</tr>';
                        });
                        $("#tableSolicitantesCorreo tbody").html(tableSolicitantes);
                        $("#modal-registro-correos").modal("show");
                    }
                }
            });
        }else{
            swal({
                title: 'Error',
                text: 'Al menos un solicitante debe presentar documentos para ratificar',
                icon: 'warning'
            });
        }
    });
    $("#btnGuardarConvenio").on("click",function(){
        if(ratifican){
            $.ajax({
                url:'/solicitud/correos/'+$("#solicitud_id").val(),
                type:'GET',
                dataType:"json",
                async:true,
                success:function(data){
                    if(data == null || data == ""){
                        $("#modal-aviso-resolucion-inmediata").modal("show");
                    }else{
                        var tableSolicitantes = '';
                        $.each(data, function(index,element){
                            tableSolicitantes +='<tr>';
                            if(element.tipo_persona_id == 1){
                                tableSolicitantes +='<td>'+element.nombre+' '+element.primer_apellido+' '+(element.segundo_apellido|| "")+'</td>';
                            }else{
                                tableSolicitantes +='<td>'+element.nombre_comercial+'</td>';
                            }
                            tableSolicitantes += '  <td>';
                            tableSolicitantes += '      <div class="col-md-12">';
                            tableSolicitantes += '          <span class="text-muted m-l-5 m-r-20" for="checkCorreo'+element.id+'">Proporcionar accesos</span>';
                            tableSolicitantes += '          <input type="checkbox" class="checkCorreo" data-id="'+element.id+'" checked="checked" id="checkCorreo'+element.id+'" name="checkCorreo'+element.id+'" onclick="checkCorreo('+element.id+')"/>';
                            tableSolicitantes += '      </div>';
                            tableSolicitantes += '  </td>';
                            tableSolicitantes += '  <td>';
                            tableSolicitantes += '      <input type="text" class="form-control" disabled="disabled" id="correoValidar'+element.id+'">';
                            tableSolicitantes += '  </td>';
                            tableSolicitantes +='</tr>';
                        });
                        $("#tableSolicitantesCorreo tbody").html(tableSolicitantes);
                        $("#modal-registro-correos").modal("show");
                    }
                }
            });
        }else{
            swal({
                title: 'Error',
                text: 'Al menos un solicitante debe presentar documentos para ratificar',
                icon: 'warning'
            });
        }
    });
    $("#btnRatificarInmediata").on("click",function(){
        swal({
            title: '¿Estas seguro?',
            text: 'Al oprimir aceptar se creará un expediente y podra relizar la resolución inmediatamente',
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
                    className: 'btn btn-danger',
                    closeModal: true
                }
            }
        }).then(function(isConfirm){
            if(isConfirm){
                $.ajax({
                    url:'/solicitud/ratificar',
                    type:'POST',
                    dataType:"json",
                    async:true,
                    data:{
                        id:$("#solicitud_id").val(),
                        inmediata:true,
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            $("#modal-aviso-resolucion-inmediata").modal("hide");
                            $("#modalRatificacion").modal("hide");
                            swal({
                                title: 'Correcto',
                                text: 'Solicitud ratificada correctamente',
                                icon: 'success'
                            });
                            window.location.href = "/guiaAudiencia/"+data.id;
                        }else{
                            swal({
                                title: 'Error',
                                text: 'No se pudo ratificar',
                                icon: 'error'
                            });
                        }
                    },error:function(data){
                        console.log(data);
                        swal({
                            title: 'Error',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
    function continuarRatificacion(){
        $("#modalRatificacion").modal('show');
    }
    function checkCorreo(id){
        if(!$("#checkCorreo"+id).is(":checked")){
            $("#correoValidar"+id).prop("disabled",false);
        }else{
            $("#correoValidar"+id).prop("disabled",true);
        }
    }
    $("#btnGuardarCorreos").on("click",function(){
        var validacion = validarCorreos();
        if(!validacion.error){
            $.ajax({
                url:'/solicitud/correos',
                type:'POST',
                dataType:"json",
                async:true,
                data:{
                    _token:"{{ csrf_token() }}",
                    listaCorreos:validacion.listaCorreos
                },
                success:function(data){
                    $("#modal-registro-correos").modal("hide");
                },error:function(error){
                    swal({
                        title: 'Error',
                        text: 'Ocurrio un error al guardar los correos',
                        icon: 'warning'
                    });
                }
            });
        }else{
            swal({
                title: 'Error',
                text: 'Si no se desea generar accesos, se deben proporcionar los correos',
                icon: 'warning'
            });
        }
    });
    function validarCorreos(){
        var listaCorreos = [];
        var error = false;
        $.each($(".checkCorreo"),function(index,element){
            var id = $(element).data('id');
            $("#correoValidar"+id).css("border-color","");
            if($(element).is(":checked")){
                listaCorreos.push({
                    crearAcceso:true,
                    correo:"",
                    parte_id:id
                });
            }else{
                if($("#correoValidar"+id).val() != ""){
                    listaCorreos.push({
                        crearAcceso:false,
                        correo:$("#correoValidar"+id).val(),
                        parte_id:id
                    });
                }else{
                    error = true;
                    $("#correoValidar"+id).css("border-color","red");
                }
            }
        });
        var respuesta = new Array();
        respuesta.error=error;
        respuesta.listaCorreos=listaCorreos;
        return respuesta;
    }

    //funcion para obtener informacion de la excepcion
    function getExcepcion(){
        var excepcion = {};
        return excepcion;
    }

    function highlightText(string){
        return string.replace($("#term").val().trim(),'<span class="highlighted">'+$("#term").val().trim()+"</span>");
    }
    $("#giro_comercial_solicitante").select2({
        ajax: {
            url: '/externo/giros_comerciales/filtrarGirosComerciales',
            type:"POST",
            dataType:"json",
            delay: 1000,
            async:false,
            cache: true,
            data:function (params) {
                $("#term").val(params.term);
                var data = {
                    nombre: params.term,
                    _token:"{{ csrf_token() }}"
                }
                return data;
            },
            processResults:function(json){
                try{
                    $.each(json.data, function (key, node) {
                        var html = '';
                        html += '<table>';
                        var ancestors = node.ancestors.reverse();
                        html += '<tr><th colspan="2"><h5>* '+highlightText(node.nombre)+'</h5><th></tr>';
                        $.each(ancestors, function (index, ancestor) {
                            if(ancestor.id != 1){
                                var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(index);
                                html += '<tr><td ><b>'+ancestor.codigo+'</b></td>'+' <td style="border-left:1px solid;">'+tab+highlightText(ancestor.nombre)+'</td></tr>';
                            }
                        });
                        var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(node.ancestors.length);
                        html += '<tr><td><b>'+node.codigo+'</b></td>'+'<td style="border-left:1px solid;"> '+ tab+highlightText(node.nombre)+'</td></tr>';
                        html += '</table>';
                        json.data[key].html = html;
                    });
                    return {
                        results: json.data
                    };
                }catch(error){
                    console.log(error);
                }
            }
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            if(data.loading) return 'Buscando...';
            return data.html;
        },templateSelection: function(data) {
            if(data.id != ""){
                return "<b>"+data.codigo+"</b>&nbsp;&nbsp;"+data.nombre;
            }
            return data.text;
        },
        placeholder:'Seleccione una opción',
        minimumInputLength:4,
        allowClear: true,
        language: "es"
    });

    $("#giro_comercial_solicitante").change(function(){
        $("#giro_comercial_hidden").val($(this).val());
    });


    $("#solicita_traductor_solicitado").change(function(){
        if($("#solicita_traductor_solicitado").is(":checked")){
            $("#selectIndigenaSolicitado").show();
        }else{
            $("#selectIndigenaSolicitado").hide();
        }
    });

    $("#idFechaNacimientoSolicitante").change(function(){
        if($("#idFechaNacimientoSolicitante").val() != ""){
            var edad = Edad($("#idFechaNacimientoSolicitante").val());
            if(edad > 15){
                $("#idEdadSolicitante").val(edad);
            }else{
                $("#idFechaNacimientoSolicitante").val("")
                swal({
                    title: 'Error',
                    text: 'La edad debe ser mayor de 15 años',
                    icon: 'warning'
                });
            }
        }else{
            $("#idEdadSolicitante").val("");
        }
    });
    $("#idFechaNacimientoSolicitado").change(function(){
        if($("#idFechaNacimientoSolicitado").val() != ""){
            var edad = Edad($("#idFechaNacimientoSolicitado").val())
            if(edad > 18){
                $("#idEdadSolicitado").val(edad)
            }else{
                $("#idFechaNacimientoSolicitado").val("");
                swal({
                    title: 'Error',
                    text: 'La edad debe ser mayor de 18 años',
                    icon: 'warning'
                });
            }
        }else{
            $("#idEdadSolicitado").val("");
        }
    });
    $("#solicita_traductor_solicitante").change(function(){
        if($("#solicita_traductor_solicitante").is(":checked")){
            $("#selectIndigenaSolicitante").show();
        }else{
            $("#selectIndigenaSolicitante").hide();
        }
    });
    $(".catSelect").select2({width: '100%'});
    $(".dateBirth").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "c-80:",
        format:'dd/mm/yyyy',
    });
    $(".date").datepicker({useCurrent: false,format:'dd/mm/yyyy'});
    $(".dateTime").datetimepicker({useCurrent: false,format:'DD/MM/YYYY HH:mm:ss'});
    $(".date").keypress(function(event){
        event.preventDefault();
    });


    $('.upper').on('keyup', function () {
        var valor = $(this).val();
        $(this).val(valor.toUpperCase());
    });


/**
*  Aqui comienzan las funciones para carga de documentos de la solicitud
*/
    $("#btnAgregarArchivo").on("click",function(){
        $("#btnCancelFiles").click();
        $("#modal-archivos").modal("show");
    });
    function cargarDocumentos(){
        $.ajax({
            url:"/solicitudes/documentos/"+$("#solicitud_id").val(),
            type:"GET",
            dataType:"json",
            async:true,
            success:function(data){
                try{
                    if(data != null && data != ""){
                        //Carga información en la ratificacion
                        var html = "";
                   $.each(data, function (key, value) {
                       if(value.documentable_type == "App\\Parte"){
                            // var parte = arraySolicitantes.find(x=>x.id == value.documentable_id);
                            // if(parte != undefined){
                                html += "<tr>";
                                html += "<td>"+value.parte+"</td>";
                                html += "<td>"+value.nombre_original + " "+ value.clasificacion_archivo_id+"</td>";
                                html += "</tr>";
                                ratifican = true;
                            // }
                       }
                   });
                    $("#tbodyRatificacion").html(html);
                        // end carga ratificacion
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
                    }
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
    function getDocumentoAcuse(){
        $.ajax({
            url:"/solicitudes/documentos/"+$("#solicitud_id").val()+"/acuse",
            type:"GET",
            dataType:"json",
            async:true,
            success:function(data){
                try{
                    if(data != null && data != ""){
                        $("#btnGuardar").hide();
                        $("#btnAcuse").attr("href","/api/documentos/getFile/"+data[0].id)
                        $("#btnGetAcuse").show();
                    }
                }catch(error){
                    console.log(error);
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
            //   $("#modal-archivos").modal("hide");
            },uploadTemplate: function (o) {
                var rows = $();
                $.each(o.files, function (index, file) {
                    var row = $('<tr class="template-upload fade show">'+
                    '    <td>'+
                    '        <span class="preview"></span>'+
                    '    </td>'+
                    '    <td>'+
                    '        <div class="bg-light rounded p-10 mb-2">'+
                    '            <dl class="m-b-0">'+
                    '                <dt class="text-inverse">Nombre del documento:</dt>'+
                    '                <dd class="name">'+file.name+'</dd>'+
                    '                <dt class="text-inverse m-t-10">File Size:</dt>'+
                    '                <dd class="size">Processing...</dd>'+
                    '            </dl>'+
                    '        </div>'+
                    '        <strong class="error text-danger h-auto d-block text-left"></strong>'+
                    '    </td>'+
                    '    <td>'+
                    '        <select class="form-control catSelectFile" name="tipo_documento_id[]">'+
                    '            <option value="">Seleccione una opci&oacute;n</option>'+
                    '            @if(isset($clasificacion_archivo))'+
                    '                @foreach($clasificacion_archivo as $clasificacion)'+
                    '                    @if($clasificacion->tipo_archivo_id == 1 || $clasificacion->tipo_archivo_id == 9)'+
                    '                    <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>'+
                    '                    @endif'+
                    '                @endforeach'+
                    '            @endif'+
                    '        </select>'+
                    '    </td>'+
                    '    <td>'+
                    '        <select class="form-control catSelectFile parteClass" name="parte[]">'+
                    '            <option value="">Seleccione una opci&oacute;n</option>'+
                    '            @if(isset($solicitud))'+
                    '                @foreach($solicitud->partes as $parte)'+
                    '                    @if(($parte->tipo_parte_id == 1 || $parte->tipo_parte_id == 3) && $parte->tipo_persona_id == 1  )'+
                    '                        <option value="{{$parte->id}}">{{$parte->nombre_comercial}}{{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}</option>'+
                    '                    @endif'+
                    '                @endforeach'+
                    '            @endif'+
                    '        </select>'+
                    '    </td>'+
                    '    <td>'+
                    '        <dl>'+
                    '            <dt class="text-inverse m-t-3">Progress:</dt>'+
                    '            <dd class="m-t-5">'+
                    '                <div class="progress progress-sm progress-striped active rounded-corner"><div class="progress-bar progress-bar-primary" style="width:0%; min-width: 0px;">0%</div></div>'+
                    '            </dd>'+
                    '        </dl>'+
                    '    </td>'+
                    '    <td nowrap>'+
                    '            <button class="btn btn-primary start width-100 p-r-20 m-r-3" disabled>'+
                    '                <i class="fa fa-upload fa-fw text-inverse"></i>'+
                    '                <span>Guardar</span>'+
                    '            </button>'+
                    '    </td>'+
                    '    <td nowrap>'+
                    '            <button class="btn btn-default cancel width-100 p-r-20">'+
                    '                <i class="fa fa-trash fa-fw text-muted"></i>'+
                    '                <span>Cancel</span>'+
                    '            </button>'+
                    '    </td>'+
                    '</tr>');
                    if (file.error) {
                        row.find('.error').text(file.error);
                    }
                    rows = rows.add(row);
                });
                return rows;
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
        $('#fileupload').on('fileuploadsend', function (e, data) {

            // if(){
            //     e.preventDefault();
            // }
        })
        $('#fileupload').bind('fileuploadadd', function(e, data) {
            $('#fileupload [data-id="empty"]').hide();
            $(".catSelectFile").select2();
        });
        $('#fileupload').bind('fileuploaddone', function(e, data) {
            // console.log("add");
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
    function validarPalabras(e){
        var numeroPalabras = countPalabras(e);
        $("#numeroPalabras").html(numeroPalabras);
        $("#countObservaciones").val(numeroPalabras);
        if(numeroPalabras >= 201){
            $("#numeroPalabras").html("<span style='color:red'>"+numeroPalabras+"</span>");
        }
    }
    function pasoSolicitante(pasoActual){
        switch (pasoActual) {
            case 1:
                if($('#datosIdentificacionSolicitante').parsley().validate()){
                    $('#divContactoSolicitante').show();
                    $('#continuar1').hide();

                }
                break;
            case 2:
                if(arrayContactoSolicitantes.length > 0){
                    var tieneCorreo = arrayContactoSolicitantes.find(x=>x.tipo_contacto_id == 3);
                    if(tieneCorreo != undefined){
                        $('#divMapaSolicitante').show();
                        $('#continuar2').hide();
                    }else{
                        $("#modal_valida_correo").modal("show");
                    }
                }else{
                    swal({
                        title: 'Error',
                        text: 'Es necesario capturar al menos un contacto para continuar',
                        icon: 'error',
                    });
                }
            break;
            case 3:
                if($('#divMapaSolicitante').parsley().validate()){
                    if($("#tipo_solicitud_id").val() == 1){
                        $('#divDatoLaboralSolicitante').show();
                        $('#divBotonesSolicitante').show();
                        $(".requiredLaboral").attr('required',true);
                        $('#continuar3').hide();
                    }else{
                        $("#divDatoLaboralSolicitante").removeAttr('data-parsley-validate');
                        $(".requiredLaboral").removeAttr('required');
                        $('#divBotonesSolicitante').show();
                        $('#continuar3').hide();
                    }
                }
            break;
            default:
                break;
        }
    }
    function pasoSolicitado(pasoActual){
        switch (pasoActual) {
            case 1:
                if($('#datosIdentificacionSolicitado').parsley().validate()){
                    $('#divContactoSolicitado').show();
                    $('#continuarSolicitado1').hide();

                }
                break;
            case 2:
                $('#divMapaSolicitado').show();
                $('#continuarSolicitado2').hide();
            break;
            case 3:
                if($('#divMapaSolicitado').parsley().validate()){
                    $('#divBotonesSolicitado').show();
                    $('#continuarSolicitado3').hide();
                }else{
                    swal({
                        title: 'Error',
                        text: 'Es necesario capturar al menos un domicilio del citado',
                        icon: 'error',
                    });
                }
            break;
            default:
                break;
        }
    }

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
    $("#excepcionForm").submit(function(e){
        var falta = false;

        $(".fileGrupoVulnerable").each(function(e){
            if($(this).val() == ""){
                falta = true;
            }
        });
        if($("#conciliador_excepcion_id").val() == "" && falta){
            e.preventDefault();
        }
    });
    var listaContactos = [];
    function AgregarRepresentante(parte_id,tipoRepresentante){
        $.ajax({
            url:"/partes/representante/"+parte_id,
            type:"GET",
            dataType:"json",
            success:function(data){
                if(data != null && data != ""){
                    data = data[0];
                    $("#tieneRepresentante"+parte_id).html("<i class='fa fa-check'></i> ");
                    $("#btnaddRep"+parte_id).html("Ver Representante");
                    $("#curp").val(data.curp);
                    $("#nombre").val(data.nombre);
                    $("#primer_apellido").val(data.primer_apellido);
                    $("#segundo_apellido").val((data.segundo_apellido|| ""));
                    $("#fecha_nacimiento").val(dateFormat(data.fecha_nacimiento,4));
                    $("#genero_id").val(data.genero_id).trigger("change");
                    $("#clasificacion_archivo_id").val(data.clasificacion_archivo_id).change();
                    $("#feha_instrumento").val(dateFormat(data.feha_instrumento,4));
                    $("#detalle_instrumento").val(data.detalle_instrumento);
                    $("#parte_id").val(data.id);
                    listaContactos = data.contactos;
                }else{
                    $("#curp").val("");
                    $("#nombre").val("");
                    $("#primer_apellido").val("");
                    $("#segundo_apellido").val("");
                    $("#fecha_nacimiento").val("");
                    $("#genero_id").val("").trigger("change");
                    $("#clasificacion_archivo_id").val("").change();
                    $("#feha_instrumento").val("");
                    $("#detalle_instrumento").val("");
                    $("#parte_id").val("");
                    listaContactos = [];
                }
                $("#tipo_contacto_id").val("").trigger("change");
                $("#contacto").val("");
                $("#parte_representada_id").val(parte_id);
                if(tipoRepresentante == 1){
                    $("#menorAlert").show();
                    $("#representanteMoral").hide();
                }else{
                    $("#menorAlert").hide();
                    $("#representanteMoral").show();
                }
                cargarContactos();
                $("#modal-representante").modal("show");
            }
        });
    }
    function cargarGeneros(){
        $.ajax({
            url:"/generos",
            type:"GET",
            dataType:"json",
            success:function(data){
                $("#genero_id").html("<option value=''>-- Selecciona un género</option>");
                if(data.data.length > 0){
                    $.each(data.data,function(index,element){
                        $("#genero_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                    });
                }
                $("#genero_id").trigger("change");
            }
        });
    }

    function cargarTipoContactos(){
        $.ajax({
            url:"/tipos_contactos",
            type:"GET",
            dataType:"json",
            success:function(data){
                if(data.data.total > 0){
                    $("#tipo_contacto_id").html("<option value=''>-- Selecciona un tipo de contacto</option>");
                    $.each(data.data.data,function(index,element){
                        $("#tipo_contacto_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                    });
                }else{
                    $("#tipo_contacto_id").html("<option value=''>-- Selecciona un tipo de contacto</option>");
                }
                $("#tipo_contacto_id").trigger("change");
            }
        });
    }
    $("#btnAgregarContacto").on("click",function(){
        if($("#parte_id").val() != ""){
            $.ajax({
                url:"/partes/representante/contacto",
                type:"POST",
                dataType:"json",
                data:{
                    tipo_contacto_id:$("#tipo_contacto_id").val(),
                    contacto:$("#contacto").val(),
                    parte_id:$("#parte_id").val(),
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    if(data != null && data != ""){
                        listaContactos = data;
                        cargarContactos();
                    }else{
                        swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
                    }
                }
            });
        }else{
            listaContactos.push({
                tipo_contacto_id:$("#tipo_contacto_id").val(),
                contacto:$("#contacto").val(),
                id:null,
                tipo_contacto:{
                    nombre:$("#tipo_contacto_id option:selected").text()
                }
            });
        }
        cargarContactos();
    });
    function validarRepresentante(){
        var error=false;
        $(".control-label").css("color","");
        if($("#curp").val() == ""){
            $("#curp").prev().css("color","red");
            error = true;
        }
        if($("#nombre").val() == ""){
            $("#nombre").prev().css("color","red");
            error = true;
        }
        if($("#primer_apellido").val() == ""){
            $("#primer_apellido").prev().css("color","red");
            error = true;
        }
        if($("#segundo_apellido").val() == ""){
            $("#segundo_apellido").prev().css("color","red");
            error = true;
        }
        if($("#fecha_nacimiento").val() == ""){
            $("#fecha_nacimiento").prev().css("color","red");
            error = true;
        }
        if($("#genero_id").val() == ""){
            $("#genero_id").prev().css("color","red");
            error = true;
        }
        if($("#instrumento").val() == ""){
            $("#instrumento").prev().css("color","red");
            error = true;
        }
        if($("#feha_instrumento").val() == ""){
            $("#feha_instrumento").prev().css("color","red");
            error = true;
        }
        if($("#numero_notaria").val() == ""){
            $("#numero_notaria").prev().css("color","red");
            error = true;
        }
        if($("#nombre_notario").val() == ""){
            $("#nombre_notario").prev().css("color","red");
            error = true;
        }
        if($("#localidad_notaria").val() == ""){
            $("#localidad_notaria").prev().css("color","red");
            error = true;
        }
        if(listaContactos.length == 0){
            $("#contacto").prev().css("color","red");
            $("#tipo_contacto_id").prev().css("color","red");
            error = true;
            error = true;
        }
        return error;
    }

    function cargarContactos(){
        var table = "";
        $.each(listaContactos, function(index,element){
            table +='<tr>';
            table +='   <td>'+element.tipo_contacto.nombre+'</td>';
            table +='   <td>'+element.contacto+'</td>';
            table +='   <td style="text-align: center;">';
            table +='       <a class="btn btn-xs btn-warning" onclick="eliminarContacto('+index+')">'
            table +='           <i class="fa fa-trash" style="color:white;"></i>';
            table +='       </a>';
            table +='   </td>';
            table +='<tr>';
        });
        $("#tbodyContacto").html(table);
    }

    $("#btnGuardarRepresentante").on("click",function(){
        if(!validarRepresentante()){
            $.ajax({
                url:"/partes/representante",
                type:"POST",
                dataType:"json",
                data:{
                    clasificacion_archivo_id:$("#clasificacion_archivo_id").val(),
                    detalle_instrumento:$("#detalle_instrumento").val(),
                    parte_representada_id:$("#parte_representada_id").val(),
                    curp:$("#curp").val(),
                    nombre:$("#nombre").val(),
                    primer_apellido:$("#primer_apellido").val(),
                    segundo_apellido:$("#segundo_apellido").val(),
                    fecha_nacimiento:dateFormat($("#fecha_nacimiento").val()),
                    genero_id:$("#genero_id").val(),
                    instrumento:$("#instrumento").val(),
                    feha_instrumento:dateFormat($("#feha_instrumento").val()),
                    parte_id:$("#parte_id").val(),
                    parte_representada_id:$("#parte_representada_id").val(),
                    listaContactos:listaContactos,
                    fuente_solicitud:true,
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    if(data != null && data != ""){
                        $("#tieneRepresentante"+data.id).html("Correcto <i class='fa fa-check'></i> ");
                        $("#btnaddRep"+data.id).html("Ver Representante");
                        swal({title: 'Exito',text: 'Se agrego el representante',icon: 'success'});
                        actualizarPartes();
                        $("#modal-representante").modal("hide");
                    }else{
                        swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
                    }
                }
            });
        }else{
            swal({title: 'Error',text: 'Llena todos los campos',icon: 'warning'});
        }
    });

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
    function eliminarContacto(indice){
            if(listaContactos[indice].id != null){
                $.ajax({
                    url:"/partes/representante/contacto/eliminar",
                    type:"POST",
                    dataType:"json",
                    data:{
                        contacto_id:listaContactos[indice].id,
                        parte_id:$("#parte_id").val(),
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            listaContactos = data;
                            cargarContactos();
                        }else{
                            swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
                        }
                    }
                });
            }else{
                listaContactos.splice(indice,1);
                cargarContactos();
            }
        }
        function actualizarPartes(){
            $.ajax({
                url:"/partes/getComboDocumentos/{{isset($solicitud->id) ? $solicitud->id: '' }}",
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data != null && data != ""){
                        var html="";
                        $('#fileupload').fileupload({
                            uploadTemplate: function (o) {
                                var rows = $();
                                $.each(o.files, function (index, file) {
                                    var html= '<tr class="template-upload fade show">'+
                                    '    <td>'+
                                    '        <span class="preview"></span>'+
                                    '    </td>'+
                                    '    <td>'+
                                    '        <div class="bg-light rounded p-10 mb-2">'+
                                    '            <dl class="m-b-0">'+
                                    '                <dt class="text-inverse">Nombre del documento:</dt>'+
                                    '                <dd class="name">'+file.name+'</dd>'+
                                    '                <dt class="text-inverse m-t-10">File Size:</dt>'+
                                    '                <dd class="size">Processing...</dd>'+
                                    '            </dl>'+
                                    '        </div>'+
                                    '        <strong class="error text-danger h-auto d-block text-left"></strong>'+
                                    '    </td>'+
                                    '    <td>'+
                                    '        <select class="form-control catSelectFile" name="tipo_documento_id[]">'+
                                    '            <option value="">Seleccione una opci&oacute;n</option>'+
                                    '            @if(isset($clasificacion_archivo))'+
                                    '                @foreach($clasificacion_archivo as $clasificacion)'+
                                    '                    @if($clasificacion->tipo_archivo_id == 1 || $clasificacion->tipo_archivo_id == 9)'+
                                    '                    <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>'+
                                    '                    @endif'+
                                    '                @endforeach'+
                                    '            @endif'+
                                    '        </select>'+
                                    '    </td>'+
                                    '    <td>'+
                                    '        <select class="form-control catSelectFile parteClass" name="parte[]">'+
                                    '            <option value="">Seleccione una opci&oacute;n</option>'+
                                    '            @if(isset($solicitud))';
                                    $.each(data, function(index,element){
                                        if(element.tipo_persona_id == 1){
                                            html +='<option value="'+element.id+'">'+element.nombre+' '+element.primer_apellido+' '+(element.segundo_apellido|| "")+'</option>';
                                        }
                                        // else{
                                        //     html +='<option value="'+element.id+'">'+element.nombre_comercial+'</option>';
                                        //     // html +='<option value="'+element.id+'">'+element.nombre_comercial+'</option>';
                                        // }
                                    });
                                    html +='    @endif'+
                                    '        </select>'+
                                    '    </td>'+
                                    '    <td>'+
                                    '        <dl>'+
                                    '            <dt class="text-inverse m-t-3">Progress:</dt>'+
                                    '            <dd class="m-t-5">'+
                                    '                <div class="progress progress-sm progress-striped active rounded-corner"><div class="progress-bar progress-bar-primary" style="width:0%; min-width: 0px;">0%</div></div>'+
                                    '            </dd>'+
                                    '        </dl>'+
                                    '    </td>'+
                                    '    <td nowrap>'+
                                    '            <button class="btn btn-primary start width-100 p-r-20 m-r-3" disabled>'+
                                    '                <i class="fa fa-upload fa-fw text-inverse"></i>'+
                                    '                <span>Guardar</span>'+
                                    '            </button>'+
                                    '    </td>'+
                                    '    <td nowrap>'+
                                    '            <button class="btn btn-default cancel width-100 p-r-20">'+
                                    '                <i class="fa fa-trash fa-fw text-muted"></i>'+
                                    '                <span>Cancel</span>'+
                                    '            </button>'+
                                    '    </td>'+
                                    '</tr>';
                                    var row = $(html);
                                    if (file.error) {
                                        row.find('.error').text(file.error);
                                    }
                                    rows = rows.add(row);
                                });
                                return rows;
                            }
                        });
                    }else{
                        swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
                    }
                }
            });
        }


    $('[data-toggle="tooltip"]').tooltip();
</script>


<script src="/assets/plugins/highlight.js/highlight.min.js"></script>

@endpush

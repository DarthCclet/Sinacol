
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
    .ui-datepicker.ui-widget-content{
        z-index: 9000 !important;
    }

</style>
@if(auth()->user())
    <input type="hidden" id="externo" value="0">
@else
    <input type="hidden" id="externo" value="1">
@endif
<input type="hidden" id="instancia" value="{{ env('INSTACIA') ? env('INSTACIA') : 'federal'}}">
<div class="tab-content" style="background: #f2f3f4 !important;">
<div class="tab-pane fade active show" id="default-tab-1">
    <div id="wizard" class="col-md-12" >
        <!-- begin wizard-step -->
        <ul class="wizard-steps">
            <li >
                <a id="paso1" href="#step-1">
                    <span style="font-size:large;" class="">
                        Solicitud
                    </span>
                </a>
            </li>
            <li>
                <a id="paso2" href="#step-2">
                    <span style="font-size:large;" class="">
                        Solicitante
                    </span>
                </a>
            </li>
            <li>
                <a id="paso3" href="#step-3">
                    <span style="font-size:large;" class="">
                        Citado
                    </span>
                </a>
            </li>
            <li>
                <a id="paso4" href="#step-4">
                    <span style="font-size:large;" class="">
                        Revisi&oacute;n
                    </span>
                </a>
            </li>



            <!-- El paso 5 Es para asignar Audiencias -->
            {{-- <li class="step-5">
                <a id="paso5" href="#step-5">

                    <span class="">
                        Audiencias
                        <small>Audiencias de conciliación</small>
                    </span>
                </a>
            </li> --}}

            <!-- El paso 5 Es para asignar Audiencias -->
            {{-- <li class="step-6">
                <a id="paso6" href="#step-6">

                    <span class="">
                        Historial
                        <small>Historial de acciones</small>
                    </span>
                </a>
            </li> --}}
            <!-- El paso 5 Es para asignar Audiencias -->
            {{-- <li id="paso7" class="step-7">
                <a href="#step-7">

                    <span class="">
                        Documentos
                        <small>Documentos del expediente</small>
                    </span>
                </a>
            </li> --}}
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
                            <h1>Solicitud de <span id="labelTipoSolicitud"></span></h1>
                            <div class="col-md-12 mt-4">
                                <h2>Datos generales de la solicitud</h2>
                                <hr class="red">
                            </div>
                            <div style="margin: 2%;">
                                <h5>Nota: Los campos marcados con <span style="color: red;">(*)</span> son datos obligatorios, favor de proporcionarlos.</h5>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control date" required id="fechaConflicto" placeholder="Fecha de Conflicto" type="text" value="">
                                <p class="help-block needed">Fecha de conflicto</p>
                            </div>
                            <div class="col-md-12 row">
                                <div class="col-md-6">
                                    {!! Form::select('objeto_solicitud_id', isset($objeto_solicitudes) ? $objeto_solicitudes : [] , null, ['id'=>'objeto_solicitud_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                    {!! $errors->first('objeto_solicitud_id', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block needed">Objeto de la solicitud</p>
                                </div>
                                {{-- <div class="col-md-6">
                                    <button class="btn btn-primary" type="button" onclick="agregarObjetoSol()" id="btnObjetoSol" > <i class="fa fa-plus-circle"></i> Agregar Objeto</button>
                                </div> --}}
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
                            <div class="col-md-12">
                                <label>Rama industrial del negocio</label>
                                <div title="Escoge de la lista de ramas industriales principales." data-toggle="tooltip" data-placement="top">
                                    <p class="help-block "><span class="needed">Paso 1. Rama industrial</span></p>
                                <select id="girosNivel" class="form-control select-element">
                                    <option value="">- Selecciona una rama industrial</option>
                                    @foreach($giros as $cc)
                                    <option value="{{$cc->id}}">{{$cc->nombre}}</option>
                                    @endforeach
                                </select>

                                </div>
                            </div><br>
                            <div class="col-md-12 form-group row" id="divGiro" style="display:none;">
                                <input type="hidden" id="term">
                                <div class="col-md-12 " title="Teclea palabras claves que describen la actividad económica de tu patrón, y escoge de la lista disponible de actividades" data-toggle="tooltip" data-placement="top" >
                                    <p class="help-block "><span class="needed">Paso 2: Actividad económica del patrón</span></p>
                                    <select name="giro_comercial_solicitante" placeholder="Paso 2. Actividad económica del patrón" id="giro_comercial_solicitante" class="form-control"></select>
                                </div>
                                <div class="col-md-12">
                                    @if($tipo_solicitud_id == 1)
                                        <p class="help-block "><span class="needed">&iquest;Cuál es la actividad principal de tu patrón?</span> <br> Ejemplos: comercio de productos al por menor, construcción, servicios médicos...</p>
                                    @elseif($tipo_solicitud_id == 4)
                                        <p class="help-block "><span class="needed">&iquest;Cuál es la actividad principal del patrón?</span> <br> Ejemplos: comercio de productos al por menor, construcción, servicios médicos...</p>
                                    @else
                                        <p class="help-block "><span class="needed">&iquest;Cuál es tu actividad principal?</span> <br> Ejemplos: comercio de productos al por menor, construcción, servicios médicos...</p>
                                    @endif
                                <label id="giro_solicitante"></label>
                                </div>
                            </div>
                            {!! Form::select('giro_comercial_hidden', isset($giros_comerciales) ? $giros_comerciales : [] , null, ['id'=>'giro_comercial_hidden','placeholder' => 'Seleccione una opción','style'=>'display:none;']);  !!}

                        </div>
                    </div>
                    <button class="btn btn-primary" style="float: right; margin-top: 2%;" type="button" onclick="validarSolicitud()" > <i class="fa fa-arrow-right"></i> Validar y Continuar</button>

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
                    <div class="row" id="form">

                        <div class="col-xl-10 offset-xl-1">
                            <div id="divCancelarSolicitante" style="display: none;">
                                <button style="float: right;" class="btn btn-primary" onclick="$('#wizard').smartWizard('goToStep', 3);limpiarSolicitante();" type="button" > Cancelar agregar solicitante <i class="fa fa-times"></i></button>
                            </div>
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
                                            <p class="help-block needed">Raz&oacute;n social</p>
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
                                            <input class="form-control upper" id="idSolicitanteRfc" {{($tipo_solicitud_id == 2 || $tipo_solicitud_id == 3 || $tipo_solicitud_id == 4) ? "required":"" }} onblur="validaRFC(this.value);" placeholder="RFC del solicitante" type="text" value="">
                                            <p class="help-block {{($tipo_solicitud_id == 2 || $tipo_solicitud_id == 3 || $tipo_solicitud_id == 4) ? "needed":"" }}">RFC del solicitante</p>
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
                                            {!! Form::select('genero_id_solicitante', isset($generos) ? $generos : [] , null, ['id'=>'genero_id_solicitante','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect']);  !!}
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
                                            <p id="labelEstadoNacimiento" class="help-block needed">Estado de nacimiento</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12 row personaFisicaSolicitanteNO">
                                        <div class="col-md-4">
                                            <div >
                                                <span class="text-muted m-l-5 m-r-20" for='switch1'>Solicita traductor</span>
                                            </div>
                                            <div >
                                                <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="solicita_traductor_solicitante" name='solicita_traductor_solicitante'/>
                                            </div>
                                        </div>

                                        <div class="col-md-4" id="selectIndigenaSolicitante" style="display:none;">
                                            {!! Form::select('lengua_indigena_id_solicitante', isset($lengua_indigena) ? $lengua_indigena : [] , null, ['id'=>'lengua_indigena_id_solicitante','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                            {!! $errors->first('lengua_indigena_id_solicitante', '<span class=text-danger>:message</span>') !!}
                                            <p class="help-block needed">Lengua indígena</p>
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
                                                    <th style="width:20%; text-align: center;">Acci&oacute;n</th>
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
                                        <input class="form-control numero" maxlength="11" minlength="11" length="11" data-parsley-type='integer' id="nss" placeholder="N&uacute;mero de seguro social"  type="text" value="">
                                        <p class="help-block ">N&uacute;mero de seguro social</p>
                                    </div>
                                    <div class="col-md-12 row">
                                        <div class="col-md-6">
                                            <input class="form-control upper requiredLaboral" required id="puesto" placeholder="Puesto" type="text" value="">
                                            <p class="help-block needed">Puesto</p>
                                        </div>
                                        <div class="col-md-6" >
                                            {!! Form::select('ocupacion_id', isset($ocupaciones) ? $ocupaciones : [] , null, ['id'=>'ocupacion_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                            {!! $errors->first('ocupacion_id', '<span class=text-danger>:message</span>') !!}
                                            <p class="help-block ">En caso de desempeñar un oficio que cuenta con salario mínimo distinto al general, escoge del catálogo. Si no, d&eacute;jalo vac&iacute;o.</p>
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
                                            <input class="form-control requiredLaboral" required id="fecha_ingreso" placeholder="Fecha de ingreso" type="text" value="">
                                            <p class="help-block needed">Fecha de ingreso</p>
                                        </div>
                                        <div class="col-md-4" id="divFechaSalida">
                                            <input class="form-control requiredLaboral" required id="fecha_salida" placeholder="Fecha salida" type="text" value="">
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
                                    <button class="btn btn-primary" style="float: right;" type="button" id="agregarSolicitante" > <i class="fa fa-plus-circle"></i> Validar o agregar solicitante</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- end row -->
                </fieldset>
                <!-- end fieldset -->
            </div>
            <!-- end step-2 -->
            <!-- begin step-3 -->
            <div id="step-3" data-parsley-validate="true">
                <!-- begin fieldset -->
                <fieldset>
                    <!-- begin row -->
                    <div class="row">
                        <div class="col-xl-10 offset-xl-1">
                            <div id="divCancelarCitado" style="display: none;">
                                <button style="float: right;" class="btn btn-primary" onclick="$('#wizard').smartWizard('goToStep', 3);limpiarSolicitado();" type="button" > Cancelar agregar citado <i class="fa fa-times"></i></button>
                            </div>
                            <div>
                                <center><h1>Citado</h1></center>
                                <div id="editandoSolicitado"></div>
                            </div>
                            <div  id="divSolicitado">
                               @if($tipo_solicitud_id == 1) 
                                <div id="divAyudaCitado" style="margin-top: 2%; margin-bottom: 2%;">
                                    <div>
                                        <p>
                                            Debes citar a tu patrón, la persona física (un individuo) o moral (una empresa) responsable de la relación de trabajo contigo. Para ayudarte a determinar a quién deberías citar, te hacemos las siguientes preguntas:<br>
                                            ¿Tienes un recibo o recibos de nómina oficiales (que contenga tu número de seguridad social)?
                                        </p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="radio radio-css radio-inline">
                                                <input name="recibo_oficial" type="radio" id="recibo_oficial_si" value="1"/>
                                                <label for="recibo_oficial_si">S&Iacute;</label>
                                            </div>
                                            <div class="radio radio-css radio-inline">
                                                <input name="recibo_oficial" type="radio" id="recibo_oficial_no" value="2"/>
                                                <label for="recibo_oficial_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="margin-top: 2%; display:none;" id="divReciboOficial" >
                                        <p>
                                            El nombre de la persona o empresa que te paga en este recibo de nómina es más probablemente el patrón a quien debes de citar.
                                        </p>
                                    </div>
                                    <div id="divReciboNomina" style=" display:none; margin-top:2%;">
                                        <p>
                                            ¿Tienes algún recibo de nómina o pago donde aparece el nombre de quién te paga tu sueldo?
                                        </p>
                                        <div >
                                            <div class="row col-md-12">
                                                <div class="radio radio-css radio-inline">
                                                    <input name="recibo_pago" type="radio" id="recibo_pago_si" value="1"/>
                                                    <label for="recibo_pago_si">S&Iacute;</label>
                                                </div>
                                                <div class="radio radio-css radio-inline">
                                                    <input name="recibo_pago" type="radio" id="recibo_pago_no" value="2"/>
                                                    <label for="recibo_pago_no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="divSiReciboNomina" style="margin-top: 2%; display:none;">
                                            <p>
                                                El nombre de la persona o empresa que te paga en este recibo de nómina o de pago es más probablemente el patrón a quien debes de citar.
                                            </p>
                                        </div>
                                        <div id="divNoReciboNomina" style="margin-top: 2%; display:none;" >
                                            <p>
                                                En caso de no contar con ningún tipo de recibo de nómina o pago en el que aparece el nombre del patrón, para decidir a quién citar a la conciliación debes considerar las siguientes preguntas:
                                            </p>
                                            <ul>
                                                <li>¿Con qué persona o empresa firmaste tu contrato de trabajo?</li>
                                                <li>¿Cómo se llama la persona o empresa que te paga tu sueldo?</li>
                                                <li>¿De quién recibes tus órdenes de dónde, cómo y qué tareas de trabajo debes realizar?</li>
                                            </ul>
                                            <div>
                                                <p>
                                                    A partir de tus respuestas a estas preguntas podrás determinar el nombre de la persona que debes citar para la conciliación. Nota lo siguiente: Si conoces el nombre de tu patrón, es preferible que solamente cites a quien realmente es tu patrón, para buscar una solución al conflicto con la persona o empresa correcta y no perder tiempo en arreglar tu conflicto por citar a diversas personas que no son responsables de tu relación de trabajo. Sin embargo, si tienes dudas respecto a quién es tu patrón, porque las respuestas a las preguntas anteriores son diferentes personas, podrás citar a más de una persona o empresa a la conciliación.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @else
                                    <input type="hidden" id="recibo_oficial_si" name="recibo_oficial_si" value="1">
                                @endif
                                <div id="datosIdentificacionSolicitado" style="display: {{$tipo_solicitud_id ==1 ? 'none' : 'block'}};" data-parsley-validate="true">

                                    <div class="col-md-12 mt-4">
                                        <h4>Datos de identificaci&oacute;n</h4>
                                        <hr class="red">
                                    </div>
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
                                            <p class="help-block needed">Raz&oacute;n social</p>
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
                                    @if($tipo_solicitud_id == 1 || $tipo_solicitud_id == 2)
                                        <div class="col-md-12 row personaFisicaSolicitadoNO">
                                            <div class="col-md-4">
                                                <div  >
                                                    <span class="text-muted m-l-5 m-r-20" for='switch1'>Solicita traductor</span>
                                                </div>
                                                <div >
                                                    <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="solicita_traductor_solicitado" name='solicita_traductor_solicitado'/>
                                                </div>
                                            </div>
                                            <div class="col-md-4" id="selectIndigenaSolicitado" style="display:none">
                                                {!! Form::select('lengua_indigena_id_solicitado', isset($lengua_indigena) ? $lengua_indigena : [] , null, ['id'=>'lengua_indigena_id_solicitado','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                                {!! $errors->first('lengua_indigena_id_solicitado', '<span class=text-danger>:message</span>') !!}
                                                <p class="help-block needed">Lengua indígena</p>
                                            </div>
                                        </div>
                                    @endif
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
                                        <div class="col-md-12 row">
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
                                    </div>
                                        <div class="col-md-10 offset-md-1" >
                                            <table class="table table-bordered" >
                                                <thead>
                                                    <tr>
                                                        <th style="width:80%;">Tipo</th>
                                                        <th style="width:80%;">Contacto</th>
                                                        <th style="width:20%; text-align: center;">Acci&oacute;n</th>
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
                                                            <th style="width:20%; text-align: center;">Acci&oacute;n</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbodyDomicilioSolicitado">
                                                    </tbody>
                                                </table>
                                            </div> --}}
                                        </div>
                                    </div>

                                </div>
                                    <!-- end seccion de domicilios citado -->
                                <hr style="margin-top:5%;">
                                <div id="divBotonesSolicitado" style="display: none;">
                                    <button class="btn btn-danger" type="button" onclick="limpiarSolicitado()"> <i class="fa fa-eraser"></i> Limpiar campos</button>
                                    <button class="btn btn-primary" style="float: right;" type="button" id="agregarSolicitado" > <i class="fa fa-plus-circle"></i> Validar o Agregar citado</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </fieldset>
                <!-- end fieldset -->
            </div>
            <!-- begin step-4 -->
            <div id="step-4" data-parsley-validate="true">
                <div class="row">
                    <div class="col-xl-10 offset-xl-1">
                        <center>  <h1>Solicitud</h1></center>
                    <div class="col-md-12 row">
                        <input type="hidden" id="solicitud_id">
                        <input type="hidden" id="tipo_solicitud_id" value="{{$tipo_solicitud_id}}">
                        <div class="col-md-4 showEdit" >
                            <input class="form-control dateTime" id="fechaRatificacion" disabled placeholder="Fecha de ratificación" type="text" value="">
                            <p class="help-block">Fecha de ratificación</p>
                        </div>
                        <div class="col-md-4 showEdit">
                            <input class="form-control dateTime" id="fechaRecepcion" disabled placeholder="Fecha de Recepción" type="text" value="">
                            <p class="help-block needed">Fecha de recepción</p>
                        </div>
                        <div class="col-md-4 estatusSolicitud">
                            {!! Form::select('estatus_solicitud_id', isset($estatus_solicitudes) ? $estatus_solicitudes : [] , isset($solicitud->estatus_solicitud_id) ?  $solicitud->estatus_solicitud_id : null, ['id'=>'estatus_solicitud_id','disabled','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                            {!! $errors->first('estatus_solicitud_id', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block needed">Estatus de la solicitud</p>
                        </div>
                        <div class="col-md-12">
                            <h4>Giro Comercial</h4>
                            <h5 id="giro_solicitanteSol"></h4>
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
                            <button class="btn btn-primary pull-right" onclick="$('#wizard').smartWizard('goToStep', 0);"><i class="fa fa-pencil-alt" ></i> Editar datos de solicitud</button>
                            <div class="col-md-12 row"> <div><h4>Solicitantes</h4></div> <div style="float: left; margin-left: 2%" ><button class="btn btn-primary pull-right" onclick="$('#wizard').smartWizard('goToStep', 1); $('#divCancelarSolicitante').show()"><i class="fa fa-plus" ></i> Agregar solicitante</button></div></div>
                            <div class="col-md-10 offset-md-1" style="margin-top: 3%;" >
                                <table class="table table-bordered" >
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Curp</th>
                                            <th>RFC</th>
                                            <th>Acci&oacute;n</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodySolicitanteRevision">
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 row"> <div><h4>Citados</h4></div> <div style="float: left; margin-left: 2%" ><button class="btn btn-primary pull-right" onclick="$('#wizard').smartWizard('goToStep', 2);$('#divCancelarCitado').show()"><i class="fa fa-plus" ></i> Agregar citado</button></div></div>
                            <div class="col-md-10 offset-md-1" style="margin-top: 3%;" >
                                <table class="table table-bordered" >
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Curp</th>
                                            <th>RFC</th>
                                            <th style="width:15%; text-align: center;">Acci&oacute;n</th>
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

                {{-- @if(isset($solicitud->estatus_solicitud_id) && $solicitud->estatus_solicitud_id == 1)
                    <div class="form-group">
                        <button class="btn btn-primary btn-sm m-l-5" id="btnRatificarSolicitud"><i class="fa fa-check"></i> Ratificar Solicitud</button>
                    </div>
                @endif --}}
                    <div class="col-md-12" id="btnGuardar">
                        <button style="float: right;" class="btn btn-primary pull-right btn-lg m-l-5" onclick="guardarSolicitud()"><i class="fa fa-save" ></i> Guardar</button>
                    </div>

                </div>
            </div>
        </div>
        <!-- end wizard-content -->
    </div>
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

<!-- inicio Modal Domicilio-->

<div class="modal" id="modal-acuse" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog ">
        <div class="modal-content">

            <div class="modal-body" >
                <div class="col-md-12">
                    <div style="width: 100%; text-align:center;">
                        <h1>Solicitud guardada correctamente</h1>
                    </div>
                    <div style="width: 100%; text-align:center;">
                        <h1 style="color: green; font-size: 4rem !important;"><i class="far fa-check-circle"></i></h1>
                    </div>
                </div>
                <br>
                <div class="alert alert-warning"><h4>Descarga tu acuse para presentarlo al realizar la ratificación</h4></div>
                <br>
                <div>
                    <div class="col-md-10 offset-1" id="btnGetAcuse" style="display: none;">
                        <a id="btnAcuse" href="/api/documentos/getFile/" style="text-align:left; font-size: large; width:100%; margin: 2px;" onclick="$('#divBtnFinalizarAcuse').show();" class="btn btn-primary pull-right btn-lg m-l-5" target="_blank"><i style="float: left;margin-left: 5%;margin-top: 2%;" class="fa fa-file" ></i> Descargar Acuse</a>
                    </div>
                    <div style="display: none;" id="divBtnFinalizarAcuse">
                        <div class="col-md-10 offset-1">
                            <a id="btnAcuse" href="/" style=" text-align:left; font-size: large; width:100%; margin: 2px;" class="btn btn-primary pull-right btn-lg m-l-5" ><i style="float: left;margin-left: 5%;margin-top: 2%;" class="fa fa-sign-out-alt"></i> Finalizar</a>
                        </div>
                        <div class="col-md-10 offset-1">
                            <a id="btnAcuse" onclick="window.location.reload();" style="text-align:left; font-size: large; width:100%; margin: 2px;" class="btn btn-primary pull-right btn-lg m-l-5" > <i style="float: left;margin-left: 5%;margin-top: 2%;" class="fa fa-redo-alt"></i> Capturar nueva solicitud</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Domicilio-->

<!-- inicio Modal Alerta Giro-->

<div class="modal" id="modal-giro" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Advertencia<i class="fa fa-warning"></i></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" >
                <div id="msjFederal">
                    <p style="font-size:large;">
                        El sistema indica que la actividad principal del patrón es de competencia local, no federal.
                    </p>
                    <p style="font-size:large;">
                        Acuda al Centro de Conciliación local de su entidad para realizar la solicitud, si no tiene la posibilidad de realizar a tiempo su solicitud en el Centro de Conciliación local, puede continuar la solicitud en el sistema federal y en el momento de ratificación su solicitud será revisada por un funcionario el CFCRL, quien determinará una corrección de la actividad principal o la emisión de una constancia de incompetencia y el envío de su solicitud al centro de conciliación competente.
                    </p>
                </div>
                <div style="display: none;" id="msjLocal">
                    <p style="font-size:large;">
                        El sistema indica que la actividad principal del patrón es de competencia federal, no local.
                    </p>
                    <p style="font-size:large;">
                        Acuda a la Oficina Estatal del Centro Federal de Conciliación y Registro Laboral de su entidad para realizar la solicitud, si no tiene la posibilidad de realizar a tiempo su solicitud en el CFCRL, puede continuar la solicitud en el Centro de Conciliación Local y en el momento de ratificación su solicitud será revisada por un funcionario del Centro, quien determinará una corrección de la actividad principal o la emisión de una constancia de incompetencia y el envío de su solicitud al CFCRL.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-primary btn-sm" class="close" data-dismiss="modal" aria-hidden="true" ><i class="fa fa-times"></i> Aceptar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Alerta Giro-->

{{-- Modal confirma falta de correo --}}
<div class="modal" id="modal_valida_correo" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5>No captur&oacute; correo electr&oacute;nico, tome en cuenta que el correo electr&oacute;nico es muy importante para el seguimiento del proceso de conciliaci&oacute;n</h5>
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
<div class="modal" id="modal-ratificacion-success" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Audiencia generada</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    <p>
                        Se generó la audiencia con la siguiente información
                    </p>
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Folio: </strong><span id="spanFolio"></span><br>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Fecha de audiencia: </strong><span id="spanFechaAudiencia"></span><br>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Hora de inicio: </strong><span id="spanHoraInicio"></span><br>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Hora de t&eacute;rmino: </strong><span id="spanHoraFin"></span><br>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <table class="table table-striped table-hover" id="tableAudienciaSuccess">
                        <thead>
                            <tr>
                                <th>Tipo de parte</th>
                                <th>Conciliador</th>
                                <th>Sala</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button class="btn btn-primary btn-sm m-l-5" id="btnFinalizarRatificacion"><i class="fa fa-check"></i> Finalizar</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!--Fin de modal de representante-->
<input type="hidden" id="expediente_id">
<!--</div>-->
@push('scripts')

<script>
    // Se declaran las variables globales
    var arraySolicitados = []; //Lista de citados
    var objAyudaCitado = {recibo_oficial:"",recibo_pago:""}; //Lista de citados
    var arraySolicitantes = []; //Lista de solicitantes
    var arrayDomiciliosSolicitante = []; // Array de domicilios para el solicitante
    var arrayDomiciliosSolicitado = []; // Array de domicilios para el citado
    var arrayObjetoSolicitudes = []; // Array de objeto_solicitude para el citado
    var arrayContactoSolicitantes = []; // Array de objeto_solicitude para el citado
    var arrayContactoSolicitados = []; // Array de objeto_solicitude para el citado
    // var arraySolicitanteExcepcion = {}; // Array de solicitante excepción
    var ratifican = false;; // Array de solicitante excepción
    var editSolicitud = false; //Lista de citados
    var editCitado = false; //Lista de citados
    var editSolicitante = false; //Lista de citados
    var listaContactos=[];
    var municipiosCede = []; //Lista de citados

    $(document).ready(function() {
        $('#wizard').smartWizard({
            selected: 0,
            keyNavigation: false,
            theme: 'default',
            transitionEffect: 'fade',
            showStepURLhash: false,
            anchorSettings: {
                anchorClickable: false, // Enable/Disable anchor navigation
                enableAllAnchors: false, // Activates all anchors clickable all times
                markDoneStep: true, // add done css
                enableAnchorOnDoneStep: false // Enable/Disable the done steps navigation
            },
            lang: { next: 'Siguiente', previous: 'Anterior' }
        });
        $('.sw-btn-prev').hide();
        $('.sw-btn-next').hide();
        if(edit){
            $(".estatusSolicitud").show();
            $(".showEdit").show();
            var solicitud='{{ $solicitud->id ?? ""}}';
            editSolicitud = true;
            editCitado = true;
            editSolicitante = true;
            // FormMultipleUpload.init();
            // Gallery.init();
        }else{
            $(".showEdit").hide();
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
                if($('#step-2').parsley().validate()){

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
                    $("#divCancelarCitado").hide();
                    if(editCitado){
                        var btnText = "Continuar a Revisión";
                    }else{
                        var btnText = "Capturar Citado(s)";
                    }
                    swal({
                        title: '¿Quieres seguir capturando solicitante(s) o proceder a '+btnText+'?',
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
                                text: btnText,
                                value: true,
                                visible: true,
                                className: 'btn btn-primary',
                                closeModal: true
                            }
                        }
                    }).then(function(isConfirm){
                        if(isConfirm){
                            if(!editCitado){
                                editCitado = true;
                                $('#wizard').smartWizard('goToStep', 2);
                            }else{
                                $('#wizard').smartWizard('goToStep', 3);
                            }
                        }else{
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
                if($('#step-3').parsley().validate()  ){
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
                        $("#tipo_persona_fisica_solicitado").click().trigger('change');
                        $(".pasoSolicitado").show();
                        $("#divCancelarSolicitante").hide();
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
                                if(!editCitado){
                                    editCitado = true;
                                    $('#wizard').smartWizard('goToStep', 3);
                                }else{
                                    $('#wizard').smartWizard('goToStep', 3);
                                }
                            }else{
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
        /**
        * Funcion para conocer si el tipo persona del solicitante es moral o fisica
        */
        $("#municipiosolicitante").change(function(){
           if(($("#tipo_solicitud_id").val() == 4 || $("#tipo_solicitud_id").val() == 2)  && $("#municipiosolicitante").val() != ""){
               if(municipiosCede.length > 0){
                    var municipioExistente = municipiosCede.find(x=>x.municipio == $("#municipiosolicitante").val());
                    if(municipioExistente == undefined){
                        swal({
                            title: 'Advertencia',
                            text: ' Lamentamos que su municipio no está incluido en la etapa actual de la implementación de la reforma a la justicia laboral ',
                            icon: 'warning'
                        });
                        $("#municipiosolicitante").val("").trigger('change');
                    }
               }
           }
        });
        $("#municipiosolicitado").change(function(){
           if(($("#tipo_solicitud_id").val() == 1 || $("#tipo_solicitud_id").val() == 3) && $("#municipiosolicitado").val() != ""){
                if(municipiosCede.length > 0){
                    var municipioExistente = municipiosCede.find(x=>x.municipio == $("#municipiosolicitado").val());
                    if(municipioExistente == undefined){
                        swal({
                            title: 'Advertencia',
                            text: ' Lamentamos que su municipio no está incluido en la etapa actual de la implementación de la reforma a la justicia laboral ',
                            icon: 'warning'
                        });
                        $("#municipiosolicitado").val("").trigger('change');
                    }
                }
           }
        });
        $("#labora_actualmente").change(function(){
            if($("#labora_actualmente").is(":checked")){
                console.log("fecha_salida no requerida");
                $("#fecha_salida").removeAttr("required");
                $("#divFechaSalida").hide();
            }else{
                console.log("fecha_salida requerida");
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
            // cargarDocumentos();
            getSolicitudFromBD(solicitud);
            editSolicitud = true;
            editCitado = true;
            editSolicitante = true;
        }else{
            if(localStorage.getItem("datos_laborales")){
                var datos_laborales_storage = localStorage.getItem("datos_laborales");
                datos_laborales_storage = JSON.parse(datos_laborales_storage);
                $("#periodicidad_id").val(datos_laborales_storage.periodicidad_id).trigger('change');
                $("#remuneracion").val(datos_laborales_storage.remuneracion);
                $("#ocupacion_id").val(datos_laborales_storage.ocupacion_id).trigger('change');
                $("#fecha_ingreso").val(dateFormat(datos_laborales_storage.fecha_ingreso,4));
                if(datos_laborales_storage.labora_actualmente != $("#labora_actualmente").is(":checked")){
                    $("#labora_actualmente").click();
                }
                $("#fecha_salida").val(dateFormat(datos_laborales_storage.fecha_salida,4));
                localStorage.clear();
            }
        }
        // getGironivel("",1,"girosNivel1solicitante");
        if($("#tipo_solicitud_id").val() == 4){
            $("#labelTipoSolicitante").text("Sindicato")
            $("#labelTipoSolicitud").text("Sindicato")
            $("#divTipoPersona").hide();
            $("#tipo_persona_moral_solicitante").prop("checked", true).trigger('change');
            $(".sindicato").show();
            $("#registro_sindical").attr("required",true);
        }else if($("#tipo_solicitud_id").val() == 3){
            $("#labelTipoSolicitante").text("Patrón (colectiva)")
            $("#labelTipoSolicitud").text("Patrón (colectiva)")
        }else if($("#tipo_solicitud_id").val() == 2){
            $("#labelTipoSolicitante").text("Patrón (individual)")
            $("#labelTipoSolicitud").text("Patrón (individual)")
        }else if($("#tipo_solicitud_id").val() == 1){
            $("#labelTipoSolicitante").text("Trabajador")
            $("#labelTipoSolicitud").text("Trabajador")
        }
    });
    // function exepcionConciliacion(){
    //     var formData = new FormData();

    //     $.ajax({
    //         url:'/solicitud/excepcion',
    //         type:'POST',
    //         dataType:"json",
    //         contentType: false,
    //         processData: false,
    //         data:{
    //             arraySolicitanteExcepcion:arraySolicitanteExcepcion,
    //             _token:$("input[name=_token]").val()

    //         },
    //         success:function(data){
    //             if(data.success){
    //                 swal({
    //                     title: 'Correcto',
    //                     text: 'Solicitud guardada correctamente',
    //                     icon: 'success',

    //                 });
    //                 setTimeout('', 5000);
    //                 location.href='{{ route('solicitudes.index')  }}'
    //             }else{

    //             }

    //         },error:function(data){
    //             swal({
    //                 title: 'Error',
    //                 text: ' Error al guardar excepción',
    //                 icon: 'error'
    //             });
    //         }
    //     });
    // }

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
        // $("#tbodyGruposPrioritarios").html(html);
        // $(".fileGrupoVulnerable").change(function(e){
        //     var id = $(this).attr("idsolicitante");
        //     $("#fileName"+id).html(e.target.files[0].name);
        //     var solicitanteExcepcion = {};
        //     solicitanteExcepcion.file = e.target.files[0];
        //     solicitanteExcepcion.id = $(this).attr("idsolicitante");
        //     solicitanteExcepcion.conciliador_id = $("#conciliador_excepcion_id").val();
        //     arraySolicitanteExcepcion[$(this).attr("idsolicitante")] = solicitanteExcepcion;
        // });
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
                    $("#datosIdentificacionSolicitado").show();
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
                    if(data.recibo_oficial == true){
                        $("#recibo_oficial_no").attr("checked",true);
                    }else{
                        $("#recibo_oficial_no").attr("checked",true);
                    }
                    if(data.recibo_pago == true){
                        $("#recibo_pago_si").attr("checked",true);
                    }else{
                        $("#recibo_pago_no").attr("checked",true);
                    }
                    $("input[name='recibo_oficial']").trigger("change");
                    $("input[name='recibo_pago']").trigger("change");
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
                    $("#giro_comercial_hidden").val(data.giro_comercial_id)
                    $("#giro_solicitante").html("<b>"+$("#giro_comercial_hidden :selected").text() + "</b>");
                    $("#giro_solicitanteSol").html("<b>"+$("#giro_comercial_hidden :selected").text() + "</b>");
                    // var excepcion = false;
                    // $.each(arraySolicitantes,function(key,value){
                    //     if(value.grupo_prioritario_id != null){
                    //         excepcion = true;
                    //     }
                    // }) ;
                    // console.log(excepcion);
                    // $(".step-6").show();
                    if(data.ratificada){
                        $("#btnRatificarSolicitud").hide();
                        $("#expediente_id").val(data.expediente.id);
                        expedientee = true;
                        expediente_id = data.expediente.id;
                    }else{
                        $("#btnRatificarSolicitud").show();
                        $("#expediente_id").val("");
                    }
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
    $("#nacionalidad_id_solicitante").change(function(){
        if($(this).val() == 1 || $(this).val() == "" ){
            $("#entidad_nacimiento_id_solicitante").attr('required');
            $("#labelEstadoNacimiento").addClass('needed');
        }else{
            $("#entidad_nacimiento_id_solicitante").removeAttr('required');;
            $("#labelEstadoNacimiento").removeClass('needed');
        }
    });
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
        $("#ocupacion_id").val("");
        $("#puesto").val("");
        $("#nss").val("");
        $("#no_issste").val("");
        $("#remuneracion").val("");
        $("#periodicidad_id").val("");
        if($("#labora_actualmente").is(":checked")){
            $("#labora_actualmente").trigger('click');
        }
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
        $("input[name='tipo_persona_solicitante']").trigger("change")
        arrayContactoSolicitantes = [];
        formarTablaContacto(true);
        $('.catSelect').trigger('change');
        domicilioObj.limpiarDomicilios();
        $('#step-2').parsley().reset();
        $("#editandoSolicitante").html("");
        $("#botonAgregarSolicitante").show();
        $('#divCancelarSolicitante').hide()
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
            $('#step-3').parsley().reset();
            $("#editandoSolicitado").html("");
            $("#botonAgregarSolicitado").show();
            $('#divCancelarCitado').hide()
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
                    arrayContactoSolicitantes.splice(key,1);
                }else{
                    arrayContactoSolicitantes[key].activo = 0;
                }
            }else{
                if(arrayContactoSolicitados[key].id == ""){
                    arrayContactoSolicitados.splice(key,1);
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
        $("#objeto_solicitud_id").val("").trigger("change");
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
        if(arraySolicitantes[key].id == ""){
            arraySolicitantes.splice(key,1);
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
            arrayDomiciliosSolicitado.splice(key,1);
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
            arrayObjetoSolicitudes.splice(key,1);
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
            arraySolicitados.splice(key,1);
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
        $('#wizard').smartWizard('goToStep', 1);
        $("#agregarSolicitante").html('<i class="fa fa-edit"></i> Validar o Editar solicitante');
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
            // getGiroEditar("solicitante");
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
        $('#wizard').smartWizard('goToStep', 2);
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
    // function agregarObjetoSol(){
        $("#objeto_solicitud_id").change(function(){
        var objeto = $(this).val();

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

    });

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
            if($('#step-4').parsley().validate() && arraySolicitados.length > 0 && arraySolicitantes.length > 0 && $("#countObservaciones").val() <= 200 && arrayObjetoSolicitudes.length > 0 ){

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
                            // swal({
                            //     title: 'Correcto',
                            //     text: 'Solicitud guardada correctamente',
                            //     icon: 'success',

                            // });
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
                        text: 'Es necesario capturar al menos un solicitante, un citado y todos los datos de la solicitud',
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
            solicitud.solicita_excepcion = $("#solicita_excepcion").is(":checked");
            solicitud.fecha_ratificacion = dateFormat($("#fechaRatificacion").val(),3);
            solicitud.fecha_recepcion = dateFormat($("#fechaRecepcion").val(),3);
            solicitud.fecha_conflicto = dateFormat($("#fechaConflicto").val());
            solicitud.tipo_solicitud_id = $("#tipo_solicitud_id").val();
            solicitud.giro_comercial_id = $("#giro_comercial_hidden").val();
            if($("input[name='recibo_oficial']").val() == 1){
                recibo_oficial = true;
            }else{
                recibo_oficial = false;
            }
            if($("input[name='recibo_pago']").val() == 1){
                recibo_pago = true;
            }else{
                recibo_pago = false;
            }
            solicitud.recibo_oficial = recibo_oficial;
            solicitud.recibo_pago = recibo_pago;
            return solicitud;
        }catch(error){
            console.log(error);
        }
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
                    parent_id: $("#girosNivel").val(),
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

                        html += '<tr><th><h5> '+highlightText(node.nombre)+'</h5><th></tr>';
                        $.each(ancestors, function (index, ancestor) {
                            if(ancestor.id != 1){
                                var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(index);
                                html += '<tr><td style="border-left:1px solid;">'+tab+highlightText(ancestor.nombre)+'</td></tr>';
                            }
                        });
                        var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(node.ancestors.length);
                        html += '<tr><td style="border-left:1px solid;"> '+ tab+highlightText(node.nombre)+'</td></tr>';
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
            if(data && data.id != "" ){
                var instancia = $("#instancia").val();
                
                if((data.ambito_id != 1 && instancia == "federal") || (data.ambito_id != 2 && instancia == "local")){
                    if(instancia == "local"){
                        $("#msjFederal").hide();
                        $("#msjLocal").show();
                    }
                    $("#modal-giro").modal("show");
                }
                $("#giro_solicitanteSol").html(data.nombre);
                $("#giro_comercial_hidden").val(data.id);
                return data.nombre;
            }
            return data.text;
        },
        placeholder:'Seleccione una opción',
        minimumInputLength:4,
        allowClear: true,
        language: "es"
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
            if(edad >= 15){
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
            if(edad >= 18){
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


 var getDate = function (input) {
    return new Date(input.date.valueOf());
 }

    $('#fecha_ingreso').datepicker({
        format: "dd/mm/yyyy",
        changeMonth: true,
        changeYear: true,
        maxDate:0,
        yearRange: "c-80:",
        language: 'es',
        autoclose: true,
    });
    var a = $('#fecha_ingreso').datepicker("getDate");
    $('#fecha_salida').datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        maxDate:0,
        yearRange: "c-80:",
        changeMonth: true,
        changeYear: true,
        autoclose: true
    });

    $('#fecha_ingreso').datepicker().on('change', function (ev) {
        var date2 = $('#fecha_ingreso').datepicker('getDate');
        date2.setDate(date2.getDate()+1);
        $('#fecha_salida').datepicker("option", "minDate", date2);
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
                        if(!edit){
                            $("#modal-acuse").modal("show");
                        }else{
                            swal({title: 'ÉXITO',text: 'Solicitud Guardada correctamente',icon: 'success'});
                        }
                        $("#btnAcuse").attr("href","/api/documentos/getFile/"+data[0].id)
                        $("#btnGetAcuse").show();
                    }
                }catch(error){
                    console.log(error);
                }
            }
        });
    }

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
                // $('#continuarSolicitado2').hide();
                 $('#divBotonesSolicitado').show();
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

    function validarSolicitud() {
        if($('#fechaConflicto').val() != '' && $('#giro_comercial_hidden').val() != '' && arrayObjetoSolicitudes.length > 0){
            if(!editSolicitud){
                editSolicitud = true;
                $('#wizard').smartWizard('goToStep', 1);
            }else{
                $('#wizard').smartWizard('goToStep', 3);
            }
        }else{
            swal({title: 'Error',text: 'Es necesario llenar todos los campos para continuar',icon: 'error',});º
        }
    }


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
                            swal({title: 'Error',text: 'Algo salió mal',icon: 'warning'});
                        }
                    }
                });
            }else{
                listaContactos.splice(indice,1);
                cargarContactos();
            }
        }

        $("input[name='recibo_oficial']").change(function(){
            if($(this).val() == 1){
                $("#divReciboOficial").show()
                $("#divReciboNomina").hide()
                objAyudaCitado.recibo_oficial = true;
                $("#datosIdentificacionSolicitado").show()
            }else{
                $("#divReciboOficial").hide()
                $("#divReciboNomina").show()
                objAyudaCitado.recibo_oficial = false;
                $("#datosIdentificacionSolicitado").hide()
            }
        });
        $("input[name='recibo_pago']").change(function(){
            if($(this).val() == 1){
                $("#divSiReciboNomina").show()
                $("#divNoReciboNomina").hide()
                objAyudaCitado.recibo_pago = true;
            }else{
                $("#divSiReciboNomina").hide()
                $("#divNoReciboNomina").show()
                objAyudaCitado.recibo_pago = false;
            }
            $("#datosIdentificacionSolicitado").show()
        });
    $("#girosNivel").on("change",function(){
        if($(this).val() != ""){
            $("#divGiro").show();
        }else{
            $("#divGiro").hide();
        }
        $("#giro_comercial_solicitante").val("").change();
    });


    $('[data-toggle="tooltip"]').tooltip();

    history.pushState(null, document.title, location.href);
    history.back();
    history.forward();
    window.onpopstate = function () {
        history.go(1);
    };
</script>


<script src="/assets/plugins/highlight.js/highlight.min.js"></script>

@endpush

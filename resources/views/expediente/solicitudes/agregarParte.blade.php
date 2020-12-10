<div class="row">
    <div class="col-xl-10 offset-xl-1">
        <div id="divCancelarCitado" style="display: none;">
            <button style="float: right;" class="btn btn-primary" onclick="$('#wizard').smartWizard('goToStep', 3);limpiarSolicitado();" type="button" > Cancelar agregar citado <i class="fa fa-times"></i></button>
        </div>
        <div>
            <center><h1>Agregar Citado Compareciente</h1></center>
            <div id="editandoCitado"></div>
        </div>
        <div  id="divCitado">
            <input type="hidden" id="recibo_oficial_si" name="recibo_oficial_si" value="1">
        <input type="hidden" id="solicitud_id" name="recibo_oficial_si" value="{{$solicitud_id}}">
            <div id="datosIdentificacionCitado" style="display: {{$tipo_solicitud_id ==1 ? 'none' : 'block'}};" data-parsley-validate="true">

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
                            <input checked="checked" name="tipo_persona_citado" type="radio" id="tipo_persona_fisica_solicitado" value="1"/>
                            <label for="tipo_persona_fisica_solicitado">Física</label>
                        </div>
                        <div class="radio radio-css radio-inline">
                            <input name="tipo_persona_citado" type="radio" id="tipo_persona_moral_solicitado" value="2"/>
                            <label for="tipo_persona_moral_solicitado">Moral</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 personaFisicaCitadoNO">
                    <input class="form-control upper" id="idCitadoCURP" maxlength="18" onblur="validaCURP(this.value);" placeholder="CURP del citado" type="text" value="">
                    <p class="help-block">CURP del citado</p>
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-4" style="display:none;">
                        <input class="form-control" id="idsolicitado" type="text" value="253">
                    </div>
                    <div class="col-md-4 personaFisicaCitado">
                        <input class="form-control upper" required id="idNombreCitado" placeholder="Nombre del citado" type="text" value="">
                        <p class="help-block needed">Nombre del citado</p>
                    </div>
                    <div class="col-md-4 personaFisicaCitado">
                        <input class="form-control upper" required id="idPrimerACitado" placeholder="Primer apellido del citado" type="text" value="">

                        <p class="help-block needed">Primer apellido</p>
                    </div>
                    <div class="col-md-4 personaFisicaCitadoNO">
                        <input class="form-control upper" id="idSegundoACitado" placeholder="Segundo apellido del citado" type="text" value="">

                        <p class="help-block">Segundo apellido</p>
                    </div>
                    <div class="col-md-8 personaMoralCitado">
                        <input class="form-control upper" id="idNombreCCitado" required placeholder="Raz&oacute;n social del citado" type="text" value="">
                        <p class="help-block needed">Raz&oacute;n social</p>
                    </div>
                    <div class="col-md-4 personaFisicaCitadoNO">
                        <input class="form-control dateBirth" id="idFechaNacimientoCitado" placeholder="Fecha de nacimiento del citado" type="text" value="">
                        <p class="help-block">Fecha de nacimiento</p>
                    </div>
                    <div class="col-md-4 personaFisicaCitadoNO">
                        <input class="form-control numero" disabled id="idEdadCitado" placeholder="Edad del citado" type="text" value="">
                        <p class="help-block">Edad del citado</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control upper" id="idCitadoRfc" placeholder="RFC del citado" type="text" value="">
                        <p class="help-block">RFC del citado</p>
                    </div>
                    <div class="col-md-4 personaFisicaCitadoNO">
                        {!! Form::select('genero_id_solicitado', isset($generos) ? $generos : [] , null, ['id'=>'genero_id_solicitado','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('genero_id_solicitado', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Género</p>
                    </div>
                    <div class="col-md-4 personaFisicaCitadoNO">
                        {!! Form::select('nacionalidad_id_solicitado', isset($nacionalidades) ? $nacionalidades : [] , null, ['id'=>'nacionalidad_id_solicitado','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                        {!! $errors->first('nacionalidad_id_solicitado', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Nacionalidad</p>
                    </div>
                    <div class="col-md-4 personaFisicaCitadoNO">
                        <select id="estado_id" class="form-control catSelect " name="domicilio[estado_id]" >
                            <option value="">Seleccione una opción</option>
                            @foreach ($estados as $estado)
                                <option value="{{$estado->id}}">{{$estado->nombre}}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('entidad_nacimiento_id_solicitado', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block">Estado de nacimiento</p>
                    </div>
                </div>
                @if($tipo_solicitud_id == 1 || $tipo_solicitud_id == 2)
                    <div class="col-md-12 row personaFisicaCitadoNO">
                        <div class="col-md-4">
                            <div  >
                                <span class="text-muted m-l-5 m-r-20" for='switch1'>Solicita traductor</span>
                            </div>
                            <div >
                                <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="solicita_traductor_solicitado" name='solicita_traductor_solicitado'/>
                            </div>
                        </div>
                        <div class="col-md-4" id="selectIndigenaCitado" style="display:none">
                            {!! Form::select('lengua_indigena_id_solicitado', isset($lengua_indigena) ? $lengua_indigena : [] , null, ['id'=>'lengua_indigena_id_solicitado','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                            {!! $errors->first('lengua_indigena_id_solicitado', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block needed">Lengua indígena</p>
                        </div>
                    </div>
                @endif
                <div class="col-md-12 pasoCitado" id="continuarCitado1">
                    <button style="float: right;" class="btn btn-primary" onclick="pasoCitado(1)" type="button" > Validar <i class="fa fa-arrow-right"></i></button>
                </div>
            </div>
            {{-- Seccion de contactos solicitados --}}
            <div id="divContactoCitado" data-parsley-validate="true" style="display:none;">
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
                            <button class="btn btn-primary" type="button" onclick="agregarContactoCitado();" > <i class="fa fa-plus-circle"></i> Agregar Contacto</button>
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
                            <tbody id="tbodyContactoCitado">
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 pasoCitado" id="continuarCitado2">
                        <button style="float: right;" class="btn btn-primary" onclick="pasoCitado(2)" type="button" > Validar <i class="fa fa-arrow-right"></i></button>
                    </div>
            </div>
            {{-- end seccion de contactos solicitados --}}
            <!-- seccion de domicilios citado -->
            <div id="divMapaCitado" data-parsley-validate="true" style="display: none;">
                <div  class="col-md-12 row">
                    <div class="row">
                        <h4>Domicilio(s)</h4>
                        <hr class="red">
                    </div>
                    @include('includes.component.map',['identificador' => 'solicitado','needsMaps'=>"true", 'instancia' => 2])
                    <div style="margin-top: 2%;" class="col-md-12">
                    </div>
                </div>
            </div>
                <!-- end seccion de domicilios citado -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    var arrayContactoSolicitados = [];
    function pasoCitado(pasoActual){
        switch (pasoActual) {
            case 1:
                if($('#datosIdentificacionCitado').parsley().validate()){
                    $('#divContactoCitado').show();
                    $('#continuarCitado1').hide();

                }
                break;
            case 2:
                $('#divMapaCitado').show();
                $('#continuarCitado2').hide();
                $('#divBotonesCitado').show();
            break;
            case 3:
                if($('#divMapaCitado').parsley().validate()){
                    $('#divBotonesCitado').show();
                    $('#continuarCitado3').hide();
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

    $("#btnGuardarParte").click(function(){
        var arrayDomiciliosSolicitado = []; // Array de domicilios para el citado
        arrayDomiciliosSolicitado[0] = domicilioObj2.getDomicilio();
        if(arrayDomiciliosSolicitado.length > 0){
            $.ajax({
                url:"/parte",
                type:"POST",
                dataType:"json",
                data:{
                    solicitud_id : $("#solicitud_id").val(),
                    nombre : $("#idNombreCitado").val(),
                    primer_apellido: $("#idPrimerACitado").val(),
                    segundo_apellido:$("#idSegundoACitado").val(),
                    fecha_nacimiento:dateFormat($("#idFechaNacimientoCitado").val()),
                    curp:$("#idCitadoCURP").val(),
                    edad:$("#idEdadCitado").val(),
                    genero_id: $("#genero_id_Citado").val(),
                    nacionalidad_id:$("#nacionalidad_id_Citado").val(),
                    entidad_nacimiento_id: $("#entidad_nacimiento_id_Citado").val(),
                    lengua_indigena_id: $("#lengua_indigena_id_Citado").val(),
                    nombre_comercial: $("#idNombreCCitado").val(),
                    solicita_traductor: $("input[name='solicita_traductor_Citado']:checked").val(),
                    tipo_persona_id: $("input[name='tipo_persona_citado']:checked").val(),
                    tipo_parte_id:2,
                    rfc: $("#idCitadoRfc").val(),
                    activo: 1,
                    audiencia_id: $("#audiencia_id").val(),
                    domicilios: arrayDomiciliosSolicitado,
                    contactos: arrayContactoSolicitados,
                    _token:$("input[name=_token]").val()
                },
                success:function(data){
                    try{

                        if(data != null && data != ""){
                            swal({title: 'ÉXITO',text: 'Se Agrego correctamente la parte ',icon: 'success'});
                            window.location.reload();
                        }else{
                            swal({title: 'Error',text: 'Algo salió mal',icon: 'warning'});
                        }
                    }catch(error){
                        console.log(error);
                    }
                },error:function(data){
                    var mensajes = "";
                    $.each(data.responseJSON.errors, function (key, value) {
                        var origen = key.split(".");

                        mensajes += "- "+value[0]+ " del "+origen[0].slice(0,-1)+" "+(parseInt(origen[1])+1)+" \n";
                    });
                    swal({
                        title: 'Error',
                        text: 'Es necesario validar los siguientes campos \n'+mensajes,
                        icon: 'error'
                    });
                }
            });
        }
    });
    function agregarContactoCitado(){
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
    $("input[name='tipo_persona_citado']").change(function(){
        if($("input[name='tipo_persona_citado']:checked").val() == 1){
            $(".personaFisicaCitado input").attr("required","");
            $(".personaMoralCitado input").removeAttr("required");
            $(".personaFisicaCitado select").attr("required","");
            $(".personaMoralCitado select").removeAttr("required");
            $(".personaMoralCitado").hide();
            $(".personaFisicaCitado").show();
            $(".personaFisicaCitadoNO").show();
        }else{
            $(".personaFisicaCitado input").removeAttr("required");
            $(".personaMoralCitado input").attr("required","");
            $(".personaFisicaCitado select").removeAttr("required");
            $(".personaMoralCitado select").attr("required","");
            $(".personaMoralCitado").show();
            $(".personaFisicaCitado").hide();
            $(".personaFisicaCitadoNO").hide();
        }
    });
</script>
@endpush

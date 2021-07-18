<!-- Fin Modal de Correos citado-->
<div class="modal" id="modal-registro-correos" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buz&oacute;n Electr&oacute;nico</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="parte_correo">
                <input type="hidden" id="parte_representada_correo">
                <div class="alert alert-muted">
                    - Para acceder al buz&oacute;n electr&oacute;nico se deber&aacute; registrar 
                    <ol>
                        <li>El CURP o RFC de la persona y </li>
                        <li>Un correo electr&oacute;nico al cual asociarlo.  </li>
                    </ol>
                    En el caso de que no se haya proporcionado un correo electr&oacute;nico con anterioridad podr&aacute; capturarlo en este momento, de lo contrario seleccione "Proporcionar accesos" y el sisteme le proporcionar&aacute; un pseudocorreo y una contrase&ntilde;a para acceder al buz&oacute;n eletr&oacute;nico.
                </div>
                <div id="divExisteCorreo">
                    <h3>Ya existe un correo asignado : <span id="correoParte"></span></h3>
                </div>
                <table class="table table-bordered table-striped table-hover" id="tableSolicitantesCorreo">
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th></th>
                            <th>RFC/CURP</th>
                            <th>Correo electr&oacute;nico</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="col-md-4">
                    <div >
                        <span class="text-muted m-l-5 m-r-20" for='switch1' id="aceptarNotifLabel">Acepto notificacion por buzon</span>
                    </div>
                    <div >
                        <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="aceptar_notif_buzon" name='aceptar_notif_buzon'/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" id="btnCancelarCorreos" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarCorreos"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Correo citado-->

@push('scripts')
<script>
    function correoBuzon(parte_correo_id,parte_id){
        $("#correoParte").html("")
        $("#parte_correo").val(parte_id);
        $("#parte_representada_correo").val(parte_correo_id);
        if($("#checkCompareciente"+parte_id).is(":checked")){
            $.ajax({
                url:'/parte/correo/'+parte_correo_id,
                type:'GET',
                dataType:"json",
                async:true,
                success:function(data){
                    if(!data.notificacion_buzon){
                        if(data.tieneCorreo){
                            $("#tableSolicitantesCorreo").hide();
                            $("#divExisteCorreo").show();
                            if(data.correo_buzon != null){
                                $("#correoParte").html(data.correo_buzon);
                            }else{
                                var correo = data.contactos.find(e => e.tipo_contacto_id == 3);
                                $("#correoParte").html(correo.contacto);
                            }
                            $("#modal-registro-correos").modal("show");
                        }else{
                            $("#divExisteCorreo").hide();
                            $("#tableSolicitantesCorreo").show();
                            var tableSolicitantes = '';
                                tableSolicitantes +='<tr>';
                                if(data.tipo_persona_id == 1){
                                    tableSolicitantes +='<td>'+data.nombre+' '+data.primer_apellido+' '+(data.segundo_apellido|| "")+'</td>';
                                }else{
                                    tableSolicitantes +='<td>'+data.nombre_comercial+'</td>';
                                }
                                tableSolicitantes += '  <td>';
                                tableSolicitantes += '      <div class="col-md-12">';
                                tableSolicitantes += '          <span class="text-muted m-l-5 m-r-20" for="checkCorreo'+data.id+'">Proporcionar accesos</span>';
                                tableSolicitantes += '          <input type="checkbox" class="checkCorreo" data-id="'+data.id+'" checked="checked" id="checkCorreo'+data.id+'" name="checkCorreo'+data.id+'" onclick="checkCorreo('+data.id+')"/>';
                                tableSolicitantes += '      </div>';
                                tableSolicitantes += '  </td>';
                                tableSolicitantes += '  <td>';
                                if(data.tipo_persona_id == 1){
                                    tableSolicitantes += '      <input type="text" class="form-control upper" value="'+(data.curp || '') +'" id="rfcCurpValidar'+data.id+'">';
                                }else{
                                    tableSolicitantes += '      <input type="text" class="form-control upper" value="'+(data.rfc || '') +'" id="rfcCurpValidar'+data.id+'">';
                                }
                                tableSolicitantes += '  </td>';
                                tableSolicitantes += '  <td>';
                                tableSolicitantes += '      <input type="text" class="form-control" disabled="disabled" id="correoValidar'+data.id+'">';
                                tableSolicitantes += '  </td>';
                                tableSolicitantes +='</tr>';
                            $("#tableSolicitantesCorreo tbody").html(tableSolicitantes);
                            $("#modal-registro-correos").modal("show");
                        }
                    }
                }
            });
        }
    }

    function checkCorreo(id){
        if(!$("#checkCorreo"+id).is(":checked")){
            $("#correoValidar"+id).prop("disabled",false);
        }else{
            $("#correoValidar"+id).prop("disabled",true);
        }
    }

    $("#btnCancelarCorreos").on("click",function(){
        var parte_id = $("#parte_correo").val();
        $("#checkCompareciente"+parte_id).prop("checked",false);
    });
    $("#btnGuardarCorreos").on("click",function(){
        swal({
            title: '¿Estas seguro?',
            text: 'Al aceptar el buzón se generará el Acta de Aceptación. En caso de no aceptarlo se generará el Acta de No Aceptación.',
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
                var validacion = validarCorreos();
                if(!validacion.error ){
                    if(!validacion.existeCorreo){
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
                                swal({
                                    title: 'Correcto',
                                    text: 'Información almacenada correctamente',
                                    icon: 'success'
                                });
                                aceptarBuzon();
                            },error:function(error){
                                swal({
                                    title: 'Error',
                                    text: 'Ocurrio un error al guardar los correos',
                                    icon: 'warning'
                                });
                            }
                        });
                    }else{
                        $("#modal-registro-correos").modal("hide");
                        aceptarBuzon();
                    }
                    
                }else{
                    // if(!$("#aceptar_notif_buzon").is(":checked")){
                    //     swal({
                    //         title: 'Error',
                    //         text: 'Es necesario aceptar buzon electronico',
                    //         icon: 'warning'
                    //     });
                    // }else{
                        if(validacion.crearAcceso){
                            swal({
                                title: 'Error',
                                text: 'Si desea generar accesos, se deben proporcionar el RFC/CURP',
                                icon: 'warning'
                            });
                        }else{
                            swal({
                                title: 'Error',
                                text: 'Si no se desea generar accesos, se deben proporcionar los correos',
                                icon: 'warning'
                            });
                        }
                    // }
                }
            }
        });
        
    });

    function aceptarBuzon(){
        $.ajax({
            url:'/aceptar_buzon',
            type:'POST',
            dataType:"json",
            async:true,
            data:{
                _token:"{{ csrf_token() }}",
                acepta_buzon:$("#aceptar_notif_buzon").is(":checked"),
                parte_id:$("#parte_representada_correo").val()
            },
            success:function(data){
                $("#modal-registro-correos").modal("hide");
                swal({
                    title: 'Correcto',
                    text: 'Información almacenada correctamente',
                    icon: 'success'
                });
            },error:function(error){
                swal({
                    title: 'Error',
                    text: 'Ocurrio un error al guardar los correos',
                    icon: 'warning'
                });
            }
        });
    }

    function validarCorreos(){
        var respuesta = new Array();
        var listaCorreos = [];
        var error = false;
        if($("#correoParte").html() == ""){
            $.each($(".checkCorreo"),function(index,element){
                var id = $(element).data('id');
                respuesta.crearAcceso =true;
                $("#correoValidar"+id).css("border-color","");
                $("#rfcCurpValidar"+id).css("border-color","");
                if($(element).is(":checked")){
                    if( $("#rfcCurpValidar"+id).val() != ""){
                        listaCorreos.push({
                            crearAcceso:true,
                            correo:"",
                            rfcCurp:$("#rfcCurpValidar"+id).val(),
                            parte_id:id
                        });
                    }else{
                        error = true;
                        $("#rfcCurpValidar"+id).css("border-color","red");
                    }
                }else{
                    respuesta.crearAcceso =false;
                    if($("#correoValidar"+id).val() != "" && $("#rfcCurpValidar"+id).val() != ""){
                        listaCorreos.push({
                            crearAcceso:false,
                            correo:$("#correoValidar"+id).val(),
                            rfcCurp:$("#rfcCurpValidar"+id).val(),
                            parte_id:id
                        });
                    }else{
                        error = true;
                        $("#correoValidar"+id).css("border-color","red");
                        $("#rfcCurpValidar"+id).css("border-color","red");
                    }
                }
            });
            respuesta.existeCorreo =false;
        }else{
            respuesta.existeCorreo =true;
        }
        
        respuesta.error=error;
        respuesta.listaCorreos=listaCorreos;
        return respuesta;
    }
</script>
@endpush
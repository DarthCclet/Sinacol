<input id="parte_dom_id" type="hidden" />
<!--Inicio modal para fechas de pagos diferidos convenio-->
<div class="modal" id="modal-seleccionar-parte" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" style="overflow: scroll">
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
                        <tbody id="tbodyCitado">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Fin de modal pagos diferidos-->
<!--Inicio modal para fechas de pagos diferidos convenio-->
<div class="modal" id="modal-parte-domicilios" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Actualizar Información</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            </div>
            <div class="modal-body">
                <div class="col-md-4 personaFisica">
                    <input class="form-control upper " id="NombreCitado" required placeholder="Nombre del solicitante" type="text" value="">
                    <p class="help-block needed">Nombre del solicitante</p>
                </div>
                <div class="col-md-4 personaFisica ">
                    <input class="form-control upper " id="PrimerACitado" required placeholder="Primer apellido del solicitante" type="text" value="">
                    <p class="help-block needed">Primer apellido</p>
                </div>
                <div class="col-md-4 personaFisica">
                    <input class="form-control upper " id="SegundoACitado" placeholder="Segundo apellido del solicitante" type="text" value="">
                    <p class="help-block">Segundo apellido</p>
                </div>
                <div class="col-md-12 personaMoral">
                    <input class="form-control upper " id="NombreCCitado" placeholder="Raz&oacute;n social" type="text" value="">
                    <p class="help-block needed">Raz&oacute;n social</p>
                </div>
                <div id="divMapaCitado">
                    <div  class="col-md-12 row">
                        <div class="row">
                            <h4>Domicilio(s)</h4>
                            <hr class="red">
                        </div>
                        @include('includes.component.map',['identificador' => 'citado','needsMaps'=>"true", 'instancia' => 1])
                        <div style="margin-top: 2%;" class="col-md-12">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnActualizarParte"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Fin de modal pagos diferidos-->

@push('scripts')
<script>
    var arrayCitados = []; //Lista de citados
    /**
    * Funcion para generar tabla a partir de array de solicitados
    */
    function formarTablaCitado(){
        var html = "";

        $("#tbodyCitado").html("");
        $.each(arrayCitados, function (key, value) {
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

            html += "<td style='text-align: center;'><a class='btn btn-xs btn-primary' onclick='cargarEditarCitado("+key+")'><i class='fa fa-pencil-alt'></i></a> ";
            html += "</tr>";
        });
        $("#tbodyCitado").html(html);
    }

    function cargarEditarCitado(id){
        $("#parte_dom_id").val(arrayCitados[id].id);
        if(arrayCitados[id].tipo_persona_id == 1){
            $(".personaFisica").show();
            $(".personaMoral").hide();
            $("#NombreCitado").val(arrayCitados[id].nombre);
            $("#PrimerACitado").val(arrayCitados[id].primer_apellido);
            $("#SegundoACitado").val(arrayCitados[id].segundo_apellido);
        }else{
            $(".personaMoral").show();
            $(".personaFisica").hide();
            $("#NombreCCitado").val(arrayCitados[id].nombre_comercial);
        }
        domicilioObj2.cargarDomicilio(arrayCitados[id].domicilios[0]);
        $("#modal-parte-domicilios").modal("show")
        $("#modal-seleccionar-parte").modal("hide");
    }

    function loadCitados(){
        $.ajax({
            url:"/partes/getCitados/"+$("#solicitud_id").val(),
            type:"GET",
            dataType:"json",
            async:false,
            success:function(json){
                try{
                    if(json.success){
                        arrayCitados = json.data;
                        formarTablaCitado();
                    }
                }catch(error){
                    console.log(error);
                }
            },error:function(data){
                swal({
                    title: 'Error',
                    text: ' Error al cargar citados ',
                    icon: 'error'
                });
            }
        });
    }
    $("#btnActualizarParte").click(function(){
        // var citado = {};
        // citado.id = $("#parte_dom_id").val();
        // citado.nombre = $("#NombreCitado").val();
        // citado.primer_apellido = $("#PrimerACitado").val();
        // citado.segundo_apellido = $("#SegundoACitado").val();
        // citado.nombre_comercial = $("#NombreCCitado").val();
        // var domicilio = {};
        // domicilio = domicilioObj.getDomicilio();
        // citado.domicilio = domicilio;
        $.ajax({
            url:"/parte/"+$("#parte_dom_id").val(),
            type:"PUT",
            data:{
                nombre:$("#NombreCitado").val(),
                primer_apellido : $("#PrimerACitado").val(),
                segundo_apellido : $("#SegundoACitado").val(),
                nombre_comercial : $("#NombreCCitado").val(),
                domicilio : domicilioObj2.getDomicilio(),
                _token:"{{ csrf_token() }}"
            },
            dataType:"json",
            success:function(json){
                try{
                    location.reload();
                }catch(error){
                    console.log(error);
                }
            },error:function(data){
                swal({
                    title: 'Error',
                    text: ' Error al cargar citados ',
                    icon: 'error'
                });
            }
        });
    });
</script>
@endpush

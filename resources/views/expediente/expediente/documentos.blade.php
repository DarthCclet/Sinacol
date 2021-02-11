
{{-- @section('content') --}}
    <!-- begin page-header -->
    <div class=" col-md-12 row">
        <h1 class="page-header col-md-6">Documentos del expediente</h1>
        <div class="float-right col-md-6">
            <button class="btn btn-primary" style="float: right;" onclick="$('#modal_cargar_documento').modal('show')"><i class="fa fa-plus"></i> Agregar Documento</button>
        </div>
    </div>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div class="panel-heading-btn">
                
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            <div class="col-md-12 row">

                <div class="col-md-6">
                    <h3>Solicitud</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        @foreach ($documentos as $documento)
                        @if ($documento->tipo_doc == 1 || $documento->tipo_doc == 2 )    
                            @if($documento->clasificacion_archivo_id == 37)
                                <tr>
                                    <td>{{isset($documento->nombre) ? $$documento->nombre:""}} </td><td><a class="btn btn-link" href="/api/documentos/getFile/{{$documento->uuid}}" target="_blank">Descargar</a></td>
                                </tr>
                            @else
                                <tr>
                                    <td><b>{{isset($documento->parte) ? $documento->parte."- ":""  }}</b>{{$documento->clasificacionArchivo->nombre}} </td><td><a class="btn btn-link" href="/api/documentos/getFile/{{$documento->uuid}}" target="_blank">Descargar</a></td>
                                </tr>
                            @endif

                        @endif
                        @endforeach
                    </table>
                </div>
                {{-- <hr style="height:2px;border-width:0;color:lightgray;background-color:lightgray"> --}}
                <div class="col-md-6">
                    <h3>Audiencias</h3>
                    <table class="table table-bordered" >
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        @foreach ($documentos as $documento)
                            @if ($documento->tipo_doc == 3)    
                                <tr>
                                    <td><b>{{isset($documento->audiencia_id) ? "".$documento->audiencia."- ":""  }}</b>{{$documento->clasificacionArchivo->nombre}} </td><td><a class="btn btn-link" href="/api/documentos/getFile/{{$documento->uuid}}" target="_blank">Descargar</a></td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

{{-- @endsection --}}
{{-- Modal confirma falta de correo --}}
<div class="modal" id="modal_cargar_documento" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5>Agregar Documento</h5>
                <div>
                    <div class="col-md-6">
                        <span class="btn btn-primary fileinput-button m-r-3">
                            <i class="fa fa-fw fa-plus"></i>
                            <span>Seleccionar Documento</span>
                            <input type="file" id="fileDocumento" accept=".pdf" name="files">
                        </span>
                        <p style="margin-top: 1%;" id="labelDocumento"></p>
                    </div>
                    <div class="col-md-8 ">
                        <input class="form-control" id="nombre_documento" placeholder="Nombre del documento" type="text" value="">
                        <p class="help-block needed">Nombre del documento</p>
                    </div>
                    <div>
                        <textarea rows="4" class="form-control" id="descripcion" ></textarea>
                        <p class="help-block needed">Justificaci&oacute;n</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal" ><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" onclick="guardarDocumento();"  ><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $("#fileDocumento").change(function(e){
            $("#labelDocumento").html("<b>Archivo: </b>"+e.target.files[0].name+"");
        });

        function guardarDocumento(){
            var formData = new FormData(); 
            if($("#fileDocumento").val() != ""){
                formData.append('fileDocumento', $("#fileDocumento")[0].files[0]);
            }else{
                swal({title: 'Error',text: 'Es necesario cargar el documento',icon: 'warning'});
                return false;
            }
            if($("#nombre_documento").val() == "" || $("#descripcion").val() == "" || $("#solicitud_id").val() == "" ){
                swal({title: 'Error',text: 'Todos los campos son obligatorios',icon: 'warning'});
                return false;
            }
            formData.append('nombre_documento', $("#nombre_documento").val());
            formData.append('descripcion', $("#descripcion").val());
            formData.append('solicitud_id', $("#solicitud_id").val());
            formData.append('_token', "{{ csrf_token() }}");
            $.ajax({
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    var progreso = 0;
                     // Download progress
                     xhr.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            // Do something with download progress
                            console.log(percentComplete);
                            $('#progress-bar').show();
                            var percent = parseInt(percentComplete * 100)
                            $("#progressbar-ajax-value").text(percent+"%");;
                            $('#progressbar-ajax').css({
                                width: percent + '%'
                            });
                            if (percentComplete === 1) {
                                $('#progress-bar').hide();
                                $('#progressbar-ajax').css({
                                    width: '0%'
                                });
                            }
                        }
                    }, false);
                    // Upload progress
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            //Do something with upload progress
                            console.log(percentComplete);
                            $('#progress-bar').show();
                            var percent = parseInt(percentComplete * 100)
                            $("#progressbar-ajax-value").text(percent+"%");;
                            $('#progressbar-ajax').css({
                                width: percent + '%'
                            });
                            if (percentComplete === 1) {
                                $('#progress-bar').hide();
                                $('#progressbar-ajax').css({
                                    width: '0%'
                                });
                            }
                        }
                    }, false);
                    return xhr;
                },
                url:"/guardar_documento",
                type:"POST",
                dataType:"json",
                processData: false,
                contentType: false,
                data:formData,
                success:function(data){
                    try{
                        console.log(data);
                        if(data.success){
                            swal({title: 'ÉXITO',text: data.message,icon: 'success'});
                            location.reload();
                        }else{
                            swal({title: 'Error',text: data.message,icon: 'warning'});
                        }
                    }catch(error){
                        console.log(error);
                    }
                },
                error: function(){
                    swal({title: 'Error',text: 'No se pudo capturar el representante legal, revisa que el tamaño de tus documentos nos sea mayo a 10M ',icon: 'warning'});
                }
            });
        }
    </script>
@endpush
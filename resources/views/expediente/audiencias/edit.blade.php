@extends('layouts.default', ['paceTop' => true])

@section('title', 'Audiencias')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.dropzone')

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Centros</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar Audiencias <small>Resolución de Audiencias</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <a href="{!! route('audiencias.index') !!}" class="btn btn-info btn-sm pull-right"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#default-tab-1" data-toggle="tab" class="nav-link active">
                <span class="d-sm-none">Aud</span>
                <span class="d-sm-block d-none">Audiencia</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#default-tab-2" data-toggle="tab" class="nav-link">
                <span class="d-sm-none">Docs</span>
                <span class="d-sm-block d-none">Documentos</span>
            </a>
        </li>
    </ul>
    <div class="tab-content" style="background: #f2f3f4 !important;">
        <!-- begin tab-pane -->
        <div class="tab-pane fade active show" id="default-tab-1">
            @include('expediente.audiencias._form')
            <div class="text-right">
                <a href="{!! route('audiencias.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
                <button class="btn btn-primary btn-sm m-l-5" id='btnGuardar'><i class="fa fa-save"></i> Guardar resolución</button>
            </div>
        </div>
        <div class="tab-pane fade show row" id="default-tab-2">
            <div class="col-md-12">
                <div class="text-right">
                    <button class="btn btn-primary btn-sm m-l-5" id='btnAgregarArchivo'><i class="fa fa-plus"></i> Agregar documento</button>
                </div>
            </div><br>
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
    <!-- inicio Modal cargar archivos-->
    <div class="modal" id="modal-archivos" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Archivos de Audiencia</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form id="fileupload" action="/api/documentoAudiencia" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="audiencia_id[]" id='audiencia_id'/>
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
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#audiencia_id").val('{{ $audiencia->id }}');
            $("#duracionAudiencia").datetimepicker({format:"HH:mm"});
            $('#convenio').wysihtml5(); 
            $('#desahogo').wysihtml5(); 
            $(".tipo_documento").select2();
            cargarDocumentos();
            $.ajax({
                url:"/api/resoluciones",
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data.data.data != null && data.data.data != ""){
                        $("#resolucion_id").html("<option value=''>-- Selecciona un centro</option>");
                        $.each(data.data.data,function(index,element){
                            $("#resolucion_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }else{
                        $("#resolucion_id").html("<option value=''>-- Selecciona un centro</option>");
                    }
                    $("#resolucion_id").val('{{ $audiencia->resolucion_id }}').select2();
                }
            });
            
            FormMultipleUpload.init();
            Gallery.init();
        });
        $("#btnGuardar").on("click",function(){
            var validar = validarResolucion();
            if(!validar){
                $.ajax({
                    url:"/api/audiencia/resolucion",
                    type:"POST",
                    dataType:"json",
                    data:{
                        audiencia_id:'{{ $audiencia->id }}',
                        convenio:$("#convenio").val(),
                        desahogo:$("#desahogo").val(),
                        resolucion_id:$("#resolucion_id").val()
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            window.location.href = "{{ route('audiencias.index')}}";
                        }else{
                            swal({
                                title: 'Algo salio mal',
                                text: 'No se guardo el registro',
                                icon: 'warning'
                            });
                        }
                    }
                });
            }
        });
        function validarResolucion(){
            if($("#convenio").val() == ""){
                swal({title: 'Error',text: 'Describe el convenio',icon: 'warning'});
                return true;
            }
            if($("#desahogo").val() == ""){
                swal({title: 'Error',text: 'Describe el desahogo',icon: 'warning'});
                return true;
            }
            if($("#resolucion_id").val() == ""){
                swal({title: 'Error',text: 'Selecciona una resolucion',icon: 'warning'});
                return true;
            }
            return false;
        }
        $("#btnAgregarArchivo").on("click",function(){
            $("#btnCancelFiles").click();
            $("#modal-archivos").modal("show");
        });
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
                console.log(result);
                    $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});
            });
        };
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
        function cargarDocumentos(){
            $.ajax({
                url:"/api/audiencia/documentos/"+$("#audiencia_id").val(),
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    if(data != null && data != ""){
                        var table = "";
                        var div = "";
                        $.each(data, function(index,element){
                            console.log(element);
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
                       
                    }
                }
            });       
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

@endpush
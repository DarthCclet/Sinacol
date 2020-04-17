<h4 class="offset-2"><i class="fa fa-cog"></i>  Configuración de plantillas</h4><br>

    <label for="nombre-plantilla" class="control-label offset-2">Nombre de plantilla </label>
    {!! Form::text('nombre-plantilla', isset($plantillaDocumento->nombre_plantilla) ? $plantillaDocumento->nombre_plantilla : null, ['class'=>'form-control offset-2 col-md-8 ', 'id'=>'nombre-plantilla', 'placeholder'=>'Nombre de la plantilla', 'maxlength'=>'60', 'size'=>'10', 'autofocus'=>true]) !!}
    <br>
     <label for="tipo-plantilla-id" class="control-label offset-2">Tipo de plantilla </label>

  <div class="col-md-8 offset-2">
      {!! Form::select('tipo-plantilla-id', isset($tipo_plantilla) ? $tipo_plantilla : [] ,  isset($plantillaDocumento)? $plantillaDocumento->tipo_documento_id: null , ['id'=>'tipo-plantilla-id','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
      {!! $errors->first('tipo-plantilla-id', '<span class=text-danger>:message</span>') !!}
  </div>

    <br><br><br>
    <label for="nombre-plantilla" class="control-label offset-2">Contenido de plantilla </label>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <div id="objeto"></div>
            <div id="plantilla-header" class="sectionPlantilla" style="border:solid 1px lightgray;" contenteditable="true" >{!! isset($plantillaDocumento->plantilla_header) ? $plantillaDocumento->plantilla_header : "" !!}</div>

            <div id="plantilla-body" class="sectionPlantilla" style="border:solid 1px lightgray;" contenteditable="true">{!! isset($plantillaDocumento->plantilla_body) ? $plantillaDocumento->plantilla_body : "" !!}</div>

            <div id="plantilla-footer" style="border:solid 1px lightgray;">{!! isset($plantillaDocumento->plantilla_footer) ? $plantillaDocumento->plantilla_footer : "" !!}</div>

        </div>
        <div class="col-md-2"></div>
    </div>


@push('scripts')
    <script src='/js/tinymce/tinymce.min.js'></script>

    <script>
        var config_tmce = function(selector, objDoc = null) {
          let botonesHeader = ""
          let botonesBody = ""
          let botonesFooter = ""

          if(selector == "#plantilla-header"){
            botonesHeader = "btnHeader | ";
          }
          if(selector == "#plantilla-body"){
            botonesBody = "btnBody | ";
          }
          if(selector == "#plantilla-footer"){
            botonesBody = "btnFooter | ";
          }

          return {
                auto_focus: 'plantilla-header',
                selector: selector,
                document_base_url: '/public',
                relative_urls: false,
                language: 'es_MX',
                width: "622",//"670"
                language_url: '/js/tinymce/langs/es_MX.js',
                inline: true,
                menubar: false,
                toolbar_items_size: 'small',
                plugins: [
                    'noneditable advlist autolink lists link image imagetools preview',
                    ' media table paste pagebreak uploadimage lineheight'
                ],
                toolbar1: botonesHeader + botonesBody + 'basicDateButton | mybutton | fontselect fontsizeselect textcolor| undo redo ' +
                '| bold italic underline| alignleft aligncenter alignright alignjustify | bullist numlist ' +
                '| outdent indent lineheightselect | table pagebreak | uploadimage image  ',
                toolbar2: "",
                // paste_data_images: true,
              	images_upload_handler: function (blobInfo, success, failure) {
              		success("data:" + blobInfo.blob().type + ";base64," + blobInfo.base64());
              	},
              	url:'img/logo/logo-stps-786x196.png',
                image_title: true,
                automatic_uploads: true,
                file_picker_types: 'image',
                font_formats: 'Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva',
                paste_as_text: true,
                lineheight_formats: "6pt 8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt",
                // image_list: [
                //   {title: 'LogoSTPS', value: 'https://192.168.10.10/assets/img/logo/logo-stps-786x196.png'},
                //   {title: 'Logo', value: 'https://192.168.10.10/assets/img/logo/logo-stps-786x196.png'}
                // ],
                file_picker_callback: function (cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.onchange = function () {
                        var file = this.files[0];
                        var reader = new FileReader();
                        reader.onload = function () {
                            var id = 'blobid' + (new Date()).getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);
                            cb(blobInfo.blobUri(), {title: file.name});
                        };
                        reader.readAsDataURL(file);
                    };
                    input.click();
                },
                setup: function (editor) {
                  //Editor Body
                  if(selector == "#plantilla-body"){
                    arrayMenuBody =  [];
                    var arrSubmenuBodyCounter =  [];
                    if(objDoc == null){
                      objDoc = {!! json_encode($objetoDocumento)!!};
                    }
                      $.each( objDoc, function( key, objeto ) {
                            var menu = {};
                            var arrSubmenuBody =  [];
                            $.each( objeto['campos'], function( key, column ) {
                                submenu =
                                {
                                  type: 'menuitem', //nestedmenuitem menuitem
                                  text: column,
                                  onAction: function (_) {
                                    let dato = (objeto['nombre']+"_"+column).toUpperCase();
                                    let datoId = (objeto['nombre']+"_"+column).toLowerCase();
                                    editor.insertContent('<strong class="mceNonEditable" data-nombre="'+(datoId)+'">['+dato+']</strong>&nbsp;\n');
                                    // editor.insertContent('<strong class="mceNonEditable" data-nombre="solicitud_fecha_ratificacion">[fecha R]</strong>&nbsp;\n');
                                  }
                                };
                                arrSubmenuBody.push(submenu);
                            });
                            arrSubmenuBodyCounter[key] = arrSubmenuBody;
                            menu =
                            {
                              type: 'nestedmenuitem', //nestedmenuitem menuitem
                              text: objeto['nombre'],
                              getSubmenuItems: function () {
                                  return arrSubmenuBodyCounter[key];
                              }
                            };
                            arrayMenuBody.push(menu);
                      });
                  }
                    editor.on('init', function (ed) {
                        ed.target.editorCommands.execCommand("fontName", false, "Arial");
                        // ed.editorCommands.execCommand(ed,'img/logo/logo-stps-786x196.png')
                    });

                    editor.ui.registry.addButton('btnHeader', {
                      text: 'Logo',
                      onAction: function (_) {
                        editor.insertContent('<img style="width:35%;"" src="https://192.168.10.10/assets/img/logo/logo-stps-786x196.png"></img>');
                      }
                      // onAction: () => alert('Button clicked!')
                    });
                    editor.ui.registry.addMenuButton('btnBody', {
                    //     type: 'menubutton',
                        text: 'Variables',
                        // icon: false,
                      fetch: function (callback) {
                        var items = //[
                          arrayMenuBody;
                        callback(items);
                      }
                    });
                    editor.ui.registry.addMenuButton('btnFooter', {
                      text: 'Pie de Pagina',
                      fetch: function (callback) {
                        var itemsF = [
                          {
                              type: 'menuitem',
                              text: 'Fecha',
                              tooltip: 'Insert Current Date',
                              onAction: function (_) {
                                const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio","Julio", "Agosto", "Septiembre", "Octubre", "Noivembre", "Diciembre"];
                                let today = new Date();
                                let dd = today.getDate();
                                let mm = monthNames[today.getMonth()];
                                let yyyy = today.getFullYear();
                                today = dd+' de '+mm+' de '+yyyy;
                                editor.insertContent( today );
                              }
                          },
                          {
                              type: 'menuitem',
                              text: 'Lugar',
                              onAction: function (_) {
                                editor.insertContent("Ciudad de Mexico ");
                              }
                          },
                        ]
                        callback(itemsF);
                      }
                    });
                }
            };
        };

        $('#tipo-plantilla-id').change(function() {
          $.ajax({
              url:"/api/plantilla-documento/cargarVariables",
              type:"POST",
              data:{
                  id:$('#tipo-plantilla-id').val()
              },
              dataType:"json",
              success:function(data){
                  if(data != null && data != ""){
                    tinymce.execCommand('mceRemoveEditor', true, "plantilla-body");
                    tinymce.init(config_tmce('#plantilla-body',data));
                  }
              }
          });
        });

        tinymce.init(config_tmce('#plantilla-header'));
        tinymce.init(config_tmce('#plantilla-body'));
        tinymce.init(config_tmce('#plantilla-footer'));
    </script>
@endpush


<h4 class="offset-2"><i class="fa fa-cog"></i>  Configuración de plantillas</h4><br>

    <label for="nombre-plantilla" class="control-label offset-2">Nombre de plantilla </label>
    {!! Form::text('nombre-plantilla', isset($plantillaDocumento->nombre_plantilla) ? $plantillaDocumento->nombre_plantilla : null, ['class'=>'form-control offset-2 col-md-8 ', 'id'=>'nombre-plantilla', 'placeholder'=>'Nombre de la plantilla', 'maxlength'=>'60', 'size'=>'10', 'autofocus'=>true]) !!}
    <br>
     <label for="tipo-plantilla-id" class="control-label offset-2">Tipo de plantilla </label>

  <div class="col-md-8 offset-2">
      {!! Form::select('tipo-plantilla-id', isset($tipo_plantilla) ? $tipo_plantilla : [] ,  isset($plantillaDocumento)? $plantillaDocumento->tipo_documento_id: null , ['id'=>'tipo-plantilla-id','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect']);  !!}
      {!! $errors->first('tipo-plantilla-id', '<span class=text-danger>:message</span>') !!}
  </div>

    <br>
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
        var config_tmce = function(selector) {
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
                auto_focus: 'plantilla-body',
                selector: selector,
                // language: 'es_MX',
                width: "622",//"670"
                // language_url: '/js/tinymce/languages/es_MX.js',
                inline: true,
                menubar: false,
                toolbar_items_size: 'small',
                plugins: [
                    'noneditable advlist autolink lists link image imagetools preview',
                    ' media table paste pagebreak'
                ],
                toolbar1: botonesHeader + botonesBody + 'basicDateButton | mybutton | fontselect fontsizeselect | undo redo ' +
                '| bold italic underline| alignleft aligncenter alignright alignjustify | bullist numlist ' +
                '| outdent indent | table pagebreak',
                toolbar2: "",
                image_title: true,
                automatic_uploads: true,
                file_picker_types: 'image',
                font_formats: 'Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva',
                paste_as_text: true,
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
                    @foreach($objetoDocumento as $key=>$objeto)
                        var menu = {};
                        var arrSubmenuBody =  [];
                        @foreach($objeto['campos'] as $column)
                            submenu =
                            {
                              type: 'menuitem', //nestedmenuitem menuitem
                              text: "{{ $column }}",
                              onAction: function (_) {
                                let dato = "{{ strtoupper( $objeto['nombre']."_".$column) }}";
                                let datoId = "{{ strtolower( $objeto['nombre']."_".$column) }}";
                                editor.insertContent('<strong class="mceNonEditable" data-nombre="'+(datoId)+'">['+dato+']</strong>&nbsp;\n');
                                // editor.insertContent('<strong class="mceNonEditable" data-nombre="solicitud_fecha_ratificacion">[fecha R]</strong>&nbsp;\n');
                              }
                            };
                            arrSubmenuBody.push(submenu);
                        @endforeach
                        arrSubmenuBodyCounter[{{$key}}] = arrSubmenuBody;
                        console.log(arrSubmenuBodyCounter);
                        menu =
                        {
                          type: 'nestedmenuitem', //nestedmenuitem menuitem
                          text: "{{ $objeto['nombre'] }}",
                          getSubmenuItems: function () {
                              return arrSubmenuBodyCounter[{{$key}}];
                          }
                        };
                    // console.log(menu);
                        arrayMenuBody.push(menu);
                    @endforeach
                    // console.log(arrayMenuBody);
                  }
                    editor.on('init', function (ed) {
                        ed.target.editorCommands.execCommand("fontName", false, "Arial");
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
                          arrayMenuBody
                            // {
                            //     type: 'menuitem',
                            //     text: 'Empresa',
                            //     onAction: function (_) {
                            //       editor.insertContent(new Date());
                            //     }
                            // },
                            // {
                            //     type: 'menuitem',
                            //     text: 'Vehículo',
                            //     onAction: function (_) {
                            //       editor.insertContent(new Date());
                            //     }
                            // },

                            // {
                            //     type: 'nestedmenuitem',
                            //     text: 'Other formats',
                            //     getSubmenuItems: function () {
                            //       return [
                            //         {
                            //           type: 'menuitem',
                            //           text: 'GMT',
                            //           onAction: function (_) {
                            //             editor.insertContent(new Date());
                            //           }
                            //         },
                            //         {
                            //           type: 'menuitem',
                            //           text: 'ISO',
                            //           onAction: function (_) {
                            //             editor.insertContent("new ISO");
                            //           }
                            //         }
                            //       ];
                            //     }
                            // },

                            // {
                            //     type: 'nestedmenuitem',
                            //     text: 'OTRO',
                            //     getSubmenuItems: function () {
                            //       return arrayMenuBoby;
                            //     }
                            //
                            // }

                        //];
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

        tinymce.init(config_tmce('#plantilla-header'));
        tinymce.init(config_tmce('#plantilla-body'));
        tinymce.init(config_tmce('#plantilla-footer'));
    </script>
@endpush

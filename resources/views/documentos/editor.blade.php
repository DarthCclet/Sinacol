
<h4 class="offset-2"><i class="fa fa-cog"></i>  Configuración de plantillas</h4><br>

    <label for="nombre-plantilla" class="control-label offset-2">Nombre de plantilla </label>
    {!! Form::text('nombre-plantilla', isset($plantillaDocumento->nombre_plantilla) ? $plantillaDocumento->nombre_plantilla : null, ['class'=>'form-control offset-2 col-md-8 ', 'id'=>'nombre-plantilla', 'placeholder'=>'Nombre de la plantilla', 'maxlength'=>'60', 'size'=>'10', 'autofocus'=>true]) !!}
    <br><div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">

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
            return {
                auto_focus: 'plantilla-body',
                selector: selector,
                // language: 'es_MX',
                width: "670",
                // language_url: '/js/tinymce/languages/es_MX.js',
                inline: true,
                menubar: false,
                toolbar_items_size: 'small',
                plugins: [
                    'noneditable advlist autolink lists link image imagetools preview',
                    ' media table paste pagebreak'
                ],
                toolbar1: 'basicDateButton | mybutton | fontselect fontsizeselect | undo redo ' +
                '| bold italic underline| alignleft aligncenter alignright alignjustify | bullist numlist ' +
                '| outdent indent | link unlink image | table pagebreak',
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
                    editor.on('init', function (ed) {
                        ed.target.editorCommands.execCommand("fontName", false, "Arial");
                    });
                    // editor.ui.registry.addButton('mybutton', {
                    //   text: 'My Custom Button',
                    //   onAction: () => alert('Button clicked!')
                    // });
                    editor.ui.registry.addButton('basicDateButton', {
                      text: 'Insert Date',
                      tooltip: 'Insert Current Date',
                      onAction: function (_) {
                        // editor.insertContent(new Date());
                         // editor.insertContent('<strong class="mceNonEditable" data-nombre="nombre_empresa">[NOMBRE EMPRESA]</strong>&nbsp;\n');

                         editor.insertContent('<strong class="mceNonEditable" data-nombre="fecha_conflicto">[FECHA]</strong>&nbsp;\n');
                      }
                    });
                    editor.ui.registry.addMenuButton('mybutton', {
                    //     type: 'menubutton',
                        text: 'Variables',
                        // icon: false,
                      fetch: function (callback) {
                        var items = [
                            {
                                type: 'menuitem',
                                text: 'Fecha Actual',
                                onAction: function (_) {
                                  editor.insertContent(new Date());
                                }
                    //             menu:
                    //             [
                    //                 {
                    //                     text: 'Nombre de la empresa',
                    //                     onclick: function () {
                    //                         editor.insertContent('<strong class="mceNonEditable" data-nombre="nombre_empresa">[NOMBRE EMPRESA]</strong>&nbsp;\n');
                    //                     }
                    //                 },
                    //                 {
                    //                     text: 'Nombre legal de la empresa',
                    //                     onclick: function () {
                    //                         editor.insertContent('<strong class="mceNonEditable" data-nombre="nombre_legal_empresa">[NOMBRE LEGAL EMPRESA]</strong>&nbsp;\n');
                    //                     }
                    //                 },
                    //             ]
                            },
                            // {
                            //     type: 'menuitem',
                            //     text: 'Vehículo',
                            //     onAction: function (_) {
                            //       editor.insertContent(new Date());
                            //     }
                    //             menu:
                    //                 [
                    //                     {
                    //                         text: 'Placa',
                    //                         onclick: function () {
                    //                             editor.insertContent('<strong class="mceNonEditable" data-nombre="placa">[PLACA]</strong>&nbsp;\n');
                    //                         }
                    //                     },
                    //                     {
                    //                         text: 'NIV',
                    //                         onclick: function () {
                    //                             editor.insertContent('<strong class="mceNonEditable" data-nombre="niv">[NIV]</strong>&nbsp;\n');
                    //                         }
                    //                     },
                    //                     {
                    //                         text: 'Número de inventario',
                    //                         onclick: function () {
                    //                             editor.insertContent('<strong class="mceNonEditable" data-nombre="numero_inventario">[NÚMERO DE INVENTARIO]</strong>&nbsp;\n');
                    //                         }
                    //                     },
                    //                     {
                    //                         text: 'Número de motor',
                    //                         onclick: function () {
                    //                             editor.insertContent('<strong class="mceNonEditable" data-nombre="numero_motor">[NÚMERO DE MOTOR]</strong>&nbsp;\n');
                    //                         }
                    //                     },
                    //                     {
                    //                         text: 'Odómetro',
                    //                         onclick: function () {
                    //                             editor.insertContent('<strong class="mceNonEditable" data-nombre="odometro">[ODÓMETRO]</strong>&nbsp;\n');
                    //                         }
                    //                     },
                    //                     {
                    //                         text: 'Marca',
                    //                         onclick: function () {
                    //                             editor.insertContent('<strong class="mceNonEditable" data-nombre="marca">[MARCA]</strong>&nbsp;\n');
                    //                         }
                    //                     },
                    //                     {
                    //                         text: 'Submarca',
                    //                         onclick: function () {
                    //                             editor.insertContent('<strong class="mceNonEditable" data-nombre="submarca">[SUBMARCA]</strong>&nbsp;\n');
                    //                         }
                    //                     },
                    //                     {
                    //                         text: 'Modelo',
                    //                         onclick: function () {
                    //                             editor.insertContent('<strong class="mceNonEditable" data-nombre="modelo">[MODELO]</strong>&nbsp;\n');
                    //                         }
                    //                     },
                    //                     {
                    //                         text: 'Versión',
                    //                         onclick: function () {
                    //                             editor.insertContent('<strong class="mceNonEditable" data-nombre="version">[VERSIÓN]</strong>&nbsp;\n');
                    //                         }
                    //                     }
                    //                 ]
                            // },

                            {
                                type: 'nestedmenuitem',
                                text: 'Solicitante',
                                getSubmenuItems: function () {
                                  return [
                                    {
                                      type: 'menuitem',
                                      text: 'Nombre',
                                      onAction: function (_) {
                                        // editor.insertContent(new Date());
                                        editor.insertContent('<strong class="mceNonEditable" data-nombre="nombre">[NOMBRE]</strong>&nbsp;\n');
                                      }
                                    },
                                    {
                                      type: 'menuitem',
                                      text: 'Primer apellido',
                                      onAction: function (_) {
                                        editor.insertContent('<strong class="mceNonEditable" data-nombre="primer_apellido">[PRIMER APELLIDO]</strong>&nbsp;\n');
                                      }
                                    }
                                  ];
                                }

                            },
                            {
                                type: 'nestedmenuitem',
                                text: 'Solicitado',
                                getSubmenuItems: function () {
                                  return [
                                    {
                                      type: 'menuitem',
                                      text: 'Nombre',
                                      onAction: function (_) {
                                        editor.insertContent('<strong class="mceNonEditable" data-nombre="nombre">[NOMBRE]</strong>&nbsp;\n');
                                      }
                                    },
                                    {
                                      type: 'menuitem',
                                      text: 'Primer apellido',
                                      onAction: function (_) {
                                        editor.insertContent('<strong class="mceNonEditable" data-nombre="primer_apellido">[PRIMER APELLIDO]</strong>&nbsp;\n');
                                      }
                                    }
                                  ];
                                }

                            },
                            {
                                type: 'nestedmenuitem',
                                text: 'Solicitud',
                                getSubmenuItems: function () {
                                  return [
                                    {
                                      type: 'menuitem',
                                      text: 'Fecha Conflicto',
                                      onAction: function (_) {
                                        editor.insertContent('<strong class="mceNonEditable" data-nombre="fecha_conflicto">[FECHA]</strong>&nbsp;\n');
                                      }
                                    // },
                                    // {
                                    //   type: 'menuitem',
                                    //   text: 'Motivo',
                                    //   onAction: function (_) {
                                    //     // editor.insertContent('<strong class="mceNonEditable" data-nombre="primer_apellido">[PRIMER APELLIDO]</strong>&nbsp;\n');
                                    //     editor.insertContent("new ISO");
                                    //   }
                                    }
                                  ];
                                }

                            }
                        ];
                        callback(items);
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

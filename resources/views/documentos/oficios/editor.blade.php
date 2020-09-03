<h4 class="offset-2"><i class="fa fa-cog"></i>  Creación de oficio</h4><br>

  {!! Form::open([ 'route' => 'oficio-documento.imprimirPDF' ]) !!}
    <div class="">
    </div><br>
    <div class="row">
        <input type="hidden" name='id' value='{{$id}}'>
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <div id="oficio-header" name="oficio-header" class="sectionPlantilla" style="border:solid 1px lightgray;" contenteditable="true" >{!! isset($plantilla['plantilla_header']) ? $plantilla['plantilla_header'] : "<br>" !!}</div>

          <div id="oficio-body" name="oficio-body" class="sectionPlantilla" style="border:solid 1px lightgray;" contenteditable="true">{!! isset($plantilla['plantilla_body']) ? $plantilla['plantilla_body'] : "<br><br>" !!}</div>

          <div id="oficio-footer" name="oficio-footer" style="border:solid 1px lightgray;">{!! isset($plantilla['plantilla_footer']) ? $plantilla['plantilla_footer'] : "" !!}</div>
        </div>
        <div class="col-md-2"></div>
    </div>
    <div class="form-group">
      
      <button class="btn btn-danger"><i class="fa fa-save"></i> PDF </button>
    </div>
  {!! Form::close() !!}

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
                '| outdent indent | link unlink image | table pagebreak forecolor backcolor',
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
                }
            };
        };
        tinymce.init(config_tmce('#oficio-header'));
        tinymce.init(config_tmce('#oficio-body'));
        tinymce.init(config_tmce('#oficio-footer'));
    </script>
@endpush

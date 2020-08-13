<style>
    .needed:after {
      color:darkred;
      content: " (*)";
   }
	.widget-maps{
        min-height: 350px;
        position: relative;
        border: thin solid #c0c0c0;
        width:100%;
        height: 100%;
    }
    .panel-botones {
        position: absolute;
        right: 10%;
        z-index: 5;
        padding: 5px;
        text-align: center;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 10px;
    }
    .panel-botones {
        display: none;
        margin-left: 50%;
    }
    .pac-container {
        z-index: 9999 !important;
    }
    .pac-logo{
        z-index: 9999 !important;
    }
</style>
<div class="col-md-12 mt-4 row">
	<div class="col-md-12">
        <h4>Domicilios</h4>

        <hr class="red">
	</div>
	<input type="hidden" id="domicilio_id{{$identificador}}">
	<input type="hidden" id="direccion_marker{{$identificador}}">
    <input type="hidden" name="domicilio[latitud]" value="{{isset($domicilio->latitud) ? $domicilio->latitud : '' }}" id="latitud{{$identificador}}">
	<input type="hidden" name="domicilio[longitud]" value="{{isset($domicilio->longitud) ? $domicilio->longitud : '' }}" id="longitud{{$identificador}}">
    <input type="hidden" id="pais{{$identificador}}">
    <input type="hidden" id="tipo_vialidad{{$identificador}}" value="{{isset($domicilio->tipo_vialidad) ? $domicilio->tipo_vialidad : '' }}" name="domicilio[tipo_vialidad]">
    <input type="hidden" id="tipo_asentamiento{{$identificador}}" value="{{isset($domicilio->tipo_asentamiento) ? $domicilio->tipo_asentamiento : '' }}" name="domicilio[tipo_asentamiento]">
    <input type="hidden" id="estado{{$identificador}}" value="{{isset($domicilio->estado) ? $domicilio->estado : '' }}" name="domicilio[estado]">
    <div class="col-md-4">
        {{-- <input class="form-control direccionUpd{{$identificador}}" name="domicilio[vialidad]" id="vialidad{{$identificador}}" placeholder="Vialidad" required type="text" value=""> --}}
        {!! Form::text('domicilio[vialidad]', isset($domicilio->vialidad) ? $domicilio->vialidad : null, ['id'=>'vialidad'.$identificador,'required', 'class'=>'form-control direccionUpd'.$identificador, 'placeholder'=>'Calle']) !!}
        {!! $errors->first('domicilio[vialidad]', '<span class=text-danger>:message</span>') !!}
        <p class="help-block needed">Calle (vialidad)</p>
    </div>
    <div class="col-md-4">
        {{-- <input class="form-control numero direccionUpd{{$identificador}}" name="domicilio[num_ext]" id="num_ext{{$identificador}}" placeholder="Num Exterior" required type="text" value=""> --}}
        {!! Form::text('domicilio[num_ext]', isset($domicilio->num_ext) ? $domicilio->num_ext : null, ['id'=>'num_ext'.$identificador,'required', 'class'=>'numero form-control direccionUpd'.$identificador, 'placeholder'=>'Número Exterior']) !!}
        {!! $errors->first('domicilio[num_ext]', '<span class=text-danger>:message</span>') !!}
        <p class="help-block needed">Número exterior</p>
    </div>
    <div class="col-md-4">
        {{-- <input class="form-control numero" id="num_int{{$identificador}}" name="domicilio[num_int]" placeholder="Num Interior" required type="text" value=""> --}}
        {!! Form::text('domicilio[num_int]', isset($domicilio->num_int) ? $domicilio->num_int : null, ['id'=>'num_int'.$identificador, 'class'=>'numero form-control direccionUpd'.$identificador, 'placeholder'=>'Número Interior']) !!}
        {!! $errors->first('domicilio[num_int]', '<span class=text-danger>:message</span>') !!}
        <p class="help-block">Número interior</p>
    </div>
    <div class="col-md-12">
        <select name="autocomplete{{$identificador}}" placeholder="Seleccione" id="autocomplete{{$identificador}}" class="form-control"></select>
        <input type="hidden" id="term{{$identificador}}">
        <p class="help-block needed">Escriba la colonia correspondiente y seleccione la opción correcta o más cercana.</p>
    </div>
    <div class="col-md-4"   >
        {!! Form::select('domicilio[tipo_vialidad_id]', isset($tipos_vialidades) ? $tipos_vialidades : [] , isset($domicilio->tipo_vialidad_id) ? $domicilio->tipo_vialidad_id : 0, ['id'=>'tipo_vialidad_id'.$identificador,'required','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect'.$identificador.' direccionUpd'.$identificador]);  !!}
        {!! $errors->first('domicilio[tipo_vialidad_id]', '<span class=text-danger>:message</span>') !!}
        <p class="help-block needed">Tipo de vialidad</p>
    </div>
    <div class="col-md-4">
        {!! Form::select('domicilio[tipo_asentamiento_id]', isset($tipos_asentamientos) ? $tipos_asentamientos : [] , isset($domicilio->tipo_asentamiento_id) ? $domicilio->tipo_asentamiento_id : null, ['id'=>'tipo_asentamiento_id'.$identificador,'required','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect'.$identificador.' direccionUpd'.$identificador]);  !!}
        {!! $errors->first('domicilio[tipo_asentamiento_id]', '<span class=text-danger>:message</span>') !!}
        <p class="help-block needed">Tipo de asentamiento</p>
    </div>
    <div class="col-md-4">
        {{-- <input class="form-control direccionUpd{{$identificador}}" name="domicilio[asentamiento]" id="asentamiento{{$identificador}}" placeholder="Asentamiento" required type="text" value=""> --}}
        {!! Form::text('domicilio[asentamiento]', isset($domicilio->asentamiento) ? $domicilio->asentamiento : null, ['id'=>'asentamiento'.$identificador,'required', 'class'=>'form-control direccionUpd'.$identificador, 'placeholder'=>'Asentamiento']) !!}
        {!! $errors->first('domicilio[asentamiento]', '<span class=text-danger>:message</span>') !!}
        <p class="help-block needed">Colonia (asentamiento)</p>
    </div>


	<div class="col-md-4">
		{!! Form::select('domicilio[estado_id]', isset($estados) ? $estados : [] , isset($domicilio->estado_id) ? $domicilio->estado_id : 0, ['id'=>'estado_id'.$identificador,'required','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect'.$identificador.' direccionUpd'.$identificador]);  !!}
		{!! $errors->first('domicilio[estado_id]', '<span class=text-danger>:message</span>') !!}
		<p class="help-block needed">Estado </p>
	</div>
	<div class="col-md-4">
        {{-- <input class="form-control direccionUpd{{$identificador}}" name="domicilio[municipio]" id="municipio{{$identificador}}" required placeholder="Municipio" type="text" value=""> --}}

        {!! Form::select('domicilio[municipio]', isset($municipios) ? $municipios : [], isset($domicilio->municipio) ? $domicilio->municipio : '', ['id'=>'municipio'.$identificador,'required', 'class'=>'form-control '.' direccionUpd'.$identificador, 'placeholder'=>'Seleccione una opción']) !!}

        {!! $errors->first('domicilio[municipio]', '<span class=text-danger>:message</span>') !!}
		<p class="help-block needed">Nombre del municipio</p>
	</div>
	<div class="col-md-4">
		{{-- <input class="form-control numero" id="cp{{$identificador}}" name="domicilio[cp]" required placeholder="Código Postal" maxlength="5" type="text" value=""> --}}
        {!! Form::text('domicilio[cp]', isset($domicilio->cp) ? $domicilio->cp : null, ['id'=>'cp'.$identificador,'required', 'class'=>'numero form-control direccionUpd'.$identificador, 'placeholder'=>'Código Postal']) !!}
        {!! $errors->first('domicilio[cp]', '<span class=text-danger>:message</span>') !!}
		<p class="help-block needed">Código postal</p>
	</div>
	<div class="col-md-4">
        {{-- <input class="form-control" id="referencias{{$identificador}}" name="domicilio[referencias]" placeholder="Referencias" required type="text" value=""> --}}
        {!! Form::text('domicilio[referencias]', isset($domicilio->referencias) ? $domicilio->referencias : null, ['id'=>'referencias'.$identificador, 'class'=>'form-control direccionUpd'.$identificador, 'placeholder'=>'Referencias']) !!}
        {!! $errors->first('domicilio[referencias]', '<span class=text-danger>:message</span>') !!}
		<p class="help-block">Referencias</p>
	</div>
	<div class="col-md-4">
        {{-- <input class="form-control" id="entre_calle1{{$identificador}}" name="domicilio[entre_calle1]" placeholder="Entre calle" required type="text" value=""> --}}
        {!! Form::text('domicilio[entre_calle1]', isset($domicilio->entre_calle1) ? $domicilio->entre_calle1 : null, ['id'=>'entre_calle1'.$identificador, 'class'=>'form-control direccionUpd'.$identificador, 'placeholder'=>'Entre Calle']) !!}
        {!! $errors->first('domicilio[entre_calle1]', '<span class=text-danger>:message</span>') !!}
		<p class="help-block">Entre calle</p>
	</div>
	<div class="col-md-4">
        {{-- <input class="form-control" id="entre_calle2{{$identificador}}" name="domicilio[entre_calle2]" placeholder="Entre calle 2" required type="text" value=""> --}}
        {!! Form::text('domicilio[entre_calle2]', isset($domicilio->entre_calle2) ? $domicilio->entre_calle2 : null, ['id'=>'entre_calle2'.$identificador, 'class'=>'form-control direccionUpd'.$identificador, 'placeholder'=>'Y calle']) !!}
        {!! $errors->first('domicilio[entre_calle2]', '<span class=text-danger>:message</span>') !!}
		<p class="help-block">y calle</p>
    </div>
    @if($needsMaps == 'true')

        <div style="width:100%">
            <div class="panel-botones">
                <button class="btn btn-primary" type="button" id="validaDir{{$identificador}}"  > <i class="fa fa-map-marker"></i> Validar direcci&oacute;n</button>
            </div>
            <div class="widget-maps" id="widget-maps{{$identificador}}"></div>
        </div>
    @endif
</div>

@push('scripts')
<script>
	var identificador = '{{$identificador}}';
	var needsMaps = '{{$needsMaps}}';
	var DomicilioObject = {};
	DomicilioObject = (function(identifier,needMaps){
        var domicilio = {};
        domicilio.componentForm = {
          street_number: 'short_name',
          route: 'long_name',
          locality: 'long_name',
          administrative_area_level_1: 'long_name',
          sublocality_level_1: 'short_name',
          country: 'long_name',
          postal_code: 'short_name'
        };
        domicilio.campos = {
            street_number: 'num_ext'+identifier,
            route: 'vialidad'+identifier,
            locality: 'municipio'+identifier,
            sublocality_level_1: 'asentamiento'+identifier,
            administrative_area_level_1: 'estado_id'+identifier,
            country: 'pais'+identifier,
            postal_code: 'cp'+identifier
        }
        if(needMaps){
        domicilio.map;
        domicilio.marker;
        // domicilio.autocomplete;

            domicilio.initMap = function() {
                var lat = $('#latitud'+identifier).val() ? $('#latitud'+identifier).val() : "19.398606";
                var lon = $('#longitud'+identifier).val() ? $('#longitud'+identifier).val() : "-99.158581";

                this.map = new google.maps.Map(document.getElementById('widget-maps'+identifier), {
                    zoom: 15,
                    center: {lat: parseFloat(lat), lng: parseFloat(lon)},
                    zoomControl: true,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                this.map.panorama = this.map.getStreetView();
                this.map.panorama.addListener('visible_changed', function() {
                    if(!this.visible){
                        // document.getElementById('panel-botones'+identifier).style.display = "none";
                        $(".panel-botones").hide();
                    }else{
                        $(".panel-botones").show();
                        // document.getElementById('panel-botones'+identifier).style.display = "block";
                    }
                });

                if(needMaps == 'true'){
                    if($("#direccion_marker"+identifier).val() == ""){
                        this.seteaMarker(this.map, {lat: parseFloat(lat), lng: parseFloat(lon)});
                    }
                    else{
                        this.geocodeAddress();
                    }
                }

                // domicilio.autocomplete = new google.maps.places.Autocomplete(
                // document.getElementById('autocomplete'+identifier), {types: ['geocode']});
                // domicilio.autocomplete.setFields(['address_component']);
                // domicilio.autocomplete.addListener('place_changed', this.fillInAddress);
            };
            domicilio.tomarGeoreferencia = function () {
                var pos = this.map.panorama.getPosition() ;
                this.seteaMarker(this.map,pos);
            };
            domicilio.geocodeAddress = function() {
                var geocoder = new google.maps.Geocoder();
                var address = $("#direccion_marker"+identifier).val();
                geocoder.geocode({'address': address}, function(results, status) {
                    if (status === 'OK') {
                        domicilio.map.setCenter(results[0].geometry.location);
                        domicilio.seteaMarker(domicilio.map, results[0].geometry.location);
                        $('#btn-confirmar-direccion'+identifier).removeClass('disabled');
                    } else {
                        console.log('No se pudo completar el geocoding: %s', status);
                    }
                });
            }
            domicilio.seteaMarker = function (resultsMap, coords) {
                if(this.marker) this.borraMarker();
                this.marker = new google.maps.Marker({
                    map: resultsMap,
                    draggable: true,
                    animation: google.maps.Animation.DROP,
                    position: coords
                });
                $('#latitud'+identifier).val(coords.lat);
                $('#longitud'+identifier).val(coords.lng);
                $('#btn-confirmar-direccion'+identifier).removeClass('disabled');
                this.marker.addListener('dragend', this.seteaNuevaPosicionManual);
            };
            domicilio.seteaNuevaPosicionManual = function(ev){
                $('#latitud'+identifier).val(ev.latLng.lat());
                $('#longitud'+identifier).val(ev.latLng.lng());
                $('#btn-confirmar-direccion'+identifier).removeClass('disabled');
            }
            domicilio.borraMarker = function(){
                this.marker.setMap(null);
            }
        }

        domicilio.getDomicilio = function(){
            var domicilioLoc = {};
            domicilioLoc.id = $("#domicilio_id"+identifier).val();
            domicilioLoc.num_ext = $("#num_ext"+identifier).val();
            domicilioLoc.num_int = $("#num_int"+identifier).val();
            domicilioLoc.asentamiento = $("#asentamiento"+identifier).val();
            domicilioLoc.municipio = $("#municipio"+identifier+" option:selected").text();
            // domicilioLoc.municipio = $("#municipio"+identifier).val();
            domicilioLoc.cp = $("#cp"+identifier).val();
            domicilioLoc.entre_calle1 = $("#entre_calle1"+identifier).val();
            domicilioLoc.entre_calle2 = $("#entre_calle2"+identifier).val();
            domicilioLoc.referencias = $("#referencias"+identifier).val();
            domicilioLoc.tipo_vialidad_id = $("#tipo_vialidad_id"+identifier).val();
            domicilioLoc.tipo_vialidad = $("#tipo_vialidad"+identifier).val();
            domicilioLoc.vialidad = $("#vialidad"+identifier).val();
            domicilioLoc.tipo_asentamiento_id = $("#tipo_asentamiento_id"+identifier).val();
            domicilioLoc.tipo_asentamiento = $("#tipo_asentamiento"+identifier).val();
            domicilioLoc.estado_id = $("#estado_id"+identifier).val();
            domicilioLoc.estado = $("#estado"+identifier).val();
            domicilioLoc.latitud = $("#latitud"+identifier).val();
            domicilioLoc.longitud = $('#longitud'+identifier).val();
            domicilioLoc.activo = "1";
            return domicilioLoc;
        }
        domicilio.cargarDomicilio = function(domicilios){
            $("#domicilio_id"+identifier).val(domicilios.id);
            $("#num_ext"+identifier).val(domicilios.num_ext);
            $("#num_int"+identifier).val(domicilios.num_int);
            $("#asentamiento"+identifier).val(domicilios.asentamiento);
            $("#municipio"+identifier+" option:contains("+ domicilios.municipio +")").prop("selected",true).trigger("change");
            $("#cp"+identifier).val(domicilios.cp);
            $("#entre_calle1"+identifier).val(domicilios.entre_calle1);
            $("#entre_calle2"+identifier).val(domicilios.entre_calle2);
            $("#referencias"+identifier).val(domicilios.referencias);
            $("#tipo_vialidad_id"+identifier).val(domicilios.tipo_vialidad_id);
            $("#vialidad"+identifier).val(domicilios.vialidad);
            $("#tipo_asentamiento_id"+identifier).val(domicilios.tipo_asentamiento_id);
            $("#estado_id"+identifier).val(domicilios.estado_id);
            $("#latitud"+identifier).val(domicilios.latitud);
            $("#longitud"+identifier).val(domicilios.longitud);
            var lat = $('#latitud'+identifier).val() ? $('#latitud'+identifier).val() : "19.398606";
            var lon = $('#longitud'+identifier).val() ? $('#longitud'+identifier).val() : "-99.158581";
            $(".direccionUpd"+identifier).trigger('blur')
            $('.catSelect'+identifier).trigger('change');
            // this.seteaMarker(this.map, {lat: parseFloat(lat), lng: parseFloat(lon)});
        }
        domicilio.limpiarDomicilios = function(){
            $("#num_ext"+identifier).val("");
            $("#num_int"+identifier).val("");
            $("#asentamiento"+identifier).val("");
            $("#municipio"+identifier).val("");
            $("#cp"+identifier).val("");
            $("#entre_calle1"+identifier).val("");
            $("#entre_calle2"+identifier).val("");
            $("#referencias"+identifier).val("");
            $("#tipo_vialidad_id"+identifier).val("");
            $("#tipo_asentamiento_id"+identifier).val("");
            $("#estado_id"+identifier).val("");
            $("#domicilio_id_modal"+identifier).val("");
            $("#domicilio_key"+identifier).val("");
            $("#vialidad"+identifier).val("");
            $("#autocomplete"+identifier).val("");
            $('.catSelect'+identifier).trigger('change');
            $("#latitud"+identifier).val("");
            $("#longitud"+identifier).val("");
            var lat = "19.398606";
            var lon ="-99.158581";
            // this.seteaMarker(this.map, {lat: parseFloat(lat), lng: parseFloat(lon)});
            $(".direccionUpd"+identifier).trigger('blur')
        }

        // domicilio.fillInAddress = function() {
        //     var place = domicilio.autocomplete.getPlace();
        //     for (var component in domicilio.componentForm) {
        //         document.getElementById(domicilio.campos[component]).value = '';
        //         document.getElementById(domicilio.campos[component]).disabled = false;
        //     }
        //     for (var i = 0; i < place.address_components.length; i++) {
        //         var addressType = place.address_components[i].types[0];
        //         if (domicilio.componentForm[addressType]) {
        //             var val = place.address_components[i][domicilio.componentForm[addressType]];
        //             if($("#"+domicilio.campos[addressType])[0].type != 'select-one'){
        //                 $("#"+domicilio.campos[addressType]).val(val);
        //             }else{
        //                 if(val == 'Estado de México'){
        //                     val = 'México';
        //                 }
        //                 $("#"+domicilio.campos[addressType]+" option:contains("+ val +")").prop("selected",true);
        //             }
        //         }
        //     }
        //     $("#tipo_asentamiento_id"+identifier).val(7);
        //     $("#tipo_vialidad_id"+identifier).val(5);
        //     $(".direccionUpd"+identifier).trigger('blur');
        //     $(".direccionUpd"+identifier).trigger('change');

        // }
		$("#validaDir"+identifier).click(function(){
			domicilio.tomarGeoreferencia();
		});
        $("#tipo_vialidad_id"+identifier).change(function(){
            $("#tipo_vialidad"+identifier).val($("#tipo_vialidad_id"+identifier+" :selected").text());
        });
        $(".catSelect"+identifier).select2({width: '100%'});
        $("#municipio"+identifier).select2({width: '100%', tags: true});
        $("#estado_id"+identifier).change(function(){
            $("#estado"+identifier).val($("#estado_id"+identifier+" :selected").text());
        });
        $("#tipo_asentamiento_id"+identifier).change(function(){
            $("#tipo_asentamiento"+identifier).val($("#tipo_asentamiento_id"+identifier+" :selected").text());
        });
		$(".direccionUpd"+identifier).blur(function(){
			if($("#tipo_vialidad_id"+identifier).val() != "" && $("#vialidad"+identifier).val() != "" && $("#num_ext"+identifier).val() != "" && $("#asentamiento"+identifier).val() && $("#municipio"+identifier).val() != "" && $("#estado_id"+identifier).val() != "" ){
				var direccion = $("#tipo_vialidad_id"+identifier+" :selected").text() + "," + $("#vialidad"+identifier).val() + "," + $("#num_ext"+identifier).val() + "," + $("#asentamiento"+identifier).val() + "," + $("#municipio"+identifier).val() + "." + $("#estado_id"+identifier+" :selected").text();
				$("#direccion_marker"+identifier).val(direccion);
                if(needMaps == 'true'){
				    domicilio.geocodeAddress();
                }
			}
        });
        $("#autocomplete"+identifier).select2({
            ajax: {
                url: '/api/asentamientos/filtrarAsentamientos',
                type:"POST",
                dataType:"json",
                delay: 700,
                async:false,
                data:function (params) {
                    $("#term"+identifier).val(params.term);
                    var data = {
                        direccion: params.term
                    }
                    return data;
                },
                processResults:function(json){
                    try{
                        $.each(json.data, function (key, node) {
                            var html = '';
                            html += '<div>'+node.estado+', '+ node.municipio+', '+ node.cp+', '+ node.asentamiento+' </div>';
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
                return data.html;
            },templateSelection: function(data) {
                if(data.id != ""){
                    $("#municipio"+identifier+" option:contains("+ data.municipio +")").prop("selected",true).trigger("change");
                    var estado = "";
                    if(data.estado.toLowerCase() == 'estado de méxico'){
                        estado = 'México';
                    }else{
                        estado = data.estado.toLowerCase();
                    }
                    $("#estado_id"+identifier+" option:contains("+ estado +")").prop("selected",true).trigger("change");
                    $("#cp"+identifier).val(data.cp);
                    $("#asentamiento"+identifier).val(data.asentamiento);
                    $("#tipo_asentamiento_id"+identifier).val(7);
                    $("#tipo_vialidad_id"+identifier).val(5);
                    $(".direccionUpd"+identifier).trigger('blur');
                    $(".direccionUpd"+identifier).trigger('change');
                    return "<b>"+data.estado+', '+ data.municipio+', '+ data.cp+', '+ data.asentamiento;
                }
                return data.text;
            },
            placeholder:'Seleccione una opción',
            minimumInputLength:4,
            allowClear: true,
            language: "es"
        });

        return domicilio;
    }(identificador,needsMaps));
// $("#municipio".$identificador" option:contains("+ data.municipio +")").prop("selected",true);

    (function (a) {
        a.fn.limitKeyPress = function (b) {
            a(this).on({keypress: function (a) {
                    var c = a.which, d = a.keyCode, e = String.fromCharCode(c).toLowerCase(), f = b;
                    (-1 != f.indexOf(e) || 9 == d || 37 != c && 37 == d || 39 == d && 39 != c || 8 == d || 46 == d && 46 != c) && 161 != c || a.preventDefault()
                }})
        }
    })(jQuery);
    jQuery.expr[':'].contains = function(a, i, m) {
        return jQuery(a).text().toUpperCase()
            .indexOf(m[3].toUpperCase()) >= 0;
    };
    $(".numero").limitKeyPress('1234567890.');
</script>

@if($instancia <= 1 && $needsMaps)

	<script>
		var domicilioObj =  DomicilioObject;
	</script>
@else
<script src="https://maps.googleapis.com/maps/api/js?callback=DomicilioObject.initMap&libraries=places&key=AIzaSyBx0RdMGMOYgE_eLXfCblBP9RhYDQXjrqY"></script>
<script>
	var domicilioObj2 =  DomicilioObject;
	domicilioObj2.initMap();
</script>
@endif
@endpush

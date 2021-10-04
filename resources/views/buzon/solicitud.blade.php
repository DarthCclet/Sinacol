@extends('layouts.default', ['paceTop' => true])

@section('title', 'Buzón electrónico')

@include('includes.component.datatables')
@section('content')
	<div class="login login-v2" data-pageload-addclass="animated fadeIn" style="background: #9d2449 !important; top: 20%;bottom: 20%;">
            <!-- begin brand -->
            <div class="login-header" align="center">
                <div class="brand">
                    <span>
                        <img src="{{asset('assets/img/logo/LogoEncabezado.png')}}" width="360px">
                    </span>
                    <small>Acceso al Buzón electrónico</small>
                </div>
            </div>
            <!-- end brand -->
            <!-- begin login-content -->
            <div class="login-content">
                    <div class="checkbox checkbox-css m-b-20">
                        <input type="checkbox" id="remember_checkbox" />
                        <label for="remember_checkbox">
                            Persona Moral
                        </label>
                    </div>
                    <div class="form-group m-b-20" id="divCurp">
                        <input type="text" class="form-control form-control-lg" placeholder="Ingrese su CURP" id="curp"/>
                    </div>
                    <div class="form-group m-b-20" style="display: none;" id="divRfc">
                        <input type="text" class="form-control form-control-lg" placeholder="Ingrese su RFC" id="rfc"/>
                    </div>
                    <div class="form-group m-b-20">
                        <input type="text" class="form-control form-control-lg" placeholder="Ingrese su folio de expediente" id="folio"/>
                    </div>
                    <div class="login-buttons">
                        <button class="btn btn-success btn-block btn-lg" id="btnSolicitar">Solicitar acceso</button>
                    </div>
            </div>
            <!-- end login-content -->
	</div>
<div class="modal" id="modal-acceso" aria-hidden="true" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Acceso al buzon</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    - No encontramos correos electronicos registrados, por favor proporciona el correo y contraseña que te proporcionó el centro
                </div>
                <form action="{{route('acceso_buzon')}}" method="GET" name="form_acceso_buzon">
                    {{csrf_field()}}
                    <div class="form-group m-b-20">
                        <input type="text" class="form-control form-control-lg" placeholder="Usuario" id="correo_buzon" name="correo_buzon"/>
                    </div>
                    <div class="form-group m-b-20">
                        <input type="password" class="form-control form-control-lg" placeholder="Contraseña" id="password_buzon" name="password_buzon"/>
                    </div>
                    <div class="form-group m-b-20">
                        <input type="text" class="form-control form-control-lg" placeholder="Ingrese su folio de expediente" name="folio" id="folio_modal"/>
                    </div>
                    <div class="login-buttons">
                        <button class="btn btn-primary btn-block btn-lg" id="btnIngresar">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	$(document).ready(function(){

	});
	$("#remember_checkbox").on("click",function(){
            if($(this).is(":checked")){
                $("#divRfc").show();
                $("#divCurp").hide();
            }else{
                $("#divRfc").hide();
                $("#divCurp").show();
            }
        });
	$("#btnSolicitar").on("click",function(){
            var validar  = validarSolicitud();
            if(!validar.error){
                $.ajax({
                    url:"/api/solicitar_acceso",
                    type:"POST",
                    dataType:"json",
                    async:false,
                    data:{
                        curp:$("#curp").val(),
                        rfc:$("#rfc").val(),
                        folio:$("#folio").val(),
                        tipo_persona_id:validar.tipo_persona_id,
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        try{

                            data = data[0];
                            if(data.correo){
                                swal({
                                    title: 'Correcto',
                                    text: data.mensaje,
                                    icon: 'success'
                                });
                            }else{
                                $("#folio_modal").val($("#folio").val());
                                $("#modal-acceso").modal("show");
                            }
                        }catch(error){
                            console.log(error);
                        }
                    },error:function(data){
                        var mensajes = "";
                            swal({
                                title: 'Error',
                                text: data.responseJSON.message,
                                icon: 'error'
                            });
                    }
                });
            }else{
                swal({
                    title: 'Error',
                    text: 'Coloca el dato que se te solicita',
                    icon: 'error'
                });
            }
	});
	function validarSolicitud(){
            var error = false;
            var tipo_persona_id = 1;
            if($("#remember_checkbox").is(":checked")){
                if($("#rfc").val() == ""){
                    error = true;
                }
                tipo_persona_id = 2
            }else{
                if($("#curp").val() == ""){
                    error = true;
                }
            }
            if($("#folio").val() == ""){
                error = true;
            }
            console.log($("#rfc").val());
            console.log($("#curp").val());
            console.log(error);
            return {error:error,tipo_persona_id:tipo_persona_id};
	}
        $("#btnIngresar").on("click",function(e){
            let that = this;
            e.preventDefault();
            if($("#correo_buzon").val() != "" && $("#password_buzon").val() != ""){
                $(that).closest('form').submit();
            }else{
                swal({
                    title: 'Error',
                    text: 'Coloca usuario y password',
                    icon: 'error'
                });
            }
        });
</script>
@endpush

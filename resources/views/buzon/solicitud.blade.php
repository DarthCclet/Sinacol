@extends('layouts.empty', ['paceTop' => true])

@section('title', 'Buzón electrónico')

@include('includes.component.datatables')
@section('content')
	<div class="login login-v2" data-pageload-addclass="animated fadeIn" style="background: #9d2449 !important; top: 20%;bottom: 20%;">
            <!-- begin brand -->
            <div class="login-header" align="center">
                <div class="brand">
                    <span>
                        <img src="https://registro.centropruebas.com/images/logo.png" width="220px">
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
                        <input type="text" class="form-control form-control-lg" placeholder="curp" id="curp"/>
                    </div>
                    <div class="form-group m-b-20" style="display: none;" id="divRfc">
                        <input type="text" class="form-control form-control-lg" placeholder="rfc" id="rfc"/>
                    </div>
                    <div class="login-buttons">
                        <button class="btn btn-success btn-block btn-lg" id="btnSolicitar">Solicitar acceso</button>
                    </div>
            </div>
            <!-- end login-content -->
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
                        tipo_persona_id:validar.tipo_persona_id,
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        swal({
                            title: 'Correcto',
                            text: data.mensaje,
                            icon: 'success'
                        });
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
            console.log($("#rfc").val());
            console.log($("#curp").val());
            console.log(error);
            return {error:error,tipo_persona_id:tipo_persona_id};
	}
</script>
@endpush

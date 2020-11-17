@php
	$headerClass = (!empty($headerInverse)) ? 'navbar-inverse ' : 'navbar-default ';
	$headerMenu = (!empty($headerMenu)) ? $headerMenu : '';
	$headerMegaMenu = (!empty($headerMegaMenu)) ? $headerMegaMenu : '';
	$headerTopMenu = (!empty($headerTopMenu)) ? $headerTopMenu : '';
@endphp
<!-- begin #header -->
<div id="header" class="header {{ $headerClass }}">
    <input type="hidden" id="centro_nombre" value="{{ isset(auth()->user()->centro) ? auth()->user()->centro->nombre : ''}}">
    <nav class="navbar navbar-expand-lg navbar-dark" style="width: 100%;">
    <div class="navbar-header">
        <a href="/" class="navbar-brand"><span class=""><img src="{{asset('assets/img/logo/LogoEncabezado.png')}}"></span></a>
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#subenlaces">
            <span class="sr-only">Interruptor de Navegación</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>
    <div class="collapse navbar-collapse" id="main_nav">
        <ul class="navbar-nav mr-auto" id='divMenu'></ul>
        <ul class="navbar-nav mr-auto">
            <li class="nav-item" id='liMenuOficio'>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto" id='divUser'></ul>
    </div>

</nav>
</div>
<form id="logout-formm" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
@push('scripts')
<script>
    var rol_id = 0;
    // Prevent closing from click inside dropdown
    $(function() {
        if($("#centro_nombre").val() != ""){
            getMenu();
        }
        
        $(document).on('click', '.dropdown-menu', function (e) {
            e.stopPropagation();
        });

        // make it as accordion for smaller screens
        if ($(window).width() < 992) {
          $('.dropdown-menu a').click(function(e){
            e.preventDefault();
              if($(this).next('.submenu').length){
                $(this).next('.submenu').toggle();
              }
              $('.dropdown').on('hide.bs.dropdown', function () {
             $(this).find('.submenu').hide();
          })
          });
        }
        $("#divMenu,#divUser").on("click",".rolChange", function(event) {
            if($(this).data('rol') != rol_id){
                cambiarRol($(this).data('rol'));
            }
        });
    });
    function getMenu(){
        $.ajax({
            url:"/getMenu/1",
            type:"GET",
            dataType:"json",
            async:true,
            success:function(data){
                var div="";
                var centro = $("#centro_nombre").val();
                $.each(data.menu,function(index,element){
                    if(element.hijos.length == 0){
                        var agenda = element.name;
                        if(centro != "Oficina Central del CFCRL" && element.name != "Calendario colectivo"){
                            if(element.name == "Agenda de conciliador"){
                                agenda = "Agenda";
                            }else if(element.name == "Calendario de audiencias"){
                                agenda = "Calendario";
                            }
                            div +='<li class="nav-item"> <a class="nav-link" href="'+element.ruta+'">'+agenda+'</a> </li>';
                        }else if(centro == "Oficina Central del CFCRL" && element.name != "Calendario de audiencias"){
                            if(element.name == "Agenda de conciliador"){
                                agenda = "Agenda";
                            }else if(element.name == "Calendario colectivo"){
                                agenda = "Calendario";
                            }
                            div +='<li class="nav-item"> <a class="nav-link" href="'+element.ruta+'">'+agenda+'</a> </li>';
                        }
                    }else{
                        div +='<li class="nav-item dropdown">';
                        div +='    <a class="nav-link dropdown-toggle" href="'+element.ruta+'" data-toggle="dropdown">'+element.name+'  <b class="caret"></b></a>';
                        div +='    <ul class="dropdown-menu">';
                        div +=          construirMenu(element.hijos);
                        div +='    </ul>';
                        div +='</li>';
                    }
                });
                try{
                    if(expedientee){
                        div +='<li class="nav-item"> <a class="nav-link" href="/oficio-documentos/'+expediente_id+'">Oficios</a> </li>';
                    }
                }catch(err){}
                $("#divMenu").html(div);
                div="";
                div +='<li class="nav-item dropdown">';
                div +='    <a class="nav-link dropdown-toggle" data-toggle="dropdown">';
                div +='         <i class="fa fa-user"></i>';
                div +='         <span class="d-none d-md-inline">'+data.nombre+'</span><b class="caret"></b>';
                div +='    </a>';
                div +='    <ul class="dropdown-menu">';
                div +='         <li><a class="dropdown-item" href="#">Perfil</a>';
                div +='             <ul class="submenu dropdown-menu derecha">';
                $.each(data.roles,function(i,e){
                    var selected='';
                    if(e.id === data.rolActual.id){
                          selected = '<i class="fa fa-check-circle" style="color:#9d2449;"></i>';
                    }
                div +='                 <li><a class="dropdown-item rolChange" data-rol="'+e.id+'" href="#">'+e.name+selected+'</a>';
                });
                div +='             </ul>';
                div +='         </li>';
                var ruta_impersonate = "impersonate_leave";
                @if(SESSION('impersonated_by'))
                div +='         <li><a class="dropdown-item" href="{{route('impersonate_leave')}}">Regresar al perfil</a> </li>';
                @endif
                div +='         <li>';
                var form =  'logout-formm';
                div +='         <li>';
                div +='             <a class="dropdown-item" href="#" onclick="cerrarSesion(event)">';
                div +='                 Salir';
                div +='             </a>';
                div +='         </li>';
                div +='     </ul>';
                div +=' </li>';
                rol_id = data.rolActual.id;
                $("#divUser").html(div);
            }
        });
    }
    function construirMenu(elements, div = ''){
        $.each(elements,function(i,e){
            if(e.hijos.length > 0){
                div +='<li><a class="dropdown-item" href="#">'+e.name+' <b class="caret"></b></a>';
                div +='    <ul class="submenu dropdown-menu">';
                div +=          construirMenu(e.hijos);
                div +='    </ul>';
                div +='</li>';

            }else{
                div +='<li><a class="dropdown-item" href="'+e.ruta+'">'+e.name+'</a></li>';
            }
        });
        return div;
    }
    function cambiarRol(rol){
        $.ajax({
            url:"/cambiarRol/"+rol,
            type:"GET",
            dataType:"json",
            async:false,
            success:function(data){
                window.location.href = "{{ route('home') }}";
            }
        });
    }
    function cerrarSesion(event){
        event.preventDefault();
        $("#logout-formm").submit();
    }
</script>
@endpush

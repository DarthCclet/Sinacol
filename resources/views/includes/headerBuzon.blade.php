@php
	$headerClass = (!empty($headerInverse)) ? 'navbar-inverse ' : 'navbar-default ';
	$headerMenu = (!empty($headerMenu)) ? $headerMenu : '';
	$headerMegaMenu = (!empty($headerMegaMenu)) ? $headerMegaMenu : '';
	$headerTopMenu = (!empty($headerTopMenu)) ? $headerTopMenu : '';
@endphp
<!-- begin #header -->
<div id="header" class="header {{ $headerClass }}">

    <nav class="navbar navbar-expand-lg navbar-dark" style="width: 100%;">
    <div class="navbar-header">
        <a href="/" class="navbar-brand"><span class=""><img src="{{asset('assets/img/logo/LogoEncabezado.png')}}" width="220px"></span></a>
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#subenlaces">
            <span class="sr-only">Interruptor de Navegación</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>
    <div class="collapse navbar-collapse" id="main_nav">
        <ul class="navbar-nav mr-auto" id='divMenu'></ul>
        <ul class="navbar-nav ml-auto" id='divUser'></ul>
    </div>

</nav>
</div>
@push('scripts')
<script>

</script>
@endpush

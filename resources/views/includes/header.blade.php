@php
	$headerClass = (!empty($headerInverse)) ? 'navbar-inverse ' : 'navbar-default ';
	$headerMenu = (!empty($headerMenu)) ? $headerMenu : '';
	$headerMegaMenu = (!empty($headerMegaMenu)) ? $headerMegaMenu : '';
	$headerTopMenu = (!empty($headerTopMenu)) ? $headerTopMenu : '';
@endphp
<!-- begin #header -->
<div id="header" class="header {{ $headerClass }}">
	<!-- begin navbar-header -->
	<div class="navbar-header">
        <a href="/" class="navbar-brand"><span class=""><img src="{{asset('https://registro.centropruebas.com/images/logo.png')}}" alt=""></span></a>
	</div>
	<!-- end navbar-header -->
</div>
<!-- end #header -->

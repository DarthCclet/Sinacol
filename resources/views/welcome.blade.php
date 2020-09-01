@extends('layouts.default')

@section('content')
    <img width="1024" class="mx-auto d-block" src="/assets/img/asesoria/home.jpg" alt="" usemap="#NavMap">
    <map name="NavMap">
        <area alt="Ingrese al sistema" title="Ingrese al sistema" href="/asesoria/inicio" coords="738,421,1024,622" shape="rect">
        <area alt="Ingresoa mi buzón laboral" title="Ingreso a mi buzón laboral" href="/solicitud_buzon" coords="733,1,1023,204" shape="rect">
        <area alt="Ingresa al sistema" title="Ingresa al sistema" href="/asesoria/inicio" coords="1,1,722,619" shape="rect">
        <area alt="Ingresa al sistema" title="Ingresa al sistema" href="/asesoria/inicio" coords="725,219,1023,405" shape="rect">
    </map>
@endsection

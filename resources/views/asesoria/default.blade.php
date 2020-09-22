@extends('layouts.default')

@section('content')
    <img width="1024" class="mx-auto d-block" src="/assets/img/asesoria/trabajador-patron-sindicato.jpg" usemap="#NavMap">
    <map name="NavMap">
        <area alt="Soy trabajador" title="Soy trabajador" href="/asesoria/10" coords="600,262,900,170" shape="rect" style="border: thin solid yellow;">
        <area alt="Soy patrón" title="Soy patrón" href="/asesoria/20" coords="600,432,900,350" shape="rect">
        <area alt="Soy sindicato" title="Soy sindicato" href="/solicitudes/create-public/?solicitud=4" coords="600,610,900,528" shape="rect">
    </map>
@endsection

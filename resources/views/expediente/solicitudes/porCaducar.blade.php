@extends('layouts.default', ['paceTop' => true])

@section('title', 'Solicitudes')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')
@include('includes.component.dropzone')

@section('content')
<h1 class="h2">Solicitudes por caducar</h1>
<hr class="red">
@if(count($solicitudes) > 0)
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <td>Solicitud</td>
                <td>Expediente</td>
                <td>Fecha recepci&oacute;n</td>
                <td>Caduca en</td>
                <td>Siguiente audiencia</td>
                <td>Consulta</td>
            </tr>
        </thead>
        <tbody id="tbodyRepresentante">
            @foreach($solicitudes as $solicitud)
            <tr>
                <td>{{$solicitud->folio}}/{{$solicitud->anio}}</td>
                <td>{{isset($solicitud->expediente) ? $solicitud->expediente->folio : ""}}</td>
                <td>{{date('d/m/Y', strtotime($solicitud->fecha_recepcion))}}</td>
                <td>{{$solicitud->caduca}} d&iacute;as</td>
                <td>{{isset($solicitud->expediente) ? date('d/m/Y', strtotime($solicitud->expediente->audiencia->first()->fecha_audiencia))." ".$solicitud->expediente->audiencia->first()->hora_inicio : ""}}</td>
                <td><div title="Ver datos de la solicitud" data-toggle="tooltip" data-placement="top" style="display: inline-block;" class="m-2"><a href="{!! route("solicitudes.consulta",$solicitud->id) !!}" class="btn btn-xs btn-primary"><i class="fa fa-search"></i></a></div></td>
            </tr>
            @endforeach
        <tbody>
    </table>
@endif
@endsection
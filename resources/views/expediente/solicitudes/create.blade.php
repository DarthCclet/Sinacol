@extends('layouts.default', ['paceTop' => true])

@section('title', 'Solicitudes')

@include('includes.component.datatables')
@include('includes.component.pickers')


@section('content')
<!-- begin breadcrumb -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="">Home</a></li>
    <li class="breadcrumb-item"><a href="{!! route("solicitudes.index") !!}">Solicitudes</a></li>
    <li class="breadcrumb-item"><a href="javascript:;">Crear Solicitud</a></li>
  </ol>
  <!-- end breadcrumb -->
<button class="btn btn-info" onclick="location.href='{{ route('solicitudes.index')  }}'" > <i class="fa fa-arrow-alt-circle-left"></i> Regresar</button>
<div class="panel panel-inverse">
    <div class="panel panel-heading ui-sortable-handle">
        <h4 class="panel-title">Solicitud de conciliaci&oacute;n</h4>
    </div>
    <div class="panel-body">
        {{-- <form action="{{url('solicitudes')}}" method="POST"> --}}
            {{ csrf_field() }}
            @include('expediente.solicitudes._form')
             <button class="btn btn-info btn-lg m-l-5" onclick="guardarSolicitud()"><i class="fa fa-save" ></i> Guardar</button>
            
        {{-- </form> --}}
    </div>
</div>
@endsection
@push('scripts')
<script>
  var edit = false;
</script>
@endpush
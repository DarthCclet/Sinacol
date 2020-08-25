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

<div class="panel panel-inverse">
    <div class="panel-body">
        {{-- <form action="{{url('solicitudes')}}" method="POST"> --}}
            {{ csrf_field() }}
            @include('expediente.solicitudes._form')
             

        {{-- </form> --}}
    </div>
</div>
@endsection
@push('scripts')
<script>
  var edit = false;
</script>
@endpush

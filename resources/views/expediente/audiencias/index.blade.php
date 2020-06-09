@extends('layouts.default', ['paceTop' => true])

@section('title', 'expedientes')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Audiencias</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar expedientes <small>Listado de audiencias</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de audiencias</h4>
            <div class="panel-heading-btn">
            </div>
        </div>
        <div style="float: left;">
            <label class="col-md-12"> Filtrar</label>
            <button class="btn btn-primary pull-right" onclick="$('#estatus_audiencia').val(1).trigger('change');" >Pendientes</button>
            <button class="btn btn-primary pull-right" onclick="$('#estatus_audiencia').val(2).trigger('change');">Finalizadas</button>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('expediente.audiencias._list')
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        
    </script>
@endpush

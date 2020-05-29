@extends('layouts.default', ['paceTop' => true])

@section('title', 'Centros')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Centros</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar centros de conciliación <small>Editar centro</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
        {!! Form::model($centro, ['route' => ['centro.update', $centro->id], 'method' => 'put'] ) !!}

        <div class="panel panel-default">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <h4 class="panel-title">Nuevo centro</h4>
                <div class="panel-heading-btn">
                    <a href="{!! route('centros.index') !!}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
                </div>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                @include('centros.centros._form')
            </div>
            <!-- end panel-body -->
            <!-- begin panel-footer -->
            <div class="panel-footer text-right">
                <a href="{!! route('centros.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
                <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-save"></i> Modificar</button>
            </div>
            <!-- end panel-footer -->
        </div>
        {!! Form::close() !!}

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#duracionAudiencia").datetimepicker({format:"HH:mm"});
        });
    </script>
@endpush

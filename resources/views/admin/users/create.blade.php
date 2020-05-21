@extends('layouts.default', ['paceTop' => true])

@section('title', 'Usuarios')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="{!! route('users.index') !!}">Administración</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar usuarios <small>Nuevo usuario</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    {!! Form::open(['route' => 'users.store']) !!}

    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Nuevo usuario</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('users.index') !!}" class="btn btn-info btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('admin.users._form')
        </div>
        <!-- end panel-body -->
        <!-- begin panel-footer -->
        <div class="panel-footer text-right">
            <a href="{!! route('users.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
            <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <!-- end panel-footer -->
    </div>



    {!! Form::close() !!}

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $(".selectRol").select2({ placeholder: "Selecciona un rol" });
        });
</script>
@endpush
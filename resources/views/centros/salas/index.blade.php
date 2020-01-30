@extends('layouts.default', ['paceTop' => true])

@section('title', 'Salas de Audiencias')

@include('includes.component.datatables')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Tables</a></li>
        <li class="breadcrumb-item active">Managed Tables</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar salas de audiencias <small>Listado de salas</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de salas</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('salas.create') !!}" class="btn btn-info"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('centros.salas._list')
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {


                $('#data-table-default').DataTable({
                    responsive: true
                });


        });
    </script>
@endpush

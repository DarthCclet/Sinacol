@extends('layouts.default', ['paceTop' => true])

@section('title', 'Centros de conciliación')

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
    <h1 class="page-header">Administrar centros de conciliación <small>Listado de centros</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de centros</h4>
            <div class="panel-heading-btn">
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('centros.centros._list')
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

@extends('layouts.default', ['paceTop' => true])

@section('title', 'Géneros')

@include('includes.component.datatables')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active">Géneros</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administración de catalogos <small>Listado de generos</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de generos</h4>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('catalogos.generos._list')
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#data-table-default').DataTable({responsive: true,language: {url: "/assets/plugins/datatables.net/dataTable.es.json"}});
        });
    </script>
@endpush

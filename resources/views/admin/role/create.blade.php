@extends('layouts.default', ['paceTop' => true])

@section('title', 'Roles Conciliadores')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')
  <!-- begin breadcrumb -->
  <ol class="breadcrumb float-xl-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item active">Roles</li>
  </ol>
  <!-- end breadcrumb -->
  <!-- begin page-header -->
  <h1 class="page-header">Administrar roles <small>Nuevo rol</small></h1>
  <!-- end page-header -->


{!! Form::open([ 'route' => 'roles.store' ]) !!}
<!--@csrf-->
  <div class="panel panel-default">
      <!-- begin panel-heading -->
      <div class="panel-heading">
          <h4 class="panel-title">Nuevo rol</h4>
          <div class="panel-heading-btn">
              <a href="{!! route('roles.index') !!}" class="btn btn-info btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
          </div>
      </div>
      <div class="panel-body">
        @include('admin.role._form')
      </div>
      <div class="panel-footer text-right">
          <a href="{!! route('roles.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
          <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-save"></i> Guardar</button>
      </div>
  </div>
{!! Form::close() !!}
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $(".multiple-select2").select2({ placeholder: "Select a state" });
        });
</script>
@endpush

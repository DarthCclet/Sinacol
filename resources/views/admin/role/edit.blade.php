@extends('layouts.default', ['paceTop' => true])

@section('title', 'Roles Conciliadores')

@include('includes.component.datatables')
@include('includes.component.pickers')
@section('content')

<!-- begin breadcrumb -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
    <li class="breadcrumb-item active"><a href="javascript:;">Roles</a></li>
</ol>
<!-- end breadcrumb -->
<!-- begin page-header -->
<h1 class="page-header">Administrar roles <small>Editar rol</small></h1>
<!-- end page-header -->

<!-- begin panel -->
    {!! Form::model($role, ['route' => ['roles.update', $role->id], 'method' => 'PUT'] ) !!}
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Editar rol</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('roles.index') !!}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
          @include('admin.role._form')
        </div>
        <!-- end panel-body -->
        <!-- begin panel-footer -->
        <div class="panel-footer text-right">
            <a href="{!! route('roles.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
            <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-save"></i> Modificar</button>
        </div>
        <!-- end panel-footer -->
    </div>
{{ Form::close() }}
@endsection
@push('scripts')
    <script>
        $(document).ready(function(){
            $(".multiple-select2").select2({ placeholder: "Select a state" });
            $.ajax({
                url:"/roles/permisos/{{$role->id}}",
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    if(data != null){
                        $.each(data,function(index,element){
                            $(".multiple-select2 option[value='"+element.name+"']").attr("selected","selected");
                        });
                        $(".multiple-select2").trigger("change");
                    }
                }
            });
        });
    </script>
@endpush

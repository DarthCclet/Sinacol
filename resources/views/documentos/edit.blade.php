@extends('layouts.default', ['paceTop' => true])

@section('title', 'Documentos')

@include('includes.component.datatables')

@section('content')
<button class="btn btn-info" onclick="location.href='{{ route('plantilla-documentos.index')  }}'" ><i class="fa fa-arrow-alt-circle-left"></i> Regresar</button>
<div class="panel panel-inverse">
    <div class="panel panel-heading ui-sortable-handle">
        <h4 class="panel-title">Editar plantilla</h4>
    </div>
    <div class="panel-body">
      {!!  Form::open(array('route' => array('plantilla-documentos.update', $plantillaDocumento->id), 'method' => 'PUT')) !!}

            @include('documentos.editor')
          <div class="form-group">
            <button class="btn btn-info btn-sm m-l-5"><i class="fa fa-save"></i> Modificar</button>
            <a href="{{ route('plantilla-documento/imprimirPDF',[$plantillaDocumento])}}" class="btn btn-danger btn-sm m-l-5">
                <i class="fas fa-file-pdf"></i></i> Ver PDF
            </a>

          </div>

      {{ Form::close() }}
    </div>
</div>
@endsection

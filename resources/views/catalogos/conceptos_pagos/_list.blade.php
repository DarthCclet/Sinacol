<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th width="10%">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($conceptos as $concepto)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$concepto->id}}</td>
            <td>{{$concepto->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['ConceptoPagoResolucionesController@destroy', $concepto->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('conceptos_pagos.edit',[$concepto])}}" class="btn btn-xs btn-primary">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <button class="btn btn-xs btn-warning btn-borrar">
                        <i class="fa fa-trash btn-borrar"></i>
                    </button>
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
<p>Mostrando registros del {{ $conceptos->total() > 0 ? ((($conceptos->currentPage() -1) * 10)+1) : 0 }} al {{ ((($conceptos->currentPage() -1) * 10))+$conceptos->count() }} de un total de {{ $conceptos->total() }} registros: </p>
{{ $conceptos->links() }}
<script>

    $("formDelete").submit(function(e){
        e.preventDefault();
    });
</script>

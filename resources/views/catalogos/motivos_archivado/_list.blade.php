<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Descripción</th>
        <th width="10%">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($motivos as $motivo)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$motivo->id}}</td>
            <td>{{$motivo->descripcion}}</td>
            <td class="all">
                {!! Form::open(['action' => ['MotivoArchivadoController@destroy', $motivo->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('motivos_archivado.edit',[$motivo])}}" class="btn btn-xs btn-primary">
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
<p>Mostrando registros del {{ $motivos->total() > 0 ? ((($motivos->currentPage() -1) * 10)+1) : 0 }} al {{ ((($motivos->currentPage() -1) * 10))+$motivos->count() }} de un total de {{ $motivos->total() }} registros: </p>
{{ $motivos->links() }}
<script>

    $("formDelete").submit(function(e){
        e.preventDefault();
    });
</script>

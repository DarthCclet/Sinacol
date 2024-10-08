<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th width="10%">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($tipos as $tipo)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$tipo->id}}</td>
            <td>{{$tipo->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['TipoContactoController@destroy', $tipo->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('tipos_contactos.edit',[$tipo])}}" class="btn btn-xs btn-primary">
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
<p>Mostrando registros del {{ $tipos->total() > 0 ? ((($tipos->currentPage() -1) * 10)+1) : 0 }} al {{ ((($tipos->currentPage() -1) * 10))+$tipos->count() }} de un total de {{ $tipos->total() }} registros: </p>
{{ $tipos->links() }}
<script>

    $("formDelete").submit(function(e){
        e.preventDefault();
    });
</script>

<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th class="text-nowrap" width="20%">Equivalencia en días</th>
        <th width="10%">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($periodicidades as $periodicidad)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$periodicidad->id}}</td>
            <td>{{$periodicidad->nombre}}</td>
            <td>{{$periodicidad->dias}}</td>
            <td class="all">
                {!! Form::open(['action' => ['PeriodicidadController@destroy', $periodicidad->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('periodicidades.edit',[$periodicidad])}}" class="btn btn-xs btn-primary">
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
<p>Mostrando registros del {{ $periodicidades->total() > 0 ? ((($periodicidades->currentPage() -1) * 10)+1) : 0 }} al {{ ((($periodicidades->currentPage() -1) * 10))+$periodicidades->count() }} de un total de {{ $periodicidades->total() }} registros: </p>
{{ $periodicidades->links() }}
<script>

    $("formDelete").submit(function(e){
        e.preventDefault();
    });
</script>

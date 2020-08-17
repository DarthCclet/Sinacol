<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th width="10%">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($ambitos as $ambito)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$ambito->id}}</td>
            <td>{{$ambito->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['AmbitoController@destroy', $ambito->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('ambitos.edit',[$ambito])}}" class="btn btn-xs btn-primary">
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
<p>Mostrando registros del {{ (($ambitos->currentPage() -1) * 10)+1 }} al {{ ((($ambitos->currentPage() -1) * 10))+$ambitos->count() }} de un total de {{ $ambitos->total() }} registros: </p>
{{ $ambitos->links() }}
<script>

    $("formDelete").submit(function(e){
        e.preventDefault();
    });
</script>

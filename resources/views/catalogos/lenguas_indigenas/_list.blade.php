<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th width="10%">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($lenguas as $lengua)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$lengua->id}}</td>
            <td>{{$lengua->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['LenguaIndigenaController@destroy', $lengua->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('lenguas_indigenas.edit',[$lengua])}}" class="btn btn-xs btn-primary">
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
<p>Mostrando registros del {{ $lenguas->total() > 0 ? ((($lenguas->currentPage() -1) * 10)+1) : 0 }} al {{ ((($lenguas->currentPage() -1) * 10))+$lenguas->count() }} de un total de {{ $lenguas->total() }} registros: </p>
{{ $lenguas->links() }}
<script>

    $("formDelete").submit(function(e){
        e.preventDefault();
    });
</script>

<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Clasificación</th>
        <th class="text-nowrap">Tipo de archivo</th>
        <th class="text-nowrap">Entidad emisora</th>
        <th width="10%">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($clasificacion as $clas)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$clas->id}}</td>
            <td>{{$clas->nombre}}</td>
            <td>{{$clas->tipo_archivo->nombre}}</td>
            <td>{{$clas->entidad_emisora->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['ClasificacionArchivoController@destroy', $clas->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('clasificacion_archivos.edit',[$clas])}}" class="btn btn-xs btn-primary">
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
<p>Mostrando registros del {{ $clasificacion->total() > 0 ? ((($clasificacion->currentPage() -1) * 10)+1) : 0 }} al {{ ((($clasificacion->currentPage() -1) * 10))+$clasificacion->count() }} de un total de {{ $clasificacion->total() }} registros: </p>
{{ $clasificacion->links() }}
<script>

    $("formDelete").submit(function(e){
        e.preventDefault();
    });
</script>

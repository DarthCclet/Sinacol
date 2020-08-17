<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Sala</th>
        <th class="text-nowrap">Centro</th>
        <th class="text-nowrap" width="15%">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($salas as $sala)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$sala->id}}</td>
            <td>{{$sala->sala}}</td>
            <td>{{$sala->centro->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['SalaController@destroy', $sala->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('salas.edit',[$sala])}}" class="btn btn-xs btn-primary">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <a class="btn btn-xs btn-primary disponibilidad" onclick="getSalaDisponibilidad({{$sala->id}})">
                        <i class="fa fa-calendar"></i>
                    </a>
                    <a class="btn btn-xs btn-primary incidencia" onclick="getSalaIncidencias({{$sala->id}})">
                        <i class="fa fa-calendar-times"></i>
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
<p>Mostrando registros del {{ $salas->total() > 0 ? ((($salas->currentPage() -1) * 10)+1) : 0 }} al {{ ((($salas->currentPage() -1) * 10))+$salas->count() }} de un total de {{ $salas->total() }} registros: </p>
{{ $salas->links() }}

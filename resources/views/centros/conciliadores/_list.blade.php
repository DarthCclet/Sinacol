<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th class="text-nowrap">Primer apellido</th>
        <th class="text-nowrap">Segundo apellido</th>
        <th class="text-nowrap">RFC</th>
        <th class="text-nowrap">Centro</th>
        <th class="text-nowrap" width="15%">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($conciliadores as $conciliador)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$conciliador->id}}</td>
            <td>{{$conciliador->persona->nombre}}</td>
            <td>{{$conciliador->persona->primer_apellido}}</td>
            <td>{{$conciliador->persona->segundo_apellido}}</td>
            <td>{{$conciliador->persona->rfc}}</td>
            <td>{{$conciliador->centro->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['ConciliadorController@destroy', $conciliador->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('conciliadores.edit',[$conciliador])}}" class="btn btn-xs btn-primary">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <a class="btn btn-xs btn-primary disponibilidad" onclick="getRolesConciliador({{$conciliador->id}})">
                        <i class="fa fa-user-cog"></i>
                    </a>
                    <a class="btn btn-xs btn-primary disponibilidad" onclick="getConciliadorDisponibilidad({{$conciliador->id}})">
                        <i class="fa fa-calendar"></i>
                    </a>
                    <a class="btn btn-xs btn-primary incidencia" onclick="getConciliadorIncidencias({{$conciliador->id}})">
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
<p>Mostrando registros del {{ $conciliadores->total() > 0 ? ((($conciliadores->currentPage() -1) * 10)+1) : 0 }} al {{ ((($conciliadores->currentPage() -1) * 10))+$conciliadores->count() }} de un total de {{ $conciliadores->total() }} registros: </p>
{{ $conciliadores->links() }}

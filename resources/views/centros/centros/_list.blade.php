<table id="data-table-default" class="table table-striped table-bordered table-condensed table-td-valign-middle">
    <thead>
    <tr>
        <th class="text-nowrap"></th>
        <th class="text-nowrap">Centro de Conciliacion</th>
        <th class="text-nowrap all">Acciones</th> 
    </tr>
    </thead>
    <tbody>
    @foreach($centros as $centro)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$centro->id}}</td>
            <td>{{$centro->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['CentroController@destroy', $centro->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('centros.edit',[$centro])}}" class="btn btn-xs btn-info">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <span class="disponibilidad" class="btn btn-xs btn-info">
                        <i class="fa fa-calendar"></i>
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

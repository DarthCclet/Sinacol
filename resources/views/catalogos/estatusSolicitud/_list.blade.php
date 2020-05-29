
<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th class="text-nowrap all">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($estatusSolicitud as $estatus)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$estatus->id}}</td>
            <td>{{$estatus->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['EstatusSolicitudController@destroy', $estatus->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('estatus-solicitud.edit',[$estatus])}}" class="btn btn-xs btn-primary">
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

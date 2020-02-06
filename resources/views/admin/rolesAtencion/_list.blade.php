<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th class="text-nowrap all">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rolesAtencion as $rolAtencion)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$rolAtencion->id}}</td>
            <td>{{$rolAtencion->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['RolAtencionController@destroy', $rolAtencion->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('roles-atencion.edit',[$rolAtencion])}}" class="btn btn-xs btn-info">
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

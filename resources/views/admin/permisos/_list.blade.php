<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Permiso</th>
        <th class="text-nowrap">Descripción</th>
        <th class="text-nowrap all">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($permissions as $permiso)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$permiso->id}}</td>
            <td>{{$permiso->name}}</td>
            <td>{{$permiso->description}}</td>
            <td class="all">
                {!! Form::open(['route' => ['permisos.destroy', $permiso->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('permisos.edit',[$permiso])}}" class="btn btn-xs btn-primary">
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

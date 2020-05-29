<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Rol</th>
        <th class="text-nowrap">Descripción</th>
        <th class="text-nowrap all">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($roles as $role)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$role->id}}</td>
            <td>{{$role->name}}</td>
            <td>{{$role->description}}</td>
            <td class="all">
                {!! Form::open(['action' => ['RoleController@destroy', $role->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('roles.edit',[$role])}}" class="btn btn-xs btn-primary">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <a class="btn btn-xs btn-primary" onclick="getPermisos({{$role->id}},'{{$role->name}}')" title="Permisos">
                        <i class="fa fa-key text-light"></i>
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

<table id="data-table-default" class="table table-striped table-bordered table-condensed table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">User</th>
        <th class="text-nowrap">Email</th>
        <th class="text-nowrap">Nombre</th>
        <th class="text-nowrap">Primer Apellido</th>
        <th class="text-nowrap">Segundo Apellido</th>
        <th class="text-nowrap all">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$user->id}}</td>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->persona->nombre}}</td>
            <td>{{$user->persona->primer_apellido}}</td>
            <td>{{$user->persona->segundo_apellido}}</td>
            <td class="all">
                {!! Form::open(['action' => ['UserController@destroy', $user->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('users.edit',[$user])}}" class="btn btn-xs btn-primary">
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

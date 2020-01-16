<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th width="1%" data-orderable="false"></th>
        <th class="text-nowrap">User</th>
        <th class="text-nowrap">Email</th>
        <th class="text-nowrap">Nombre</th>
        <th class="text-nowrap">Primer Apellido</th>
        <th class="text-nowrap">Segundo Apellido</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$user->id}}</td>
            <td width="1%" class="with-img">
                <img src="/assets/img/user/user-1.jpg" class="img-rounded height-30" />
            </td>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->persona->nombre}}</td>
            <td>{{$user->persona->paterno}}</td>
            <td>{{$user->persona->materno}}</td>
        </tr>
    @endforeach

    </tbody>
</table>

<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Centro de Conciliacion</th>
        <th width="10%">Editar</th> 
        <th width="10%">Eliminar</th> 
    </tr>
    </thead>
    <tbody>
    @foreach($centros as $centro)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$centro->id}}</td>
            <td>{{$centro->nombre}}</td>
            <td><button class="btn btn-primary" onclick="location.href='{{ route('centros.edit', $centro->id)  }}'">Editar</button></td>
            <td>
                <form action="{{ url('api/centro/'.$centro->id) }}" method="POST">
                    {{method_field('DELETE')}}
                    <button class="btn btn-danger" onclick="location.href='{{ route('centros.destroy', $centro->id)  }}'">Eliminar</button>
                </form>
            </td>
        </tr>
    @endforeach

    </tbody>
</table>

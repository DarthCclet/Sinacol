<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th width=40%" class="text-nowrap">Tipo de contador</th>
        <th width=20%" class="text-nowrap">Año</th>
        <th width=20%" class="text-nowrap">Consecutivo</th>
        <th width=19%" class="text-nowrap">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($contadores as $contador)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$contador->id}}</td>
            <td>{{$contador->tipoContador->nombre}}</td>
            <td>{{$contador->anio}}</td>
            <td>{{$contador->contador}}</td>
            <td class="all">
                <div style="display: inline-block;">
                    <a href="{{route('contadores.edit',[$contador])}}" class="btn btn-xs btn-info">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>


<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th class="text-nowrap all">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($resolucionAudiencia as $resolucion)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$resolucion->id}}</td>
            <td>{{$resolucion->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['ResolucionController@destroy', $resolucion->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('resolucion-audiencia.edit',[$resolucion])}}" class="btn btn-xs btn-primary">
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

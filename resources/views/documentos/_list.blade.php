<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <!-- <th class="text-nowrap">Folio</th> -->
        <th class="text-nowrap">Nombre</th>
        <!-- <th class="text-nowrap">consecutivo</th> -->
        <th >Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($plantillas as $plantilla)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$plantilla->id}}</td>
            <td>{{$plantilla->nombre_plantilla}}</td>
            <td class="all">
                {!! Form::open(['action' => ['PlantillasDocumentosController@destroy', $plantilla->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('plantilla-documentos.edit',[$plantilla])}}" class="btn btn-xs btn-primary">
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
<p>Mostrando registros del {{ (($plantillas->currentPage() -1) * 10)+1 }} al {{ ((($plantillas->currentPage() -1) * 10))+$plantillas->count() }} de un total de {{ $plantillas->total() }} registros: </p>
{{ $plantillas->links() }}
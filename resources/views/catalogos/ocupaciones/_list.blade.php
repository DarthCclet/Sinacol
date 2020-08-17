
<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th class="text-nowrap all">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($ocupacion as $puesto)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse"><input type="checkbox" name="selectPuestos" value='{{$puesto->nombre}}' ocid={{$puesto->id}} ></td>
            <td width="1%" class="f-s-600 text-inverse">{{$puesto->id}}</td>
            <td>{{$puesto->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['OcupacionController@destroy', $puesto->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('ocupaciones.edit',[$puesto])}}" class="btn btn-xs btn-primary">
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
<p>Mostrando registros del {{ (($ocupacion->currentPage() -1) * 10)+1 }} al {{ ((($ocupacion->currentPage() -1) * 10))+$ocupacion->count() }} de un total de {{ $ocupacion->total() }} registros: </p>
{{ $ocupacion->links() }}
<!-- <div> -->
<div id="btnsMultiple" style="display:none;">
    <a id="btmMultiple" class="btn btn-primary" style="color:white">
        <i class="fa fa-pencil-alt"></i> Editar seleccionados
    </a>
    <!-- <button class="btn btn-xs btn-warning btn-borrar">
        <i class="fa fa-pencil btn-borrar"></i>
    </button> -->
</div>


@push('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endpush

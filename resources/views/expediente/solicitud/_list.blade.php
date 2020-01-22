<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Fecha Recepcion</th>
        <th class="text-nowrap">Motivo</th>
        <th class="text-nowrap">Estatus</th>
        <th class="text-nowrap">Fecha Ratificacion</th>
        <!-- <th >Editar</th> -->
    </tr>
    </thead>
    <tbody>
    @foreach($solicitud as $solicitud)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$solicitud->id}}</td>
            <td>{{$solicitud->fecha_recepcion}}</td>
            <td>{{$solicitud->motivoSolicitud->nombre}}</td>
            <td>{{$solicitud->estatusSolicitud->nombre}}</td>
            <td>{{$solicitud->fecha_ratificacion}}</td>
            <!-- <td><input type="button" class="button button-primary" value="Editar" href="{{ route('login') }}" /></td> -->

        </tr>
    @endforeach

    </tbody>
</table>

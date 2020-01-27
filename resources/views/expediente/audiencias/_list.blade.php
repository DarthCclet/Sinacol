<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Numero de audiencia</th>
        <th class="text-nowrap">Sala</th>
        <th class="text-nowrap">Conciliador</th>
        <th class="text-nowrap">Fecha de audiencia</th>
        <th class="text-nowrap">Hora inicio</th>
        <th class="text-nowrap">Hora fin</th>
        <!-- <th >Editar</th> -->
    </tr>
    </thead>
    <tbody>
    @foreach($audiencias as $audiencia)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$audiencia->id}}</td>
            <td>{{$audiencia->numero_audiencia}}</td>
            <td>{{$audiencia->sala->sala}}</td>
            <td>{{$audiencia->conciliador->persona->nombre}} {{$audiencia->conciliador->persona->primer_apellido}} {{$audiencia->conciliador->persona->segundo_apellido}}</td>
            <td>{{$audiencia->fecha_audiencia}}</td>
            <td>{{$audiencia->hora_inicio}}</td>
            <td>{{$audiencia->hora_fin}}</td>
        </tr>
    @endforeach

    </tbody>
</table>

<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Folio</th>
        <th class="text-nowrap">A&ntilde;o</th>
        <th class="text-nowrap">consecutivo</th>
        <!-- <th >Editar</th> -->
    </tr>
    </thead>
    <tbody>
    @foreach($expediente as $expediente)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$expediente->id}}</td>
            <td>{{$expediente->folio}}</td>
            <td>{{$expediente->anio}}</td>
            <td>{{$expediente->consecutivo}}</td>
            

        </tr>
    @endforeach

    </tbody>
</table>

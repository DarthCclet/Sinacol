<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
        <th class="text-nowrap" width="15%">Abreviatura</th>
    </tr>
    </thead>
    <tbody>
    @foreach($estados as $estado)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$estado->id}}</td>
            <td>{{$estado->nombre}}</td>
            <td>{{$estado->abreviatura}}</td>
        </tr>
    @endforeach

    </tbody>
</table>

<p>Mostrando registros del {{ $estados->total() > 0 ? ((($estados->currentPage() -1) * 10)+1) : 0 }} al {{ ((($estados->currentPage() -1) * 10))+$estados->count() }} de un total de {{ $estados->total() }} registros: </p>
{{ $estados->links() }}
<script>

    $("formDelete").submit(function(e){
        e.preventDefault();
    });
</script>

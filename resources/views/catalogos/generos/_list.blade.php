<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Nombre</th>
    </tr>
    </thead>
    <tbody>
    @foreach($generos as $genero)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$genero->id}}</td>
            <td>{{$genero->nombre}}</td>
        </tr>
    @endforeach

    </tbody>
</table>
<p>Mostrando registros del {{ $generos->total() > 0 ? ((($generos->currentPage() -1) * 10)+1) : 0 }} al {{ ((($generos->currentPage() -1) * 10))+$generos->count() }} de un total de {{ $generos->total() }} registros: </p>
{{ $generos->links() }}

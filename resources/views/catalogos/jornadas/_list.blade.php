<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">jornada de Conciliacion</th>
        <th width="10%">Acciones</th> 
    </tr>
    </thead>
    <tbody>
    @foreach($jornadas as $jornada)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$jornada->id}}</td>
            <td>{{$jornada->nombre}}</td>
            <td class="all">
                {!! Form::open(['action' => ['JornadaController@destroy', $jornada->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('jornadas.edit',[$jornada])}}" class="btn btn-xs btn-info">
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
<script>
    // function deleteJornada(){
    //     alert(confirm("as"));
    //     if(confirm("as")){
    //         location.href='{{ route('jornadas.destroy', $jornada->id)  }}'
    //     }
        
        
    // }

    $("formDelete").submit(function(e){
        e.preventDefault();
    });
</script>
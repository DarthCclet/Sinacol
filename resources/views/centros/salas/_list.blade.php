<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Sala</th>
        <!-- <th >Editar</th> -->
    </tr>
    </thead>
    <tbody>
    @foreach($salas as $sala)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$sala->id}}</td>
            <td>{{$sala->sala}}</td>
            <td><button class="btn btn-primary" onclick="location.href='{{ route('salas.edit', $sala->id)  }}'">Editar</button></td>



            <!-- route('salas.create') -->
            <!-- <td><a class="button " href="{{ route('login') }}">{{ __('Login') }}</a></td> -->

        </tr>
    @endforeach

    </tbody>
</table>

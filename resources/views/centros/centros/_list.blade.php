<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Centro de Conciliacion</th>
        <!-- <th >Editar</th> -->
    </tr>
    </thead>
    <tbody>
    @foreach($centros as $centro)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$centro->id}}</td>
            <td>{{$centro->nombre}}</td>
            <!-- <td><a class="button " href="{{ route('login') }}">{{ __('Login') }}</a></td> -->

        </tr>
    @endforeach

    </tbody>
</table>

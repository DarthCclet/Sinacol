
<table id="lista-ccostos" class="table table-hover">
    <thead>
    <tr>
        <td>Código</td>
        <td>Nombre</td>
        <td class="actions">Acción</td>
    </tr>
    </thead>

    <tbody>
    @foreach($giros as $cc)
        <tr data-tt-id="{{$cc->id}}" @if($cc->parent_id) data-tt-parent-id="{{$cc->parent_id}}" @endif>
            <td class="folder" nowrap>{{$cc->codigo}}</td>
            <td>
                {{$cc->nombre}}
            </td>
            <td class="">
                <a href="/{{$cc->id}}" class="btn btn-info btn-xs">
                    <i class="fa fa-edit"></i>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>

</table>

<style>
    #lista-ccostos tr {
        -moz-transition: all 0.5s;
        -o-transition: all 0.5s;
        -webkit-transition: all 0.5s;
        transition: all 0.5s;
        cursor: move;
    }

    #lista-ccostos .accept {
        background: rgba(213, 254, 209, 0.25);
    }

    #lista-ccostos .ui-draggable-dragging {
        background: rgba(254, 238, 190, 0.97);
        width: 96%;
        padding-top: 15px;
        padding-bottom: 15px;
        cursor: move;
    }

    #lista-ccostos .droppedEl {
        background-color: #d5fed1;
    }

</style>

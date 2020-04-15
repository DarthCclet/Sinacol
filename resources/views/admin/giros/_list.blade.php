

<select name="filterGiros" id="filterGiros" class="form-control">
</select>   
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
@push('scripts')

<script>
    $("#filterGiros").select2({
        ajax: {
            url: '/api/giros_comerciales/filtrarGirosComerciales',
            type:"POST",
            dataType:"json",
            async:false,
            data:function (params) {
                var data = {
                    nombre: params.term
                }
                return data;
            },
            processResults:function(json){
                $.each(json.data, function (key, node) {
                    var html = '';
                    html += '<div>';
                    var ancestors = node.ancestors.reverse();
                    html += '<h5>* '+node.nombre+'</h5><br>';
                    $.each(ancestors, function (index, ancestor) {
                        if(ancestor.id != 1){
                            var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(index);
                            html += '<p><b>'+ancestor.codigo+'</b>'+' |'+tab+ancestor.nombre+'</p>';
                        }
                    });
                    var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(node.ancestors.length);
                    html += '<p><b>'+node.codigo+'</b>'+' | '+tab+node.nombre+'</p>';
                    html += '<div>';
                    json.data[key].html = html;
                });
                return {
                    results: json.data
                };
            }
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            return data.html;
        },templateSelection: function(data) {
            console.log(data);
            if(data.id != ""){
                return "<b>"+data.codigo+"</b>&nbsp;&nbsp;"+data.nombre;
            }
            return data.text;
        },
        placeholder:'Seleccione una opcion',
        minimumInputLength:4
    })
    $("#filterGiros").on("change",function(){
        $("#lista-ccostos").treetable("reveal",$("#filterGiros").val());
        $("#lista-ccostos").treetable("node",$("#filterGiros").val());
        var droppedEl = $("tr[data-tt-id="+$("#filterGiros").val()+"]");
        $('html,body').animate({
            scrollTop: droppedEl.offset().top - 200
        }, 'slow');
        droppedEl.addClass('droppedEl');
        // setTimeout(function() {
        //     droppedEl.removeClass('droppedEl');
        // }, 3000)
    });
    
</script>
@endpush
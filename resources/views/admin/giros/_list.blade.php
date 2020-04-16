

<div class="col-md-12 ">
    <label> Filtro de giros por texto</label>
    <select name="filterGiros" placeholder="Seleccione" id="filterGiros" class="form-control"></select>
</div>
<div class="col-md-12" style="margin-top:1%">
    <label > Fitro de giros por nivel</label>
    <div class="col-md-12 row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="persona_id" class="col-sm-6 control-label">Giro nivel 1</label>
                    <select id="girosNivel1" nextLevel="2" class="form-control giroNivel">
                    </select>
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="persona_id" class="col-sm-6 control-label">Giro nivel 2</label>
                    <select id="girosNivel2" nextLevel="3" class="form-control giroNivel">
                    </select>
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="persona_id" class="col-sm-6 control-label">Giro nivel 3</label>
                    <select id="girosNivel3" nextLevel="" class="form-control giroNivel">
                    </select>
                </label>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="term">
<table id="lista-ccostos" class="table table-hover table-bordered">
    <thead>
    <tr>
        <td>Código</td>
        <td>Nombre</td>
        <td>Ambito</td>
        <td class="actions">Acción</td>
    </tr>
    </thead>

    <tbody>
    @foreach($giros as $cc)
        <tr data-tt-id="{{$cc->id}}" @if($cc->parent_id) data-tt-parent-id="{{$cc->parent_id}}" @endif>
            <td class="folder" nowrap>{{$cc->codigo}}</td>
            <td>
                <span class="spanNombre" id="spanNombre{{$cc->id}}">
                    {{$cc->nombre}}
                </span>
            </td>
            <td>
                <span class="editable-click spanAmbito" id="spanAmbito{{$cc->id}}" data-id="{{$cc->id}}" data-ambito_id="{{$cc->ambito_id}}">
                    {{$cc->ambito->nombre}}
                </span>
            </td>
            <td class="">
                <a style="color:white;" onclick="CargarGiro({{$cc->id}})" class="btn btn-info btn-xs">
                    <i class="fa fa-edit"></i>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>

</table>

<style>
    .spanAmbito{
        cursor: pointer;
    }
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
    .highlighted{
        background-color: #FFFF00;
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
                $("#term").val(params.term);
                var data = {
                    nombre: params.term
                }
                return data;
            },
            processResults:function(json){
                $.each(json.data, function (key, node) {
                    var html = '';
                    html += '<table>';
                    var ancestors = node.ancestors.reverse();
                    html += '<tr><th colspan="2"><h5>* '+highlightText(node.nombre)+'</h5><th></tr>';
                    $.each(ancestors, function (index, ancestor) {
                        if(ancestor.id != 1){
                            var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(index);
                            html += '<tr><td ><b>'+ancestor.codigo+'</b></td>'+' <td style="border-left:1px solid;">'+tab+highlightText(ancestor.nombre)+'</td></tr>';
                        }
                    });
                    var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(node.ancestors.length);
                    html += '<tr><td><b>'+node.codigo+'</b></td>'+'<td style="border-left:1px solid;"> '+ tab+highlightText(node.nombre)+'</td></tr>';
                    html += '</table>';
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
        minimumInputLength:4,
        allowClear: true,
        language: "es"
    });
    function highlightText(string){
        return string.replace($("#term").val().trim(),'<span class="highlighted">'+$("#term").val().trim()+"</span>");
    }
    $("#filterGiros").on("change",function(){
        if($("#filterGiros").val() != null){
            $("#lista-ccostos").treetable("reveal",$("#filterGiros").val());
            $("#lista-ccostos").treetable("node",$("#filterGiros").val());
            $("tr").removeClass('droppedEl');
            var droppedEl = $("tr[data-tt-id="+$("#filterGiros").val()+"]");
            $('html,body').animate({
                scrollTop: droppedEl.offset().top - 200
            }, 'slow');
            droppedEl.addClass('droppedEl');
        }else{
            $("tr").removeClass('droppedEl');
        }
    });

    function getGironivel(id,nivel,select){
        var tieneHijos = false;
        $.ajax({
            url:"/api/giros_comerciales/getGirosComercialesByNivelId",
            type:"POST",
            dataType:"json",
            async:false,
            data:{
                id:id,
                nivel:nivel
            },
            success:function(json){
                console.log(json.data != "");
                if(json != null && json.data != ""){
                    $("#"+select).html("<option value=''>Selecciona una opcion</option>");
                    $.each(json.data,function(index,element){
                        $("#"+select).append("<option value='"+element.id+"'>"+element.codigo+"|&nbsp;&nbsp;"+element.nombre+"</option>");
                    });
                    tieneHijos = true;
                }else{
                    $("#"+select).html("<option value=''>No hay opciones a mostrar</option>");
                }
                
            }
        });
        return tieneHijos;
    }
    $(".giroNivel").select2({
        placeholder:"Seleccione una opcion",
        language: "es"
    });
    $(".giroNivel").on("change",function(){
        var tieneHijos = false;
        if($(this).attr("nextLevel") != ""){
            tieneHijos = getGironivel($(this).val(),$(this).attr("nextLevel"),"girosNivel"+$(this).attr("nextLevel"));
        }
        if(!tieneHijos){
            if($(this).val() != null){
                $("#lista-ccostos").treetable("reveal",$(this).val());
                $("#lista-ccostos").treetable("node",$(this).val());
                $("tr").removeClass('droppedEl');
                var droppedEl = $("tr[data-tt-id="+$(this).val()+"]");
                $('html,body').animate({
                    scrollTop: droppedEl.offset().top - 200
                }, 'slow');
                droppedEl.addClass('droppedEl');
            }else{
                $("tr").removeClass('droppedEl');
            }
        }
    });
    $(document).ready(function() {
        getGironivel("",1,"girosNivel1");
    });
    
</script>
@endpush
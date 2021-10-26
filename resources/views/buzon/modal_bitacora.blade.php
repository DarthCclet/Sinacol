<input type="hidden" id="parte_bitacora_id" value=""/>
<input type="hidden" id="url_bitacora" value="/acta_bitacora/"/>
<input type="hidden" id="interno" value="{{$interno}}"/>

<div class="modal" id="modal-bitacora"role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"> Bitacora Buz&oacute;n </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="body-bitacora">
                {{-- <div class="col-md-12" style="overflow: scroll; max-height:400px;">
                    <ul>
                        @foreach($bitacoras as $bitacora)
                        <li><label style="font-size: x-large;"> {{$bitacora->descripcion}} {{$bitacora->created_at}} </label></li>
                        @endforeach 
                    </ul>
                </div> --}}
            </div>
            <div class="modal-footer">
                @if($interno)
                    <a class="btn btn-primary m-l-5" onclick="acta_bitacora()" target="_blank" > Generar constancia de historial</a>
                @else
                    <a class="btn btn-primary m-l-5" id="btn_generar" href="#" target="_blank" > Generar constancia de historial</a>
                @endif
                <button class="btn btn-white m-l-5" data-dismiss="modal"> Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        function getBitacoraBuzon(parte_id){
            $("#parte_bitacora_id").val(parte_id);
            $("#btn_generar").prop("href","/acta_bitacora/"+parte_id);
            $.ajax({
                url:"/bitacora_buzon/"+parte_id,
                type:"GET",
                dataType:"json",
                async:true,
                success:function(json){
                    try{
                        console.log(json);
                        if(json.success){
                            var html = "";
                            html += "<ul>";
                            $.each(json.data, function (key, bitacora) {
                                html += "<li><label style='font-size: x-large;'> "+ bitacora.descripcion+" "+bitacora.created_at+" </label></li>";
                            });
                            html += "</ul>";
                            $("#body-bitacora").html(html);
                            $("#modal-bitacora").modal("show");
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
        function acta_bitacora(){
            var parte_id = $("#parte_bitacora_id").val();
            $.ajax({
                url:"/acta_bitacora_interno/"+parte_id,
                type:"GET",
                dataType:"json",
                async:true,
                success:function(json){
                    try{
                        console.log(json);
                        if(json.success){
                            swal({
                                title: 'Éxito',
                                text: 'El acta se genero correctamente',
                                icon: 'success'
                            });
                            location.reload();
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
        
    </script>
@endpush

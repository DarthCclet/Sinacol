<div class="modal" id="modal-caduco"role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Las siguientes <span class="badge badge-pill btn-light">{{count($caducan)}}</span> solicitudes estan por caducar </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12" style="overflow: scroll; max-height:400px;">
                    @foreach($caducan as $sol)
                        <label style="font-size: x-large;">- Solicitud: <b>{{$sol->folio}}/{{$sol->anio}}</b> Caduca en <b>{{$sol->Caduca}}</b> d&iacute;as</label>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary m-l-5"  href="{{url('/solicitud/porCaducar')}}" target="_blank" > Mostrar detalle en otra pesta&ntilde;a</a>
                <button class="btn btn-white m-l-5" data-dismiss="modal"> Cerrar</button>
            </div>
        </div>
    </div>
</div>
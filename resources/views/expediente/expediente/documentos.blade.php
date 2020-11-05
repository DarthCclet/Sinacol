
{{-- @section('content') --}}
    <!-- begin page-header -->
    <h1 class="page-header">Documentos del expediente</h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div class="panel-heading-btn">
                
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            <div class="col-md-12 row">

                <div class="col-md-6">
                    <h3>Solicitud</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        @foreach ($documentos as $documento)
                        @if ($documento->tipo_doc == 1 || $documento->tipo_doc == 2 )    
                            <tr>
                                <td><b>{{isset($documento->parte) ? $documento->parte."- ":""  }}</b>{{$documento->clasificacionArchivo->nombre}} </td><td><a class="btn btn-link" href="/api/documentos/getFile/{{$documento->id}}" target="_blank">Descargar</a></td>
                            </tr>
                        @endif
                        @endforeach
                    </table>
                </div>
                {{-- <hr style="height:2px;border-width:0;color:lightgray;background-color:lightgray"> --}}
                <div class="col-md-6">
                    <h3>Audiencias</h3>
                    <table class="table table-bordered" >
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        @foreach ($documentos as $documento)
                            @if ($documento->tipo_doc == 3)    
                                <tr>
                                    <td><b>{{isset($documento->audiencia_id) ? "".$documento->audiencia."- ":$documento->parte."- "  }}</b>{{$documento->clasificacionArchivo->nombre}} </td><td><a class="btn btn-link" href="/api/documentos/getFile/{{$documento->id}}" target="_blank">Descargar</a></td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

{{-- @endsection --}}

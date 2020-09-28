{{-- @extends('layouts.header-footer') --}}
{{-- @section('content') --}}

    <p>
        CENTRO FEDERAL DE CONCILIACIÓN Y REGISTRO LABORAL 
    </p>
    <p>
        CON SEDE EN @if(isset($solicitud)) {{isset($solicitud->centro->domicilio->estado) ? Str::upper($solicitud->centro->domicilio->estado) : 'MEXICO'}} @endif
    </p>
    <p>
        NUMERO IDENTIFICACIÓN ÚNICO: @if(isset($solicitud)) {{$solicitud->expediente->folio}} @endif
    </p>
{{-- @endsection --}}

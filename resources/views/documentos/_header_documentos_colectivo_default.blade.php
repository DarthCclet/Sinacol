{{-- @extends('layouts.header-footer') --}}
{{-- @section('content') --}}
    <table style="width: 100%; border-collapse: collapse; margin-left: auto; margin-right: auto;" border="0">
    <tbody>
    <tr>
    <td class="celda-logo" style="width: 35.1477%;"><img src="/assets/img/logo/LOGO_cfcrl.png" height="70" /></td>
    <td class="celda-centro" style="width: 13.3335%;">&nbsp;</td>
    <td class="celda-derecha" style="width: 51.5187%; text-align: center;">&nbsp;</td>
    </tr>
    </tbody>
    </table>
    <p>
        CENTRO FEDERAL DE CONCILIACIÓN Y REGISTRO LABORAL 
    </p>
    <p>
        CON SEDE EN @if(isset($solicitud)) {{$solicitud->centro_id}} @endif
    </p>
    <p>
        NUMERO IDENTIFICACIÓN ÚNICO: @if(isset($solicitud)) {{$solicitud->expediente->folio}} @endif
    </p>
{{-- @endsection --}}

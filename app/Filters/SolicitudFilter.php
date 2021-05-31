<?php


namespace App\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Implementa las consultas a la base de datos correspondientes a usuario y sus relaciones
 * Class UserFilter
 * @package App\Filters
 */
class SolicitudFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'fecha_ratificacion',
        'centro_id',
        'created_at',
        'updated_at',
    ];

    const LIMITE_SUPERIOR = " 23:59:59";
    const LIMITE_INFERIOR = " 00:00:00";
    const DIAS_EXPIRAR = 44;

    /**
     * Cuando se pasa el centro_id como parametro
     * @param $centro_id
     */
    public function handleCentroIdFilter($centro_id)
    {
        if(!trim($centro_id)) return;
        $this->query->where('centro_id', $centro_id);
    }

    /**
     * Cuando se pasa la abreviatura del centro como parametro
     * @param $abreviatura
     */
    public function handleCentroFilter($abreviatura)
    {
        if(is_array($abreviatura)) {
            if(!count($abreviatura)) return;
            $this->query->whereIn('centros.abreviatura', $abreviatura);
        }
        else {
            if(!trim($abreviatura)) return;
            $this->query->where('centros.abreviatura', strtoupper($abreviatura));
        }
    }

    /**
     * Cuando se pasa como parámetro objeto_solicitud_id
     * @param $objeto_solicitud_id
     */
    public function handleObjetoSolicitudIdFilter($objeto_solicitud_id)
    {
        if(is_array($objeto_solicitud_id)){
            if(empty($objeto_solicitud_id)) return;
            $this->query->whereHas(
                'objeto_solicitudes',
                function ($q) use ($objeto_solicitud_id) {
                    $q->whereIn('objeto_solicitud_id', $objeto_solicitud_id);
                }
            );
        }
        else {
            if (!trim($objeto_solicitud_id)) {
                return;
            }
            $this->query->whereHas(
                'objeto_solicitudes',
                function ($q) use ($objeto_solicitud_id) {
                    $q->where('objeto_solicitud_id', $objeto_solicitud_id);
                }
            );
        }
    }
    public function handleFechaRatificacionFilter($fechaRatificacion){
        if(!trim($fechaRatificacion)) return;
        $this->query->whereBetween('fecha_ratificacion', [$fechaRatificacion . self::LIMITE_INFERIOR ,$fechaRatificacion . self::LIMITE_SUPERIOR]);
    }
    
    public function handleFechaRecepcionFilter($fechaRecepcion){
        if(!trim($fechaRecepcion)) return;
        $this->query->whereBetween('fecha_recepcion', [$fechaRecepcion . self::LIMITE_INFERIOR ,$fechaRecepcion . self::LIMITE_SUPERIOR]);
    }
    public function handleFechaConflictoFilter($fechaConflicto){
        if(!trim($fechaConflicto)) return;
        $this->query->where('fecha_conflicto', $fechaConflicto);
    }
    public function handleFolioFilter($folio){
        if(!trim($folio)) return;
        $this->query->where('folio', $folio);
    }
    public function handleCurpFilter($curp){
        if(!trim($curp)) return;
        $this->query->whereHas('partes', function (Builder $query) use ($curp) {
            $query->where('curp', [$curp]);
        });
    }
    public function handleNombreFilter($nombre){
        if(!trim($nombre)) return;
        $nombre = trim($nombre);
        $nombre = str_replace(' ', '&', $nombre);
        $this->query->whereHas('partes', function (Builder $query) use ($nombre) {
            $query->where('tipo_parte_id', 1)->whereRaw("to_tsvector('spanish', unaccent(trim(coalesce(nombre_comercial,' ')||' '||coalesce(nombre,' ')||' '||coalesce(primer_apellido,' ')||' '||coalesce(segundo_apellido,' ')))) @@ to_tsquery('spanish', unaccent(?))", [$nombre]);
        });
    }
    public function handleNombreCitadoFilter($nombre_citado){
        if(!trim($nombre_citado)) return;
        $nombre_citado = trim($nombre_citado);
        $nombre_citado = str_replace(' ', '&', $nombre_citado);
        $this->query->whereHas('partes', function (Builder $query) use ($nombre_citado) {
            $query->where('tipo_parte_id', 2)->whereRaw("to_tsvector('spanish', unaccent(trim(coalesce(nombre_comercial,' ')||' '||coalesce(nombre,' ')||' '||coalesce(primer_apellido,' ')||' '||coalesce(segundo_apellido,' ')))) @@ to_tsquery('spanish', unaccent(?))", [$nombre_citado]);
        });
    }
    public function handleDiasExpiracionFilter($dias_expiracion){
        if(!trim($dias_expiracion)) return;
        $dias_expiracion = $this->request->get('dias_expiracion');
        $dias_rango_inferior = self::DIAS_EXPIRAR - $dias_expiracion;
        $fecha_fin = Carbon::now()->subDays($dias_rango_inferior);
        $this->query->where('fecha_recepcion','<',$fecha_fin->toDateString())->where('estatus_solicitud_id',2);
        $rolActual = session('rolActual')->name;
        if($rolActual == "Personal conciliador"){
            $conciliador_id = auth()->user()->persona->conciliador->id;
            $this->query->whereHas('expediente.audiencia',function ($query) use ($conciliador_id) { $query->where('conciliador_id',$conciliador_id); });
        }
    }
    public function handleAnioFilter($anio){
        if(!trim($anio)) return;
        $this->query->where('anio', $anio);
    }
    public function handleEstatusSolicitudIdFilter($estatus_solicitud_id){
        if(!trim($estatus_solicitud_id)) return;
        $this->query->where('estatus_solicitud_id', $estatus_solicitud_id);
    }
    public function handleLoadPartesFilter($loadPartes){
        $this->query->with("partes","expediente");
    }
    public function handleExpedienteFilter($Expediente){
        if(!trim($Expediente)) return;
        $this->query->whereHas('expediente', function (Builder $query) use ($Expediente) {
            $query->where('folio', [$Expediente]);
        });
    }
    public function handleConciliadorIdFilter($conciliador_id){
        if(!trim($conciliador_id)) return;
        $this->query->whereHas('expediente.audiencia', function($q) use($conciliador_id) {
            $q->where('conciliador_id', $conciliador_id);
        });
    }
    public function handleTipoSolicitudIdilter($tipo_solicitud_id){
        if(!trim($tipo_solicitud_id)) return;
        $this->query->where('tipo_solicitud_id', $tipo_solicitud_id);
    }
    
}

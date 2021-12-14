<?php


namespace App\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Métodos para filtrar consultas mediante la petición HTTP de Solicitud
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

    /**
     * Indica el límite superior de consulta
     */
    const LIMITE_SUPERIOR = " 23:59:59";
    /**
     * Indica el límite inferior de consulta
     */
    const LIMITE_INFERIOR = " 00:00:00";
    /**
     * Indíca el número de días que se considera expirada la solicitud
     */
    const DIAS_EXPIRAR = 44;

    /**
     * Cuando se pasa el centro_id como parametro
     * @param integer $centro_id ID del centro
     */
    public function handleCentroIdFilter($centro_id)
    {
        if(!trim($centro_id)) return;
        $this->query->where('centro_id', $centro_id);
    }

    /**
     * Cuando se pasa la abreviatura del centro como parametro
     * @param array|string $abreviatura Arreglo de abreviaturas de centro o una sóla abreviatura de centro
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
     * @param array|integer $objeto_solicitud_id Arreglo de ID de objetos de solicitud o un sólo ID de objeto de solicitud
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

    /**
     * Cuando se pasa como parámetro la fecha de ratificación
     * @param string $fechaRatificacion Fecha en formato Y-m-d
     */
    public function handleFechaRatificacionFilter($fechaRatificacion){
        if(!trim($fechaRatificacion)) return;
        $this->query->whereBetween('fecha_ratificacion', [$fechaRatificacion . self::LIMITE_INFERIOR ,$fechaRatificacion . self::LIMITE_SUPERIOR]);
    }

    /**
     * Cuando se pasa como parámetro la fecha de recepción
     * @param string $fechaRecepcion Fecha en formato Y-m-d
     */
    public function handleFechaRecepcionFilter($fechaRecepcion){
        if(!trim($fechaRecepcion)) return;
        $this->query->whereBetween('fecha_recepcion', [$fechaRecepcion . self::LIMITE_INFERIOR ,$fechaRecepcion . self::LIMITE_SUPERIOR]);
    }

    /**
     * Cuando se pasa como parámetro la fecha de confilcto
     * @param string $fechaConflicto Fecha en formato Y-m-d
     */
    public function handleFechaConflictoFilter($fechaConflicto){
        if(!trim($fechaConflicto)) return;
        $this->query->where('fecha_conflicto', $fechaConflicto);
    }

    /**
     * Cuando se pasa como parámetro el folio de la solicitud
     * @param integer $folio Folio de la solicitud
     */
    public function handleFolioFilter($folio){
        if(!trim($folio)) return;
        $this->query->where('folio', $folio);
    }

    /**
     * Cuando se pasa la CURP como parámetro
     * @param string $curp La Clave Única de Registro de Población
     */
    public function handleCurpFilter($curp){
        if(!trim($curp)) return;
        $this->query->whereHas('partes', function (Builder $query) use ($curp) {
            $query->where('curp', [$curp]);
        });
    }

    /**
     * Cuando se pasa como parámetro el nombre del solicitante
     * @param string $nombre El nombre completo o un fragmento del nombre del solicitante
     */
    public function handleNombreFilter($nombre){
        if(!trim($nombre)) return;
        $nombre = trim($nombre);
        $nombre = str_replace(' ', '&', $nombre);
        $this->query->whereHas('partes', function (Builder $query) use ($nombre) {
            $query->where('tipo_parte_id', 1)
                ->whereRaw("to_tsvector('spanish', unaccent(trim(coalesce(nombre_comercial,' ')||' '||coalesce(nombre,' ')||' '||coalesce(primer_apellido,' ')||' '||coalesce(segundo_apellido,' ')))) @@ to_tsquery('spanish', unaccent(?))", [$nombre])
            ;
        });
    }

    /**
     * Cuando se pasa como parámetro el nombre del citado
     * @param string $nombre_citado El nombre completo del citado o un fragmento del nombre del citado
     */
    public function handleNombreCitadoFilter($nombre_citado){
        if(!trim($nombre_citado)) return;
        $nombre_citado = trim($nombre_citado);
        $nombre_citado = str_replace(' ', '&', $nombre_citado);
        $this->query->whereHas('partes', function (Builder $query) use ($nombre_citado) {
            $query->where('tipo_parte_id', 2)->whereRaw("to_tsvector('spanish', unaccent(trim(coalesce(nombre_comercial,' ')||' '||coalesce(nombre,' ')||' '||coalesce(primer_apellido,' ')||' '||coalesce(segundo_apellido,' ')))) @@ to_tsquery('spanish', unaccent(?))", [$nombre_citado]);
        });
    }

    /**
     * Cuando se pasa como parámetro un número de días de expiración
     * @param integer $dias_expiracion El número de días por expirar
     */
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

    /**
     * Cuando se pasa como parámetro el año de registro de la solicitud
     * @param integer $anio El año de registro
     */
    public function handleAnioFilter($anio){
        if(!trim($anio)) return;
        $this->query->where('anio', $anio);
    }

    /**
     * Cuando se pasa como parámetro el estatus_solicitud_id de la solicitud
     * @param integer $estatus_solicitud_id ID del estatus de la solicitud
     */
    public function handleEstatusSolicitudIdFilter($estatus_solicitud_id){
        if(!trim($estatus_solicitud_id)) return;
        $this->query->where('estatus_solicitud_id', $estatus_solicitud_id);
    }

    /**
     * Cuando se pasa parametro para la carga de las partes
     * @param string $loadPartes Indica si se carga o no las partes
     */
    public function handleLoadPartesFilter($loadPartes){
        $this->query->with("partes","expediente");
    }

    /**
     * Cuando se pasa el folio del expediente como parámetro
     * @param string $Expediente El folio del expediente
     */
    public function handleExpedienteFilter($Expediente){
        if(!trim($Expediente)) return;
        $this->query->whereHas('expediente', function (Builder $query) use ($Expediente) {
            $query->where('folio', [$Expediente]);
        });
    }

    /**
     * Cuandos se pasa como parámetro el ID del conciliador
     * @param integer $conciliador_id ID del conciliador
     */
    public function handleConciliadorIdFilter($conciliador_id){
        if(!trim($conciliador_id)) return;
        $this->query->whereHas('expediente.audiencia', function($q) use($conciliador_id) {
            $q->where('conciliador_id', $conciliador_id);
        });
    }

    /**
     * Cuando se pasa como parámetro el ID del tipo de solicitud
     * @param integer $tipo_solicitud_id ID del tipo de solicitud
     */
    public function handleTipoSolicitudIdFilter($tipo_solicitud_id){
        if(!trim($tipo_solicitud_id)) return;
        $this->query->where('tipo_solicitud_id', $tipo_solicitud_id);
    }

}

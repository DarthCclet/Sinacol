<?php

namespace App;

use App\Exceptions\FolioSolicitudExistenteException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AppendPolicies;
use App\Traits\LazyAppends;
use App\Traits\LazyLoads;
use App\Traits\RequestsAppends;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\ValidTypes;
use Carbon\Carbon;

class Solicitud extends Model implements Auditable
{
    use SoftDeletes,
        LazyLoads,
        LazyAppends,
        RequestsAppends,
        AppendPolicies,
        \OwenIt\Auditing\Auditable,
        ValidTypes,
        \App\Traits\CambiarEventoAudit;
    protected $table = 'solicitudes';
    protected $guarded = ['id','updated_at','created_at'];

    /**
     * Días para expiración de solicitudes
     */
    const DIAS_EXPIRACION = 45;

    /**
     * Las relaciones que son cargables.
     *
     * @var array
     */
    protected $loadable = [ 'estatusSolicitud','objetoSolicitud','centro','user','partes','solicitados','solicitantes'];

    public function transformAudit($data):array
    {
//        Validamos el estatus de la solicitud
        if (Arr::has($data, 'new_values.estatus_solicitud_id')) {
            if($data["event"] != "created"){
                $data['old_values']['Estatus de solicitud'] = EstatusSolicitud::find($this->getOriginal('estatus_solicitud_id'))->name;
                unset($data['old_values']["estatus_solicitud_id"]);
            }
            $data['new_values']['Estatus de solicitud'] = EstatusSolicitud::find($this->getAttribute('estatus_solicitud_id'))->name;
            unset($data['new_values']["estatus_solicitud_id"]);
        }
//        Validamos el campo ratificada
        if (Arr::has($data, 'new_values.ratificada')) {
            if($data["event"] != "created"){
                $data['old_values']['ratificada'] = $this->validBool($this->getOriginal('ratificada'));
            }
            $data['new_values']['ratificada'] = $this->validBool($this->getAttribute('ratificada'));
        }
//        Validamos el campo solicita_excepcion
        if (Arr::has($data, 'new_values.solicita_excepcion')) {
            if($data["event"] != "created"){
                $data['old_values']['Solicita excepcion'] = $this->validBool($this->getOriginal('solicita_excepcion'));
                unset($data['old_values']["solicita_excepcion"]);
            }
            $data['new_values']['Solicita excepcion'] = $this->validBool($this->getAttribute('solicita_excepcion'));
            unset($data['new_values']["solicita_excepcion"]);
        }
//        Validamos el campo fecha_ratificacion
        if (Arr::has($data, 'new_values.fecha_ratificacion')) {
            if($data["event"] != "created"){
                $data['old_values']['Fecha de ratificación'] = $this->getOriginal('fecha_ratificacion');
                unset($data['old_values']["fecha_ratificacion"]);
            }
            $data['new_values']['Fecha de ratificación'] = $this->getAttribute('fecha_ratificacion');
            unset($data['new_values']["fecha_ratificacion"]);
        }
//        Validamos el campo fecha_conflicto
        if (Arr::has($data, 'new_values.fecha_conflicto')) {
            if($data["event"] != "created"){
                $data['old_values']['Fecha de conflicto'] = $this->getOriginal('fecha_conflicto');
                unset($data['old_values']["fecha_conflicto"]);
            }
            $data['new_values']['Fecha de conflicto'] = $this->getAttribute('fecha_conflicto');
            unset($data['new_values']["fecha_conflicto"]);
        }
//        Validamos el campo fecha_ratificacion
        if (Arr::has($data, 'new_values.observaciones')) {
            if($data["event"] != "created"){
                $data['old_values']['observaciones'] = $this->getOriginal('observaciones');
            }
            $data['new_values']['observaciones'] = $this->getAttribute('observaciones');
        }
//        Validamos el campo usuario
        if (Arr::has($data, 'new_values.user_id')) {
            if($data["event"] != "created"){
                if($this->getOriginal('user_id')){
                    $userOld = User::find($this->getOriginal('user_id'))->persona;
                    $data['old_values']['usuario'] = $userOld->nombre." ".$userOld->primer_apellido." ".$userOld->segundo_apellido;
                    unset($data['old_values']["user_id"]);
                }
            }
            $userNew = User::find($this->getAttribute('user_id'))->persona;
            $data['new_values']['usuario'] = $userNew->nombre." ".$userNew->primer_apellido." ".$userNew->segundo_apellido;
            unset($data['new_values']["user_id"]);
        }
        $data = $this->cambiarEvento($data);
        return $data;
    }

    /**
     * Funcion para asociar con modelo EstatusSolicitud
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function estatusSolicitud(){
      return $this->belongsTo('App\EstatusSolicitud');
    }
    /**
     * Funcion para asociar con modelo ObjetoSolicitud con belongsTo
     * * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function objetoSolicitud(){
      return $this->belongsTo('App\ObjetoSolicitud');
    }

    /**
     * Funcion para asociar con modelo Centro con belongsTo
     * * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function centro(){
      return $this->belongsTo('App\Centro');
    }

    /**
     * Funcion para asociar con modelo User con belongsTo
     * * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
      return $this->belongsTo('App\User');
    }

    public function partes()
    {
        return $this->hasMany('App\Parte', 'solicitud_id', 'id');
    }

    public function solicitantes()
    {
        return $this->hasMany('App\Parte', 'solicitud_id', 'id')->where("tipo_parte_id",1);
    }

    public function solicitados()
    {
        return $this->hasMany('App\Parte', 'solicitud_id', 'id')->where("tipo_parte_id",2);
    }

    public function objeto_solicitudes()
    {
        return $this->belongsToMany('App\ObjetoSolicitud');
    }

    public function expediente()
    {
        return $this->hasOne(Expediente::class);
    }
    public function tipoSolicitud(){
        return $this->belongsTo(TipoSolicitud::class);
      }
    public function documentos(){
        return $this->morphMany(Documento::class,'documentable');
    }

    /**
     * El usuario comentó un documento que subió manualmente como incompetencia en su descripción
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function documentosComentadosComoIncompetencia(){
        return $this->morphMany(Documento::class,'documentable')->whereRaw('documentos.descripcion ilike '."'%incompetencia%'");
    }

    /**
     * Documentos con clase de archivo incompetencia
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function documentosClasificadossComoIncompetencia(){
        $incompetencia_id = 13; //ID de incompetencia en el catalogo de clasificacion_archivos
        return $this->morphMany(Documento::class,'documentable')->where('clasificacion_archivo_id',$incompetencia_id);
    }

    /**
     * Funcion para asociar con modelo Estado
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function giroComercial(){
        return $this->belongsTo('App\GiroComercial');
    }

    public function resuelveOficinaCentral(){
        if($this->tipo_solicitud_id == 3 || $this->tipo_solicitud_id == 4){
            return true;
        }
        return false;
    }
    public function tipoIncidenciaSolicitud(){
        return $this->belongsTo(TipoIncidenciaSolicitud::class);
    }
    public function solicitud(){
        return $this->belongsTo(Solicitud::class);
    }
    public function firmas(){
        return $this->morphMany(FirmaDocumento::class,'firmable');
    }
    public function getCaducaAttribute(){
        $fecha_recepcion = $this->fecha_recepcion;
        $date = Carbon::parse($fecha_recepcion);
        $now = Carbon::now();
        $dias = $date->diffInDays($now);
        $resultado = self::DIAS_EXPIRACION - $dias;
        return $resultado;
    }

    /**
     * Relación con el usuario que captura inicialmente esta solicitud.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuarioCaptura()
    {
        return $this->belongsTo(User::class, 'captura_user_id');
    }

    /**
     * @inheritDoc
     */
    public static function boot()
    {
        parent::boot();

        // Antes de crear la solicitud revisamos si el folio ya existe en otro expediente
        static::creating(function ($model) {
            // Si existe ya el folioenviamos excepción
            if(self::whereFolio($model->folio)->whereAnio($model->anio)->first()){
                throw new FolioSolicitudExistenteException($model);
            }
        });
    }

}

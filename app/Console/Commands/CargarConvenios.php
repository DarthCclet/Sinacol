<?php

namespace App\Console\Commands;

use App\Audiencia;
use App\ClasificacionArchivo;
use App\Parte;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CargarConvenios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cargarConvenios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para cargar convenios masivos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            $savedConv = fopen(__DIR__."/../../../public/loadedConv.txt", 'w');
            $savedIdent = fopen(__DIR__."/../../../public/loadedIdent.txt", 'w');
            $failedConv = fopen(__DIR__."/../../../public/failedConv.txt", 'w');
            $failedIdent = fopen(__DIR__."/../../../public/failedIdent.txt", 'w');

            $arreglo = $this->obtenerCurp();
            //Recorremos todas las curp
            foreach ($arreglo as $key => $curp) {
                $parte = Parte::whereCurp($curp)->first();
                if($parte){
                    $origenConv = storage_path('/app/convenios/'.$curp.".pdf");
                    $origenIne = storage_path('/app/identificaciones/'.$curp.".pdf");
                    if($parte->solicitud->expediente && $parte->solicitud->expediente->audiencia){
                        $solicitud = $parte->solicitud;
                        $audiencia = $parte->solicitud->expediente->audiencia->first();
                        $directorio = 'expedientes/' . $audiencia->expediente_id . '/audiencias/' . $audiencia->id;
                        $path = $directorio."/".$curp.".pdf";
                        $fullPath = storage_path().'/app/' .$directorio."/".$curp.".pdf";
                        $tipoArchivo = ClasificacionArchivo::find(52);
                        //Se carga convenio
                        if(file_exists($origenConv)){
                            Storage::makeDirectory($directorio);
                            File::move($origenConv,$fullPath);
                            $uuid = Str::uuid();
                            $documento = $audiencia->documentos()->create([
                                "nombre" => $curp.".pdf",
                                "nombre_original" => $curp.".pdf",
                                "descripcion" => "Identificacion ".$curp.".pdf",
                                "ruta" => $path,
                                "uuid" => $uuid,
                                "tipo_almacen" => "local",
                                "uri" => $path,
                                "longitud" => round(Storage::size($path) / 1024, 2),
                                "firmado" => "false",
                                "clasificacion_archivo_id" => $tipoArchivo->id ,
                            ]);
                            if($documento){
                                $correcto = "Se guardar convenio CURP: ".$curp. " en la linea ".($key+1);
                                dump($correcto); 
                                fputs($savedConv, $correcto."\n");
                            }else{
                                $error = "No se pudo guardar convenio de CURP: ".$curp. " en la linea ".($key+1);
                                dump($error); 
                                fputs($failedConv, $error."\n");
                            }
                        }else{
                            $error = "No se encontro archivo con curp: ".$curp. " en la linea ".($key+1);
                            dump($error); 
                            fputs($failedConv, $error."\n");    
                        }
                        //Se carga ine
                        $directorioIdentif = 'solicitud/' . $solicitud->id.'/parte/'.$parte->id;
                        $pathIdentif = $directorioIdentif."/".$curp.".pdf";
                        $fullPathIdentif = storage_path().'/app/' .$directorioIdentif."/".$curp.".pdf";
                        $tipoArchivoIdentif = ClasificacionArchivo::find(1);
                        if(file_exists($origenIne)){
                            Storage::makeDirectory($directorioIdentif);
                            File::move($origenIne,$fullPathIdentif);
                            $uuidIdentif = Str::uuid();
                            $documento = $parte->documentos()->create([
                                "nombre" => $curp.".pdf",
                                "nombre_original" => $curp.".pdf",
                                "descripcion" => "Documento de audiencia ".$curp.".pdf",
                                "ruta" => $pathIdentif,
                                "uuid" => $uuidIdentif,
                                "tipo_almacen" => "local",
                                "uri" => $pathIdentif,
                                "longitud" => round(Storage::size($pathIdentif) / 1024, 2),
                                "firmado" => "false",
                                "clasificacion_archivo_id" => $tipoArchivoIdentif->id ,
                            ]);
                            if($documento){
                                $correcto = "Se guardar convenio CURP: ".$curp. " en la linea ".($key+1);
                                dump($correcto); 
                                fputs($savedIdent, $correcto."\n");
                            }else{
                                $error = "No se pudo guardar convenio de CURP: ".$curp. " en la linea ".($key+1);
                                dump($error); 
                                fputs($failedConv, $error."\n");
                            }
                        }else{
                            $error = "No se encontro archivo con curp: ".$curp. " en la linea ".($key+1);
                            dump($error); 
                            fputs($failedIdent, $error."\n");    
                        }
                    }else{
                        $error = "No se encontro audiencia con curp: ".$curp. " en la linea ".($key+1);
                        dump($error); 
                        fputs($failedConv, $error."\n");    
                    }
                }else{
                    $error = "No se encontro CURP: ".$curp. " en la linea ".($key+1);
                    dump($error); 
                    fputs($failedConv, $error."\n");
                }
            }
        }catch(Exception $e){
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensaje: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            $error = "No se encontro Audiencia: ".$curp. " en la linea ".($key+1);
            dump($error); 
            fputs($failedConv, $error."\n");
        }
    }
    function obtenerCurp() {
        $filename = storage_path('/app/convenios.csv');
        $file = fopen($filename, "r");
        $curp = array();
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            if ($this->curpValida($data[2])) {
                $curp[] = $data[2];
            }
        }
        return $curp;
    }

    function curpValida($str) {
        $pattern = '/^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/';
        return preg_match($pattern, $str);
    }
}

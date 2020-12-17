<?php

use App\Documento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class UpdateHashUuidToDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $documentos = Documento::all();
        foreach ($documentos as $documento) {
            $documento->uuid = Str::uuid();
            $documento->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $documentos = Documento::all();
        foreach ($documentos as $documento) {
            $documento->uuid = null;
            $documento->save();
        }
    }
}

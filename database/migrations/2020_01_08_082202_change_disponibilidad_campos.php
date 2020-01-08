<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDisponibilidadCampos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('disponibilidades', function (Blueprint $table) {
            $table->dropIndex(['disponibiliable_id','disponibiliable_type']);
            $table->renameColumn('disponibiliable_id', 'disponibilidad_id');
            $table->renameColumn('disponibiliable_type', 'disponibilidad_type');
            $table->index(['disponibilidad_id', 'disponibilidad_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disponibilidades', function (Blueprint $table) {
            $table->dropIndex(['disponibilidad_id','disponibilidad_type']);
            $table->renameColumn('disponibilidad_id', 'disponibiliable_id');
            $table->renameColumn('disponibilidad_type', 'disponibiliable_type');
            $table->index(['disponibiliable_id', 'disponibiliable_type']);
        });
    }
}

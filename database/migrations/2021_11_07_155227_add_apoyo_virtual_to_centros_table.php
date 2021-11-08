<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApoyoVirtualToCentrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('centros', 'apoyo_virtual')) {
          Schema::table('centros', function (Blueprint $table) {
              $table->boolean('apoyo_virtual')->default(false)->comment('Indica si el centro va a recibir apoyo para audiencias virtuales');
          });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('centros', function (Blueprint $table) {
            $table->dropColumn('apoyo_virtual');
        });
    }
}

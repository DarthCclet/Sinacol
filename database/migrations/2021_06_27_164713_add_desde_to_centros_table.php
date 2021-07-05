<?php

use App\Centro;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDesdeToCentrosTable extends Migration
{
    protected $imp = ['CAM','CAMOAE','CHP','DUR','HID','MEX','SLP','TAB','ZAC'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('centros', function (Blueprint $table) {
            $table->date('desde')->nullable();
        });

        Centro::whereIn('abreviatura', $this->imp)->update(['desde' => '2020-11-18']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('centros', function (Blueprint $table) {
            $table->dropColumn('desde');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToRoleAndPermition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}

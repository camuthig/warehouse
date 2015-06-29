<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTimestampsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->date('updated_at')->nullable()->change();
        });

        Schema::table('stock', function (Blueprint $table) {
            $table->date('updated_at')->nullable()->change();
        });

        Schema::table('warehouse', function (Blueprint $table) {
            $table->date('updated_at')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->date('updated_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            //
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            // Non-generated migration information
            // Need to store:
            //      Name
            //      Address
            //      Latitude
            //      Longitude
            $table->string('name')->unique();
            // Will use the Google formatted address here in a string
            $table->string('address');
            $table->float('latitude', 15, 8);
            $table->float('longitude', 15, 8);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('warehouse');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            // A column to store the state: ORDERED, SHIPPED
            // For now I have two steps, but this could easily have
            // more than a binary state, so no tiny int.
            $table->string('status');

            $table->integer('warehouse_id')->unsigned()->nullable();
            $table->foreign('warehouse_id')
                ->references('id')->on('warehouse')
                ->onDelete('cascade');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')
                ->references('id')->on('product')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
    }
}

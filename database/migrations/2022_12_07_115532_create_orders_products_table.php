<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedBigInteger('price_id')->nullable();
            $table->foreign('price_id')->references('id')->on('prices');
            $table->unsignedBigInteger('measurement_id')->nullable();
            $table->foreign('measurement_id')->references('id')->on('measurement_units');
            $table->string('measurement_type');
            $table->integer('quantity');
            $table->float('sub_total',11,2)->nullable();
            $table->float('price',11,2)->nullable();
            $table->integer('returned_quantity')->default(0);
            $table->float('refund_amount',11,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders_products');
    }
}

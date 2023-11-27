<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_id')->nullable()->constrained('prices')->cascadeOnDelete();
            $table->integer('diff_qty')->nullable();
            $table->integer('total_qty')->nullable();
            $table->integer('sold_quantity')->default(0);
            $table->double('main_measurement_price')->nullable();
            $table->double('sub_measurement_price')->nullable();
            $table->double('main_measurement_price_after_sale')->nullable();
            $table->double('sub_measurement_price_after_sale')->nullable();

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
        Schema::dropIfExists('product_logs');
    }
}

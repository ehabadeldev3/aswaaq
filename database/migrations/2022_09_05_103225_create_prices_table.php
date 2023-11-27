<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('quantity')->nullable();
            $table->bigInteger('main_measurement_price')->nullable();
            $table->bigInteger('sub_measurement_price')->default(0);
            $table->bigInteger('main_measurement_price_after_sale')->default(0);
            $table->bigInteger('sub_measurement_price_after_sale')->default(0);
            $table->tinyInteger('best_offer')->default(0);
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->cascadeOnDelete();
            $table->softDeletes();
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
        Schema::dropIfExists('prices');
    }
}

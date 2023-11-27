<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRunSheetProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('run_sheet_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('run_sheet_id')->nullable()->constrained('run_sheets');
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->foreignId('measurement_id')->nullable()->constrained('measurement_units');
            $table->float('unit_price');
            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('deficit')->default(0);
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
        Schema::dropIfExists('run_sheet_products');
    }
}

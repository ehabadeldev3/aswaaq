<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('barcode')->nullable();
            $table->text('image')->nullable();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->boolean('status')->default(true);
            $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->cascadeOnDelete();
            $table->foreignId('flavor_id')->nullable()->constrained('flavors')->cascadeOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('sizes')->cascadeOnDelete();
            $table->foreignId('main_measurement_unit_id')->constrained('measurement_units')->cascadeOnDelete();
            $table->foreignId('sub_measurement_unit_id')->constrained('measurement_units')->cascadeOnDelete();
            $table->integer('count_unit')->nullable();
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
        Schema::dropIfExists('products');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->bigInteger('selected_shop_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->text('firebase_token')->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('clients');
    }
}

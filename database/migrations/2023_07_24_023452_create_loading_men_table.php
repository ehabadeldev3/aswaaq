<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadingMenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loading_men', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('National_ID');
            $table->date('birth_date');
            $table->date('hiring_date');
            $table->double('salary',20,2)->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('loading_men');
    }
}

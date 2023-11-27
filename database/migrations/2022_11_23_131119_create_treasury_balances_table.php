<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreasuryBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury_balances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('treasury_id')->unsigned();
            $table->double('amount',20,2);
            $table->foreign('treasury_id')->references('id')->on('treasuries')->onDelete('cascade');
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
        Schema::dropIfExists('treasury_balances');
    }
}

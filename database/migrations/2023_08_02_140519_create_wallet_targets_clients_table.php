<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTargetsClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_targets_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_target_id')->constrained('wallet_targets');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('points')->default(0);
            $table->integer('achieved')->default(0);// 1 for achieved 0 for not
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
        Schema::dropIfExists('wallet_targets_clients');
    }
}

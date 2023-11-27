<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('close')->default(false);
            $table->string('phone')->nullable();
            $table->time('cut_off_time')->nullable();
            $table->string('wats_app')->nullable();
            $table->integer('order_amount')->default(0);
            $table->boolean('cash_on_delivery')->default(true);
            $table->boolean('online_payment')->default(true);
            $table->integer('best_prices_limits')->default(10);
            $table->integer('refund_allowed_days')->default(10);
            $table->text('facebook')->nullable();
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
        Schema::dropIfExists('settings');
    }
}

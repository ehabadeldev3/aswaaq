<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops');
            $table->foreignId('area_id')->constrained('areas');
            $table->foreignId('user_id')->constrained('users');
            $table->string('payment_method')->default('Cash on delivery');
            $table->string('payment_status')->default('Unpaid');
            $table->string('transaction_id')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('coupon')->nullable();
            $table->float('coupon_discount',11,2)->nullable();
            $table->float('tax_amount',11,2)->nullable();
            $table->integer('used_points')->default(0);
            $table->float('refund_amount',11,2)->default(0);
            $table->float('total_amount',11,2)->nullable();
            $table->float('subtotal',11,2)->nullable();
            $table->float('shipping_cost',11,2)->default(0);
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
        Schema::dropIfExists('orders_groups');
    }
}

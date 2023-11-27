<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->float('total_amount',11,2)->nullable();
            $table->float('subtotal',11,2)->nullable();
            $table->float('shipping_cost',11,2)->default(0);
            $table->float('commission',11,2)->nullable();
            $table->float('commission_percentage',11,2)->nullable();
            $table->string('coupon')->nullable();
            $table->float('coupon_discount',11,2)->nullable();
            $table->float('tax_amount',11,2)->default(0);
            $table->float('refund_amount',11,2)->default(0);
            $table->string('order_status')->default('Pending');
            $table->string('payment_method')->default('Cash on delivery');
            $table->string('payment_status')->default('Unpaid');
            $table->date('delivery_date')->nullable();
            $table->tinyInteger('hold')->default(0);
            $table->integer('used_points')->default(0);
            $table->foreignId('shop_id')->constrained('shops');
            $table->foreignId('area_id')->constrained('areas');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('car_id')->nullable()->constrained('cars');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('group_id')->constrained('orders_groups');
            $table->foreignId('representative_id')->nullable()->constrained('users');
            $table->tinyInteger('supplier_dues')->default(0);
            $table->tinyInteger('confirmed_received_amount')->default(0);
            $table->tinyInteger('confirmed_by')->default(0);
            $table->bigInteger('changed_by_representative')->nullable();
            $table->text('representative_refund_part_note')->nullable();
            $table->foreignId('wallet_target_id')->nullable()->constrained('wallet_targets');
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
        Schema::dropIfExists('orders');
    }
}

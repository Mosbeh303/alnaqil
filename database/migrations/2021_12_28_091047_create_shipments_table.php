<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('number')->unique();
            $table->integer('invoice_number')->nullable()->unique();
            $table->tinyInteger('status');
            $table->string('store_name')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('warehouse_location')->nullable();
            $table->text('details')->nullable();
            $table->tinyInteger('failed')->unsigned()->default(0);
            $table->boolean('exchange')->default(0);
            $table->boolean('refrigerated')->default(0);
            $table->boolean('return_back')->default(0);
            $table->timestamp('shipping_date')->nullable();
            $table->timestamp('pickup_date')->nullable();
            $table->decimal('weight')->default(0.5);
            $table->integer('number_of_pieces')->default(1);
            $table->string('source')->nullable();
            $table->string('salla_order_id')->nullable();
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
        Schema::dropIfExists('shipments');
    }
}

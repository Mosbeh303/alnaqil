<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJtexpressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jtexpresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('order_number')->nullable();
            $table->string('bill_code')->nullable();
            $table->string('sum_freight')->nullable();
            $table->string('txlogistic_id')->nullable();
            $table->string('sorting_code')->nullable();
            $table->string('last_center_name')->nullable();
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
        Schema::dropIfExists('jtexpresses');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('cod')->nullable()->default(0);
            $table->decimal('base_fee')->nullable();
            $table->decimal('refrigerated_transport_fee')->nullable();
            $table->decimal('extra_fees')->nullable();  // Return back Fee included
            $table->decimal('exchange_fee')->nullable();
            $table->decimal('cod_fee')->nullable();
            $table->decimal('tax')->nullable();
            $table->enum('payment_method', ['cash', 'epayment'])->nullable();
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
        Schema::dropIfExists('financials');
    }
}

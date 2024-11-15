<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('gov_number')->nullable();
            $table->string('freelance_document')->nullable();
            $table->string('maaroof')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('website')->nullable();
            $table->decimal('base_fee')->nullable();
            $table->decimal('refrigerated_transport_fee')->nullable()->default(0);
            $table->decimal('extra_fees')->nullable()->default(0);
            $table->decimal('exchange_fee')->nullable()->default(0);
            $table->decimal('cod_fee')->nullable()->default(0);
            $table->decimal('return_fee')->nullable()->default(0);
            $table->boolean('tax_invoice')->default(0);
            $table->boolean('refrigerated_transport_active')->default(0);
            $table->boolean('exchange_active')->default(0);
            $table->boolean('cod_active')->default(1);
            $table->boolean('return_active')->default(0);
            $table->boolean('invoice_active')->default(0);
            $table->string('city')->nullable();
            $table->string('neighborhood')->nullable();
            $table->mediumText('map_location')->nullable();
            $table->decimal('dues')->default(0);
            $table->enum('status', array('in review', 'approved', 'deleted', 'closed'))->default('in review');
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
        Schema::dropIfExists('stores');
    }
}

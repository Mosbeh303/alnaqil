<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFeesColumnsToFinancialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financials', function (Blueprint $table) {
            $table->decimal('base_weight')->nullable()->after('base_fee');
            $table->decimal('addtional_fee_per_kg')->nullable()->default(0)->after('base_weight');
            $table->decimal('addtional_fees_extra_weight')->nullable()->default(0)->after('addtional_fee_per_kg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financials', function (Blueprint $table) {
            //
        });
    }
}

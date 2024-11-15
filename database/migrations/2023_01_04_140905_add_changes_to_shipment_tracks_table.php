<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangesToShipmentTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_tracks', function (Blueprint $table) {
            $table->json('changes')->nullable()->after('notice');
            $table->json('original')->nullable()->after('changes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_tracks', function (Blueprint $table) {
            //
        });
    }
}

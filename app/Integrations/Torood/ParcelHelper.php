<?php

namespace App\Integrations\Torood;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use App\Integrations\Torood\Torood;
use App\Jobs\CreateParcel;
use App\Jobs\ParcelTracking;
use App\Jobs\UpdateParcelDetails;
use App\Jobs\UpdateParcelConsignee;
use App\Jobs\UpdateParcelShipper;
use App\Jobs\UpdateParcelFinancial;

class ParcelHelper extends Torood {



    protected static $entities = [
        '10' => 9,
        '20' => 9,
        '35' => 4,
        '65' => 10,
        '100' => 10,
        '-100' => 9,
        '-1' => 9

    ];

    protected static $statuses = [
        '10' => 1,
        '20' => 2,
        '35' => 7,
        '65' => 8,
        '100' => 9,
        '-100' => 10,
        '-1' => 13

    ];

    public static function create(Shipment $shipment){
        CreateParcel::dispatch($shipment);
    }

    public static function updateDetails(Shipment $shipment){
        UpdateParcelDetails::dispatch($shipment);
    }

    public static function updateShipper(Shipment $shipment){
        UpdateParcelShipper::dispatch($shipment);
    }

    public static function updateConsignee(Shipment $shipment){
        UpdateParcelConsignee::dispatch($shipment);
    }

    public static function updateFinancial(Shipment $shipment){
        UpdateParcelFinancial::dispatch($shipment);
    }

    public static function tracking(Shipment $shipment){
        ParcelTracking::dispatch($shipment);
    }
}

<?php

namespace App\Integrations\Torood;

class Torood {
    protected static $apiKey ;
    protected static $url ;

    public function __construct() {
         $this->apiKey = env('TOROOD_API_KEY');
         $this->url = env('TOROOD_SRV_URL');
    }
}

<?php

namespace App\Http\Controllers\API\Mobile;

use App\Http\Controllers\Controller;
use App\Models\City;

class CityController extends Controller
{

    public function index()
    {

        $cities = City::where('type', 'shipping_cities')->where('active', 1)->get()->map(fn ($city) => [
            'id' => $city->id,
            'name_ar' => $city->name,
            'name_en'     => $city->name_en,
        ]) ?? [
            [
            'id' => 1,
            'name_ar' => 'الرياض',
            'name_en'     => 'alriyadh',
            ]
        ];

        try {
            $response = [
                'status' => true,
                'message' => "Active cities",
                'data' => $cities,
            ];
            return response($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);
        }
    }
}

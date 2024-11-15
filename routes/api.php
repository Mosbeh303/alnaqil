<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SallaWebhookController;
use App\Http\Controllers\API\ShipmentController;
use App\Http\Controllers\API\Mobile\ShipmentController as MobileShipmentController;
use App\Http\Controllers\JtexpressController;
use App\Http\Controllers\API\Mobile\AuthController;
use App\Http\Controllers\API\Mobile\StoreController;
use App\Http\Controllers\API\Mobile\WalletController;
use App\Http\Controllers\API\Mobile\UserController;
use App\Http\Controllers\API\Mobile\CityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Salla webhook
Route::post('/webhook', SallaWebhookController::class)->name('webhook');



Route::post('/jt-webhook/tracking', [JtexpressController::class, 'webhook']);


// Route::get('/test', function (){
//     return 'Hello !';
// });

// General API
Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    //App::setLocale("en");
    Route::post('shipments/create', [ShipmentController::class, 'store'])->name('api.shipments.create');
    Route::put('shipments/{number}/cancel', [ShipmentController::class, 'cancel'])->name('api.shipments.cancel');
    Route::get('shipments/{number}/details', [ShipmentController::class, 'show'])->name('api.shipments.get');
    Route::get('shipments/{number}/tracking', [ShipmentController::class, 'tracking'])->name('api.shipments.tracking');
    Route::get('shipments', [ShipmentController::class, 'index'])->name('api.shipments.index');
});


// Oriented for the mobile app
Route::group(['prefix' => 'v1/mobile'], function () {



    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);


    Route::group(['middleware' => ['auth:sanctum']], function () {

        //shipments
        Route::get('shipments/tracking', [MobileShipmentController::class, 'tracking']);
        Route::post('shipments/check', [MobileShipmentController::class, 'check']);
        Route::post('shipments/create', [MobileShipmentController::class, 'store']);
        Route::get('shipments', [MobileShipmentController::class, 'index']);
        Route::post('/auth/token', [AuthController::class, 'save_token']);

        //stores
        Route::post('/addresses/create', [StoreController::class, 'storeAdresses']);
        Route::put('/addresses/{pickupAddress}/update', [StoreController::class, 'updateAdresses']);
        Route::get('wallet-balance', [StoreController::class, 'getWalletBalance']);
        Route::get('/dues', [StoreController::class, 'dues']);
        Route::get('/addresses/index', [StoreController::class, 'getAddresses']);
        Route::delete('/addresses/{pickupAddress}/delete', [StoreController::class, 'deleteAdresses']);

       //user profile
        Route::get('/profile', [UserController::class, 'edit']);
        Route::put('/profile', [UserController::class, 'update']);

        //wallet
        Route::get('/wallets', [WalletController::class, 'show']);
        Route::post('/wallets/store', [WalletController::class, 'recharge']);

        //cities
        Route::get('/cities', [CityController::class, 'index']);


    });
});

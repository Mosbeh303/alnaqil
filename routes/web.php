<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShipmentNoticeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ShipmentTrackController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\CustodyController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProceedingShipController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ComplaintNoticeController;
use App\Models\Partner;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\EmpReimbursementController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ZidController;
use App\Http\Controllers\GroupsDepartmentController;
use App\Http\Controllers\ReceiverController;
use App\Integrations\Salla\SallaHelper;
use App\Integrations\Torood\ComplaintHelper;
use PHPUnit\TextUI\XmlConfiguration\Group;

use App\Http\Controllers\VehicleGroupController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ZidWebhookController;
use App\Http\Controllers\WaybillPrintController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FolderWalletController;
use App\Http\Controllers\JtexpressController;
use App\Http\Controllers\JtexpressWebhookController;
use App\Http\Controllers\PickupAddressController;
use App\Http\Controllers\MarketerController;
use App\Http\Controllers\InvoiceController;

/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        // 'laravelVersion' => Application::VERSION,
        // 'phpVersion' => PHP_VERSION,
    ]);
});*/

    Route::get('receivers/{receiverId}/create-additional-data', function($receiverId) {


    $receiverPhone =  base64_decode($receiverId);

        return app(ReceiverController::class)->create( $receiverPhone);
    })->name('receivers.create-additional-data');

     Route::post('/{receiver}/addData', [ReceiverController::class, 'additionalData'])->name('receivers.addData');





Route::get('set_locale/{locale}', function ($locale) {
    session()->put('locale', $locale);
    return redirect()->back();
})->name('set_locale');

Route::get('/', function () {
    return redirect(app()->getLocale());
});

Route::group([
    // 'prefix' => '{locale}',
    // 'where' => ['locale' => 'en|ar'],
    'middleware' => 'setlocale'
], function () {

    Route::get('/', function () {
        return view('welcome', ['partners' => Partner::all()]);
    })->name('home');

    Route::get('/terms', function () {
        return view('front.terms');
    })->name('terms');

    Route::get('/contact', function () {
        return view('front.contact');
    })->name('contact');
});


// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'approved'])->name('dashboard');



Route::get('/under-review', function () {
    return Inertia::render('UnderReview');
})->middleware('RedirectIfUserApproved')->name('under-review');

Route::get('/tracking', [ShipmentTrackController::class, 'clientTracking'])->name('shipments.client_tracking');

Route::get('/invoices/{slug}/pdf', [ShipmentController::class, 'invoicePdf'])->name('shipments.invoice.pdf');

Route::post('dashboard/shipments/file', [ShipmentController::class, 'file'])->middleware(['auth', 'approved'])->name('shipments.file');

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'approved', 'setlocale']], function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->middleware('operator.allowed')
        ->name('dashboard');


    //receivers
    Route::get('/receivers', [ReceiverController::class, 'index'])->name('receivers')->middleware(['can:isAdminOrEmployee']);
    Route::get('receivers/{receiver}/show', [ReceiverController::class, 'show'])->name('receivers.show')->middleware(['can:isAdminOrEmployee']);
    Route::get('receivers/{receiver}/edit', [ReceiverController::class, 'edit'])->name('receivers.edit')->middleware(['can:isAdminOrEmployee']);
    Route::post('receivers/{receiver}/update-data', [ReceiverController::class, 'updateData'])->name('receivers.update-data')->middleware(['can:isAdminOrEmployee']);
    Route::post('receivers/{receiver}/update-coordinates', [ReceiverController::class, 'updateCoordinates'])->name('receivers.update-coordinates')->middleware(['can:isAdminOrEmployee']);

    Route::get('receiverData/{receiver_phone}', [ReceiverController::class, 'receiverData'])->name('receiverData');

    Route::post('receivers/{receiver}/update_door_photo', [ReceiverController::class, 'update_door_photo'])->name('receivers.update-door-photo')->middleware(['can:isAdminOrEmployee']);

    Route::post('/{receiver}/addData', [ReceiverController::class, 'additionalData'])->name('receivers.admin.addData');
    Route::get('receivers/{receiver}/shipments', [ReceiverController::class, 'shipments'])->name('receivers.shipments')->middleware(['can:isAdminOrEmployee']);
    Route::post('/{receiver}/delete', [ReceiverController::class, 'destroy'])->name('receivers.delete');


    // Shipments
    Route::group(['prefix' => 'shipments', 'middleware' => ['auth', 'approved']], function () {
        Route::group(['middleware' => ['can:isNotOperator', 'can:isNotClient']], function () {
            Route::get('/query', [ShipmentController::class, 'query'])->name('shipments.query');
            Route::post('/query', [ShipmentController::class, 'getQuery'])->name('shipments.getQuery');
            Route::get('/summary', [ShipmentController::class, 'summary'])->name('shipments.summary');
            Route::get('/get-summary', [ShipmentController::class, 'getSummary'])->name('shipments.getSummary');
            Route::post('/pdf/summary', [ShipmentController::class, 'summaryPdf'])->name('shipments.pdf.summary');
            Route::post('/excel/summary', [ShipmentController::class, 'summaryShipmentExel'])->name('shipments.exel.summary');
            Route::post('jnt/excel/summary', [ShipmentController::class, 'jntSummaryExcel'])->name('shipments.jnt.excel');

            Route::get('/statement', [ShipmentController::class, 'statement'])->name('shipments.statement');
            Route::get('/get-statement', [ShipmentController::class, 'getStatement'])->name('shipments.getStatement');
            Route::post('/pdf/statement', [ShipmentController::class, 'statementPdf'])->name('shipments.pdf.statement');
            Route::post('/exel/statement', [ShipmentController::class, 'statementExel'])->name('shipments.exel.statement');

            Route::get('/proceedings', [ShipmentController::class, 'proceedings'])->name('shipments.proceedings');
            Route::post('/pdf/query', [ShipmentController::class, 'queryPdf'])->name('shipments.pdf.query');
            Route::get('/{shipment}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit');
            Route::post('/{shipment}/edit', [ShipmentController::class, 'update'])->name('shipments.update');
            Route::post('/{shipment}/updatePhone', [ShipmentController::class, 'updatePhone'])->name('shipments.update_phone');
            Route::post('/{shipment}/updateStatus', [ShipmentController::class, 'updateStatus'])->name('shipments.update_status');
            Route::post('/warehouse', [ShipmentController::class, 'warehouseLocation'])->name('shipments.warehouse');
            Route::post('/status', [ShipmentController::class, 'status'])->name('shipments.status');
            Route::post('/operator', [ShipmentController::class, 'Operator'])->name('shipments.operator');
            Route::get('/failed', [ShipmentController::class, 'failedDeliveryShipments'])->name('shipments.failed');
            Route::get('/receive', [ShipmentController::class, 'receive'])->name('shipments.receive')->withoutMiddleware('can:isNotOperator');
            Route::get('/receive/pdf', [ShipmentController::class, 'receivePdf'])->name('shipments.receive.pdf')->withoutMiddleware('can:isNotOperator');
            Route::get('/tracking', [ShipmentTrackController::class, 'tracking'])->name('shipments.track');
            Route::post('/tracking', [ShipmentTrackController::class, 'getTracking'])->name('shipments.getTracking');
            Route::get('/tracking/{number}', [ShipmentTrackController::class, 'getShipmentTracking'])->name('shipments.getShipmentTracking');
            Route::get('/tracking/{number}/pdf', [ShipmentTrackController::class, 'getShipmentTrackingPdf'])->name('shipments.getShipmentTrackingPdf');
            Route::post('/tracking/pdf', [ShipmentTrackController::class, 'shipmentsTrackingPdf'])->name('shipments.getShipmentsTrackingPdf');
            Route::post('/proceedings', [ProceedingShipController::class, 'getProceedingShipments'])->name('shipments.proceedings.get');
            Route::post('/proceedings/{action}', [ProceedingShipController::class, 'action'])->name('shipments.proceedings.action');
            Route::get('/proceedings/{action}', [ProceedingShipController::class, 'getProceedingShipments'])->name('shipments.proceedings.get_action');
            Route::get('/{shipment}/prints', [WaybillPrintController::class, 'index'])->name('shipments.prints');
            Route::get('/{shipment}/jt-create-orders', [JtexpressController::class, 'create'])->name('shipments.jt.create');
        });

        Route::group(['middleware' => ['can:isNotOperator']], function () {
            Route::get('/', [ShipmentController::class, 'index'])->name('shipments');
            Route::post('/cancel', [ShipmentController::class, 'cancel'])->name('shipments.cancel');
            Route::get('/download-excel', [ShipmentController::class, 'downloadExcel'])->name('shipments.excel');
            Route::get('/create', [ShipmentController::class, 'create'])->name('shipments.create');
            Route::post('/create', [ShipmentController::class, 'store'])->name('shipments.store');
            Route::post('/import', [ShipmentController::class, 'import'])->name('shipments.import');
        });

        Route::group(['middleware' => ['can:isNotClient']], function () {
            Route::post('/{id}/notice/create', [ShipmentNoticeController::class, 'store'])->name('shipments.notice.store');
            Route::post('/{id}/failed_delivery', [ShipmentController::class, 'failedDelivery'])->name('shipments.failed_delivery');
            Route::get('/{billCode}/jtwaybill', [JtexpressController::class, 'print'])->name('shipments.jtwaybill');
        });

        Route::get('/{slug}/exchange-waybill', [ShipmentController::class, 'exchangedBillPdf'])->name('shipments.exchangebill');

        Route::get('/{shipment}/show', [ShipmentController::class, 'show'])->name('shipments.show');
    });


    // Operators
    Route::group(['prefix' => 'operators', 'as' => 'operator', 'middleware' => ['auth', 'approved', 'can:isNotClient']], function () {

        Route::group(['middleware' => ['can:isAdminOrEmployee']], function () {
            Route::get('/', [OperatorController::class, 'index']);

            Route::get('/create', [OperatorController::class, 'create'])->name('.create');
            Route::post('/create', [OperatorController::class, 'store'])->name('.store');
            Route::get('/{operator}/edit', [OperatorController::class, 'edit'])->name('.edit');
            Route::post('/{operator}/edit', [OperatorController::class, 'update'])->name('.update');

            Route::get('/{operator}/show', [OperatorController::class, 'show'])->name('.show');
            Route::post('/{operator}/reset', [OperatorController::class, 'reset'])->name('.reset');
            Route::get('/{operator}/shipments', [OperatorController::class, 'shipments'])->name('.shipments');



            Route::post('/{operator}/status', [OperatorController::class, 'updateStatus'])->name('.update_status');
        });

        Route::post('/{id}/shipments/pdf', [OperatorController::class, 'shipmentsPdf'])->name('.shipments_pdf');

        //operation
        Route::post('/received/{shipment?}', [OperationController::class, 'received'])->name('.shipment.receive');
        Route::get('/received', [OperationController::class, 'received'])->name('.get.shipment.receive');
        Route::post('/{shipment}/processing', [OperationController::class, 'processing'])->name('.shipment.processing');
        Route::post('/assign/{shipment?}', [OperationController::class, 'assign'])->name('.shipment.assign');
        Route::post('/{shipment}/completed', [OperationController::class, 'completed'])->name('.shipment.completed');
        Route::post('/{shipment}/returned', [OperationController::class, 'returned'])->name('.shipment.returned');

        Route::get('/receive-shipments', [OperationController::class, 'getReceiveShipments'])->middleware('operator.allowed')->name('.get.shipments.receive');
        Route::post('/receive-shipments', [OperationController::class, 'getReceiveShipments'])->middleware('operator.allowed')->name('.post.shipments.receive');
    });

    // Employees
    Route::group(['prefix' => 'employees', 'as' => 'employee', 'middleware' => ['auth', 'approved', 'can:isAdminOrEmployee']], function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/groupes/{depart?}/index', [GroupController::class, 'index'])->name('.groupes');
        Route::get('/groupes/create', [GroupController::class, 'create'])->name('.groupes.create');
        Route::post('/groupes/create', [GroupController::class, 'store'])->name('.groupes.store');
        Route::get('/groupes/{groupe}/edit', [GroupController::class, 'edit'])->name('.groupes.edit');
        Route::post('/groupes/{groupe}/edit', [GroupController::class, 'update'])->name('.groupes.update');

        Route::get('/departments', [GroupsDepartmentController::class, 'index'])->name('.departments');
        Route::get('/departments/create', [GroupsDepartmentController::class, 'create'])->name('.departments.create');
        Route::post('/departments/create', [GroupsDepartmentController::class, 'store'])->name('.departments.store');
        Route::get('/departments/{depart}/edit', [GroupsDepartmentController::class, 'edit'])->name('.departments.edit');
        Route::post('/departments/{depart}/edit', [GroupsDepartmentController::class, 'update'])->name('.departments.update');

        Route::get('/create', [EmployeeController::class, 'create'])->name('.create');
        Route::post('/create', [EmployeeController::class, 'store'])->name('.store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('.edit');
        Route::post('/{employee}/edit', [EmployeeController::class, 'update'])->name('.update');
        Route::post('/{employee}/edit-permissions', [EmployeeController::class, 'updatePermissions'])->name('.update_permissions');
        Route::post('/{employee}/update-password', [EmployeeController::class, 'updatePassword'])->name('.update_password');

    });

    //groupes
    Route::group(['prefix' => 'groupes', 'as' => 'groupes', 'middleware' => ['auth', 'approved', 'can:isAdminOrEmployee']], function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::get('/create', [GroupController::class, 'create'])->middleware('can:isAdmin')->name('.create');
        Route::post('/create', [GroupController::class, 'store'])->middleware('can:isAdmin')->name('.store');
        Route::get('/{groupe}/edit', [GroupController::class, 'edit'])->middleware('can:isAdmin')->name('.edit');
        Route::post('/{groupe}/edit', [GroupController::class, 'update'])->middleware('can:isAdmin')->name('.update');
    });

    // Clients
    Route::group(['prefix' => 'stores', 'as' => 'stores.', 'middleware' => ['auth', 'approved', 'can:isAdminOrEmployee']], function () {

        Route::get('/', [StoreController::class, 'index']);

        Route::get('/export', [StoreController::class, 'export'])->name('export');

        Route::get('/create', [StoreController::class, 'create'])->name('create')->withoutMiddleware('can:isAdminOrEmployee')->middleware(['can:isNotOperator','can:isNotClient']);
        Route::post('/create', [StoreController::class, 'store'])->name('store')->withoutMiddleware('can:isAdminOrEmployee')->middleware(['can:isNotOperator','can:isNotClient']);

        Route::get('/{store}/edit', [StoreController::class, 'edit'])->name('edit');
        Route::post('/{store}/edit', [StoreController::class, 'update'])->name('update')->withoutMiddleware('can:isAdminOrEmployee');
        Route::post('/{store}/edit-bank-account', [StoreController::class, 'updateBankAccount'])->name('update_bank_account');
        Route::get('/{store}/show', [StoreController::class, 'show'])->name('show');

        Route::get('/dues', [StoreController::class, 'dues'])->name('dues');
        Route::get('/dues/pdf', [StoreController::class, 'duesPdf'])->name('dues.pdf');
        Route::get('/dues/excel', [StoreController::class, 'duesExcel'])->name('dues.excel');

        Route::get('/wallets', [StoreController::class, 'wallets'])->name('wallets');
        Route::get('/wallets/pdf', [StoreController::class, 'walletsPdf'])->name('wallets.pdf');
        Route::get('/wallets/excel', [StoreController::class, 'walletsExcel'])->name('wallets.excel');

        Route::get('/{store}/shipments', [StoreController::class, 'shipments'])->middleware(['can:isNotClient'])->withoutMiddleware(['can:isAdminOrEmployee'])->name('shipments');

        Route::post('/{store}/update-status', [StoreController::class, 'updateStatus'])->name('update.status');

        Route::post('/{store}/update-feature/{feature}', [StoreController::class, 'updateFeature'])->name('update.feature');
        Route::post('/{store}/update-billing-period', [StoreController::class, 'updateBilling'])->name('update.billing');

        Route::get('/summary', [StoreController::class, 'summary'])->name('summary')->withoutMiddleware(['can:isAdminOrEmployee']);
        Route::post('/summary', [StoreController::class, 'getSummary'])->name('getSummary')->withoutMiddleware(['can:isAdminOrEmployee']);
        Route::post('/summary/pdf', [StoreController::class, 'summaryPdf'])->name('summary.pdf')->withoutMiddleware(['can:isAdminOrEmployee']);
        Route::post('/summary/exel', [StoreController::class, 'summaryExel'])->name('summary.exel')->withoutMiddleware(['can:isAdminOrEmployee']);

        Route::get('/invoice', [StoreController::class, 'invoice'])->name('invoice')->withoutMiddleware(['can:isAdminOrEmployee']);
        Route::post('/invoice', [StoreController::class, 'getInvoice'])->name('getInvoice')->withoutMiddleware(['can:isAdminOrEmployee']);
        Route::post('/invoice/pdf', [StoreController::class, 'invoicePdf'])->name('invoice.pdf')->withoutMiddleware(['can:isAdminOrEmployee']);
        // files
        Route::post('/{store}/files', [FileController::class, 'store'])->name('files.store');
        Route::post('/files/{file}', [FileController::class, 'destroy'])->name('files.remove');

        Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers')->withoutMiddleware(['can:isAdminOrEmployee']);
        Route::post('/vouchers', [VoucherController::class, 'getVouchers'])->name('get.vouchers')->withoutMiddleware(['can:isAdminOrEmployee']);
        Route::post('/vouchers/pdf', [VoucherController::class, 'vouchersPdf'])->name('vouchers.pdf')->withoutMiddleware(['can:isAdminOrEmployee']);

        Route::post('/{store}/operator/{address?}', [StoreController::class, 'setOperator'])->name('setoperator');

        Route::post('/{store}/addresses/create', [PickupAddressController::class, 'store'])->name('addresses.create')->withoutMiddleware('can:isAdminOrEmployee');
        Route::post('addresses/{pickupAddress}/delete', [PickupAddressController::class, 'destroy'])->name('addresses.delete')->withoutMiddleware('can:isAdminOrEmployee');
        Route::post('addresses/{pickupAddress}/update', [PickupAddressController::class, 'update'])->name('addresses.update')->withoutMiddleware('can:isAdminOrEmployee');

    });

    // user
    Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => ['auth', 'approved']], function () {

        Route::post('/{user}/update-password', [UserController::class, 'updatePassword'])->name('update_password');
        Route::post('/{employee}/update-username', [UserController::class, 'updateUsername'])->name('update_username');

        Route::post('/{user}/update-avatar', [UserController::class, 'updateAvatar'])->name('update_avatar');
        Route::get('/profile', [UserController::class, 'edit'])->name('edit');
        Route::post('/profile', [UserController::class, 'update'])->name('update');
        Route::get('/api', [UserController::class, 'api'])->name('api');
        Route::post('/api/token', [UserController::class, 'generateToken'])->name('api.token');
        Route::post('/webhook/url', [WebhookController::class, 'url'])->name('webhook.url');
        Route::post('/webhook/token', [WebhookController::class, 'generateToken'])->name('webhook.token');
        Route::post('/webhook/update-status', [WebhookController::class, 'updateStatus'])->name('webhook.update_status');
    });

    // Vouchers
    Route::group(['prefix' => 'vouchers', 'as' => 'vouchers.', 'middleware' => ['auth', 'approved', 'can:isAdminOrEmployee']], function () {

        // Vouchers
        Route::post('/store/{user?}', [VoucherController::class, 'store'])->name('store');
        Route::post('/{voucher}/delete', [VoucherController::class, 'destroy'])->name('delete');
        Route::post('/{voucher}/edit', [VoucherController::class, 'update'])->name('update');
        Route::get('/{voucher}/show', [VoucherController::class, 'show'])->name('show');
    });

    // settings
    Route::group(['prefix' => 'settings', 'as' => 'settings.', 'middleware' => ['auth', 'approved', 'can:isAdminOrEmployee']], function () {
        Route::get('/', [SettingController::class, 'index'])->name('general');
        Route::post('/general/{option}', [SettingController::class, 'store'])->name('store');
        Route::post('/store-in-array/{option}', [SettingController::class, 'storeArray'])->name('storeinarray');

        Route::post('districts', [DistrictController::class, 'store'])->name('districts.store');
        Route::post('districts/{district}/destroy', [DistrictController::class, 'destroy'])->name('districts.remove');


        Route::get('/cities', [SettingController::class, 'cities'])->name('cities');
        Route::post('cities/register', [CityController::class, 'storeRegisterCity'])->name('register_cities.store');
        Route::post('districts/{district}/cities', [CityController::class, 'store'])->name('districts.cities.store');
        Route::post('districts/cities/{city}/destroy', [CityController::class, 'destroy'])->name('cities.remove');
        Route::post('districts/cities/{city}/update', [CityController::class, 'update'])->name('cities.update');

        Route::get('/cities/{city}/show', [CityController::class, 'show'])->name('cities.show');
        Route::post('/cities/{city}/branches/create', [BranchController::class, 'store'])->name('cities.branches.store');
        Route::post('/cities/branches/{branch}/update', [BranchController::class, 'update'])->name('cities.branches.update');
        Route::post('/cities/branches/{branch}/delete', [BranchController::class, 'destroy'])->name('cities.branches.delete');

        Route::post('/cities/export/{district?}', [CityController::class, 'export'])->name('cities.export');



        Route::get('/messages', [SettingController::class, 'messages'])->name('messages');
        Route::get('/notices', [SettingController::class, 'notices'])->name('notices');
        Route::post('/notices/{option}/{value}', [SettingController::class, 'removeFromArray'])->name('remove_from_array');
    });

    Route::get('cities/{city}', [AreaController::class, 'index'])->name('cities.areas');
    Route::post('cities/{city}/store', [AreaController::class, 'store'])->name('cities.areas.store');
    Route::post('cities/{area}/delete', [AreaController::class, 'destroy'])->name('cities.areas.delete');
    Route::post('cities/{area}/update', [AreaController::class, 'update'])->name('cities.areas.update');

    Route::post('cities/{area}/neighborhoods/store', [NeighborhoodController::class, 'store'])->name('cities.areas.neighborhood.store');
    Route::post('cities/neighborhoods/{neighborhood}/delete', [NeighborhoodController::class, 'destroy'])->name('cities.areas.neighborhood.delete');

    Route::get('neighborhoods/{city}', [NeighborhoodController::class, 'getNeighborhoods'])->name('neighborhoods');
    Route::get('branches/{city}', [BranchController::class, 'getBranches'])->name('branches');

    // Partners
    Route::group(['prefix' => 'partners', 'as' => 'partners.', 'middleware' => ['auth', 'approved', 'can:isAdminOrEmployee']], function () {
        Route::get('/', [PartnerController::class, 'index'])->name('index');
        Route::get('/{partner}/edit', [PartnerController::class, 'edit'])->name('edit');
        Route::post('{partner}/edit', [PartnerController::class, 'update'])->name('update');
        Route::get('/create', [PartnerController::class, 'create'])->name('create');
        Route::post('/create', [PartnerController::class, 'store'])->name('store');
        Route::post('/{partner}/remove', [PartnerController::class, 'destroy'])->name('destroy');
    });

    Route::get('/financials', [FinancialController::class, 'index'])->middleware(['auth', 'can:isAdminOrEmployee'])->name('financials');

    // complaints
    Route::group(['prefix' => 'complaints', 'as' => 'complaints', 'middleware' => ['auth', 'can:isAdminOrEmployee']], function () {
        Route::get('/', [ComplaintController::class, 'index'])->name('.index');
        Route::get('/create', [ComplaintController::class, 'create'])->name('.create');
        Route::post('/create', [ComplaintController::class, 'store'])->name('.store');
        Route::get('/{complaint}/edit', [ComplaintController::class, 'edit'])->name('.edit');
        Route::post('/{complaint}/edit', [ComplaintController::class, 'update'])->name('.update');
        Route::post('/{complaint}/update-status', [ComplaintController::class, 'updateStatus'])->name('.update_status');

        Route::get('/{complaint}/show', [ComplaintController::class, 'show'])->name('.show');

        Route::post('/{complaint}/notices/create', [ComplaintNoticeController::class, 'store'])->name('.notices.store');
        Route::post('/notices/{notice}/edit', [ComplaintNoticeController::class, 'update'])->name('.notices.update');
    });

    // accounts
    Route::group(['prefix' => 'accounts', 'as' => 'accounts', 'middleware' => ['auth', 'can:isAdminOrEmployee']], function () {
        // Route::get('/', function () {
        //     return inertia('Accounts');
        // })->middleware('can:isAdminOrEmployee');

        Route::get('/', [FolderController::class, 'index']);

        Route::get('folders/create', [FolderController::class, 'create'])->name('.folders.create');
        Route::post('/create', [FolderController::class, 'store'])->name('.folders.store');
        Route::get('/{folder}/edit', [FolderController::class, 'edit'])->name('.folders.edit');
        Route::post('/{folder}/edit', [FolderController::class, 'update'])->name('.folders.update');

        Route::post('folders/{folder}/wallets/create', [FolderWalletController::class, 'store'])->name('.folders.wallets.store');
        Route::get('folders/wallets/{wallet}/show', [FolderWalletController::class, 'show'])->name('.folders.wallets.show');
        Route::get('folders/wallets/{wallet}/pdf', [FolderWalletController::class, 'pdf'])->name('.folders.wallets.pdf');
        Route::get('folders/wallets/{wallet}/excel', [FolderWalletController::class, 'export'])->name('.folders.wallets.export');
        Route::get('folders/wallets/{wallet}/edit', [FolderWalletController::class, 'edit'])->name('.folders.wallets.edit');
        Route::post('folders/wallets/{wallet}/update', [FolderWalletController::class, 'update'])->name('.folders.wallets.update');
        Route::get('folders/{folder}/show', [FolderController::class, 'show'])->name('.folders.show');

    });


    // wallets
    Route::group(['prefix' => 'wallets', 'as' => 'wallets', 'middleware' => ['auth', 'can:isAdminOrEmployee']], function () {
        Route::get('/', [WalletController::class, 'index']);

        Route::get('/create', [WalletController::class, 'create'])->name('.create');
        Route::post('/create', [WalletController::class, 'store'])->name('.store');

        Route::get('/{wallet}/edit', [WalletController::class, 'edit'])->name('.edit');
        Route::post('/{wallet}/edit', [WalletController::class, 'update'])->name('.update');

        Route::get('/{wallet}/show', [WalletController::class, 'show'])->name('.show');
        Route::post('/{wallet}/pdf', [WalletController::class, 'pdf'])->name('.pdf');

        Route::post('/{wallet}/export', [WalletController::class, 'export'])->name('.export');
    });

    Route::group(['prefix' => 'employees-reimbursements', 'as' => 'reimbursements', 'middleware' => ['auth', 'can:isAdminOrEmployee']], function () {
        Route::get('/', [EmpReimbursementController::class, 'index']);
        Route::get('/{groupe}', [EmpReimbursementController::class, 'groupe'])->name('.groupe');
        Route::get('/{employee}/details', [EmpReimbursementController::class, 'empReimbursements'])->name('.empReimbursements');
        Route::get('/{employee}/details/pdf', [EmpReimbursementController::class, 'pdf'])->name('.empReimbursements.pdf');
        Route::get('/{employee}/details/exel', [EmpReimbursementController::class, 'export'])->name('.empReimbursements.exel');

        Route::get('/{empReimbursement}/show', [EmpReimbursementController::class, 'show'])->name('.show');
        Route::post('/{employee}/create', [EmpReimbursementController::class, 'store'])->name('.store');
        Route::post('/{empReimbursement}/update', [EmpReimbursementController::class, 'update'])->name('.update');
        Route::post('/{empReimbursement}/delete', [EmpReimbursementController::class, 'destroy'])->name('.delete');
    });

    Route::group(['prefix'=>'vehicles','as'=>'vehicles', 'middleware' => ['auth', 'can:isAdminOrEmployee']], function(){
        Route::get('/groups', [VehicleGroupController::class, 'index'])->name('.groups');
        Route::get('/groups/create', [VehicleGroupController::class, 'create'])->name('.groups.create');
        Route::get('/groups/{group}/edit', [VehicleGroupController::class, 'edit'])->name('.groups.edit');
        Route::post('/groups/create', [VehicleGroupController::class, 'store'])->name('.groups.store');
        Route::put('/groups/{group}/edit', [VehicleGroupController::class, 'update'])->name('.groups.update');
        Route::get('/', [VehicleController::class, 'index']);
        Route::get('/create', [VehicleController::class, 'create'])->name('.create');
        Route::post('/create', [VehicleController::class, 'store'])->name('.store');
        Route::get('/{vehicle}/edit', [VehicleController::class, 'edit'])->name('.edit');
        Route::put('/{vehicle}/edit', [VehicleController::class, 'update'])->name('.update');
    });

     // custodies
    Route::group(['prefix'=>'custodies','as'=>'custodies.', 'middleware' => ['auth', 'approved', 'can:isAdminOrEmployee']], function(){

        // Vouchers
        Route::post('/store/{user}', [CustodyController::class, 'store'])->name('store');
        Route::post('/{custody}/delete', [CustodyController::class, 'destroy'])->name('delete');
        Route::post('/{custody}/edit', [CustodyController::class, 'update'])->name('update');
        // Route::get('/{voucher}/show', [VoucherController::class, 'show'])->name('show');

    });

        // Marketers
        Route::group(['prefix' => 'marketers', 'as' => 'marketers', 'middleware' => ['auth', 'can:isAdminOrEmployee']], function () {
            Route::get('/', [MarketerController::class, 'index']);

            Route::get('/create', [MarketerController::class, 'create'])->name('.create');
            Route::post('/create', [MarketerController::class, 'store'])->name('.store');
            Route::get('/{marketer}/edit', [MarketerController::class, 'edit'])->name('.edit');
            Route::post('/{marketer}/edit', [MarketerController::class, 'update'])->name('.update');
            Route::get('/{marketer}/show', [MarketerController::class, 'show'])->name('.show');

            Route::get('/clients', [MarketerController::class, 'clients'])->middleware('can:isNotOperator')->withoutMiddleware('can:isAdminOrEmployee')->name('.clients');
            Route::get('commissions/{marketer?}/', [MarketerController::class, 'commissions'])->middleware(['can:isNotOperator', 'can:isNotClient'])->withoutMiddleware('can:isAdminOrEmployee')->name('.commissions');
            Route::get('{marketer?}/commissions/export', [MarketerController::class, 'exportComissions'])->middleware(['can:isNotOperator', 'can:isNotClient'])->withoutMiddleware('can:isAdminOrEmployee')->name('.commissions.export');
        });

        // Invoices
        Route::group(['prefix' => 'invoices', 'as' => 'invoices', 'middleware' => ['auth', 'approved']], function () {
            Route::get('/', [InvoiceController::class, 'index']);
            Route::any('/invoices/pdf', [InvoiceController::class, 'invoicePdf'])->name('.pdf');
            Route::any('/exel/invoices', [InvoiceController::class, 'invoiceExel'])->name('.exel');

            Route::get('/create', [InvoiceController::class, 'create'])->name('.create');
            Route::get('/preview', [InvoiceController::class, 'preview'])->name('.preview');
            Route::get('/create/date-range', [InvoiceController::class, 'dateRange'])->name('.date_range');
            Route::post('/store', [InvoiceController::class, 'store'])->name('.store');
            Route::get('/{invoice}/show', [InvoiceController::class, 'show'])->name('.show');
            // Route::post('/create', [MarketerController::class, 'store'])->name('.store');
            // Route::get('/{marketer}/edit', [MarketerController::class, 'edit'])->name('.edit');
            // Route::post('/{marketer}/edit', [MarketerController::class, 'update'])->name('.update');
            // Route::get('/{marketer}/show', [MarketerController::class, 'show'])->name('.show');

            // Route::get('/clients', [MarketerController::class, 'clients'])->middleware('can:isNotOperator')->withoutMiddleware('can:isAdminOrEmployee')->name('.clients');
            // Route::get('commissions/{marketer?}/', [MarketerController::class, 'commissions'])->middleware(['can:isNotOperator', 'can:isNotClient'])->withoutMiddleware('can:isAdminOrEmployee')->name('.commissions');
            // Route::get('{marketer?}/commissions/export', [MarketerController::class, 'exportComissions'])->middleware(['can:isNotOperator', 'can:isNotClient'])->withoutMiddleware('can:isAdminOrEmployee')->name('.commissions.export');
        });



});



Route::get('zid-redirect', [ZidController::class, 'zidRedirect'])->name('zid-redirect');
Route::group(['middleware' => 'auth'], function () {
    Route::get('zid-call-back', [ZidController::class, 'zidCallBack'])->name('zid-call-back');
});
// Zid webhook
Route::post('/zid-webhook', ZidWebhookController::class)->name('zid-webhook');

// shipment policy
Route::get('/{slug}/policy', [ShipmentController::class, 'policy'])->name('shipments.policy');




require __DIR__ . '/auth.php';

Route::get('command', function () {

    \Artisan::call('optimize:clear');

    dd("Done!");
});


Route::get('migrate', function () {

    \Artisan::call('migrate --path="database/migrations/2023_10_03_185149_add_language_to_device_tokens_table.php"');
    dd("Done!");
});




//Route::webhooks('salla-webhook');

Route::get('/salla-callback', [App\Http\Controllers\SallaController::class, 'callback'])->name('salla');



Route::get('jt-webhook/tracking', [JtexpressController::class, 'webhook']);
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('apiAuth')->get('/', function () {
    return "Tallent";
});

Route::any('/Login', 'Api\AuthenticationController@login');
Route::any('/login',  'Api\AuthenticationController@login');

Route::any('/Logout', 'Api\AuthenticationController@logout');
Route::any('/logout', 'Api\AuthenticationController@logout');

Route::get('/app_welcome', 'Api\AuthenticationController@welcome');
Route::middleware('apiAuth')->get('/me', 'Api\ProfileController@info');
Route::middleware('apiAuth')->get('/profile', 'Api\ProfileController@info');
Route::middleware('apiAuth')->post('/update_fcm', 'Api\ProfileController@update_fcm');
Route::middleware('apiAuth')->patch('/me/update', 'Api\ProfileController@update');
Route::middleware('apiAuth')->patch('/me/res/update', 'Api\ProfileController@updateReseller');
Route::middleware('apiAuth')->get('/dashboard', 'Api\ProfileController@dashboard');
Route::middleware('apiAuth')->post('/validate_transaction_pin', 'Api\ProfileController@verify_transaction_pin');

Route::middleware('apiAuth')->get('/user', 'Api\UserController@list');
Route::middleware('apiAuth')->post('/user', 'Api\UserController@create');
Route::middleware('apiAuth')->get('/user/{user_id}', 'Api\UserController@info');
Route::middleware('apiAuth')->patch('/user/{user_id}', 'Api\UserController@update');
Route::middleware('apiAuth')->delete('/user/{user_id}', 'Api\UserController@remove');
Route::middleware('apiAuth')->get('/user/{user_id}/permissions', 'Api\UserController@permission');
Route::middleware('apiAuth')->post('/user/{user_id}/permissions', 'Api\UserController@update_permission');

Route::middleware('apiAuth')->post('/store', 'Api\StoreController@list');
Route::middleware('apiAuth')->post('/store_c', 'Api\StoreController@create');
Route::middleware('apiAuth')->get('/store/{store_id}', 'Api\StoreController@info');
Route::middleware('apiAuth')->post('/store/{store_id}', 'Api\StoreController@update');
Route::middleware('apiAuth')->put('/store/{store_id}', 'Api\StoreController@adjust');
Route::middleware('apiAuth')->patch('/store/{store_id}', 'Api\StoreController@receive_euro');
Route::middleware('apiAuth')->options('/store/{store_id}', 'Api\StoreController@return_amount');

Route::middleware('apiAuth')->delete('/store/{store_id}', 'Api\StoreController@remove');
Route::middleware('apiAuth')->get('/store/phone_number/{store_id}', 'Api\StoreController@list_saved_phone_number');
Route::middleware('apiAuth')->post('/store/phone_number/{store_id}', 'Api\StoreController@save_phone_number');
Route::middleware('apiAuth')->get('/stores/load_conf', 'Api\StoreController@load_configuration');
//Route::middleware('apiAuth')->post('/stores/load_store_currency', 'Api\StoreController@load_store_currency');
Route::middleware('apiAuth')->post('/stores/save_store_currency', 'Api\StoreController@save_store_currency');
Route::middleware('apiAuth')->post('/stores/save_store_conversion_rate', 'Api\StoreController@save_store_conversion_rate');

Route::middleware('apiAuth')->post('/vendor', 'Api\VendorController@list');
Route::middleware('apiAuth')->post('/vendor_c', 'Api\VendorController@create');
Route::middleware('apiAuth')->get('/vendor/{vendor_id}', 'Api\VendorController@info');
Route::middleware('apiAuth')->patch('/vendor/{vendor_id}', 'Api\VendorController@update');
Route::middleware('apiAuth')->put('/vendor/{vendor_id}', 'Api\VendorController@adjust');
Route::middleware('apiAuth')->delete('/vendor/{vendor_id}', 'Api\VendorController@remove');

Route::middleware('apiAuth')->post('/mfs', 'Api\MFSController@list');
Route::middleware('apiAuth')->post('/mfs_c', 'Api\MFSController@create');
Route::middleware('apiAuth')->get('/mfs/{mfs_id}', 'Api\MFSController@info');
Route::middleware('apiAuth')->patch('/mfs/{mfs_id}', 'Api\MFSController@update');

Route::middleware('apiAuth')->post('/mfs_package', 'Api\MFSController@package_list');
Route::middleware('apiAuth')->post('/mfs_package/create', 'Api\MFSController@create_package');
Route::middleware('apiAuth')->patch('/mfs_package/update/{mfs_package_id}', 'Api\MFSController@update_package');
Route::middleware('apiAuth')->get('/mfs_package/info/{mfs_package_id}', 'Api\MFSController@package_info');

Route::middleware('apiAuth')->post('/promotion', 'Api\PromotionController@list');
Route::middleware('apiAuth')->post('/promotion_c', 'Api\PromotionController@create');
Route::middleware('apiAuth')->get('/promotion/{promotion_id}', 'Api\PromotionController@info');
Route::middleware('apiAuth')->patch('/promotion/{promotion_id}', 'Api\PromotionController@update');

Route::middleware('apiAuth')->post('/report/adjustment_history/{type}', 'Api\ReportController@adjustment_history');
Route::middleware('apiAuth')->post('/report/reseller_due_statement', 'Api\ReportController@reseller_due_statement');
Route::middleware('apiAuth')->post('/report/recharge_by_mfs', 'Api\ReportController@recharge_by_mfs');
Route::middleware('apiAuth')->post('/report/reseller_return_payment', 'Api\ReportController@reseller_return_payment');
Route::middleware('apiAuth')->post('/report/payment_doc_upload_statement', 'Api\ReportController@payment_doc_upload_statement');


Route::middleware('apiAuth')->post('/recharge/activity', 'Api\RechargeController@recent_activity');
Route::middleware('apiAuth')->post('/recharge/create', 'Api\RechargeController@create');
Route::middleware('apiAuth')->post('/recharge/lock/{recharge_id}', 'Api\RechargeController@lock');
Route::middleware('apiAuth')->post('/recharge/unlock/{recharge_id}', 'Api\RechargeController@unlock');
Route::middleware('apiAuth')->post('/recharge/reinit/{recharge_id}', 'Api\RechargeController@reinit');
Route::middleware('apiAuth')->post('/recharge/approve_reject/{recharge_id}', 'Api\RechargeController@approveReject');
Route::middleware('apiAuth')->post('/recharge/update_note/{recharge_id}', 'Api\RechargeController@updateNote');
Route::middleware('apiAuth')->get('/recharge/html_receipt/{recharge_id}', 'Api\RechargeController@getHtml_receipt');
Route::middleware('apiAuth')->post('/recharge/save_number', 'Api\RechargeController@save_number');
Route::middleware('apiAuth')->post('/recharge/search_number', 'Api\RechargeController@search_number');

Route::prefix('inventory/product')->middleware('apiAuth')->group(function () {
    Route::post('list', 'Api\inventory\ProductController@list');
    Route::post('create', 'Api\inventory\ProductController@create');
    Route::put('update/{product_id}', 'Api\inventory\ProductController@update');
    Route::get('info/{product_id}', 'Api\inventory\ProductController@info');
});

Route::middleware('apiAuth')->post('simcard/all', 'Api\simcard\SimCardController@all');

Route::prefix('simcard')->middleware('apiAuth')->group(function () {
    Route::post('add', 'Api\simcard\SimCardController@add');
    Route::post('list', 'Api\simcard\SimCardController@list');
    Route::post('remove_file/{sim_card_id}', 'Api\simcard\SimCardController@remove_file');
    Route::post('remove_sim_card', 'Api\simcard\SimCardController@remove_sim_card');
    Route::post('upload_file/{sim_card_id}', 'Api\simcard\SimCardController@upload_file');
    Route::post('sale/{sim_card_id}', 'Api\simcard\SimCardController@sale');
    Route::post('update/{sim_card_id}', 'Api\simcard\SimCardController@update');
    Route::post('reject/{sim_card_id}', 'Api\simcard\SimCardController@reject');
    Route::get('change_lock_status/{sim_card_id}', 'Api\simcard\SimCardController@change_lock_status');
    Route::get('emergency_unlock/{sim_card_id}', 'Api\simcard\SimCardController@emergency_unlock');
    Route::post('activate/{sim_card_id}', 'Api\simcard\SimCardController@activate');

    Route::post('change_status', 'Api\simcard\SimCardController@change_status');
});

Route::prefix('simcard/order')->middleware('apiAuth')->group(function () {
    Route::post('list', 'Api\simcard\OrderController@list');
    Route::post('create', 'Api\simcard\OrderController@create');

    Route::get('remove/{order_id}', 'Api\simcard\OrderController@remove');
    Route::get('reject/{order_id}', 'Api\simcard\OrderController@reject');


    Route::post('update/{order_id}', 'Api\simcard\OrderController@update');
    Route::post('appoint_sim_card', 'Api\simcard\OrderController@appoint_sim_card');

    //Route::get('info/{product_id}', 'api\simcard\OrderController@info');
});

Route::prefix('simcard/mnp_operators')->middleware('apiAuth')->group(function () {
    Route::post('list', 'Api\simcard\MnpOperatorsController@list');
    Route::post('create', 'Api\simcard\MnpOperatorsController@create');
    Route::post('change_status/{mnp_operators_id}', 'Api\simcard\MnpOperatorsController@change_status');
    Route::post('update/{mnp_operators_id}', 'Api\simcard\MnpOperatorsController@update');
});

Route::prefix('simcard/promo')->middleware('apiAuth')->group(function () {
    Route::post('list', 'Api\simcard\PromoController@list');
    Route::post('create', 'Api\simcard\PromoController@create');
    Route::post('change_status/{promo_id}', 'Api\simcard\PromoController@change_status');
    Route::post('update/{promo_id}', 'Api\simcard\PromoController@update');

    Route::post('config_reseller_bonus/{store_id}', 'Api\simcard\PromoController@config_reseller_bonus');
});

Route::prefix('simcard/report')->middleware('apiAuth')->group(function () {
    Route::post('sales', 'Api\simcard\ReportController@sales');
    Route::post('recharge', 'Api\simcard\ReportController@recharge');
    Route::post('adjustment', 'Api\simcard\ReportController@adjustment');
});

Route::prefix('simcard/banner')->middleware('apiAuth')->group(function () {
    Route::post('list', 'Api\simcard\BannerController@list');
    Route::post('create', 'Api\simcard\BannerController@create');
    Route::get('info/{promo_id}', 'Api\simcard\BannerController@info');
    Route::post('update/{promo_id}', 'Api\simcard\BannerController@update');
});

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

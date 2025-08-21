<?php

use Illuminate\Support\Facades\Route;;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::any('/{any}', function (Request $request) {
    if (!$request->session()->has('logged_in')) {
        return redirect('Logout');
    }
    return view('vuejs', array(
        'radis_token'=>$request->session()->get("radis_token")
    ));
    return response()->view('vuejs', array(
        'radis_token'=>$request->session()->get("radis_token")
    ))->header('Cache-Control', 'no-cache, no-store, must-revalidate')
      ->header('Pragma', 'no-cache')
      ->header('Expires', '0');
})->where('any', '^((?!Login|Logout|login|logout).)*');*/

/*Route::any('/Login', 'Authentication@login');
Route::any('/login',  'Authentication@login');

Route::any('/Logout', 'Authentication@logout');
Route::any('/logout', 'Authentication@logout');*/

Route::any('/', function (Request $request) {
    return view('landing');
});

Route::any('/Login', 'Web\Authentication@login');
Route::any('/login',  'Web\Authentication@login');
Route::any('/Logout', 'Web\Authentication@logout');
Route::any('/logout',  'Web\Authentication@logout');

Route::middleware('apiAuthWeb')->as('dashboard')->get('/manage', 'Web\Dashboard@manage');
Route::middleware('apiAuthWeb')->as('refill')->get('/refill', 'Web\Dashboard@refill');
Route::middleware('apiAuthWeb')->as('user_list')->get('/users', 'Web\Users@list');
Route::middleware('apiAuthWeb')->as('user_permission')->get('/user/{user_id}/user_permission', 'Web\Users@permission_management');
Route::middleware('apiAuthWeb')->as('reseller_list')->get('/reseller', 'Web\Reseller@list');
Route::middleware('apiAuthWeb')->as('reseller_list_simcard')->get('/reseller_simcard', 'Web\Reseller@list_simcard');
Route::middleware('apiAuthWeb')->as('currency_conversion')->get('/currency_conversion', 'Web\Reseller@currency_conversion');
Route::middleware('apiAuthWeb')->as('add_new_reseller')->get('/reseller/create', 'Web\Reseller@create');
Route::middleware('apiAuthWeb')->as('update_existing_reseller')->get('/reseller/{reseller_id}/update', 'Web\Reseller@update');
Route::middleware('apiAuthWeb')->as('vendor_list')->get('/vendor', 'Web\Vendor@list');
Route::middleware('apiAuthWeb')->as('mfs_list')->get('/mfs', 'Web\Mfs@list');
Route::middleware('apiAuthWeb')->as('mfs_package_list')->get('/mfs_package', 'Web\Mfs@mfs_package');


Route::middleware('apiAuthWeb')->as('report_recharge_history')->get('/report/recharge', 'Web\Report@recharge_history');
Route::middleware('apiAuthWeb')->as('report_mfs_summery')->get('/report/mfs_summery', 'Web\Report@mfs_summery');
Route::middleware('apiAuthWeb')->as('reseller_balance_recharge')->get('/report/reseller_balance_recharge', 'Web\Report@reseller_balance_recharge');
Route::middleware('apiAuthWeb')->as('reseller_due_adjust')->get('/report/reseller_due_adjust', 'Web\Report@reseller_due_adjust');
Route::middleware('apiAuthWeb')->as('reseller_due_adjust_by_store_id')->get('/report/reseller_due_adjust/{store_id}', 'Web\Report@reseller_due_adjust');
Route::middleware('apiAuthWeb')->as('reseller_due_statement')->get('/report/reseller_due_statement', 'Web\Report@reseller_due_statement');
Route::middleware('apiAuthWeb')->as('reseller_return_payment')->get('/report/reseller_return_payment', 'Web\Report@reseller_return_payment');
Route::middleware('apiAuthWeb')->as('report_payment_receipt_upload')->get('/report/report_payment_receipt_upload', 'Web\Report@report_payment_receipt_upload');


Route::prefix('simcard')->middleware('apiAuthWeb')->group(function () {
    Route::as('simcard_order_list')->get('orders', 'Web\SimCard@orders');
    Route::as('simcard_approve_order')->get('orders/approve/{order_id}', 'Web\SimCard@approve_order');
    Route::as('simcard_all')->get('all', 'Web\SimCard@all');
    Route::as('simcard_add')->get('add', 'Web\SimCard@add');
    Route::as('simcard_sale')->get('sale/{sim_card_id}', 'Web\SimCard@sale');
    Route::as('simcard_update')->get('update/{sim_card_id}', 'Web\SimCard@update');
    Route::as('simcard_info')->get('info/{sim_card_id}', 'Web\SimCard@info');

    Route::as('simcard_list')->get('list/{status}', 'Web\SimCard@list');
    Route::as('simcard_list_by_order')->get('list/{status}/{order_id}', 'Web\SimCard@list');

    Route::as('simcard_mnp_operators')->get('mnp_operators', 'Web\SimCard@mnp_operators');
    Route::as('simcard_update_mnp_operator')->get('update_mnp_operator/{mnp_operator_id}', 'Web\SimCard@update_mnp_operator');
    Route::as('simcard_promo')->get('promo', 'Web\SimCard@promo');
    Route::as('simcard_update_promo')->get('update_promo/{promo_id}', 'Web\SimCard@update_promo');
    Route::as('simcard_configure_reseller_promo')->get('configure_reseller_promo/{reseller_id}', 'Web\SimCard@configure_reseller_promo');



    Route::as('simcard_report_sales')->get('report/sales', 'Web\SimCard@report_sales');
    Route::as('simcard_report_recharge')->get('report/recharge', 'Web\SimCard@report_recharge');
    Route::as('simcard_report_adjustment')->get('report/adjustment', 'Web\SimCard@report_adjustment');
    Route::as('simcard_report_adjustment_by_id')->get('report/adjustment/{reseller_id}', 'Web\SimCard@report_adjustment');


    Route::as('simcard_banners')->get('banners', 'Web\SimCard@banners');
    Route::as('simcard_banner_update')->get('banner/update/{banner_id}', 'Web\SimCard@update_banner');
    Route::as('simcard_banner_details')->get('banner_i/{banner_id}', 'Web\SimCard@banner_details');
});


Route::prefix('inventory')->middleware('apiAuthWeb')->group(function () {
    Route::as('inventory_product_list')->get('product_list', 'Web\Inventory@product_list');
});

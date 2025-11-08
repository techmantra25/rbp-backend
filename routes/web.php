<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Front\UserController;
use App\Http\Controllers\Front\ReportController;
use App\Http\Controllers\Front\TestController;
use App\Http\Controllers\Admin\StoreController;
Route::get('/cache-clear', function() {
	// \Artisan::call('route:cache');
	\Artisan::call('config:cache');
   //	\Artisan::call('cache:clear');
	\Artisan::call('view:clear');
	\Artisan::call('config:clear');
	\Artisan::call('view:cache');
	\Artisan::call('route:clear');
	dd('Cache cleared');
});

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/user/logout', [UserController::class, 'logout']);
Route::get('/store/video/update', [ReportController::class, 'StoreVideoUpdate']);
Route::get('/store/video/downlod-in-local', [ReportController::class, 'VideoDownloadInLocal']);
Route::get('/store/video/replace', [ReportController::class, 'StoreVideoReplace']);
Route::get('/store/video/update/status', [ReportController::class, 'StoreVideoUploadReport']);
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::middleware(['auth:web'])->name('front.')->group(function () {
	// notification
	Route::post('/read', [UserController::class, 'notificationRead'])->name('notification.read');
	 Route::prefix('user/')->name('user.')->group(function () {
		  Route::view('profile', 'front.profile.index')->name('profile');
		  Route::get('order', [UserController::class, 'order'])->name('order');
	});
	//sales person list
	Route::get('sales-person', [UserController::class, 'list'])->name('salesperson.list');
	//store wise sales
	Route::get('store/order', [ReportController::class, 'index'])->name('store.order.report');
	//store wise sales csv download 
	Route::get('order-csv-download', [ReportController::class, 'csvExport'])->name('order.csv.download');
	Route::get('/orders/{id}/pdf/export', [ReportController::class, 'pdfExport'])->name('orders.pdf');
	Route::get('/orders/{id}/csv/download', [ReportController::class, 'individualcsvExport'])->name('orders.report.csv');
	//product wise sales
	Route::get('product-wise-sales', [UserController::class, 'productorder'])->name('product.order');
	//product wise sales csv download 
	Route::get('product-order-csv-download', [UserController::class, 'productorderCsv'])->name('product.order.csv.download');
    //zone wise sales
	Route::get('zone-wise-sales', [UserController::class, 'areaorder'])->name('zone.order');
	//zone wise sales csv download
	Route::get('zone-order-csv-download', [UserController::class, 'areaorderCsv'])->name('zone.order.csv.download');
	//activity list
	Route::get('activity', [UserController::class, 'activityList'])->name('activity.index');
	//store list
	Route::get('store/list', [UserController::class, 'storeList'])->name('store.list');
	Route::get('store/list/approve', [UserController::class, 'storeApproveList'])->name('store.list.approve');
	Route::get('store/list/detail/{id}', [UserController::class, 'storeDetail'])->name('store.detail');
	Route::get('store/list/edit/{id}', [UserController::class, 'storeEdit'])->name('store.edit');
	Route::post('store/list/update/{id}', [UserController::class, 'storeUpdate'])->name('store.update');
	Route::get('store/list/approve/status/update/{id}', [UserController::class, 'storeApproveStatus'])->name('store.list.approve.status.update');
	
	//team report
	Route::get('/ZSM/team/report', [ReportController::class, 'zsmreportIndex'])->name('team.order.report');
    Route::get('/ZSM/team/report/detail', [ReportController::class, 'zsmreportDetail'])->name('team.order.report.detail');
	});
Route::get('/store/uniquecode/update', [Admin\StoreController::class, 'StoreCodeUpdate']);
Route::get('/app/logout', [ReportController::class, 'logout'])->name('app.logout');
Route::get('/app/endvisit', [ReportController::class, 'endvisit'])->name('app.endvisit');
Route::get('/app/daily/endvisit', [UserController::class, 'endvisit'])->name('app.daily.endvisit');
Route::get('/privacy-policy', [UserController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/one8/privacy-policy', [UserController::class, 'one8privacyPolicy'])->name('one8-privacy.policy');
Route::get('/artimas/privacy-policy', [UserController::class, 'artimasprivacyPolicy'])->name('artimas-privacy.policy');
Route::get('/lux/test/data', [TestController::class, 'test'])->name('test.data');
Route::get('/stores/all/status', [StoreController::class, 'allStatus'])->name('all.stores.status');
Route::get('/stores/all/distributor', [StoreController::class, 'allStorePincodechange'])->name('all.stores.pincode.change');
Route::get('/stores/all/pincode/upload', [StoreController::class, 'allStorePincodeupload'])->name('all.stores.pincode.upload');
Route::get('/stores/all/pincode/upload/save', [StoreController::class, 'processCsvFiles'])->name('all.stores.pincode.upload.save');
Route::get('/activity/storeId/save', [Admin\StoreController::class, 'storeIdSave'])->name('all.stores.id.save');
Route::get('/activity/storeId/save/orderoncall', [Admin\StoreController::class, 'storeIdSaveForOrderOnCall'])->name('all.stores.id.save.orderoncall');
Route::get('/activity/storeId/save/noorderreason', [Admin\StoreController::class, 'storeIdSaveForNoOrderReason'])->name('all.stores.id.save.noorderreason');
Route::get('/activity/storeId/save/storeadd', [Admin\StoreController::class, 'storeIdSaveStoreADD'])->name('all.stores.id.save.storeadd');

Route::get('/page', [UserController::class, 'page']);
require 'admin.php';




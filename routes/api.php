<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\VisitController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\NoOrderReasonController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CatalogueController;
use App\Http\Controllers\Api\SchemeController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ASMController;
use App\Http\Controllers\Api\SMController;
use App\Http\Controllers\Api\RSMController;
use App\Http\Controllers\Api\ZSMController;
use App\Http\Controllers\Api\NSMController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\DistributorController;
use App\Http\Controllers\Retailer\RetailerOrderController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//ase list
Route::get('ase/list', [UserController::class, 'ase']);
//distributor list
Route::get('distributor/data', [UserController::class, 'distributor']);
// Login
Route::post('login', [LoginController::class, 'index']);
Route::post('distributor/login', [LoginController::class, 'distributorLogin']);
//check login flag
Route::get('check/login/flag/{id}', [LoginController::class, 'checkLogin']);
//login flag update
Route::post('login/flag/update', [LoginController::class, 'loginflagStore']);

Route::get('check/leave/flag/{id}', [LoginController::class, 'checkLeave']);
//catalogue
Route::get('catalogue', [CatalogueController::class, 'index']);
//scheme list
Route::get('banner', [SchemeController::class, 'index']);
//area list for RSM & SM & ZSM & NSM
Route::get('allarea/list/{id}', [RSMController::class, 'areaList']);
/** ASE **/
//start-visit
Route::post('visit/start', [VisitController::class, 'visitStart']);
//check visit started or not
Route::get('check/visit/{id}', [VisitController::class, 'checkVisit']);
//end-visit
Route::post('visit/end', [VisitController::class, 'visitEnd']);
//activity store
Route::post('activity/create', [VisitController::class, 'activityStore']);
//activity list
Route::get('activity', [VisitController::class, 'activityList']);
//area list
Route::get('area/list/{id}', [VisitController::class, 'areaList']);
//other activity
Route::post('other-activity/create', [VisitController::class, 'otheractivityStore']);
//distributor visit
//start-visit
Route::post('distributor/visit/start', [VisitController::class, 'distributorvisitStart']);
//check visit started or not
Route::get('check/distributor/visit/{id}/{distributor_id}', [VisitController::class, 'checkdistributorVisit']);
//end-visit
Route::post('distributor/visit/end', [VisitController::class, 'distributorvisitEnd']);
//store list
Route::get('store', [StoreController::class, 'index']);
//store search
Route::get('store/search', [StoreController::class, 'search']);
//all store search
Route::get('all/store/search', [StoreController::class, 'searchStoreAll']);
//store search for individual ASE's store
Route::get('user/store/search', [StoreController::class, 'searchuserStore']);
//store create
Route::post('store/create', [StoreController::class, 'store']);
//store details
Route::get('store/details', [StoreController::class, 'show']);
//inactive store list
Route::get('inactive/store', [StoreController::class, 'inactiveStorelist']);
//store image create
Route::post('store/image/create', [StoreController::class, 'imageCreate']);
//distributor list
Route::get('distributor/list', [StoreController::class, 'distributorList']);
//distributor list with store count
Route::get('distributor/list/store/count', [DashboardController::class, 'distributorListStoreCountASE']);
//store list distributor wise
Route::get('store-distributor-wise', [DashboardController::class, 'index']);
//area wise state list
Route::get('state/list', [StoreController::class, 'stateList']);
Route::get('state/list/all', [SchemeController::class, 'stateList']);
//no order reason
Route::get('no-order-reason', [NoOrderReasonController::class, 'index']);
Route::get('no-order-history/{id}/{user_id}', [NoOrderReasonController::class, 'show']);
Route::post('no-order-reason/update', [NoOrderReasonController::class, 'update']);

// PLACE ORDER 
//category list
Route::get('category', [CategoryController::class, 'index']);
//collection list category wise
Route::get('collection/{id}', [CollectionController::class, 'show']);
//product list collection wise
Route::get('product/{id}', [ProductController::class, 'show']);

//color list
Route::get('color/list/{id}', [ProductController::class, 'colors']);
//size list
Route::get('size/list', [ProductController::class, 'sizes']);

//cart
//cart list user wise
Route::get('cart/list/{id}/{user_id}', [CartController::class, 'show']);
//add to cart
Route::post('addTocart', [CartController::class, 'store']);
//cart remove
Route::get('cart/clear/{id}', [CartController::class, 'destroy']);
Route::get('cart/delete/{id}', [CartController::class, 'delete']);
//cart update
Route::get('cart/qty/{cartId}/{q}',[CartController::class, 'update']);
// cart preview url
Route::get('cart/pdf/url/{id}/{user_id}', [CartController::class, 'PDF_URL']);
//cart preview pdf
Route::get('cart/pdf/view/{id}/{user_id}', [CartController::class, 'PDF_view']);
/* order */
//order list user wise
Route::get('order/list/{id}/{user_id}', [OrderController::class, 'index']);
//place order
Route::post('place-order', [OrderController::class, 'store']);
//order details
Route::get('order/details/{id}', [OrderController::class, 'show']);
// order preview url
Route::get('order/pdf/url/{id}', [OrderController::class, 'PDF_URL']);
//order preview pdf
Route::get('order/pdf/view/{id}', [OrderController::class, 'PDF_view']);
//my order list
Route::post('my-orders', [OrderController::class, 'myOrdersFilter']);
//dashboard order count
Route::post('store/order/count', [OrderController::class, 'dashboardCount']);
//report
//store wise report for ASE
Route::post('store-wise-report-ase', [ReportController::class, 'storeReportASE']);
//product wise report for ASE
Route::post('product-wise-report-ase', [ReportController::class, 'productReportASE']);
//productivity for ase
Route::post('ase-productivity', [ReportController::class, 'aseProductivity']);
/** ASM **/
//inactive ASE report for ASM in dashboard
Route::get('inactive/ase/report/asm', [ASMController::class, 'inactiveAseListASM']);
//area list
Route::get('asm/area/list/{id}', [ASMController::class, 'areaList']);
//distributor list
Route::get('asm/distributor/list', [ASMController::class, 'distributorList']);
//distributor list with store count for asm
Route::get('distributor/list/store/count/asm', [DashboardController::class, 'distributorListStoreCountASM']);
//store list
Route::get('asm/store', [ASMController::class, 'storeList']);
//store search for individual ASE's store
Route::get('asm/store/search', [ASMController::class, 'searchStore']);
//store create
Route::post('asm/store/create', [ASMController::class, 'storeCreate']);
//store details
Route::get('asm/store/details', [ASMController::class, 'storesShow']);
//inactive store list
Route::get('asm/inactive/store', [ASMController::class, 'inactiveStorelist']);
//store image create
Route::post('asm/store/image/create', [ASMController::class, 'imageCreate']);

//no order reason
Route::get('asm/no-order-history/{id}/{user_id}', [ASMController::class, 'noOrderReasonDetail']);
Route::post('asm/no-order-reason/update', [ASMController::class, 'noOrderReasonUpdate']);

// PLACE ORDER 
//cart
//cart list user wise
Route::get('asm/cart/list/{id}/{user_id}', [ASMController::class, 'cartList']);
//add to cart
Route::post('asm/addTocart', [ASMController::class, 'addToCart']);
//cart remove
Route::get('asm/cart/clear/{id}', [ASMController::class, 'cartDestroy']);
//cart update
Route::get('asm/cart/qty/{cartId}/{q}',[ASMController::class, 'cartUpdate']);
// cart preview url
Route::get('asm/cart/pdf/url/{id}/{user_id}', [ASMController::class, 'CartPDF_URL']);
//cart preview pdf
Route::get('asm/cart/pdf/view/{id}/{user_id}', [ASMController::class, 'CartPDF_view']);
//order 
//order list user wise
Route::get('asm/order/list/{id}', [ASMController::class, 'orderList']);
//place order
Route::post('asm/place-order', [ASMController::class, 'placeOrder']);
//order details
Route::get('asm/order/details/{id}', [ASMController::class, 'orderDetails']);
// order preview url
Route::get('asm/order/pdf/url/{id}', [ASMController::class, 'orderPDF_URL']);
//order preview pdf
Route::get('asm/order/pdf/view/{id}', [ASMController::class, 'orderPDF_view']);

//my order list
Route::post('asm/my-orders', [ASMController::class, 'myOrders']);
//store wise team report
Route::post('asm/store-wise-report', [ASMController::class, 'storeReportASM']);

//ASM wise ASE list
Route::get('asm/ase/list/{id}', [ASMController::class, 'aseList']);
//activity log ase wise
Route::get('asm/activity', [ASMController::class, 'activityList']);
//notification list
Route::post('asm/notification-list', [ASMController::class, 'notificationList']);
//notification update
Route::post('asm/read-notification', [ASMController::class, 'readNotification']);

//product wise team report for ASM
Route::post('asm/product-report-detail', [ASMController::class, 'productReportASM']);
//productivity for ase
Route::post('asm-productivity', [ReportController::class, 'asmProductivity']);
//SM//
//inactive ASE report for ASM in dashboard
Route::get('inactive/ase/report/sm', [SMController::class, 'inactiveAseListSM']);
//store wise team report
Route::post('sm/store-wise-report', [SMController::class, 'storeReportSM']);
//product wise team report for ASM
Route::post('sm/product-wise-report', [SMController::class, 'productReportSM']);
//notification list
Route::post('sm/notification-list', [SMController::class, 'notificationList']);
//notification update
Route::post('sm/read-notification', [SMController::class, 'readNotification']);

//RSM//
//inactive ASE report for RSM in dashboard
Route::get('inactive/ase/report/rsm', [RSMController::class, 'inactiveAseListRSM']);
//store list
Route::get('store/list/rsm', [RSMController::class, 'storeList']);
//store wise team report for RSM
Route::post('rsm/store-wise-report', [RSMController::class, 'storeReportRSM']);
//product wise team report for RSM
Route::post('rsm/product-wise-report', [RSMController::class, 'productReportRSM']);
//notification list
Route::post('rsm/notification-list', [RSMController::class, 'notificationList']);
//notification update
Route::post('rsm/read-notification', [RSMController::class, 'readNotification']);
// ASE report for RSM 
Route::get('ase/list/rsm', [RSMController::class, 'aseListRSM']);
// ASM report for RSM 
Route::get('asm/list/rsm', [RSMController::class, 'asmListRSM']);
//distributor list with store count for rsm
Route::get('distributor/list/store/count/rsm', [DashboardController::class, 'distributorListStoreCountRSM']);
//area list
Route::get('rsm/area/list', [RSMController::class, 'rsmareaList']);
//distributor list
Route::get('rsm/distributor/list', [RSMController::class, 'distributorList']);
//ZSM//
//inactive ASE report for ZSM in dashboard
Route::get('inactive/ase/report/zsm', [ZSMController::class, 'inactiveAseListZSM']);
//store wise team report for RSM
Route::post('zsm/store-wise-report', [ZSMController::class, 'storeReportZSM']);
//product wise team report for RSM
Route::post('zsm/product-wise-report', [ZSMController::class, 'productReportZSM']);
//notification list
Route::post('zsm/notification-list', [ZSMController::class, 'notificationList']);
//notification update
Route::post('zsm/read-notification', [ZSMController::class, 'readNotification']); 
//distributor list with store count for zsm
Route::get('distributor/list/store/count/zsm', [DashboardController::class, 'distributorListStoreCountZSM']);
//area list
Route::get('zsm/area/list', [ZSMController::class, 'areaList']);
//distributor list
Route::get('zsm/distributor/list', [ZSMController::class, 'distributorList']);
//NSM//
//inactive ASE report for NSM in dashboard
Route::get('inactive/ase/report/nsm', [NSMController::class, 'inactiveAseListNSM']);
//store wise team report for NSM
Route::post('nsm/store-wise-report', [NSMController::class, 'storeReportNSM']);
//product wise team report for NSM
Route::post('nsm/product-wise-report', [NSMController::class, 'productReportNSM']);
//notification list
Route::post('nsm/notification-list', [NSMController::class, 'notificationList']);
//notification update
Route::post('nsm/read-notification', [NSMController::class, 'readNotification']);
// ASE report for RSM 
Route::get('ase/list/zsm', [ZSMController::class, 'aseListZSM']);
// ASM report for RSM 
Route::get('asm/list/zsm', [ZSMController::class, 'asmListZSM']);

Route::get('rsm/list/zsm', [ZSMController::class, 'rsmListZSM']);

//Distributor Management System
//cart list

Route::get('distributor/cart/list/{id}', [DistributorController::class, 'show']);

//addTocart
Route::post('distributor/addTocart', [DistributorController::class, 'store']);
//cart remove
Route::get('distributor/cart/clear/{id}', [DistributorController::class, 'destroy']);
Route::get('distributor/cart/delete/{id}', [DistributorController::class, 'delete']);
//cart update
Route::get('distributor/cart/qty/{cartId}/{q}',[DistributorController::class, 'update']);
// cart preview url
Route::get('distributor/cart/pdf/url/{user_id}', [DistributorController::class, 'PDF_URL']);
//cart preview pdf
Route::get('distributor/cart/pdf/view/{user_id}', [DistributorController::class, 'PDF_view']);




//place order

Route::get('distributor/order/list/{id}', [DistributorController::class, 'orderList']);

Route::get('distributor/order/detail/{id}', [DistributorController::class, 'orderDetail']);

Route::post('distributor/place-order', [DistributorController::class, 'placeOrder']);
Route::get('distributor/order/pdf/url/{user_id}', [DistributorController::class, 'orderPDF_URL']);
//cart preview pdf
Route::get('distributor/order/pdf/view/{user_id}', [DistributorController::class, 'orderPDF_view']);

//retailer list distributor wise
Route::get('distributor/retailer/list/{id}', [DistributorController::class, 'retailerList']);
//retailer wise order list
Route::post('store/order/distributorwise', [DistributorController::class, 'storeOrder']);
//order csv
Route::post('store/order/csv/distributorwise', [DistributorController::class, 'storeOrderCsv']);


//retailer wise product order list
Route::post('product/order/distributorwise', [DistributorController::class, 'productOrder']);
//order csv
Route::post('product/order/csv/distributorwise', [DistributorController::class, 'productOrderCsv']);
//distributor wise reward order
Route::post('store/reward/order/detail/distributor', [DistributorController::class,'rewardorderdistributorDetail']);
//order approval 
Route::post('store/reward/order/status/distributor', [DistributorController::class,'rewardorderdistributorStatus']);

///api for dms
Route::get('distributor/wallet/balance/{id}', [DistributorController::class, 'walletBalance']);

//category list
Route::get('category-fetch', [CategoryController::class, 'categoryFetch']);

//retailer list distributor wise
Route::get('distributor/retailer/fetch/{id}', [DistributorController::class, 'retailerFetch']);

Route::get('content/terms', [ContentController::class, 'termByState']);
Route::get('distributor/list/areawise',[StoreController::class,'distributorListArea']);

Route::post('branding/store/retailer', [DistributorController::class, 'branding']);


Route::get('coupon/issued/{id}', [DistributorController::class, 'couponData']);

Route::get('retailer/reward/order/{id}', [DistributorController::class, 'retailerOrder']);



Route::get('branding/video', [CategoryController::class, 'brandingVideo']);


//form submit
Route::post('distributor/form', [DistributorController::class, 'formSubmit']);

Route::get('distributor/form/check/{id}', [DistributorController::class, 'formSubmitCheck']);

Route::post('distributor/video/download', [DistributorController::class, 'videoDownload']);


Route::get('pdf/generate', [RetailerOrderController::class, 'pdfGenerateFunction']);

Route::get('distributor/product/list', [DistributorController::class, 'view']);


Route::get('distributor/product/{id}', [DistributorController::class, 'show']);

Route::get('distributor/wallet/balance/{id}', [DistributorController::class, 'walletBalance']);

Route::post('distributor/reward/place/order', [DistributorController::class, 'rewardplaceOrder']);

Route::get('distributor/reward/order/{userid}', [DistributorController::class, 'index']);

Route::get('distributor/reward/order/details/{id}', [DistributorController::class, 'order']);

Route::post('distributor/reward/transaction/history', [DistributorController::class, 'view']);

Route::post('distributor/reward/history', [DistributorController::class, 'reward']);


Route::get('distributor/reward/terms', [DistributorController::class, 'terms']);

Route::get('store/balance', [UserController::class, 'wallet']);

//warehouse

Route::get('sapcode/search', [UserController::class, 'codesearch']);

Route::post('sapcode/stock-in', [UserController::class, 'codestockIn']);

Route::get('stock-in/code/{id}', [UserController::class, 'Stockincode']);

Route::get('primary-order/search', [UserController::class, 'posearch']);

Route::post('qrcode-stock-out-warehouse', [UserController::class, 'codestockOut']);

Route::get('primary-order/search/distributor', [UserController::class, 'distributorposearch']);

Route::post('item/stock-in/distributor', [UserController::class, 'scanQrDistributor']);

Route::get('secondary-order/search', [UserController::class, 'ordersearch']);

Route::get('stock-in/code/distributor/{id}', [UserController::class, 'Stockincodedistributor']);

Route::post('qrcode-stock-out-distributor', [UserController::class, 'codestockOutDistributor']);

Route::post('earn/points', [UserController::class, 'index']);
Route::post('debit/points', [UserController::class, 'debit']);
Route::post('retailer/ledger', [UserController::class, 'ledger']);

Route::get('retailer/list/save/cron', [UserController::class, 'retailerSave']);

Route::get('state/list/save/cron', [UserController::class, 'stateSave']);

Route::get('beat/list/save/cron', [UserController::class, 'beatSave']);

Route::get('employee/list/save/cron', [UserController::class, 'employeeSave']);

Route::get('retailer/balance', [UserController::class, 'balance']);


<?php

/** REWARD APP */



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Retailer\UserController;
use App\Http\Controllers\Retailer\RetailerProductController;
use App\Http\Controllers\Retailer\BarcodeController;
use App\Http\Controllers\Retailer\RetailerOrderController;
use App\Http\Controllers\Retailer\CartController;
use App\Http\Controllers\Retailer\OrderController;



// Login
Route::post('login', [UserController::class, 'login']);
Route::post('login/demo', [UserController::class, 'demologin']);

Route::post('check/otp', [UserController::class, 'checkCode']);
//login with pin
Route::post('login-with-pin', [UserController::class, 'loginPin']);
// remove profile
Route::get('user/remove/profile/{id}', [UserController::class, 'removeProfile']);
Route::get('user/remove/profile/demo/{id}', [UserController::class, 'demoremoveProfile']);
// Registration
Route::post('user/register', [UserController::class, 'demoregister']);
Route::post('user/register/demo', [UserController::class, 'register']);
//password/pin generate
Route::post('user/pin/generate', [UserController::class, 'pinGenerate']);
// aadhar document add
Route::post('aadhar/upload', [UserController::class, 'retailerCreateAadhar']);
Route::post('aadhar/upload/demo', [UserController::class, 'demoretailerCreateAadhar']);
// pan document add
Route::post('pan/upload', [UserController::class, 'retailerCreatePan']);
Route::post('pan/upload/demo', [UserController::class, 'demoretailerCreatePan']);
// gst document add
Route::post('gst/upload', [UserController::class, 'retailerCreateGst']);
Route::post('gst/upload/demo', [UserController::class, 'demoretailerCreateGst']);

//contact no wise store list
Route::get('store/list', [UserController::class, 'storeList']);
//form submit
Route::post('user/form', [UserController::class, 'formSubmit']);
// image add
Route::post('image/upload', [UserController::class, 'retailerCreateImage']);
Route::post('image/upload/demo', [UserController::class, 'demoretailerCreateImage']);
//user profile details
Route::get('user/profile/{id}', [UserController::class, 'myprofile']);
Route::get('user/profile/demo/{id}', [UserController::class, 'demomyprofile']);
//edit profile
Route::post('update/profile/{id}', [UserController::class, 'updateProfile']);
Route::post('update/profile/demo/{id}', [UserController::class, 'demoupdateProfile']);
//change password
Route::post('change/password', [UserController::class, 'changePassword']);
Route::post('change/password/demo', [UserController::class, 'demochangePassword']);
//fetch top 5  product
Route::get('product/list', [RetailerProductController::class, 'view']);
//fetch all product
Route::get('product', [RetailerProductController::class, 'index']);
//fetch product by slug
Route::get('product/{id}', [RetailerProductController::class, 'show']);
//total wallet balance count
Route::get('wallet/balance/{id}', [UserController::class, 'walletBalance']);
Route::get('wallet/balance/demo/{id}', [UserController::class, 'demowalletBalance']);
//brochure
Route::get('brochure', [RetailerProductController::class, 'brochureindex']);

//barcode scan
Route::post('barcode', [UserController::class, 'index']);
Route::post('barcode/demo', [BarcodeController::class, 'demoindex']);
//5 order history user wise 
Route::get('order/{userid}', [RetailerOrderController::class, 'index']);
Route::get('order/demo/{userid}', [RetailerOrderController::class, 'demoindex']);

Route::get('order/details/{id}', [RetailerOrderController::class, 'order']);
Route::get('order/details/demo/{id}', [RetailerOrderController::class, 'demoorder']);
//reward history
Route::post('reward/history', [RetailerOrderController::class, 'reward']);
Route::post('reward/history/demo', [RetailerOrderController::class, 'demoreward']);
//transaction history
Route::post('transaction/history', [RetailerOrderController::class, 'view']);
Route::post('transaction/history/demo', [RetailerOrderController::class, 'demoview']);

Route::post('redemption/history', [RetailerOrderController::class, 'redemptionHistory']);
//reward cart list

Route::get('reward/cart/user/{id}', [CartController::class, 'list']);
Route::get('reward/cart/clear/{id}', [CartController::class, 'clear']);
Route::get('reward/cart/qty/{cartId}/{q}', [CartController::class, 'qtyUpdate']);
Route::post('reward/AddTocart', [CartController::class, 'rewardbulkAddTocart']);
//order-place
Route::post('place/order', [RetailerOrderController::class, 'placeOrder']);
Route::post('place/order/demo', [RetailerOrderController::class, 'demoplaceOrder']);
Route::post('reward/place/order', [RetailerOrderController::class, 'rewardplaceOrder']);

//invoice image store
Route::post('invoice/image', [BarcodeController::class, 'invoiceIndex']);

//invoice store
Route::post('invoice/store', [BarcodeController::class, 'invoiceStore']);


//b2b product order

//cart list

Route::get('cart/user/{id}', [CartController::class, 'showByUser']);
Route::get('cart/clear/{id}', [CartController::class, 'clearCart']);
Route::get('cart/qty/{cartId}/{q}', [CartController::class, 'qtyUpdateLatest']);
//cart -delete
Route::get('cart/delete/{id}', [CartController::class, 'delete']);
// cart preview
Route::get('cart/pdf/url/{id}', [CartController::class, 'cartPlacePDF_URL']);
Route::get('cart/pdf/view/{id}', [CartController::class, 'cartPreviewPDF_view'])->name('retailer.cart.place.pdf');
//multicolor bulk add
Route::post('bulkAddTocart', [CartController::class, 'bulkAddTocart']);
//order
Route::post('product-place-order', [OrderController::class, 'placeOrder']);

Route::get('order/list', [OrderController::class, 'list']);
Route::get('order/show/{id}', [OrderController::class, 'show']);
Route::get('store/order/details/{id}', [OrderController::class, 'orderDetails']);
Route::get('order/place/pdf/url/{id}', [OrderController::class, 'orderPlacePDF_URL']);
Route::get('order/place/pdf/view/{id}', [OrderController::class, 'orderPlacePDF_view'])->name('retailer.order.place.pdf');

//terms & condition
Route::get('terms', [UserController::class, 'terms']);
Route::get('duplicate/records', [BarcodeController::class, 'duplicateCheck']);


Route::post('branding/store', [UserController::class, 'branding']);
Route::get('branding/list/{id}', [UserController::class, 'brandingList']);


Route::post('video/download', [UserController::class, 'videoDownload']);

//monthly scan limit
Route::get('monthly/scan/limit/{id}', [UserController::class, 'monthlyScan']);
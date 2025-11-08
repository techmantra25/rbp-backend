<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CatalogueController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\HQController;
use App\Http\Controllers\Admin\BarcodeController;
use App\Http\Controllers\Admin\TermsController;
use App\Http\Controllers\Admin\RetailerOrderController;
use App\Http\Controllers\Admin\RetailerProductController;
use App\Http\Controllers\Admin\RetailerUserController;
// admin guard
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [AuthController::class, 'index'])->name('login');
        Route::post('/login/check',[AuthController::class, 'store'])->name('login.check');
        Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
        Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
        Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
       
    });
    // dashboard
        Route::get('/dashboard', [AuthController::class, 'show'])->name('dashboard');
	    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('/profile',[AuthController::class, 'update'])->name('profile.update');
	    Route::post('reset-password', [AuthController::class, 'changePassword'])->name('reset.password.post');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        // products
        Route::resource('products', ProductController::class);
        Route::get('/products/{id}/status', [ProductController::class, 'status'])->name('products.status');
        Route::get('/products/csv/export', [ProductController::class, 'csvExport'])->name('products.csv.export');
        Route::post('/products/size', [ProductController::class, 'size'])->name('products.size');
        Route::post('/csv/upload', [ProductController::class, 'variationCSVUpload'])->name('products.variation.csv.upload');
        Route::post('/bulk/edit', [ProductController::class, 'variationBulkEdit'])->name('products.variation.bulk.edit');
        Route::post('/bulk/update', [ProductController::class, 'variationBulkUpdate'])->name('products.variation.bulk.update');
        // variation
        Route::post('/variation/color/add', [ProductController::class, 'variationColorAdd'])->name('products.variation.color.add');
        Route::post('/variation/color/position', [ProductController::class, 'variationColorPosition'])->name('products.variation.color.position');
        Route::post('/variation/color/status/toggle', [ProductController::class, 'variationStatusToggle'])->name('products.variation.color.status.toggle');
        Route::post('/variation/color/edit', [ProductController::class, 'variationColorEdit'])->name('products.variation.color.edit');
        Route::post('/variation/color/rename', [ProductController::class, 'variationColorRename'])->name('products.variation.color.rename');
        Route::post('/variation/color/fabric/upload', [ProductController::class, 'variationFabricUpload'])->name('products.variation.color.fabric.upload');
        Route::get('/variation/{productId}/color/{colorId}/delete', [ProductController::class, 'variationColorDestroy'])->name('products.variation.color.delete');
        Route::post('/variation/size/add', [ProductController::class, 'variationSizeUpload'])->name('products.variation.size.add');   
        Route::post('/variation/size/edit', [ProductController::class, 'variationSizeEdit'])->name('products.variation.size.edit');
        Route::get('/variation/{id}/size/remove', [ProductController::class, 'variationSizeDestroy'])->name('products.variation.size.delete');
        Route::post('/variation/image/add', [ProductController::class, 'variationImageUpload'])->name('products.variation.image.add');
        Route::post('/variation/image/remove', [ProductController::class, 'variationImageDestroy'])->name('products.variation.image.delete');
        //categories
        Route::resource('categories', CategoryController::class);
        Route::get('/{id}/status', [CategoryController::class, 'status'])->name('categories.status');
        Route::get('/categories/csv/export', [CategoryController::class, 'csvExport'])->name('categories.csv.export');
        //collections
        Route::resource('collections', CollectionController::class);
        Route::get('/collections/{id}/status', [CollectionController::class, 'status'])->name('collections.status');
        Route::get('/collections/csv/export', [CollectionController::class, 'csvExport'])->name('collections.csv.export');
        //catalogues
        Route::resource('catalogues', CatalogueController::class);
        Route::get('/catalogues/{id}/status', [CatalogueController::class, 'status'])->name('catalogues.status');
        //offers
        Route::resource('schemes', OfferController::class);
        Route::get('/schemes/{id}/status', [OfferController::class, 'status'])->name('schemes.status');
         //users
        Route::resource('users', UserController::class);
	    //team create
        Route::post('/users/team/add', [UserController::class, 'userTeamAdd'])->name('users.team.add');
        //team edit
        Route::post('/users/team/update/{id}', [UserController::class, 'userTeamEdit'])->name('users.team.update');
        //team delete
        Route::get('/users/team/destroy/{id}', [UserController::class, 'userTeamDestroy'])->name('users.team.delete');
	
        Route::get('/users/{id}/status', [UserController::class, 'status'])->name('users.status');
        Route::get('/users/collection/{id}', [UserController::class, 'collection'])->name('users.collection');
        Route::post('/users/{id}/collection/create', [UserController::class, 'collectionCreate'])->name('users.collection.create');
		Route::get('/collection/delete/{id}', [UserController::class, 'collectionDelete'])->name('users.collection.delete');
        Route::get('/users/state/{state}', [UserController::class, 'state'])->name('users.state');
		Route::post('/users/area', [UserController::class, 'areaStore'])->name('users.area.store');
	    Route::get('/users/area/delete/{id}', [UserController::class, 'areaDelete'])->name('users.area.delete');
        Route::get('/users/csv/export', [UserController::class, 'csvExport'])->name('users.csv.export');
        Route::post('/users/password/generate', [UserController::class, 'passwordGenerate'])->name('users.password.generate');
		Route::post('/users/password/reset', [UserController::class, 'passwordReset'])->name('users.password.reset');
		//logout from other device
		Route::get('/user/{id}/logout', [UserController::class, 'logout'])->name('users.logout');
        //user activity
        Route::get('/activity/list', [UserController::class, 'activityList'])->name('users.activity.index');
        Route::get('/activity/csv/export', [UserController::class, 'activityCSV'])->name('users.activity.csv.export');
        Route::get('/daily/activity/list', [UserController::class, 'dailyactivityList'])->name('users.daily.activity.index');
        //user notification
        Route::get('/notification/list', [UserController::class, 'notificationList'])->name('users.notification.index');
        //user attendance
        Route::get('/attendance/daily', [UserController::class, 'attendanceList'])->name('users.attendance.index');
        Route::get('/attendance/daily/csv', [UserController::class, 'attendanceListCSV'])->name('users.attendance.csv.download');
        Route::get('/attendance/report', [UserController::class, 'attendanceReport'])->name('users.attendance.report');
        Route::get('/attendance/report/csv/export', [UserController::class, 'attendanceReportCSV'])->name('users.attendance.csv.export');
        Route::get('/attendance/report/csv/export/ajax', [UserController::class, 'attendanceReportCSVAjax'])->name('users.attendance.csv.export.ajax');
        //employee productivity
        Route::get('/employee/productivity', [UserController::class, 'employeeProductivity'])->name('employee.productivity');
        Route::get('/employee/productivity/report/csv', [UserController::class, 'employeeProductivityCSV'])->name('employee.productivity.csv.download');
        Route::get('/employee/productivecall', [UserController::class, 'employeeProductivitycall'])->name('employee.productive.call');
       
        Route::get('/monthly/employee/productivity', [UserController::class, 'employeeProductivityMonthly'])->name('employee.productivity.monthly');
        //stores
        Route::resource('stores', StoreController::class);
        Route::get('/stores/{id}/status', [StoreController::class, 'status'])->name('stores.status');
        Route::get('/stores/inactive', [StoreController::class, 'inactiveList'])->name('stores.inactive');
        Route::get('/stores/csv/export', [StoreController::class, 'csvExport'])->name('stores.csv.export');
        Route::get('state-wise-area/{state}', [StoreController::class, 'stateWiseArea']);
        Route::get('/stores/noorderreason/csv/export', [StoreController::class, 'noOrderreasonCSV'])->name('stores.noorderreason.csv.export');
        Route::get('/stores/noorderreason/list', [StoreController::class, 'noOrderreason'])->name('stores.noorderreason.index');
        //states
        Route::resource('states', StateController::class);
        Route::get('/states/{id}/status', [StateController::class, 'status'])->name('states.status');
        //areas
        Route::resource('areas', AreaController::class);
        Route::get('/areas/{id}/status', [AreaController::class, 'status'])->name('areas.status');
        Route::post('/areas/csv/upload', [AreaController::class, 'areaCSVUpload'])->name('areas.csv.upload');
	   //HeadQuater
        Route::resource('headquaters', HQController::class);
        Route::get('/headquaters/{id}/status', [HQController::class, 'status'])->name('headquaters.status');
        Route::post('/headquaters/csv/upload', [HQController::class, 'headquaterCSVUpload'])->name('headquaters.csv.upload');
        //colors
        Route::resource('colors', ColorController::class);
        Route::get('/colors/{id}/status', [ColorController::class, 'status'])->name('colors.status');
        //sizes
        Route::resource('sizes', SizeController::class);
        Route::get('/sizes/{id}/status', [SizeController::class, 'status'])->name('sizes.status');
        
         //store wise orders
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        //product wise order report
        Route::get('orders/product', [OrderController::class, 'productwiseOrder'])->name('orders.product.index');
        //store wise order csv export
        Route::get('/orders/csv/export', [OrderController::class, 'csvExport'])->name('orders.csv.export');
        Route::get('orders/dump', [OrderController::class, 'orderDumpindex'])->name('orders.dump');
        Route::get('/orders/show-files', [OrderController::class, 'showFiles'])->name('show.files');
        Route::get('/download-file', [OrderController::class, 'downloadFile'])->name('download.file');
         Route::get('/orders/dump/csv/export', [OrderController::class, 'orderDump'])->name('orders.dump.csv.export');
        Route::get('/orders/{id}/pdf/export', [OrderController::class, 'pdfExport'])->name('orders.pdf');
        Route::get('/orders/{id}/csv/download', [OrderController::class, 'individualcsvExport'])->name('orders.report.csv');
         //product wise order csv export
        Route::get('/orders/product/csv/export', [OrderController::class, 'productcsvExport'])->name('orders.product.csv.export');
        //category wise sales
        Route::get('orders/category', [OrderController::class, 'categorywiseOrder'])->name('orders.category.index');
        Route::get('/orders/category/csv/export', [OrderController::class, 'categorycsvExport'])->name('orders.category.csv.export');
        //area wise sales
        Route::get('orders/area', [OrderController::class, 'areawiseOrder'])->name('orders.area.index');
        Route::get('/orders/area/csv/export', [OrderController::class, 'areacsvExport'])->name('orders.area.csv.export');
        //zsm wise rsm
        Route::get('rsm/list/zsmwise/{id}', [UserController::class, 'zsmwiseRsm']);
        //zsm wise state
        Route::get('state/list/zsmwise/{id}', [UserController::class, 'zsmwiseState']);
        //state wise rsm
        Route::get('rsm/list/statewise/{id}', [UserController::class, 'statewiseRSM']);
        //rsm wise sm
        Route::get('sm/list/rsmwise/{id}', [UserController::class, 'rsmwiseSm']);
        //sm wise asm
        Route::get('asm/list/smwise/{id}', [UserController::class, 'smwiseAsm']);
        //sm wise asm and ase
        Route::get('asm-ase/list/smwise/{id}', [UserController::class, 'smwiseAsmAse']);
        //asm wise ase
        Route::get('ase/list/asmwise/{id}', [UserController::class, 'asmwiseAse']);
        //report
        //login report
        Route::get('login/report', [OrderController::class, 'loginReport'])->name('login.report.index');
        Route::get('login/report/csv/export', [OrderController::class, 'loginReportcsvExport'])->name('login.report.csv.export');
		Route::post('/user/csv/upload', [AreaController::class, 'userCSVUpload'])->name('users.csv.upload');
	    Route::post('/video/csv/upload', [AreaController::class, 'videoCSVUpload'])->name('video.csv.upload');
	    //hirerchy
	    
	    Route::get('/user/hiererchy', [UserController::class, 'hiererchy'])->name('users.hiererchy');
		Route::get('/user/hiererchy/csv/export', [UserController::class, 'hiererchyExport'])->name('users.hiererchy.export');
	    Route::post('/distributor/password/generate', [AreaController::class, 'passwordGenerate'])->name('distributor.password.generate');
	     Route::post('/store/pan/update', [AreaController::class, 'pannoUpdate'])->name('store.pan.update');
	
	//reward
	
	Route::prefix('reward')->name('reward.')->group(function () {
        Route::prefix('/user')->name('retailer.user.')->group(function () {
            Route::get('/', [RetailerUserController::class, 'index'])->name('index');
            Route::get('/create', [RetailerUserController::class, 'create'])->name('create');
            Route::post('/store', [RetailerUserController::class, 'store'])->name('store');
            Route::get('/{id}/view', [RetailerUserController::class, 'show'])->name('view');
            Route::get('/{id}/edit', [RetailerUserController::class, 'edit'])->name('edit');
            Route::post('/{id}/update', [RetailerUserController::class, 'update'])->name('update');
            Route::get('/{id}/status', [RetailerUserController::class, 'status'])->name('status');
            Route::get('/{id}/verification', [RetailerUserController::class, 'verification'])->name('verification');
            Route::get('/{id}/delete', [RetailerUserController::class, 'destroy'])->name('delete');
        	Route::get('/export/csv', [RetailerUserController::class, 'exportCSV'])->name('export.csv');
			Route::get('/login/count', [RetailerUserController::class, 'loginCount'])->name('login.count');
			Route::get('/login/count/export/csv', [RetailerUserController::class, 'loginCountexportCSV'])->name('login.count.export.csv');
			Route::get('/login/store/count/{state}', [RetailerUserController::class, 'loginStoreCount'])->name('login.store.count');
			Route::get('/login/store/count/export/csv/{state}', [RetailerUserController::class, 'loginStoreCountCsv'])->name('login.store.export.csv');
			Route::get('/retailer/program', [RetailerUserController::class, 'retailerProgram'])->name('program.index');
			Route::get('/retailer/program/export/csv', [RetailerUserController::class, 'retailerProgramexportCSV'])->name('program.index.export.csv');
        });
		    // product
          Route::prefix('/product')->name('retailer.product.')->group(function () {
            Route::get('/', [RetailerProductController::class, 'index'])->name('index');
            Route::get('/create', [RetailerProductController::class, 'create'])->name('create');
            Route::post('/store', [RetailerProductController::class, 'store'])->name('store');
            Route::get('/{id}/view', [RetailerProductController::class, 'show'])->name('view');
            Route::get('/{id}/edit', [RetailerProductController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [RetailerProductController::class, 'update'])->name('update');
            Route::get('/{id}/status', [RetailerProductController::class, 'status'])->name('status');
            Route::get('/{id}/delete', [RetailerProductController::class, 'destroy'])->name('delete');
            Route::get('/export/csv', [RetailerProductController::class, 'exportCSV'])->name('export.csv');
			Route::post('/specification/add', [RetailerProductController::class, 'specificationAdd'])->name('specification.add');
            Route::get('/specification/{id}/delete', [RetailerProductController::class, 'specificationDestroy'])->name('specification.delete');
            Route::post('/specification/{id}/edit', [RetailerProductController::class, 'specificationEdit'])->name('specification.edit');

        });

        // product
        Route::prefix('/qrcode')->name('retailer.barcode.')->group(function () {
            Route::get('/', [BarcodeController::class, 'index'])->name('index');
            Route::get('/create', [BarcodeController::class, 'create'])->name('create');
            Route::get('/csv/export', [BarcodeController::class, 'csvExport'])->name('csv.export');
            Route::get('{slug}/csv/export', [BarcodeController::class, 'csvExportSlug'])->name('detail.csv.export');
            Route::post('/store', [BarcodeController::class, 'store'])->name('store');
            Route::get('/{id}/view', [BarcodeController::class, 'show'])->name('view');
		    Route::get('/{id}/detail', [BarcodeController::class, 'view'])->name('show');
			Route::get('/{id}/used/qrcode', [BarcodeController::class, 'useqrcode'])->name('useqrcode');
			Route::get('/{id}/edit', [BarcodeController::class, 'edit'])->name('edit');
            Route::post('/{id}/update', [BarcodeController::class, 'update'])->name('update');
            Route::get('/{id}/status', [BarcodeController::class, 'status'])->name('status');
            Route::get('/{id}/delete', [BarcodeController::class, 'destroy'])->name('delete');
            Route::get('/bulkDelete', [BarcodeController::class, 'bulkDestroy'])->name('bulkDestroy');
			Route::get('qr/csv/export/page', [BarcodeController::class, 'qrcsvExport'])->name('qr.details.csv.export');
			
        });
  			// invoice
          Route::prefix('/qrcode/redeem')->name('qrcode.redeem.')->group(function () {
            Route::get('/', [BarcodeController::class, 'qrRedeem'])->name('index');
            Route::get('/list/csv/export', [BarcodeController::class, 'qrRedeemcsvExport'])->name('csv.export');
            
          

        });
		
		// invoice
          Route::prefix('/terms')->name('retailer.terms.')->group(function () {
            Route::get('/', [TermsController::class, 'index'])->name('index');
            Route::post('/update', [TermsController::class, 'update'])->name('update');
          

        });
        // product
        Route::prefix('/order')->name('retailer.order.')->group(function () {
            Route::get('/', [RetailerOrderController::class,'index'])->name('index');
            Route::get('/{id}/view', [RetailerOrderController::class,'show'])->name('view');
            Route::get('/export/csv', [RetailerOrderController::class,'exportCSV'])->name('export.csv');
			Route::get('/{id}/approval/{status}', [RetailerOrderController::class,'approval'])->name('approval');
			Route::get('/{id}/status/{status}', [RetailerOrderController::class,'status'])->name('status');
			Route::get('/{id}/product/status/{status}', [RetailerOrderController::class,'orderProductStatus'])->name('product.status');
        });
    });
});
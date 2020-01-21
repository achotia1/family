<?php

/*Route::get('/adminLte', function(){
	return view('adminLte');
});*/

Route::group(['prefix' => '','middleware' => 'AdminGeneral','namespace'=>'Admin'], function ()
{
	Route::get('/superadmin', 'CompanyController@frontindex')->name('admin.companies.frontindex');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

	Route::group(['prefix' => 'admin','middleware' => 'AdminGeneral','namespace'=>'Admin'], function ()
	{
		/*-----------------------------------
		|	Guest Routes
		-------------------------------------------------*/
			Route::group(['middleware' => 'AdminRedirectIfAuthenticated'],function()
			{

				$BASECONTROLLER = 'AuthController@';
				$PREFIX = 'admin.auth.';

				// Default Route
				Route::redirect('/','admin/login');

				// Login
				Route::get('/login/{companyId?}', $BASECONTROLLER.'login')->name($PREFIX.'login');
				Route::post('/login/{companyId?}', $BASECONTROLLER.'checkLogin')->name($PREFIX.'check.login');
				
				// Forgot password
				Route::get('/forgot-password/{companyId?}',  	$BASECONTROLLER.'forgotPassword')->name($PREFIX.'forgot.password');
				Route::post('/forgot-password/{companyId?}',  	$BASECONTROLLER.'forgotPasswordSubmit')->name($PREFIX.'forgot.password');

				// Reset password
				Route::get('/reset-password/{id}', 	$BASECONTROLLER.'resetPassword')->name($PREFIX.'reset.password');
				Route::post('/reset-password/{id}', $BASECONTROLLER.'resetPasswordSubmit')->name($PREFIX.'reset.password');
			});

		/*-----------------------------------
		|	Auth Routes
		-------------------------------------------------*/
			Route::group(['middleware' => ['AdminAuthenticate']],function()
			{
				$PREFIX = 'admin';	

				// Logout
				Route::get('/logout',  'AuthController@logout')->name($PREFIX.'.logout');

				// Dashboard
				Route::group(['middleware' => ['permission:store-dashboard']], function () use($PREFIX)
				{

					Route::get('/dashboard',  'DashboardController@index')->name($PREFIX.'.dashboard');
				});

				// Users
				Route::group(['middleware' => ['permission:store-manage-users']], function () use($PREFIX)
				{
					Route::get('/users/getRecords', 'UsersController@getRecords')->name('admin.users.getRecords');
					Route::resource('users', 'UsersController', ['as' => $PREFIX]);
					
					// Permissions
					Route::post('permissions/byRole', 'PermissionsController@byRole')->name('admin.permissions.byrole');
					Route::get('permissions/getRole', 'PermissionsController@getRole')->name('admin.permissions.getRole');
					Route::resource('permissions', 'PermissionsController', ['as' => $PREFIX]);
					
					//Roles
					Route::get('/roles/getRecords', 'RolesController@getRecords')->name('admin.roles.getRecords');
					Route::post('/roles/updateRole/{endID}', 'RolesController@updateRole')->name('admin.roles.updateRole');
					Route::resource('roles', 'RolesController', ['as' => $PREFIX]);

				});
				Route::post('/users/updatePassword', 'UsersController@updatePassword');

				// Raw Material
				Route::group(['middleware' => ['permission:store-manage-materials']], function () use($PREFIX)
				{
					Route::get('/materials/getRecords',  'StoreRawMaterialController@getRecords');
					Route::post('/materials/bulkDelete',  'StoreRawMaterialController@bulkDelete');
					Route::resource('materials', 'StoreRawMaterialController', ['as' => $PREFIX]);
				});

				// Material In
				Route::group(['middleware' => ['permission:store-manage-material-in']], function () use($PREFIX)
				{
					Route::get('/materials-in/getRecords',  'StoreInMaterialController@getRecords');
					Route::post('/materials-in/bulkDelete',  'StoreInMaterialController@bulkDelete');
					Route::get('/materials-in/correct-balance/{id}',  'StoreInMaterialController@correctBalance')->name($PREFIX.'.materials-in.correct-balance');
					Route::post('/materials-in/updateBalance',  'StoreInMaterialController@updateBalance')->name($PREFIX.'.materials-in.updateBalance');
					Route::resource('materials-in', 'StoreInMaterialController', ['as' => $PREFIX]);
				});

				// RM Store
				Route::group(['middleware' => ['permission:store-manage-batches']], function () use($PREFIX)
				{
					Route::get('/rms-store/getRecords',  'StoreBatchCardController@getRecords');
					Route::post('/rms-store/bulkDelete',  'StoreBatchCardController@bulkDelete');
					Route::get('/rms-store/show/{id}',  'StoreBatchCardController@show')->name($PREFIX.'.rms-store.show');
					Route::post('/rms-store/getAvailableStock',  'StoreBatchCardController@getAvailableStock');
					Route::resource('rms-store', 'StoreBatchCardController', ['as' => $PREFIX]);
				});

				// Production
				Route::group(['middleware' => ['permission:store-manage-material-plans']], function () use($PREFIX)
				{
					Route::post('/production/getBatchMaterials',  'StoreProductionController@getBatchMaterials');
					Route::post('/production/getMaterialLots',  'StoreProductionController@getMaterialLots');
					Route::post('/production/getExistingBatch',  'StoreProductionController@getExistingBatch');
					Route::post('/production/getWastageBatchesOrMaterials',  'StoreProductionController@getWastageBatchesOrMaterials');
					Route::get('/production/getRecords',  'StoreProductionController@getRecords');
					Route::get('/production/show/{id}',  'StoreProductionController@show')->name($PREFIX.'.production.show');
					Route::post('/production/bulkDelete',  'StoreProductionController@bulkDelete');
					Route::resource('production', 'StoreProductionController', ['as' => $PREFIX]);
				});

				// Return Material
				Route::group(['middleware' => ['permission:store-manage-returned-material']], function () use($PREFIX)
				{
					Route::post('/return/getMaterialLots',  'StoreReturnedMaterialController@getMaterialLots');
					Route::post('/return/checkExistingRecord',  'StoreReturnedMaterialController@checkExistingRecord');
					Route::post('/return/getPlanMaterials',  'StoreReturnedMaterialController@getPlanMaterials');
					Route::get('/return/getRecords',  'StoreReturnedMaterialController@getRecords');
					Route::post('/return/bulkDelete',  'StoreReturnedMaterialController@bulkDelete');
					Route::resource('return', 'StoreReturnedMaterialController', ['as' => $PREFIX]);
				});

				// Material Out
				Route::group(['middleware' => ['permission:store-manage-material-output']], function () use($PREFIX)
				{
					// RC EASTER MATERIAL OUT
					Route::post('/materials-out/rc-update/{encId}',  'StoreOutMaterialController@rcUpdate')->name($PREFIX.'.materials-out.rcUpdate');
					Route::post('/materials-out/rcStore',  'StoreOutMaterialController@rcStore')->name($PREFIX.'.materials-out.rcStore');
					
					Route::get('/materials-out/show/{id}',  'StoreOutMaterialController@show')->name($PREFIX.'.materials-out.show');
					Route::get('/materials-out/getRecords',  'StoreOutMaterialController@getRecords');
					Route::post('/materials-out/bulkDelete',  'StoreOutMaterialController@bulkDelete');
					Route::post('/materials-out/getExistingPlan',  'StoreOutMaterialController@getExistingPlan');
					Route::post('/materials-out/send-to-sale',  'StoreOutMaterialController@sendToSale');
					Route::resource('materials-out', 'StoreOutMaterialController', ['as' => $PREFIX]);

				});

				//Batchwise Summary
				Route::group(['prefix' => 'batch-summary','middleware' => ['permission:store-batch-wise-report']], function () use($PREFIX)
				{
					Route::get('materials-out/{encId}',  'StoreOutMaterialController@showBatchViewReport')->name('admin.report.showBatch');
					Route::get('getBatchRecords', 'ReportController@getBatchRecords');
					Route::get('/', 'ReportController@batchIndex')->name('admin.report.batch');
				});


				//Aged Material Report
				Route::group(['prefix' => 'aged-materials','middleware' => ['permission:store-aged-material-report']], function () use($PREFIX)
				{
					Route::get('getAgedMaterialRecords', 'ReportController@getAgedMaterialRecords');
					Route::get('/', 'ReportController@agedMaterialIndex')->name('admin.report.agedMaterials');
				});

				//Date Wise Material Report
				Route::group(['prefix' => 'raw-materials','middleware' => ['permission:store-raw-material-report']], function () use($PREFIX)
				{
					Route::get('getRawMaterialRecords', 'ReportController@getRawMaterialRecords');
					Route::get('/', 'ReportController@rawMaterialIndex')->name('admin.report.rawMaterials');
				});

				// YIELD AVG REPORT
				Route::group(['prefix' => 'avg-yield','middleware' => ['permission:store-avg-yield-report']], function () use($PREFIX)
				{
					Route::get('getAvgYieldRecords', 'ReportController@getAvgYieldRecords');
					Route::get('/', 'ReportController@avgYieldIndex')->name('admin.report.avgYield');
				});

				// WASTE MATERIAL SUMMARY
				Route::group(['prefix' => 'waste-material-summary','middleware' => ['permission:store-wastage-summary-report']], function () use($PREFIX)
				{
					Route::get('getWasteSummaryRecords', 'ReportController@getWasteSummaryRecords');
					Route::get('/', 'ReportController@wasteMaterialSummaryIndex')->name('admin.report.avgYield');
				});

				//Contribution Report
				Route::group(['prefix' => 'contribution-report','middleware' => ['permission:store-contribution-report']], function () use($PREFIX)
				{
					Route::get('getContributionRecords', 'ReportController@getContributionRecords');
					Route::get('/', 'ReportController@contributionIndex')->name('admin.report.contribution');
				});

				//Aged Product Report
				Route::group(['prefix' => 'aged-products','middleware' => ['permission:store-aged-product-report']], function () use($PREFIX)
				{
					Route::get('getAgedProductRecords', 'ReportController@getAgedProductRecords');
					Route::get('/', 'ReportController@agedProductIndex')->name('admin.report.agedProducts');
				});

				//Deviation Report
				Route::group(['prefix' => 'deviation-material','middleware' => ['permission:store-material-deviation-report']], function () use($PREFIX)
				{
					Route::get('lot-history/{encId}', 'ReportController@deviationLotHistoryIndex')->name('admin.report.deviationLotHistory');
					Route::get('lot-history/getdeviationLotHistoryRecords/{encId}', 'ReportController@getdeviationLotHistoryRecords');
					Route::get('getdeviationMaterialRecords', 'ReportController@getdeviationMaterialRecords');
					Route::get('/', 'ReportController@deviationMaterialIndex')->name('admin.report.deviationMaterial');
				});
				

				// Stock Deviation Report
				Route::group(['prefix' => 'stock-deviation','middleware' => ['permission:store-stock-deviation-report']], function () use($PREFIX)
				{
					Route::get('getdeviationStockRecords', 'ReportController@getdeviationStockRecords');
					Route::get('stock-history/{encId}', 'ReportController@deviationStockHistoryIndex')->name('admin.report.deviationStockHistory');
					Route::get('stock-history/getdeviationStockHistoryRecords/{encId}', 'ReportController@getdeviationStockHistoryRecords');
					Route::get('/', 'ReportController@deviationStockIndex')->name('admin.report.stockDeviation');
				});
				
				//Sales Management
				Route::group(['middleware' => ['permission:store-manage-sales']], function () use($PREFIX)
				{
					Route::get('/sales/getRecords',  'StoreSalesController@getRecords');
					Route::post('/sales/getProductBatches',  'StoreSalesController@getProductBatches');
					Route::post('/sales/getProductWastageBatches',  'StoreSalesController@getProductWastageBatches');
					Route::resource('sales', 'StoreSalesController', ['as' => $PREFIX]);
				});


				// Return Sale
				Route::group(['middleware' => ['permission:store-manage-returned-sales']], function () use($PREFIX)
				{
					//,'middleware' => ['permission:store-manage-returned-sale']]
					Route::post('/return-sale/checkExistingRecord',  'StoreReturnedSaleController@checkExistingRecord');
					Route::post('/return-sale/getProductBatches',  'StoreReturnedSaleController@getProductBatches');
					Route::post('/return-sale/getSaleProducts',  'StoreReturnedSaleController@getSaleProducts');
					Route::get('/return-sale/getRecords',  'StoreReturnedSaleController@getRecords');
					Route::resource('return-sale', 'StoreReturnedSaleController', ['as' => $PREFIX]);
				});

				// Return Sale
				Route::group(['middleware' => ['permission:store-manage-stock']], function () use($PREFIX)
				{
					//,'middleware' => ['permission:store-manage-returned-sale']]
					/*Route::post('/sale-stock/checkExistingRecord',  'StoreReturnedSaleController@checkExistingRecord');
					Route::post('/return-sale/getProductBatches',  'StoreReturnedSaleController@getProductBatches');
					Route::post('/return-sale/getSaleProducts',  'StoreReturnedSaleController@getSaleProducts');*/
					Route::get('/sale-stock/correct-balance/{id}',  'StoreSaleStockController@correctBalance')->name($PREFIX.'.sale-stock.correct-balance');
					Route::post('/sale-stock/updateBalance',  'StoreSaleStockController@updateBalance')->name($PREFIX.'.sale-stock.updateBalance');
					Route::get('/sale-stock/show/{id}',  'StoreSaleStockController@show')->name($PREFIX.'.sale-stock.show');
					Route::get('/sale-stock/getRecords',  'StoreSaleStockController@getRecords');
					Route::resource('sale-stock', 'StoreSaleStockController', ['as' => $PREFIX]);
				});

				//Wastage Material Management
				Route::group(['middleware' => ['permission:store-manage-wastage-material']], function () use($PREFIX)
				{
				Route::get('/wastage-material/getRecords',  'StoreWasteStockController@getRecords');
				Route::get('/wastage-material/correct-balance/{id}',  'StoreWasteStockController@correctBalance')->name($PREFIX.'.wastage-material.correct-balance');
				Route::post('/wastage-material/updateBalance',  'StoreWasteStockController@updateBalance')->name($PREFIX.'.wastage-material.updateBalance');
				Route::resource('wastage-material', 'StoreWasteStockController', ['as' => $PREFIX]);
				});

				## TEST
				/*Route::group(['middleware' => ['permission:store-manage-materials']], function () use($PREFIX)
				{*/
					Route::get('/test/getRecords',  'StoreTestController@getRecords');
					Route::resource('test', 'StoreTestController', ['as' => $PREFIX]);
				/*});*/
				## END TEST
				/*Route::get('/customers/update/{encodedCustomerId}',  'CustomersController@showCustomerProfile')->name('admin.customers.showCustomerProfile');
				Route::post('/customers/update/{encodedCustomerId}',  'CustomersController@updateCustomerProfile')->name('admin.customers.updateCustomerProfile');

				// Customers
				Route::group(['middleware' => ['permission:manage-customers']], function () use($PREFIX)
				{
					Route::get('/customers/assignproducts/{encodedCustomerId?}',  'CustomersController@assignProductsIndex')->name($PREFIX.'.customers.assignproductindex');
					Route::get('/customers/products/{encodedCustomerId}',  'CustomersController@customerProductIndex')->name($PREFIX.'.customers.customerproductindex');
					Route::get('/customers/orders/{encodedCustomerId}',  'CustomersController@customerOrderIndex')->name($PREFIX.'.customers.customerorderindex');

					Route::get('/customers/getCustomerOrders/{customer_id}',  'CustomersController@getCustomerOrders');
					Route::post('/customers/assignproducts',  'CustomersController@assignProduucts')->name($PREFIX.'.customers.assignproduct');
					Route::post('/customers/getCustomerProducts',  'CustomersController@getCustomerProducts');
					Route::post('/customers/bulkDelete',  'CustomersController@bulkDelete');
					
					Route::get('/customers/getRecords',  'CustomersController@getRecords');

					Route::resource('customers', 'CustomersController', ['as' => $PREFIX]);

				});*/

			});

	});


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// Default Route
Route::redirect('/','admin/login');
/*Route::get('/', function () {
    // return view('welcome');
});*/

/*
|--------------------------------------------------------------------------
| COMMAND ROUTES
|--------------------------------------------------------------------------
*/

	//Clear Route cache:
	Route::get('/clear-cache', function() 
	{
		$exitCode = Artisan::call('cache:clear');
		return '<h1>Cache facade value cleared</h1>';
	});

	//Clear Route cache:
	Route::get('/route-clear', function() 
	{
		$exitCode = Artisan::call('route:clear');
		return '<h1>Route cache cleared</h1>';
	});

	//Clear View cache:
	Route::get('/view-clear', function() 
	{
		$exitCode = Artisan::call('view:clear');
		return '<h1>View cache cleared</h1>';
	});

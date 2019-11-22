<?php

Route::get('/adminLte', function(){
	return view('adminLte');
});

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
				Route::get('/forgot-password',  	$BASECONTROLLER.'forgotPassword')->name($PREFIX.'forgot.password');
				Route::post('/forgot-password',  	$BASECONTROLLER.'forgotPasswordSubmit')->name($PREFIX.'forgot.password');

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

				
				// Vehicles
				/*Route::group(['middleware' => ['permission:manage-vehicles']], function () use($PREFIX)
				{
					Route::get('/vehicles/getRecords',  'VehiclesController@getRecords');
					Route::post('/vehicles/bulkDelete',  'VehiclesController@bulkDelete');
					Route::resource('vehicles', 'VehiclesController', ['as' => $PREFIX]);
				});*/

				// Offences
				/*Route::group(['middleware' => ['permission:manage-offences']], function () use($PREFIX)
				{
					Route::get('/offences/getRecords',  'OffencesController@getRecords');
					Route::resource('offences', 'OffencesController', ['as' => $PREFIX]);
				});*/

				// Raw Material
				/*Route::group(['middleware' => ['permission:manage-materials']], function () use($PREFIX)
				{*/
					Route::get('/materials/getRecords',  'StoreRawMaterialController@getRecords');
					Route::post('/materials/bulkDelete',  'StoreRawMaterialController@bulkDelete');
					Route::resource('materials', 'StoreRawMaterialController', ['as' => $PREFIX]);
				/*});*/

				// Material In
				/*Route::group(['middleware' => ['permission:manage-materials']], function () use($PREFIX)
				{*/
					Route::get('/materials-in/getRecords',  'StoreInMaterialController@getRecords');
					Route::post('/materials-in/bulkDelete',  'StoreInMaterialController@bulkDelete');
					Route::resource('materials-in', 'StoreInMaterialController', ['as' => $PREFIX]);
				/*});*/

				// RM Store
				/*Route::group(['middleware' => ['permission:manage-materials']], function () use($PREFIX)
				{*/
					Route::get('/rms-store/getRecords',  'StoreBatchCardController@getRecords');
					Route::post('/rms-store/bulkDelete',  'StoreBatchCardController@bulkDelete');
					Route::resource('rms-store', 'StoreBatchCardController', ['as' => $PREFIX]);
				/*});*/

				// Production
				/*Route::group(['middleware' => ['permission:manage-batches']], function () use($PREFIX)
				{*/
					Route::post('/production/getBatchMaterials',  'StoreProductionController@getBatchMaterials');
					Route::post('/production/getMaterialLots',  'StoreProductionController@getMaterialLots');
					Route::post('/production/getExistingBatch',  'StoreProductionController@getExistingBatch');
					Route::get('/production/getRecords',  'StoreProductionController@getRecords');
					Route::get('/production/show/{id}',  'StoreProductionController@show')->name($PREFIX.'.production.show');
					Route::post('/production/bulkDelete',  'StoreProductionController@bulkDelete');
					Route::resource('production', 'StoreProductionController', ['as' => $PREFIX]);
				/*});*/

				// Sales
				/*Route::group(['middleware' => ['permission:manage-batches']], function () use($PREFIX)
				{*/
					Route::post('/sales/getBatchMaterials',  'StoreIssuedMaterialController@getBatchMaterials');
					Route::get('/sales/getRecords',  'StoreIssuedMaterialController@getRecords');
					Route::post('/sales/bulkDelete',  'StoreIssuedMaterialController@bulkDelete');
					Route::resource('sales', 'StoreIssuedMaterialController', ['as' => $PREFIX]);
				/*});*/

				// Return
				/*Route::group(['middleware' => ['permission:manage-batches']], function () use($PREFIX)
				{*/
					Route::post('/return/getMaterialLots',  'StoreReturnedMaterialController@getMaterialLots');
					Route::post('/return/getBatchMaterials',  'StoreReturnedMaterialController@getBatchMaterials');
					Route::get('/return/getRecords',  'StoreReturnedMaterialController@getRecords');
					Route::post('/return/bulkDelete',  'StoreReturnedMaterialController@bulkDelete');
					Route::resource('return', 'StoreReturnedMaterialController', ['as' => $PREFIX]);
				/*});*/

				// Material In
				/*Route::group(['middleware' => ['permission:manage-materials']], function () use($PREFIX)
				{*/
					Route::get('/materials-out/getRecords',  'StoreOutMaterialController@getRecords');
					/*Route::post('/materials-out/bulkDelete',  'StoreOutMaterialController@bulkDelete');*/
					Route::resource('materials-out', 'StoreOutMaterialController', ['as' => $PREFIX]);
				/*});*/

				// Review Batch card
				/*Route::group(['middleware' => ['permission:manage-batches']], function () use($PREFIX)
				{*/
					/*Route::get('/review-batch-card',  'StoreReviewBatchCardController@index')->name($PREFIX.'.review-batch-card');
					Route::get('/review-batch-card/getRecords',  'StoreReviewBatchCardController@getRecords');
					Route::get('/review-batch-card/show/{id}',  'StoreReviewBatchCardController@show')->name($PREFIX.'.review-batch-card.show');
					Route::post('/review-batch-card/send-to-billing/{id}','StoreReviewBatchCardController@sendToBilling')->name($PREFIX.'.review-batch-card.send-to-billing');*/
					
				

				/*});*/

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
				
				// Orders
				/*Route::group(['middleware' => ['permission:manage-orders']], function () use($PREFIX)
				{
					Route::post('/orders/dispatch/{id}',  'OrdersController@dispatcherAccountantHistoryStore')->name($PREFIX.'.orders.dispatch.store');	
					Route::post('/orders/decline/{id}',  'OrdersController@decline')->name($PREFIX.'.orders.decline');	
					Route::post('/orders/dispatched/{id}',  'OrdersController@dispatched')->name($PREFIX.'.orders.dispatched');	
					Route::post('/orders/confirm/{id}',  'OrdersController@confirm')->name($PREFIX.'.orders.confirm');				
					
					Route::get('/orders/getRecords',  'OrdersController@getRecords');
					Route::get('/orders/dispatcher/history/{id}',  'OrdersController@viewDispatcherHistory')->name('admin.orders.viewDispatcherHistory');
					Route::get('/orders/getDispatcherHistory/{order_id}',  'OrdersController@getDispatcherHistory');
					Route::get('/orders/notes/history/{encoded_order_id}',  'OrdersController@viewNotesHistory')->name('admin.orders.viewNotesHistory');
					Route::get('/orders/getNotesHistory/{order_id}',  'OrdersController@getNotesHistory');

					Route::post('/orders/note',  'OrdersController@storeNote')->name('admin.orders.storeNote');;
					Route::resource('orders', 'OrdersController', ['as' => $PREFIX]);
				
				});	

				// Companies
				Route::group(['middleware' => ['permission:manage-companies']], function () use($PREFIX)
				{	

					Route::get('/company/getRecords',  'CompanyController@getRecords');
					Route::post('/company/bulkDelete',  'CompanyController@bulkDelete');
					Route::resource('company', 'CompanyController', ['as' => $PREFIX]);

				});	*/

				

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

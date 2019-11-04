 $arrAllPermissions = [];

$arrAllPermissions[] = array('name' => 'dashboard', 'module_slug'=>'dashboard', 'title' => 'Dashboard');
$arrAllPermissions[] = array('name' => 'total-products', 'module_slug'=>'dashboard', 'title' => 'Total Products');
$arrAllPermissions[] = array('name' => 'total-customers', 'module_slug'=>'dashboard', 'title' => 'Total Customer');
$arrAllPermissions[] = array('name' => 'total-orders', 'module_slug'=>'dashboard', 'title' => 'Total Orders');
$arrAllPermissions[] = array('name' => 'completed-orders', 'module_slug'=>'dashboard', 'title' => 'Completed Orders');
$arrAllPermissions[] = array('name' => 'pending-order', 'module_slug'=>'dashboard', 'title' => 'Pending Order');
$arrAllPermissions[] = array('name' => 'dispatched-order', 'module_slug'=>'dashboard', 'title' => 'Dispatched Order');

$arrAllPermissions[] = array('name' => 'manage-products', 'module_slug'=>'manage-products', 'title' => 'Manage Products');
$arrAllPermissions[] = array('name' => 'product-listing', 'module_slug'=>'manage-products', 'title' => 'View Products');
$arrAllPermissions[] = array('name' => 'product-add', 'module_slug'=>'manage-products', 'title' => 'Add Product');

$arrAllPermissions[] = array('name' => 'manage-customers', 'module_slug'=>'manage-customers', 'title' => 'Manage Customers');
$arrAllPermissions[] = array('name' => 'customer-add', 'module_slug'=>'manage-customers', 'title' => 'Add Customer');
$arrAllPermissions[] = array('name' => 'customer-listing', 'module_slug'=>'manage-customers', 'title' => 'View Customers');
$arrAllPermissions[] = array('name' => 'assign-products', 'module_slug'=>'manage-customers', 'title' => 'Assign Products');

$arrAllPermissions[] = array('name' => 'manage-orders', 'module_slug'=>'manage-orders', 'title' => 'Manage Orders');
$arrAllPermissions[] = array('name' => 'order-add', 'module_slug'=>'manage-orders', 'title' => 'Add Order');
$arrAllPermissions[] = array('name' => 'order-listing', 'module_slug'=>'manage-orders', 'title' => 'View Orders');
$arrAllPermissions[] = array('name' => 'order-note', 'module_slug'=>'manage-orders', 'title' => 'Order Note');
$arrAllPermissions[] = array('name' => 'order-history', 'module_slug'=>'manage-orders', 'title' => 'Order History');
$arrAllPermissions[] = array('name' => 'order-dispatcher-history', 'module_slug'=>'manage-orders', 'title' => 'Order Dispatcher History');
$arrAllPermissions[] = array('name' => 'order-delete', 'module_slug'=>'manage-orders', 'title' => 'Order Delete');


$arrAllPermissions[] = array('name' => 'manage-users', 'module_slug'=>'manage-users', 'title' => 'Manage Users');
$arrAllPermissions[] = array('name' => 'users-add', 'module_slug'=>'manage-users', 'title' => 'Add User');
$arrAllPermissions[] = array('name' => 'users-listing', 'module_slug'=>'manage-users', 'title' => 'View Users');
$arrAllPermissions[] = array('name' => 'manage-permissions', 'module_slug'=>'manage-users', 'title' => 'Manage Permissions');

$arrAllPermissions[] = array('name' => 'manage-companies', 'module_slug'=>'manage-companies', 'title' => 'Manage Companies');
$arrAllPermissions[] = array('name' => 'company-add', 'module_slug'=>'manage-companies', 'title' => 'Add Company');
$arrAllPermissions[] = array('name' => 'company-list', 'module_slug'=>'manage-companies', 'title' => 'View Companies');


$arrAllPermissions[] = array('name' => 'store-dashboard', 'module_slug'=>'store-dashboard', 'title' => 'Store Dashboard');
$arrAllPermissions[] = array('name' => 'store-total-users', 'module_slug'=>'store-dashboard', 'title' => 'Store Total Users');

$arrAllPermissions[] = array('name' => 'store-manage-materials', 'module_slug'=>'store-manage-materials', 'title' => 'Store Manage Materials');
$arrAllPermissions[] = array('name' => 'store-material-listing', 'module_slug'=>'store-manage-materials', 'title' => 'Store Material Listing');
$arrAllPermissions[] = array('name' => 'store-material-add', 'module_slug'=>'store-manage-materials', 'title' => 'Store Material Add');

$arrAllPermissions[] = array('name' => 'store-manage-users', 'module_slug'=>'store-manage-users', 'title' => 'Store Manage Users');
$arrAllPermissions[] = array('name' => 'store-users-listing', 'module_slug'=>'store-manage-users', 'title' => 'Store Users Listing');
$arrAllPermissions[] = array('name' => 'store-users-add', 'module_slug'=>'store-manage-users', 'title' => 'Store Users Add');
$arrAllPermissions[] = array('name' => 'store-manage-roles', 'module_slug'=>'store-manage-users', 'title' => 'Store Manage Roles');
$arrAllPermissions[] = array('name' => 'store-manage-permissions', 'module_slug'=>'store-manage-users', 'title' => 'Store Manage Permissions');

// use commend : php artisan permission:cache-reset or below function to reset cache
    dump(app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions());
    // $permission = Permission::create($arrAllPermissions);

    foreach ($arrAllPermissions as $key => $value) 
    { 
        // dd($value);           
        $permission = Permission::create($value);
    }
    dd('pass');

    //INSERT INTO `permissions` (`id`, `module_slug`, `name`, `title`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'store-dashboard', 'store-dashboard', 'Store Dashboard', 'admin', NULL, NULL), (NULL, 'store-dashboard', 'store-total-users', 'Store Total Users', 'admin', NULL, NULL);
    //INSERT INTO `permissions` (`id`, `module_slug`, `name`, `title`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'store-manage-materials', 'store-manage-materials', 'Store Manage Materials', 'admin', NULL, NULL), (NULL, 'store-manage-materials', 'store-material-listing', 'Store Material Listing', 'admin', NULL, NULL), (NULL, 'store-manage-materials', 'store-material-add', 'Store Material Add', 'admin', NULL, NULL)
     //INSERT INTO `permissions` (`id`, `module_slug`, `name`, `title`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'store-manage-materials', 'store-manage-users', 'Store Manage Users', 'admin', NULL, NULL), (NULL, 'store-manage-users', 'store-users-listing', 'Store Users Listing', 'admin', NULL, NULL), (NULL, 'store-manage-users', 'store-users-add', 'Store Users Add', 'admin', NULL, NULL), (NULL, 'store-manage-users', 'store-manage-roles', 'Store Manage Roles', 'admin', NULL, NULL), (NULL, 'store-manage-users', 'store-manage-permissions', 'Store Manage Permissions', 'admin', NULL, NULL)
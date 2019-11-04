<div id="roleWisePermissions">

    {{-- Dashboard --}}
    <div class="panel panel-default">
        <div class="panel-heading active">
            <div class="list-group-item d-flex">
                <a href="#">Dashboard </a>
                <label class="switch ml-auto">
                    <input type="checkbox" class="checkbox-permissions" id="permission-dashboard" name="dashboard" value="dashboard" >
                    <span class="knob"></span>
                </label>
                <a data-toggle="collapse" href="#permission1" class="theme-green ml-3 text-underline">details</a>
            </div>
        </div>
        <div id="permission1" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">Total Products</a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-total-products-access" name="total-products-access" value="total-products-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-total-products" name="total-products" value="total-products" >
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Total Customers</a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-total-customers-access" name="total-customers-access" value="total-customers-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-total-customers" name="total-customers" value="total-customers" >
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Total Orders</a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-total-customers-access" name="total-customers-access" value="total-customers-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-total-orders" name="total-orders" value="total-orders" >
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Completed Orders</a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-pending-order-access" name="pending-order-access" value="pending-order-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-completed-orders" name="completed-orders" value="completed-orders" >
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Pending Order</a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-pending-order-access" name="pending-order-access" value="pending-order-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-pending-order" name="pending-order" value="pending-order" >
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Dispatched Order</a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-dispatched-order-access" name="dispatched-order-access" value="dispatched-order-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-dispatched-order" name="dispatched-order" value="dispatched-order" >
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                   
                </ul>
            </div>
        </div>
    </div>

    {{-- Products --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Products </a>
                <label class="switch ml-auto">
                    <input type="checkbox" class="checkbox-permissions" id="permission-manage-products"
                        name="manage-products" value="manage-products">
                    <span class="knob"></span>
                </label>
                <a data-toggle="collapse" href="#permission2"
                    class="theme-green ml-3 text-underline">details</a>
            </div>
        </div>
        <div id="permission2" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">View Products </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-product-listing-access" name="product-listing-access" value="product-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-product-listing" name="product-listing"
                                value="product-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Product </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-product-add-access" name="product-add-access" value="product-add-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-product-add" name="product-add"
                                value="product-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Customers --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Customers </a>
                <label class="switch ml-auto">
                    <input type="checkbox" class="checkbox-permissions" id="permission-manage-customers"
                        name="manage-customers" value="manage-customers">
                    <span class="knob"></span>
                </label>
                <a data-toggle="collapse" href="#permission3"
                    class="theme-green ml-3 text-underline">details</a>
            </div>
        </div>
        <div id="permission3" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">View Customers </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-customer-listing-access" name="customer-listing-access" value="customer-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-customer-listing" name="customer-listing"
                                value="customer-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Customer </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-customer-listing-access" name="customer-listing-access" value="customer-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-customer-add" name="customer-add"
                                value="customer-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Assign Products </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-customer-listing-access" name="customer-listing-access" value="customer-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-assign-products" name="assign-products"
                                value="assign-products">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>

    {{-- Orders --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Orders </a>
                <label class="switch ml-auto">
                    <input type="checkbox" class="checkbox-permissions"
                        id="permission-manage-orders" name="manage-orders"
                        value="manage-orders">
                    <span class="knob"></span>
                </label>
                <a data-toggle="collapse" href="#permission4"
                    class="theme-green ml-3 text-underline">details</a>
            </div>
        </div>
        <div id="permission4" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">Add Order </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-order-add-access" name="order-add-access" value="order-add-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-order-add" name="order-add"
                                value="order-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">View Orders </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-order-listing-access" name="order-listing-access" value="order-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-order-listing" name="order-listing"
                                value="order-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Order Note </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-order-listing-access" name="order-listing-access" value="order-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-order-note" name="order-note"
                                value="order-note">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Order History </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-order-listing-access" name="order-listing-access" value="order-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-order-history" name="order-history"
                                value="order-history">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Order Dispatcher History </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-order-listing-access" name="order-listing-access" value="order-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-order-dispatcher-history" name="order-dispatcher-history"
                                value="order-dispatcher-history">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Order Delete </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-order-listing-access" name="order-listing-access" value="order-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-order-delete" name="order-delete"
                                value="order-delete">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Users --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Users </a>
                <label class="switch ml-auto">
                    <input type="checkbox" class="checkbox-permissions"
                        id="permission-manage-users" name="manage-users"
                        value="manage-users">
                    <span class="knob"></span>
                </label>
                <a data-toggle="collapse" href="#permission5"
                    class="theme-green ml-3 text-underline">details</a>
            </div>
        </div>
        <div id="permission5" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">Add User </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-order-add-access" name="order-add-access" value="order-add-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-users-add" name="users-add"
                                value="users-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">View Users </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-order-add-access" name="order-add-access" value="order-add-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-users-add" name="users-add"
                                value="users-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Manage Permissions </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-customer-listing-access" name="customer-listing-access" value="customer-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-manage-permissions" name="manage-permissions"
                                value="manage-permissions">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Companies --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Companies </a>
                <label class="switch ml-auto">
                    <input type="checkbox" class="checkbox-permissions"
                        id="permission-manage-companies" name="manage-companies"
                        value="manage-companies">
                    <span class="knob"></span>
                </label>
                <a data-toggle="collapse" href="#permission6"
                    class="theme-green ml-3 text-underline">details</a>
            </div>
        </div>
        <div id="permission6" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">Add Company </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-order-add-access" name="order-add-access" value="order-add-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-company-add" name="company-add"
                                value="company-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">View Companies </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-order-add-access" name="order-add-access" value="order-add-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-company-list" name="company-list"
                                value="company-list">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>



</div>
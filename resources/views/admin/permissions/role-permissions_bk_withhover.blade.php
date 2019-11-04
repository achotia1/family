<div id="roleWisePermissions">

    {{-- Dashboard --}}
    <div class="box box-default box-solid collapsed-box">
        <div class="box-header with-border">
          <h3 class="box-title">Dashboard</h3>

          <div class="box-tools pull-right">
            <label class="switch ml-auto">
                <input type="checkbox" class="checkbox-permissions" id="permission-dashboard" name="dashboard" value="dashboard" >
                <span class="knob"></span>
            </label>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
          </div>
        
        </div>
        
        <div class="box-body" style="display: none;">
          <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <div class="box-header with-border">
                            <a href="#">Total Officers</a>
                           
                            <label class="sub-menu switch ml-auto">
                                <input type="checkbox" class="checkbox-permissions" id="permission-total-officers" name="total-officers" value="total-officers" >
                                <span class="knob text-white"><em>off</em></span>
                            </label>
                        </div>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Total Users </a>
                       
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-total-users" name="total-users" value="total-users" >
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Total Vehicles</a>
                       
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-total-vehicles" name="total-vehicles" value="total-vehicles" >
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Total Offence Types</a>
                      
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-total-offence-types" name="total-offence-types" value="total-offence-types" >
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        
    </div>



    {{-- Officers --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Officers </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions" id="permission-manage-officers"
                            name="manage-officers" value="manage-officers">
                        <span class="knob"></span>
                    </label>
                    <a data-toggle="collapse" href="#permission2"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission2" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">View Officers </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-product-listing-access" name="product-listing-access" value="product-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-officer-listing" name="officer-listing"
                                value="officer-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Officer </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-product-add-access" name="product-add-access" value="product-add-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-officer-add" name="officer-add"
                                value="officer-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Vehicles --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Vehicles </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions" id="permission-manage-vehicles"
                            name="manage-vehicles" value="manage-vehicles">
                        <span class="knob"></span>
                    </label>
                    <a data-toggle="collapse" href="#permission3"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission3" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">View Vehicles </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-vehicle-listing-access" name="vehicle-listing-access" value="vehicle-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-vehicle-listing" name="vehicle-listing"
                                value="vehicle-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Vehicle </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-vehicle-listing-access" name="vehicle-listing-access" value="vehicle-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-vehicle-add" name="vehicle-add"
                                value="vehicle-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>

    {{-- Offences --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Offences </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-manage-offences" name="manage-offences"
                            value="manage-offences">
                        <span class="knob"></span>
                    </label>
                    <a data-toggle="collapse" href="#permission4"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission4" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">View Offence </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-offence-listing-access" name="offence-listing-access" value="offence-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-offence-listing" name="offence-listing"
                                value="offence-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Offence </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-offence-add-access" name="offence-add-access" value="offence-add-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-offence-add" name="offence-add"
                                value="offence-add">
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
                <div class="label-anchor">
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
        </div>
        <div id="permission5" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
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
                        <a href="#">Manage Roles </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-customer-listing-access" name="customer-listing-access" value="customer-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-manage-roles" name="manage-roles"
                                value="manage-roles">
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

    {{-- Reports --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Reports </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-manage-reports" name="manage-reports"
                            value="manage-reports">
                        <span class="knob"></span>
                    </label>
                    <a data-toggle="collapse" href="#permission6"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission6" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">Vehicle History Report </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-offence-listing-access" name="offence-listing-access" value="offence-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-vehicle-history" name="vehicle-history"
                                value="vehicle-history">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                   
                    
                    
                </ul>
            </div>
        </div>
    </div>

</div>
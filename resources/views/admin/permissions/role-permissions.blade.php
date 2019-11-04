<div id="roleWisePermissions">

    {{-- Dashboard --}}
    <div class="panel panel-default">
        <div class="panel-heading active">
            <div class="list-group-item">
                <a href="#">Dashboard </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions" id="permission-store-dashboard" name="store-dashboard" value="store-dashboard" >
                        <span class="knob"></span>
                    </label>
                    <a data-toggle="collapse" href="#permission1" class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission1" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                 <li class="list-group-item d-flex">
                        <a href="#">Total Users </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-store-total-users" name="store-total-users" value="store-total-users" >
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Raw Materials --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Raw Materials </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions" id="permission-store-manage-materials"
                            name="store-manage-materials" value="store-manage-materials">
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
                        <a href="#">View Raw Materials </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-product-listing-access" name="product-listing-access" value="product-listing-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-material-listing" name="store-material-listing"
                                value="store-material-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Raw Material </a>
                        <!-- <label class="switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions" id="permission-product-add-access" name="product-add-access" value="product-add-access" >
                            <span class="knob"></span>
                        </label> -->
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-material-add" name="store-material-add"
                                value="store-material-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Production --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Production </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions" id="permission-store-manage-production"
                            name="store-manage-production" value="store-manage-production">
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
                        <a href="#">View Productions </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-production-listing" name="store-production-listing"
                                value="store-production-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Production </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-production-add" name="store-production-add"
                                value="store-production-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>

    {{-- FG Store --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage FG </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-store-manage-fg" name="store-manage-fg"
                            value="store-manage-fg">
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
                        <a href="#">View FG </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-fg-listing" name="store-fg-listing"
                                value="store-fg-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add FG </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-fg-add" name="store-fg-add"
                                value="store-fg-add">
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
                            id="permission-store-manage-users" name="store-manage-users"
                            value="store-manage-users">
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
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-users-listing" name="store-users-listing"
                                value="store-users-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add User </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-users-add" name="store-users-add"
                                value="store-users-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Manage Roles </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-manage-roles" name="store-manage-roles"
                                value="store-manage-roles">
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
                                id="permission-store-manage-permissions" name="store-manage-permissions"
                                value="store-manage-permissions">
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
                            id="permission-store-manage-reports" name="store-manage-reports"
                            value="store-manage-reports">
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
                    <!-- <li class="list-group-item d-flex">
                        <a href="#">Vehicle History Report </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-vehicle-history" name="vehicle-history"
                                value="vehicle-history">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li> -->
                   
                    
                    
                </ul>
            </div>
        </div>
    </div>

</div>
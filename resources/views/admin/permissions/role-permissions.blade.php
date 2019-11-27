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
                <a href="#">Manage Materials </a>
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
                        <a href="#">View Materials </a>
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
                        <a href="#">Add Material </a>
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

    {{-- Production Or Material In --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Material In </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions" id="permission-store-manage-material-in"
                            name="store-manage-material-in" value="store-manage-material-in">
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
                        <a href="#">View Material In </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-material-in-listing" name="store-material-in-listing"
                                value="store-material-in-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Material In </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-material-in-add" name="store-material-in-add"
                                value="store-material-in-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>

    {{-- Manage Batches --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Batches </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-store-manage-batches" name="store-manage-batches"
                            value="store-manage-batches">
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
                        <a href="#">View Batches </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-batches-listing" name="store-batches-listing"
                                value="store-batches-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Batch </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-batches-add" name="store-batches-add"
                                value="store-batches-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>

    {{-- Manage Material Plans --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Material Plans </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-store-manage-material-plans" name="store-manage-material-plans"
                            value="store-manage-material-plans">
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
                        <a href="#">View Material Plan </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-material-plan-listing" name="store-material-plan-listing"
                                value="store-material-plan-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Material Plan </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-material-plan-add" name="store-material-plan-add"
                                value="store-material-plan-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>

    {{-- Manage Returned Material --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Returned Material </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-store-manage-returned-material" name="store-manage-returned-material"
                            value="store-manage-returned-material">
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
                        <a href="#">View Returned Material </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-returned-material-listing" name="store-returned-material-listing"
                                value="store-returned-material-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Returned Material </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-returned-material-add" name="store-returned-material-add"
                                value="store-returned-material-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>

    {{-- Manage Material Output --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Material Output </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-store-manage-material-output" name="store-manage-material-output"
                            value="store-manage-material-output">
                        <span class="knob"></span>
                    </label>
                    <a data-toggle="collapse" href="#permission7"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission7" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">View Material Output </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-material-output-listing" name="store-material-output-listing"
                                value="store-material-output-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Material Output </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-material-output-add" name="store-material-output-add"
                                value="store-material-output-add">
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
                    <a data-toggle="collapse" href="#permission8"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission8" class="panel-collapse collapse">
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
                    <a data-toggle="collapse" href="#permission9"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission9" class="panel-collapse collapse">
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
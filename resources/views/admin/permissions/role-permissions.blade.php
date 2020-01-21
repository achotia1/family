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
                    <a data-toggle="collapse" href="#permission2"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission2" class="panel-collapse collapse">
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
                    <a data-toggle="collapse" href="#permission3"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission3" class="panel-collapse collapse">
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
                    <a data-toggle="collapse" href="#permission4"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission4" class="panel-collapse collapse">
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
                    <a data-toggle="collapse" href="#permission5"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission5" class="panel-collapse collapse">
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
                    <a data-toggle="collapse" href="#permission6"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission6" class="panel-collapse collapse">
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
                    <a data-toggle="collapse" href="#permission7"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission7" class="panel-collapse collapse">
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
                    <a data-toggle="collapse" href="#permission8"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission8" class="panel-collapse collapse">
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

    {{-- Manage Wastage Materials --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Wastage Materials </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-store-manage-wastage-material" name="store-manage-wastage-material"
                            value="store-manage-wastage-material">
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
                    <li class="list-group-item d-flex">
                        <a href="#">View Wastage Material </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-wastage-material-listing" name="store-wastage-material-listing"
                                value="store-wastage-material-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Correct Wastage Material Balance </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-wastage-material-correct-balance" name="store-wastage-material-correct-balance"
                                value="store-wastage-material-correct-balance">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>


    {{-- Manage Stock --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Stock </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-store-manage-stock" name="store-manage-stock"
                            value="store-manage-stock">
                        <span class="knob"></span>
                    </label>
                    <a data-toggle="collapse" href="#permission10"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission10" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">View Stock </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-stock-listing" name="store-stock-listing"
                                value="store-stock-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Opening Stock </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-stock-add" name="store-stock-add"
                                value="store-stock-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Correct Stock Balance </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-stock-correct-balance" name="store-stock-correct-balance"
                                value="store-stock-correct-balance">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>


    {{-- Manage Sales --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Sales </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-store-manage-sales" name="store-manage-sales"
                            value="store-manage-sales">
                        <span class="knob"></span>
                    </label>
                    <a data-toggle="collapse" href="#permission11"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission11" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">View Sales </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-sales-listing" name="store-sales-listing"
                                value="store-sales-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Sale </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-sales-add" name="store-sales-add"
                                value="store-sales-add">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>


    {{-- Manage Returned Sales --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="list-group-item d-flex">
                <a href="#">Manage Returned Sales </a>
                <div class="label-anchor">
                    <label class="switch ml-auto">
                        <input type="checkbox" class="checkbox-permissions"
                            id="permission-store-manage-returned-sales" name="store-manage-returned-sales"
                            value="store-manage-returned-sales">
                        <span class="knob"></span>
                    </label>
                    <a data-toggle="collapse" href="#permission12"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission12" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">View Returned Sale </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-returned-sales-listing" name="store-returned-sales-listing"
                                value="store-returned-sales-listing">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Add Returned Sale </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-sales-returned-add" name="store-sales-returned-add"
                                value="store-sales-returned-add">
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
                    <a data-toggle="collapse" href="#permission13"
                        class="theme-green ml-3 text-underline">details</a>
                </div>
            </div>
        </div>
        <div id="permission13" class="panel-collapse collapse">
            <div class="panel-body border-0 py-0">
                <ul class="list-group toggle-wrapper">
                    <li class="list-group-item d-flex">
                        <a href="#">Batch-Wise Report </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-batch-wise-report" name="store-batch-wise-report"
                                value="store-batch-wise-report">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Aged Material Report </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-aged-material-report" name="store-aged-material-report"
                                value="store-aged-material-report">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>                    
                    <li class="list-group-item d-flex">
                        <a href="#">Raw Material Report </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-raw-material-report" name="store-raw-material-report"
                                value="store-raw-material-report">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>                    
                    <li class="list-group-item d-flex">
                        <a href="#">Material Deviation Report </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-material-deviation-report" name="store-material-deviation-report"
                                value="store-material-deviation-report">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Contribution Report </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-contribution-report" name="store-contribution-report"
                                value="store-contribution-report">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                    <li class="list-group-item d-flex">
                        <a href="#">Yield Average Report </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-avg-yield-report" name="store-avg-yield-report"
                                value="store-avg-yield-report">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                    
                    <li class="list-group-item d-flex">
                        <a href="#">Aged Product Report </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-aged-product-report" name="store-aged-product-report"
                                value="store-aged-product-report">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    <li class="list-group-item d-flex">
                        <a href="#">Stock Deviation Report </a>
                        <label class="sub-menu switch ml-auto">
                            <input type="checkbox" class="checkbox-permissions"
                                id="permission-store-stock-deviation-report" name="store-stock-deviation-report"
                                value="store-stock-deviation-report">
                            <span class="knob text-white"><em>off</em></span>
                        </label>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>

</div>
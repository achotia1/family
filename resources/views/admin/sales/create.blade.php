@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
              <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
            </div>
        <form id="salesForm" method="post" data-toggle="validator" action="{{ route($modulePath.'store') }}">

            <div class="col-md-12">
                <div class="form-group col-md-4">
                    <label class="theme-blue">Invoice Number
                        <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="invoice_no"
                        class="form-control" 
                        required                                       
                        data-error="Invoice Number field is required." 
                    >
                    <span class="help-block with-errors">
                        <ul class="list-unstyled">
                            <li class="err_invoice_no"></li>
                        </ul>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label class="theme-blue">Invoice Date
                        <span class="required">*</span></label>
                    <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                    <input 
                        type="text" 
                        name="invoice_date"                    
                        class="form-control acc_depreciation" 
                        required                                                        
                        data-error="Invoice Date field is required." 
                    >
                    <span class="input-group-addon">
                         <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                    <span class="help-block with-errors">
                        <ul class="list-unstyled">
                            <li class="err_invoice_date"></li>
                        </ul>
                    </span>
                </div> 

                <div class="form-group col-md-4">
                    <label class="theme-blue"> 
                    Customer Name <span class="required">*</span></label>
                    <select class="form-control select2" 
                     id="customer_id"
                     name="customer_id" 
                     required="" 
                     data-error="Customer field is required.">                    
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->contact_name }} ({{ $customer->company_name }})</option>
                        @endforeach
                    </select>  
                    <span class="help-block with-errors">
                        <ul class="list-unstyled">
                            <li class="err_customer_id"></li>
                        </ul>
                    </span>
                </div>

            </div>

            
            <div class="with-border col-md-12">
                <h4 class="">Store Stock</h4>
            </div>
            <div class="col-md-12">
                <table class="table mb-0 border-none" id="plan-table">
                    <thead class="theme-bg-blue-light-opacity-15">
                        <tr class="heading-tr">                                  
                            <th class="w-160-px">Product Name</th>                            
                            <th class="w-160-px">Batch Code</th>
                            <th class="w-160-px">Quantity</th>
                            <th class="w-160-px">Rate</th>
                            <th class="w-50-px"></th>
                        </tr>
                    </thead>
                    <tbody class="no-border">
                    <tr class="inner-td add_plan_area plan">                    
                    <td>
                    <div class="form-group"> 
                        <select 
                            class="form-control my-select select2 products" 
                            placeholder="All Products"
                            name="sales[0][product_id]"
                            id="product_0"
                            required
                            onchange="loadBatches(this);"
                            data-error="Product field is required." 
                        >
                            <option value="">Select Product</option>
                            @if(!empty($getStockProducts) && sizeof($getStockProducts) > 0)
                            @foreach($getStockProducts as $stock)
                             plan_options += `<option value="{{ $stock->assignedProduct->id }}">{{ $stock->assignedProduct->code }} ({{ $stock->assignedProduct->name }})</option>`;
                            @endforeach
                            @endif
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[0][product_id][] err_production_product"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                        <div class="form-group"> 
                        <select 
                            class="form-control my-select select2 batch_id" 
                            placeholder="All Batches"
                            name="sales[0][batch_id]"
                            onchange="setQuantityLimit(0);"
                            id="batches_product_0"
                            required
                            data-error="Batch field is required." 
                        >
                            <option value="">Select Batch</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[0][batch_id][] err_batch_id"></li>
                            </ul>
                        </span>
                        </div>
                    </td>
                    <td>
                    <div class="add_quantity form-group">
                        <input 
                            type="number" 
                            class="form-control quantity"
                            name="sales[0][quantity]" 
                            id="quantity_0"
                            required
                            step="any" 
                            data-error="Quantity should be number."
                        >
                        <input 
                            type="hidden" 
                            id="quantityLimit_0"
                            name="sales[0][quantityLimit]"
                            value="" 
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[0][quantity][] err_quantity"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                    <div class="add_rate form-group">
                        <input 
                            type="number" 
                            class="form-control rate"
                            name="sales[0][rate]" 
                            id="rate_0"
                            required
                            step="any" 
                            data-error="Rate should be number."
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[0][rate][] err_rate"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td></td>
                    </tr>
                     <input type="hidden" name="total_items" id="total_items" value="1">
                    </tbody>
                </table>
                <div class="col-md-8">
                <a href="javascript:void(0)" class="add-more-btn theme-green bold f-16 text-underline"
                                onclick="return addPlan()" style="cursor: pointer;">
                                <span class="mr-2"><img src="{{ url('/assets/admin/images') }}/icons/green_plus.svg"
                                        alt=" view"></span> Add More
                            </a>
                </div>
            </div> 

            <div class="box-footer">
                <div class="col-md-12 align-right">
                <button type="reset" class="btn btn-danger">Reset</button>
                <button type="submit" class="btn btn-success pull-right">Save</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</section>

@endsection
@section('scripts')
    <script type="text/javascript">
        var material_id = "";
        var sale_invoice_id = "";
        var editFlag = 0;

        // PLAN OPTIONS
        var plan_options = '<option value="">Select Product</option>';
        @if(!empty($getStockProducts) && sizeof($getStockProducts) > 0)
        @foreach($getStockProducts as $stock)
         plan_options += `<option value="{{ $stock->assignedProduct->id }}">{{ $stock->assignedProduct->code }} ({{ $stock->assignedProduct->name }})</option>`;
        @endforeach
        @endif
    </script>
    <script type="text/javascript" src="{{ url('assets/admin/js/sales/create-edit.js') }}"></script>
        
@endsection
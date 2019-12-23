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
        <form id="salesForm" method="post" data-toggle="validator" action="{{ route($modulePath.'update', [base64_encode(base64_encode($object->id))]) }}">
            <input type="hidden" name="_method" value="PUT">

            <div class="col-md-12">
                <div class="form-group col-md-4">
                    <label class="theme-blue">Invoice Number
                        <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="invoice_no"
                        class="form-control" 
                        value="{{$object->invoice_no??''}}" 
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
                        value="{{ date('d-m-Y',strtotime($object->invoice_date)) }}" 
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
                     data-error="Customer field is required.">                    
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @if($customer->id==$object->customer_id) selected @endif >{{ $customer->contact_name }} ({{ $customer->company_name }})</option>
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
                    @php
                     $k = 0;
                    @endphp
                    @if(!empty($object->hasSaleInvoiceProducts))
                    @foreach($object->hasSaleInvoiceProducts as $hasSaleInvoiceProducts)
                    <tbody class="no-border">
                    <tr class="inner-td add_plan_area plan">                    
                    <td>
                    <div class="form-group"> 
                        <select 
                            class="form-control my-select select2 products" 
                            placeholder="All Products"
                            name="sales[{{$k}}][product_id]"
                            id="product_{{$k}}"
                            onchange="loadBatches(this);"
                            data-error="Product field is required." 
                        >
                           <option value="{{ $hasSaleInvoiceProducts->product_id }}"  selected >{{ $hasSaleInvoiceProducts->assignedProduct->name }} ({{ $hasSaleInvoiceProducts->assignedProduct->code }})</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[{{$k}}][product_id][] err_production_product"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                        @php
                            $stock_qty = 0;
                            if(!empty($hasSaleInvoiceProducts->assignedBatch->hasStockProducts))
                            {
                                foreach($hasSaleInvoiceProducts->assignedBatch->hasStockProducts as $sale_stock)
                                {
                                    if($hasSaleInvoiceProducts->product_id==$sale_stock->product_id && $hasSaleInvoiceProducts->batch_id==$sale_stock->batch_id)
                                    {
                                        if($sale_stock->balance_quantity<$sale_stock->quantity){
                                            $stock_qty=$sale_stock->balance_quantity+$hasSaleInvoiceProducts->quantity;
                                        }else{
                                            $stock_qty=$sale_stock->balance_quantity;
                                        }
                                        
                                    }
                                }
                            }

                        @endphp
                        <div class="form-group"> 
                        <select 
                            class="form-control my-select select2 batch_id" 
                            placeholder="All Batches"
                            name="sales[{{$k}}][batch_id]"
                            onchange="setQuantityLimit({{$k}});"
                            id="batches_product_{{$k}}"
                            data-error="Batch field is required." 
                        >
                             <option data-qty="{{ $stock_qty }}" value="{{ $hasSaleInvoiceProducts->batch_id.'||'.$hasSaleInvoiceProducts->sale_stock_id }}"  selected >{{ $hasSaleInvoiceProducts->assignedBatch->batch_card_no }} ({{ $stock_qty }})</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[{{$k}}][batch_id][] err_batch_id"></li>
                            </ul>
                        </span>
                        </div>
                    </td>
                    <td>
                    <div class="add_quantity form-group">
                        <input 
                            type="number" 
                            class="form-control quantity"
                            name="sales[{{$k}}][quantity]" 
                            id="quantity_{{$k}}"
                            value="{{ $hasSaleInvoiceProducts->quantity }}" 
                            min="1"
                            max="{{$stock_qty}}"
                            step="any" 
                            data-error="You can not select more than available quantity: {{$stock_qty}}"
                        >
                        <input 
                            type="hidden" 
                            id="quantityLimit_{{$k}}"
                            name="sales[{{$k}}][quantityLimit]"
                            value="{{ $stock_qty }}" 
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[{{$k}}][quantity][] err_quantity"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                    <div class="add_rate form-group">
                        <input 
                            type="number" 
                            class="form-control rate"
                            name="sales[{{$k}}][rate]" 
                            id="rate_{{$k}}"
                            value="{{ $hasSaleInvoiceProducts->rate }}" 
                            step="any" 
                            data-error="Rate should be number."
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[{{$k}}][rate][] err_rate"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                        <p class="m-0 red bold deletebtn" style="display:block;cursor:pointer" onclick="return deletePlan(this)"  id="{{$k}}" style="cursor:pointer">Remove</p>
                    </td>
                    </tr>
                    @php
                        $k++;
                    @endphp
                    @endforeach
                    @endif
                     <input type="hidden" name="total_items" id="total_items" value="{{$k}}">
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
        var sale_invoice_id = "{{ $object->id }}";
        var editFlag = 1;

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
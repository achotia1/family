@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
        <form id="salesForm" method="post" data-toggle="validator" action="{{ route($modulePath.'update', [base64_encode(base64_encode($object->id))]) }}">
            <input type="hidden" name="_method" value="PUT">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
              <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
            </div>

            <div class="col-md-12">
                <div class="form-group col-md-4">
                    <label class="theme-blue">Invoice Number
                        <span class="required">*</span></label>
                    <select class="form-control select2" 
                     id="sale_invoice_id"
                     name="sale_invoice_id" 
                     data-error="Customer field is required.">                    
<!--                         <option value="">Select Invoice Number</option> -->
                        <option value="{{ $object->assignedSale->id }}" selected>{{ $object->assignedSale->invoice_no }}</option>
                    </select>  
                    <span class="help-block with-errors">
                        <ul class="list-unstyled">
                            <li class="err_sale_invoice_id"></li>
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
                       <!--  <option value="">Select Customer</option> -->
                            <option value="{{ $object->customer_id }}" selected>{{ $object->assignedCustomer->contact_name }} ({{ $object->assignedCustomer->company_name }})</option>
                    </select>  
                    <span class="help-block with-errors">
                        <ul class="list-unstyled">
                            <li class="err_customer_id"></li>
                        </ul>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label class="theme-blue">Invoice Return Date
                        <span class="required">*</span></label>
                    <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                    <input 
                        type="text" 
                        name="return_date"                    
                        class="form-control acc_depreciation" 
                        data-error="Invoice Return Date field is required." 
                        value="{{ date('d-m-Y',strtotime($object->return_date)) }}" 
                    >
                    <span class="input-group-addon">
                         <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                    <span class="help-block with-errors">
                        <ul class="list-unstyled">
                            <li class="err_return_date"></li>
                        </ul>
                    </span>
                </div> 

            </div>
            
            <div class="with-border col-md-12">
                <h4 class="">Returned Sale Products Quantities</h4>
            </div>
            <div class="col-md-12">
                <table class="table mb-0 border-none" id="plan-table">
                    <thead class="theme-bg-blue-light-opacity-15">
                        <tr class="heading-tr">                                  
                            <th class="w-160-px">Product Name</th>                            
                            <th class="w-160-px">Batch Code</th>
                            <th class="w-160-px">Quantity</th>
                            <!-- <th class="w-160-px">Rate</th> -->
                            <th class="w-50-px"></th>
                        </tr>
                    </thead>
                    <tbody class="no-border">
                @php
                    $k = 0;
                @endphp
                @foreach($object->hasReturnedProducts as $hasReturnedProduct)                        
                    <tr class="inner-td add_plan_area plan">                    
                    <td>
                    <div class="form-group"> 
                        <select 
                            class="form-control my-select products" 
                            placeholder="All Products"
                            name="sales[{{$k}}][product_id]"
                            id="product_{{$k}}"
                            onchange="loadBatches(this);"
                            data-error="Product field is required." 
                        >
                            <!-- <option value="">Select Product</option> -->
                        <option value="{{ $hasReturnedProduct->assignedProduct->id }}"  selected >{{ $hasReturnedProduct->assignedProduct->code }} ({{ $hasReturnedProduct->assignedProduct->name }})</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[{{$k}}][product_id][] err_production_product"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    @php
                        $sale_qty=$hasReturnedProduct->quantity;
                        if(!empty($object->assignedSale->hasSaleInvoiceProducts)){
                            foreach($object->assignedSale->hasSaleInvoiceProducts as $saleProdut)
                            {
                                if($saleProdut->product_id==$hasReturnedProduct->product_id && $saleProdut->batch_id==$hasReturnedProduct->batch_id)
                                {
                                    $sale_qty=$saleProdut->quantity;
                                }
                            }
                        }
                        
                    @endphp
                    <td>
                        <div class="form-group"> 
                        <select 
                            class="form-control my-select batch_id" 
                            placeholder="All Batches"
                            name="sales[{{$k}}][batch_id]"
                            onchange="setQuantityLimit({{$k}});"
                            id="batches_product_{{$k}}"
                            data-error="Batch field is required." 
                        >
                            <!-- <option value="">Select Batch</option> -->
                            <option data-qty="{{ $sale_qty }}" value="{{ $hasReturnedProduct->assignedBatch->id }}"  selected>{{ $hasReturnedProduct->assignedBatch->batch_card_no }} ({{ $sale_qty }})</option>
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
                            value="{{ $hasReturnedProduct->quantity }}" 
                            min="1"
                            max="{{$sale_qty}}"
                            step="any" 
                            data-error="You can not select more than available quantity: {{$sale_qty}}"
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[{{$k}}][quantity][] err_quantity"></li>
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
                     <input type="hidden" name="total_items" id="total_items" value="{{$k}}">
                    </tbody>
                </table>
                <div class="col-md-8">
                <a href="javascript:void(0)" class="theme-green bold f-16 text-underline"
                                onclick="return addPlan()" style="cursor: pointer;">
                                <span class="mr-2"><img src="{{ url('/assets/admin/images') }}/icons/green_plus.svg"
                                        alt=" view"></span> Add More
                            </a>
                </div>
            </div> 

            <div class="box-footer">
                <div class="col-md-12 align-right">
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
        var plan_id = "{{ $object->sale_invoice_id }}";
        var editFlag = 1;

        // PLAN OPTIONS
        var plan_options = '<option value="">Select Product</option>';
        @if(!empty($getStockProducts) && sizeof($getStockProducts) > 0)
        @foreach($getStockProducts as $stock)
         plan_options += `<option value="{{ $stock->assignedProduct->id }}">{{ $stock->assignedProduct->code }} ({{ $stock->assignedProduct->name }})</option>`;
        @endforeach
        @endif
    </script>
    <script type="text/javascript" src="{{ url('assets/admin/js/return-sale/create-edit.js') }}"></script>
@endsection
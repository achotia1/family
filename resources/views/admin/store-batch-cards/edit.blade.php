@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('style')
<style>
    .trExpense{
        text-align: center;
        /*color: maroon;*/
        /*background: aliceblue;*/
    }
    .trExpenseTotal{
        text-align: right;
    }    
    table,tbody ,tr, td {
        border: 1px solid darkgray;
    }
    tbody:first-child{
        border-top: 3px solid darkgray;   
    }
    
</style>
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
        	<div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
              <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
            </div>        
        <form id="batchForm" data-toggle="validator" action="{{ route($modulePath.'update', [base64_encode(base64_encode($branch->id))]) }}" method="post">
            <input type="hidden" name="_method" value="PUT">
            @php
            $productName = '';
            @endphp            
            <div class="form-group col-md-12">
                <label class="theme-blue"> 
                Product Code <span class="required">*</span></label>
                <select class="form-control my-select select2" id="product_code" name="product_code"required="" onchange="checkStock(this);" data-error="Product Code field is required.">                    
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                    @php
                    if($branch->product_code==$product['id'])
                    	$productName = $product['code']. " (".$product['name'].")";
                    @endphp
                    <option value="{{$product['id']}}" @if($branch->product_code==$product['id']) selected @endif>{{$product['code']}}  ( {{$product['name']}} )</option>
                    @endforeach
                 </select>                
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_product_code"></li>
                    </ul>
                </span>
            </div>
			
            <div class="form-group col-md-6">
                <label class="theme-blue">Batch Code 
                    <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="batch_card_no"
                    value="{{ $branch->batch_card_no }}"
                    class="form-control" 
                    required                    
                    data-error="Batch Code field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_batch_card_no"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Production Quantity
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="batch_qty" 
                    class="form-control" 
                    value="{{ $branch->batch_qty }}"
                    required
                    step="any"                   
                    maxlength="20" 
                    data-error="Production Quantity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_batch_qty"></li>
                    </ul>
                </span>
            </div>
            <div class="box-footer">
                <div class="col-md-12 align-right">
                <!-- <button type="reset" class="btn btn-danger">Reset</button> -->
                <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
        <div class="col-md-12">			
          		<h4>Available Stock for: <span id="spn_product"><b>{{$productName}}</b></span></h4>        	
				<div class="table-responsive"  id="tblProduct">
					<table class="table" border="1px;">
			            <thead class="theme-bg-blue-light-opacity-15">
			            	<tr class="trExpense">	                    	
		                    	<td><b>Batch Card</b></td>
		                    	<td><b>Balance Stock</b></td>
			                </tr>	
			            </thead>
			            <tbody>
			            @php
			            if(!$stockData->isEmpty()){
			            @endphp
			            @foreach($stockData as $data)
			            @php
			            $balanceQty = number_format($data->balance_quantity, 2, '.', '');
			            @endphp
			            <tr>                          
                            <td>{{$data->assignedBatch->batch_card_no}}</td>
                            <td>{{$balanceQty}}</td>
                        </tr>
			            @endforeach
			            @php
			            } else {
			            @endphp
			            <tr><td colspan="2">No Stock Available.</td></tr>
						@php	
						}
			            @endphp  	                
			            </tbody>
		            </table>
				</div>
			</div>
        </div>
    </div>
</section>

@endsection
@section('scripts')
    <script type="text/javascript" src="{{ url('assets/admin/js/rms-store/create-edit.js') }}"></script>    
@endsection
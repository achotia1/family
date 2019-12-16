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
        <form id="stockInForm" method="post" data-toggle="validator" action="{{ route($modulePath.'store') }}">
        <div class="row">
            <div class="col-md-12">
            <div class="form-group col-md-6">
                <label class="theme-blue"> 
                Product Code <span class="required">*</span></label>
                <select class="form-control my-select" id="product_code" name="product_code" required="" data-error="Product Code field is required.">
                    <option value="">Select Product</option>
                    @foreach($products as $product){
                    <option value="{{$product['id']}}">{{$product['code']}}  ( {{$product['name']}} )</option>
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
			</div>
            </div>
            <div class="row">
            <div class="col-md-12">
            <div class="form-group col-md-6">
                <label class="theme-blue">Stock Quantity
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="quantity" 
                    class="form-control" 
                    required
                    step="any"                   
                    maxlength="20" 
                    data-error="Stock Quantity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_quantity"></li>
                    </ul>
                </span>
            </div>               
            <div class="form-group col-md-6">
                <label class="theme-blue">Manufacturing Cost per Unit
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="manufacturing_cost" 
                    class="form-control" 
                    required
                    step="any"                   
                    maxlength="20" 
                    data-error="Manufacturing Cost should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_manufacturing_cost"></li>
                    </ul>
                </span>
            </div>
            </div>
            </div>
            <div class="box-footer">
                <div class="col-md-12 align-right">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="reset" class="btn btn-danger">Reset</button>
                </div>
            </div>
        </form>        	
        </div>
    </div>
</section>

@endsection
@section('scripts')
    <script type="text/javascript" src="{{ url('assets/admin/js/stock-in/create-edit.js') }}"></script>    
@endsection
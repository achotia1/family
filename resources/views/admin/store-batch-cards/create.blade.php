@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
        <form id="batchForm" method="post" data-toggle="validator" action="{{ route($modulePath.'store') }}">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
            </div>
            
            <div class="form-group col-md-12">
                <label class="theme-blue"> 
                Product Code <span class="required">*</span></label>
                <select class="form-control my-select" id="product_code" name="product_code"required="" data-error="Product Code field is required.">
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
                <label class="theme-blue">Batch Card Number
                    <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="batch_card_no" 
                    value="{{$batchNo}}" 
                    class="form-control" 
                    required                                       
                    data-error="Batch Card Number field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_batch_card_no"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Batch Quantity
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="batch_qty" 
                    class="form-control" 
                    required
                    step="any"                   
                    maxlength="20" 
                    data-error="Batch Quantity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_batch_qty"></li>
                    </ul>
                </span>
            </div>                  
            <div class="form-group col-md-6">
                <label class="theme-blue">Status</label>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="status" checked value="1">
                      Active
                    </label>
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
    <script type="text/javascript" src="{{ url('assets/admin/js/rms-store/create-edit.js') }}"></script>    
@endsection
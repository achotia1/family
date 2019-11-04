@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">        
        <form id="materialForm" data-toggle="validator" action="{{ route($modulePath.'update', [base64_encode(base64_encode($material->id))]) }}" method="post">
            <input type="hidden" name="_method" value="PUT">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
            </div>
            
            <div class="form-group">
                <label class="theme-blue"> 
                Material Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="name"
                    value="{{ $material->name }}"
                    class="form-control" 
                    required
                    maxlength="12" 
                    data-error="Material Name field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_name"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Total Qauntity 
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="total_qty"
                    value="{{ $material->total_qty }}"
                    class="form-control cls-total-qty" 
                    required
                    step="any"
                    maxlength="12" 
                    data-error="Total Qauntity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_total_qty"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Unit 
                    <span class="required">*</span></label>
                <select class="form-control my-select" name="unit" required="" data-error="Unit field is required.">                    
                    <option value="kg" @if($material->unit=="kg") selected @endif>Kg</option>
                    <option value="gm" @if($material->unit=="gm") selected @endif>Gm</option>
                 </select>
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_unit"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Price Per <span id="price_unit">Unit</span>
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="price_per_unit"
                    value="{{ $material->price_per_unit }}"
                    class="form-control cls-unit-price" 
                    required
                    step="any"
                    maxlength="12" 
                    data-error="Price Per Unit should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_price_per_unit"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Total Price</label>
                <input 
                    type="text" 
                    name="total_price"
                    value="{{ $material->total_price }}"
                    class="form-control cls-total-price"
                    readonly                    
                    maxlength="12"                     
                    data-error="Total Price field is required" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_total_price"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Opening Stock
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="opening_stock"
                    value="{{ $material->opening_stock }}"
                    class="form-control" 
                    required
                    step="any"
                    maxlength="12"                     
                    data-error="Opening Stock should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_opening_stock"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Balance Stock
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="balance_stock"
                    value="{{ $material->balance_stock }}"
                    class="form-control" 
                    required
                    step="any"
                    maxlength="12"                     
                    data-error="Balance Stock should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_balance_stock"></li>
                    </ul>
                </span>
            </div>           

            <div class="form-group">
                <label class="theme-blue">Trigger Quanity<span class="required">*</span></label>
                <input 
                    type="number" 
                    name="trigger_qty"                    
                    value="{{ $material->trigger_qty }}"
                    class="form-control" 
                    required
                    step="any"
                    maxlength="12" 
                    data-error="Trigger Quanity should be number."                    
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_trigger_qty"></li>
                    </ul>
                </span>
            </div>            
            <div class="form-group">
                <label class="theme-blue">Status</label>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="status" value="1" @if($material->status==1) checked @endif>
                      Active
                    </label>
                </div>  
            </div>
            <div class="box-footer">
                <button type="reset" class="btn btn-danger">Reset</button>
                <button type="submit" class="btn btn-success pull-right">Save</button>
            </div>
        </form>
        </div>
    </div>
</section>

@endsection
@section('scripts')
    <script type="text/javascript" src="{{ url('assets/admin/js/materials/create-edit.js') }}"></script>    
@endsection
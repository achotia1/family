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
            
            <div class="form-group col-md-12">
                <label class="theme-blue"> 
                Material Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="name"
                    value="{{ $material->name }}" 
                    class="form-control" 
                    required
                    maxlength="50" 
                    data-error="Material Name field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_name"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Unit 
                    <span class="required">*</span></label>
                <select class="form-control my-select" name="unit" required="" data-error="Unit field is required.">
                    <!-- <option value="">Select Unit</option>    -->                
                    <option value="kg" @if($material->unit=="kg") selected @endif>Kg</option>
                    <option value="rolls" @if($material->unit=="rolls") selected @endif>Rolls</option>
                    <option value="nos" @if($material->unit=="nos") selected @endif>Nos</option>
                 </select>
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_unit"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Price Per <span id="price_unit">Unit</span>
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="price_per_unit"
                    value="{{ $material->price_per_unit }}"
                    class="form-control cls-unit-price" 
                    required
                    step="any"
                    maxlength="20" 
                    data-error="Price Per Unit should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_price_per_unit"></li>
                    </ul>
                </span>
            </div>

            <!-- <div class="form-group">
                <label class="theme-blue">Total Price</label>
                <input 
                    type="text" 
                    name="total_price" 
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
            </div> -->

            <div class="form-group col-md-6">
                <label class="theme-blue">Opening Stock
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="opening_stock"
                    value="{{ $material->opening_stock }}" 
                    class="form-control" 
                    required
                    maxlength="20"
                    step="any"
                    data-error="Opening Stock should be number."                
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_opening_stock"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Balance Stock
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="balance_stock"
                    value="{{ $material->balance_stock }}"
                    class="form-control" 
                    required
                    maxlength="20"
                    step="any"                  
                    data-error="Balance Stock should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_balance_stock"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Material Order Quantity</label>
                <input 
                    type="number" 
                    name="moq"
                    value="{{ $material->moq }}"
                    class="form-control cls-total-qty"                     
                    step="any"
                    maxlength="20" 
                    data-error="Material Order Quantity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_moq"></li>
                    </ul>
                </span>
            </div>                  

            <div class="form-group col-md-6">
                <label class="theme-blue">Trigger Quanity<span class="required">*</span></label>
                <input 
                    type="number" 
                    name="trigger_qty"
                    value="{{ $material->trigger_qty }}"
                    class="form-control" 
                    required
                    maxlength="20"
                    step="any"
                    data-error="Trigger Quanity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_trigger_qty"></li>
                    </ul>
                </span>
            </div>            
            <div class="form-group col-md-6">
                <label class="theme-blue">Status</label>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="status" checked value="1" @if($material->status==1) checked @endif>
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
    <script type="text/javascript" src="{{ url('assets/admin/js/materials/create-edit.js') }}"></script>    
@endsection
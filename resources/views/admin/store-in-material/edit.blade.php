@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">        
        <form id="materialInForm" data-toggle="validator" action="{{ route($modulePath.'update', [base64_encode(base64_encode($material->id))]) }}" method="post">
            <input type="hidden" name="_method" value="PUT">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
              <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
            </div>            
            <div class="form-group col-md-6">
                <label class="theme-blue"> 
                Material <span class="required">*</span></label>
                <select class="form-control my-select" id="material_id" name="material_id" required="" data-error="Batch Code field is required.">
                    <option value="">Select Material</option>
                    @foreach($materialIds as $val)
                    <option value="{{$val['id']}}" @if($material->material_id==$val['id']) selected @endif>{{$val['name']}}</option>
                    @endforeach
                   
                </select>                
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_material_id"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Lot Number
                    <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="lot_no"
                    value="{{$material->lot_no}}"                   
                    class="form-control" 
                    required                                       
                    data-error="Lot Number field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_lot_no"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Lot Quantity
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="lot_qty"
                    value="{{$material->lot_qty}}"
                    class="form-control" 
                    required
                    step="any"                   
                    maxlength="20" 
                    data-error="Lot Quantity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_lot_qty"></li>
                    </ul>
                </span>
            </div>
             <div class="form-group col-md-6">
                <label class="theme-blue">Price Per Unit
                    <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="price_per_unit"
                    value="{{$material->price_per_unit}}"            
                    class="form-control" 
                    required                                       
                    data-error="Price Per Unit field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_price_per_unit"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Status</label>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="status" value="1" @if($material->status==1) checked @endif>
                      Active
                    </label>
                </div>  
            </div>
            <div class="box-footer">
                <div class="col-md-12 align-right">
                    <!-- <button type="reset" class="btn btn-danger">Reset</button> -->
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</section>

@endsection
@section('scripts')    
    <script type="text/javascript" src="{{ url('assets/admin/js/materials-in/create-edit.js') }}"></script>    
@endsection
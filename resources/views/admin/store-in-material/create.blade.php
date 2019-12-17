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
        <form id="materialInForm" method="post" data-toggle="validator" action="{{ route($modulePath.'store') }}">
            <div class="row">
            <div class="col-md-12">
                <div class="form-group col-md-6">
                    <label class="theme-blue"> 
                    Material <span class="required">*</span></label>
                    <select class="form-control my-select select2" id="material_id" name="material_id" required="" data-error="Raw Material field is required.">
                        <option value="">Select Material</option>
                        @foreach($materialIds as $val)
                        <option value="{{$val['id']}}">{{$val['name']}}</option>
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
                        value="{{$lotNo}}"                   
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
            </div>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Lot Quantity
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="lot_qty" 
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
                    type="number" 
                    name="price_per_unit"                    
                    class="form-control" 
                    required
                    step="any"                   
                    maxlength="20"                                       
                    data-error="Price Per Unit field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_price_per_unit"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-12">
                <label class="theme-blue">Status</label>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="status" value="1">
                      Is Opening?
                    </label>
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
    <script type="text/javascript">
        var material_id = "";
        var batch_id = "";
    </script>
    <script type="text/javascript" src="{{ url('assets/admin/js/materials-in/create-edit.js') }}"></script>
        
@endsection
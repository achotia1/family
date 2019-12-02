@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
        <form id="materialForm" method="post" data-toggle="validator" action="{{ route($modulePath.'store') }}">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
              <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
            </div>
            
            <div class="form-group col-md-12">
                <label class="theme-blue"> 
                Material Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-control" 
                    required
                    maxlength="150" 
                    data-error="Material Name field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_name"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Material Type
                    <span class="required">*</span></label>
                <select class="form-control my-select" name="material_type" required="" data-error="Material Type field is required.">                    
                    <option value="Raw">Raw Material</option>
                    <option value="Packaging">Packaging Material</option>
                    <option value="Consumable">Consumable Material</option>
                 </select>
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_material_type"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Unit 
                    <span class="required">*</span></label>
                <select class="form-control my-select" name="unit" required="" data-error="Unit field is required.">                    
                    <option value="Kg">Kg</option>
                    <option value="Litre">Litre</option>
                    <option value="Nos">Nos</option>
                 </select>
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_unit"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Minimum Order Quantity
                <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="moq"                    
                    class="form-control cls-total-qty"
                    required                    
                    step="any"
                    maxlength="20" 
                    data-error="Minimum Order Quantity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_moq"></li>
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
    <script type="text/javascript" src="{{ url('assets/admin/js/materials/create-edit.js') }}"></script>    
@endsection
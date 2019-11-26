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
        <form id="materialOutForm" method="post" data-toggle="validator" action="{{ route($modulePath.'store') }}">            
            <div class="form-group col-md-12">
                <label class="theme-blue"> 
                Batch Code <span class="required">*</span></label>
                <select class="form-control my-select" 
                        id="plan_id" 
                        name="plan_id"
                        onchange="checkPlan(this);"                        
                        required="" 
                        data-error="Batch Code field is required." 
                        >                    
                    <option value="">Select Batch</option>
                    @foreach($plans as $plan)
                    <option value="{{$plan->id}}">{{$plan->assignedBatch->batch_card_no}} ({{$plan->assignedBatch->assignedProduct->code}} - {{$plan->assignedBatch->assignedProduct->name}})</option>
                    @endforeach
                </select>                
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_plan_id"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Sellable Quantity
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="sellable_qty" 
                    class="form-control" 
                    required
                    maxlength="20"
                    step="any"                  
                    data-error="Sellable Quantity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_sellable_qty"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Course Powder
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="course_powder" 
                    class="form-control" 
                    required
                    maxlength="20"
                    step="any"                  
                    data-error="Course Powder should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_course_powder"></li>
                    </ul>
                </span>
            </div> 
            <div class="form-group col-md-6">
                <label class="theme-blue">Rejection
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="rejection" 
                    class="form-control" 
                    required
                    maxlength="20"
                    step="any"                  
                    data-error="Rejection should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_rejection"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Dust Product
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="dust_product" 
                    class="form-control" 
                    required
                    maxlength="20"
                    step="any"                  
                    data-error="Dust Product should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_dust_product"></li>
                    </ul>
                </span>
            </div>
            <!--<div class="form-group col-md-6">
                <label class="theme-blue">Loss Material
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="loss_material"
                    id="loss_material" 
                    class="form-control" 
                    required                    
                    maxlength="20"
                    step="any"                  
                    data-error="Loss Material should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_loss_material"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Yield
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="yield"
                    id="yield" 
                    class="form-control" 
                    required                    
                    maxlength="20"
                    step="any"                  
                    data-error="Yield should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_yield"></li>
                    </ul>
                </span>
            </div>-->
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
    
    <script type="text/javascript" src="{{ url('assets/admin/js/materials-out/create-edit.js') }}"></script>
        
@endsection
@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
        <form id="returnForm" method="post" data-toggle="validator" action="{{ route($modulePath.'store') }}">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue"> 
                Batch Code <span class="required">*</span></label>
                <select class="form-control my-select" id="batch_no" name="batch_no" required="" data-error="Batch Code field is required.">                    
                    <option value="">Select Batch</option>
                    @foreach($batchNos as $val){
                    <option value="{{$val['id']}}">{{$val['batch_card_no']}}</option>
                    @endforeach
                </select>                
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_batch_no"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue"> 
                Raw Material <span class="required">*</span></label>
                <select class="form-control my-select" id="material_id" name="material_id" required="" data-error="Raw Material field is required.">                    
                    <option value="">Select Material</option>
                    @foreach($materialIds as $val){
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
                <label class="theme-blue">Quantity
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="quantity" 
                    class="form-control" 
                    required
                    step="any"                   
                    maxlength="12" 
                    data-error="Quantity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_quantity"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Bill Number
                    <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="bill_number"                    
                    class="form-control" 
                    required                                       
                    data-error="Bill Number field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_bill_number"></li>
                    </ul>
                </span>
            </div>
            
            <div class="form-group col-md-6">
                <label class="theme-blue">Return Date
                    <span class="required">*</span></label>
                <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                <input 
                    type="text" 
                    name="return_date"                    
                    class="form-control acc_depreciation" 
                    required                                                        
                    data-error="Date field is required." 
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
    <script type="text/javascript" src="{{ url('assets/admin/js/returned-material/create-edit.js') }}"></script>
        
@endsection
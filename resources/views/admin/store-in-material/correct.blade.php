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
        <form id="materialInForm" data-toggle="validator" action="{{ route($modulePath.'updateBalance') }}" method="post">
            
            <div class="form-group col-md-6">
                <label class="theme-blue">Material</label>
                <input 
                    type="text" 
                    name="material_id"
                    value="{{$material->hasMateials->name}}"                   
                    class="form-control" 
                    disabled
                >               
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Lot Number</label>
                <input 
                    type="text" 
                    name="lot_no"
                    value="{{$material->lot_no}}"                   
                    class="form-control" 
                    disabled
                >               
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Old Lot Balance</label>
                <input 
                    type="number" 
                    name="previous_balance"
                    value="{{$material->lot_balance}}"
                    class="form-control" 
                    readonly
                    step="any"                   
                    maxlength="20" 
                    data-error="Lot Quantity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_previous_balance"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">New Lot Balance
                	<span class="required">*</span></label>
                <input 
                    type="number" 
                    name="corrected_balance"
                    value=""
                    class="form-control"
                    step="any"
                    required                   
                    maxlength="20" 
                    data-error="Corrected Lot Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_corrected_balance"></li>
                    </ul>
                </span>
            </div>
            <input type="hidden" name="id" value="{{$material->id}}"/>
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
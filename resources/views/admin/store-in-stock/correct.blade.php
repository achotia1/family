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
        <form id="stockInForm" data-toggle="validator" action="{{ route($modulePath.'updateBalance') }}" method="post">
            
            <div class="form-group col-md-6">
                <label class="theme-blue">Product</label>
                <input 
                    type="text" 
                    name="code"
                    value="{{$stock->assignedProduct->code}} ({{$stock->assignedProduct->name}})"                   
                    class="form-control" 
                    disabled
                >               
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Batch Code</label>
                <input 
                    type="text" 
                    name="batch_card_no"
                    value="{{$stock->assignedBatch->batch_card_no}}"                   
                    class="form-control" 
                    disabled
                >               
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Old Stock Balance</label>
                <input 
                    type="number" 
                    name="previous_balance"
                    value="{{$stock->balance_quantity}}"
                    class="form-control" 
                    readonly
                    step="any"                   
                    maxlength="20" 
                    data-error="Stock Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_previous_balance"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">New Stock Balance
                	<span class="required">*</span></label>
                <input 
                    type="number" 
                    name="corrected_balance"
                    value=""
                    class="form-control"
                    step="any"
                    required                   
                    maxlength="20" 
                    data-error="Corrected Stock Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_corrected_balance"></li>
                    </ul>
                </span>
            </div>
            <input type="hidden" name="id" value="{{$stock->id}}"/>
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
    <script type="text/javascript" src="{{ url('assets/admin/js/stock-in/create-edit.js') }}"></script>    
@endsection
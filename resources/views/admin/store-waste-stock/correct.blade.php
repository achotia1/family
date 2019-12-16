@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
@php
$oldCbalance = number_format($material->balance_course, 2, '.','');
$oldRbalance = number_format($material->balance_rejection, 2, '.','');
$oldDbalance = number_format($material->balance_dust, 2, '.','');
$oldLbalance = number_format($material->balance_loose, 2, '.','');
@endphp
<section class="content">
    <div class="box box-primary">
        <div class="box-body">        
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
              <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
            </div>            
        <form id="wastageForm" data-toggle="validator" action="{{ route($modulePath.'updateBalance') }}" method="post">
            
            <div class="form-group col-md-6">
                <label class="theme-blue">Product</label>
                <input 
                    type="text" 
                    name="product_id"
                    value="{{$material->assignedProduct->code}} ({{$material->assignedProduct->name}})"                   
                    class="form-control" 
                    disabled
                >               
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Batch Code</label>
                <input 
                    type="text" 
                    name="batch_id"
                    value="{{$material->assignedBatch->batch_card_no}}"                   
                    class="form-control" 
                    disabled
                >               
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Old Course Balance</label>
                <input 
                    type="number" 
                    name="previous_cbalance"
                    value="{{$oldCbalance}}"
                    class="form-control" 
                    readonly
                    step="any"                   
                    maxlength="20" 
                    data-error="Old Course Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_previous_cbalance"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">New Course Balance
                	<span class="required">*</span></label>
                <input 
                    type="number" 
                    name="corrected_cbalance"
                    value="{{$oldCbalance}}"
                    class="form-control"
                    step="any"
                    required                   
                    maxlength="20" 
                    data-error="Corrected Course Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_corrected_cbalance"></li>
                    </ul>
                </span>
            </div>
            
            <div class="form-group col-md-6">
                <label class="theme-blue">Old Rejection Balance</label>
                <input 
                    type="number" 
                    name="previous_rbalance"
                    value="{{$oldRbalance}}"
                    class="form-control" 
                    readonly
                    step="any"                   
                    maxlength="20" 
                    data-error="Old Rejection Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_previous_rbalance"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">New Rejection Balance
                	<span class="required">*</span></label>
                <input 
                    type="number" 
                    name="corrected_rbalance"
                    value="{{$oldRbalance}}"
                    class="form-control"
                    step="any"
                    required                   
                    maxlength="20" 
                    data-error="Corrected Rejection Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_corrected_rbalance"></li>
                    </ul>
                </span>
            </div>
            
            <div class="form-group col-md-6">
                <label class="theme-blue">Old Dust Balance</label>
                <input 
                    type="number" 
                    name="previous_dbalance"
                    value="{{$oldDbalance}}"
                    class="form-control" 
                    readonly
                    step="any"                   
                    maxlength="20" 
                    data-error="Old Dust Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_previous_dbalance"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">New Dust Balance
                	<span class="required">*</span></label>
                <input 
                    type="number" 
                    name="corrected_dbalance"
                    value="{{$oldDbalance}}"
                    class="form-control"
                    step="any"
                    required                   
                    maxlength="20" 
                    data-error="Corrected Dust Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_corrected_dbalance"></li>
                    </ul>
                </span>
            </div>
            
            <div class="form-group col-md-6">
                <label class="theme-blue">Old Loose Balance</label>
                <input 
                    type="number" 
                    name="previous_lbalance"
                    value="{{$oldLbalance}}"
                    class="form-control" 
                    readonly
                    step="any"                   
                    maxlength="20" 
                    data-error="Old Loose Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_previous_lbalance"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">New loose Balance
                	<span class="required">*</span></label>
                <input 
                    type="number" 
                    name="corrected_lbalance"
                    value="{{$oldLbalance}}"
                    class="form-control"
                    step="any"
                    required                   
                    maxlength="20" 
                    data-error="Corrected Loose Balance should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_corrected_lbalance"></li>
                    </ul>
                </span>
            </div>
            <input type="hidden" name="id" value="{{$material->id}}"/>
            <div class="box-footer">
                <div class="col-md-12 align-right">                    
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</section>

@endsection
@section('scripts')    
    <script type="text/javascript" src="{{ url('assets/admin/js/store-waste-stock/create-edit.js') }}"></script>    
@endsection
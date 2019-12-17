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
        <form id="productionForm" method="post" data-toggle="validator" action="{{ route($modulePath.'store') }}">
            <div class="form-group col-md-8">
                <label class="theme-blue"> 
                Batch Code <span class="required">*</span></label>
                <select class="form-control my-select select2" 
                        id="batch_id" 
                        name="batch_id"
                        onchange="checkBatch(this);getWastageBatches(this);"
                        required="" 
                        data-error="Batch Code field is required." 
                        >                    
                    <option value="">Select Batch</option>
                    @foreach($batchNos as $val)
                    <option value="{{$val['id']}}" data-pid="{{ $val->assignedProduct->id }}">{{$val['batch_card_no']}} ({{$val->assignedProduct->code}} - {{$val->assignedProduct->name}})</option>
                    @endforeach
                </select>                
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_batch_id"></li>
                    </ul>
                </span>
            </div>
            <!-- <div class="form-group col-md-6">
                <label class="theme-blue"> Product </label>
                <input 
                    type="text" 
                    name="product"
                    id="product_id"
                    value=""
                    class="form-control" 
                    readonly
                >               
            </div> -->
        	<div class="with-border col-md-12">
          		<h4 class="">Plan Material</h4>
        	</div>
            <div class="col-md-12">
            	<table class="table mb-0 border-none " id="plan-table">
                    <thead class="theme-bg-blue-light-opacity-15">
                        <tr class="heading-tr">                            
                            <th class="w-160-px">Material Name</th>                            
                            <th class="w-160-px">Material Lot Number</th>
                            <th class="w-160-px">Quantity</th>                            						
                            <th class="w-50-px"></th>
                        </tr>
                    </thead>
                    <tbody class="no-border">
                    <tr class="inner-td add_plan_area plan">                    
                    <td>
                    <div class="form-group"> 
                        <select 
                            class="form-control my-select production_material" 
                            placeholder="All Materials"
                            name="production[0][material_id]"
                            id="0"
                            required
                            onchange="loadLot(this);"
                            data-error="Material Number field is required." 
                        >
                            <option value="">Select Material</option>
                            @if(!empty($materialIds) && sizeof($materialIds) > 0)
                            @foreach($materialIds as $val)
                            <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                            @endforeach
                            @endif
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_production[0][material_id][] err_production_material"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                    	<div class="form-group"> 
                        <select 
                            class="form-control my-select production_lot" 
                            placeholder="Material Lots"
                            name="production[0][lot_id]"
                            id="l_0"
                            required
                            data-error="Material Lot field is required." 
                        >
                            <option value="">Select Lot</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_production[0][lot_id][] err_production_lot"></li>
                            </ul>
                        </span>
                    	</div>
                    </td>
                    <td>
                    <div class="add_quantity form-group">
                        <input 
                            type="number" 
                            class="form-control quantity"
                            name="production[0][quantity]"
                            id="q_0"
                            onblur="checkBal(this)"
                            required
                            step="any" 
                            data-error="Quantity should be number."
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="errq_0 err_production[0][quantity][] err_quantity"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td></td>
                    </tr>
                    <input type="hidden" name="total_items" id="total_items" value="1">
                    </tbody>                    
                </table>
            	<div class="col-md-8">
            	<a href="javascript:void(0)" class="theme-green bold f-16 text-underline"
                                onclick="return addPlan()" style="cursor: pointer;">
                                <span class="mr-2"><img src="{{ url('/assets/admin/images') }}/icons/green_plus.svg"
                                        alt=" view"></span> Add More
                            </a>
            	</div>
            </div>   
			
            <div class="with-border col-md-12">
                <h4 class="">Wastage Material Stock</h4>
            </div>
            <div class="col-md-12">
                <table class="table mb-0 border-none " id="wastage-table">
                    <thead class="theme-bg-blue-light-opacity-15">
                        <tr class="heading-wastage-tr">                            
                            <th class="w-160-px">Batch Code</th>
                            <th class="w-160-px">Material</th>
                            <th class="w-160-px">Quantity</th>
                            <th class="w-50-px"></th>
                        </tr>
                    </thead>
                    <tbody class="no-border">
                    <tr class="inner-td add_wastage_area wastage">                    
                    <td>
                    <div class="form-group"> 
                        <select 
                            class="form-control my-select wastage_batch" 
                            placeholder="All Batches"
                            name="wastage[0][batch_id]"
                            id="wastage_0"
                            onchange="loadWastageBatchMaterial(this);"
                            data-error="Batch field is required." 
                        >
                            <option value="">Select Batch</option>
                           
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_wastage[0][batch_id][] err_wastage_batch"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                        <div class="form-group"> 
                        <select 
                            class="form-control my-select wastage_material" 
                            placeholder="Material"
                            name="wastage[0][material_id]"
                            id="m_wastage_0"
                            onchange="setQuantityLimit(0);"
                            data-error="Material field is required." 
                        >
                            <option value="">Select Material</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_wastage[0][lot_id][] err_wastage_material"></li>
                            </ul>
                        </span>
                        </div>
                    </td>
                    <td>
                    <div class="wastage_add_quantity form-group">
                        <input 
                            type="number" 
                            class="form-control quantity"
                            name="wastage[0][quantity]"
                            id="wastage_q_0"
                            step="any" 
                            data-error="Quantity should be number."
                        >
                        <input 
                            type="hidden" 
                            id="wastageQuantityLimit_0"
                            name="wastage[0][wastageQuantityLimit]"
                            value="" 
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="errq_0 err_wastage[0][quantity][] err_wastage_quantity"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td></td>
                    </tr>
                    <input type="hidden" name="wastage_total_items" id="wastage_total_items" value="1">
                    </tbody>                    
                </table>
                <div class="col-md-8">
                <a href="javascript:void(0)" class="theme-green bold f-16 text-underline"
                                onclick="return addWastageStockPlan()" style="cursor: pointer;">
                                <span class="mr-2"><img src="{{ url('/assets/admin/images') }}/icons/green_plus.svg"
                                        alt=" view"></span> Add More
                            </a>
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
    <script type="text/javascript">
        var material_id = "";
        var batch_id = "";
        var wastage_batch_options = "";
        // PLAN OPTIONS
        var plan_options = '';
        @if(!empty($materialIds) && sizeof($materialIds) > 0)
        @foreach($materialIds as $val)
        plan_options += `<option value='{{ $val["id"] }}'> {{$val["name"]}} </option>`;
        @endforeach
        @endif
    </script>
    <script type="text/javascript" src="{{ url('assets/admin/js/production/create-edit.js') }}"></script>    
@endsection
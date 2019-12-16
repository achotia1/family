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
        <form id="productionForm" data-toggle="validator" action="{{ route($modulePath.'update', [base64_encode(base64_encode($production->id))]) }}" method="post">
            <input type="hidden" name="_method" value="PUT">                     
            <div class="form-group col-md-8">
                <label class="theme-blue"> 
                Batch Code <span class="required">*</span></label>
                <select class="form-control my-select" 
                        id="batch_id" 
                        name="batch_id"
                        required="" 
                        data-error="Batch Code field is required." 
                        >                    
                    <option value="{{$production->batch_id}}">{{$production->assignedBatch->batch_card_no}} ({{$production->assignedBatch->assignedProduct->code}} - {{$production->assignedBatch->assignedProduct->name}})</option>
                    
                </select>                
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_batch_id"></li>
                    </ul>
                </span>
            </div>
            
           <div class="with-border col-md-12">
          		<h4 class="">Plan Material</h4>
        	</div>
        	<div class="col-md-12">
            	<table class="table mb-0 border-none" id="plan-table">
                    <thead class="theme-bg-blue-light-opacity-15">
                        <tr class="heading-tr">                            
                            <th class="w-160-px">Material Name</th>                            
                            <th class="w-160-px">Material Lot Number</th>
                            <th class="w-160-px">Quantity</th>                            						<th class="w-50-px"></th>
                        </tr>
                        @php
                        $k = 0;
                        @endphp
                        @foreach($production->hasProductionMaterials as $material)
                        <tr class="inner-td add_plan_area plan">
                        <td>
                        <input type="hidden" class="production-id" name="production[{{$k}}][id]" value="{{ $material->id }}" >
                        	<div class="form-group"> 
	                        <select 
	                            class="form-control my-select production_material" 
	                            placeholder="All Materials"
	                            name="production[{{$k}}][material_id]"
	                            id="{{$k}}"	                            
	                            onchange="loadLot(this);"
	                        >	                        	
	                            <option value="{{ $material->mateialName->id }}"  selected >{{ $material->mateialName->name }}</option>
	                        </select>
	                        <span class="help-block with-errors">
	                            <ul class="list-unstyled">
	                                <li class="err_production[{{$k}}][material_id][] err_production_material"></li>
	                            </ul>
	                        </span>
                    </div>
                        </td>
                        @php
                        $balance = $material->hasLot->lot_balance + $material->quantity;
                        @endphp
                        <td>
                        	<div class="form-group"> 
	                        <select 
	                            class="form-control my-select production_lot" 
	                            placeholder="Material Lots"
	                            name="production[{{$k}}][lot_id]"
	                            id="l_{{$k}}"	                            
	                        >	                            
	                            <option data-qty="{{$balance}}" value="{{ $material->hasLot->id }}"  selected >{{ $material->hasLot->lot_no }} ({{$balance}})</option>
	                        </select>
	                        <span class="help-block with-errors">
	                            <ul class="list-unstyled">
	                                <li class="err_production[{{$k}}][lot_id][] err_production_lot"></li>
	                            </ul>
	                        </span>
                    	</div>
                        </td>
                        <td>
                        <div class="add_quantity form-group">
	                        <input 
	                            type="number" 
	                            class="form-control quantity"
	                            name="production[{{$k}}][quantity]"
	                            value="{{$material->quantity}}"
	                            id="q_{{$k}}"
	                            onblur="checkBal(this)"
	                            step="any" 
	                            
	                        >
	                        <span class="help-block with-errors">
	                            <ul class="list-unstyled">
	                                <li class="errq_{{$k}} err_production[{{$k}}][quantity][] err_quantity"></li>
	                            </ul>
                        	</span>
                    	</div>
                        </td>
                        <td>
                        	
                        	<p class="m-0 red bold deletebtn" style="display:block;cursor:pointer" onclick="return deletePlan(this)"  id="${{$k}}" style="cursor:pointer">Remove</p>
                        </td>
                        </tr>
                        @php
                        $k++;
                        @endphp
                        @endforeach
                        <input type="hidden" name="total_items" id="total_items" value="{{$k}}">
                    </thead>
                </table>
                <div class="col-md-8">
            	<a href="javascript:void(0)" class="theme-green bold f-16 text-underline"
                                onclick="return addPlan()" style="cursor: pointer;">
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
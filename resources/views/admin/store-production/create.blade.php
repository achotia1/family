@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
        <form id="productionForm" method="post" data-toggle="validator" action="{{ route($modulePath.'store') }}">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
            </div>
            
            <div class="form-group col-md-12">
                <label class="theme-blue"> 
                Batch Code <span class="required">*</span></label>
                <select class="form-control my-select" 
                        id="batch_id" 
                        name="batch_id"
                        required="" 
                        data-error="Batch Code field is required." 
                        >                    
                    <option value="">Select Batch</option>
                    @foreach($batchNos as $val)
                    <option value="{{$val['id']}}">{{$val['batch_card_no']}}</option>
                    @endforeach
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
            	<table class="table mb-0 border-none ">
                    <thead class="theme-bg-blue-light-opacity-15">
                        <tr>                            
                            <th class="w-160-px">Material Name</th>                            
                            <th class="w-160-px">Material Lot Number</th>
                            <th class="w-160-px">Quantity</th>                            						<th class="w-50-px"></th>
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
                            required
                            step="any" 
                            data-error="Quantity should be number."
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_production[0][quantity][] err_quantity"></li>
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
			
           

                      
            <!--<div class="form-group col-md-12">
                <label class="theme-blue">Status</label>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="status" checked value="1">
                      Active
                    </label>
                </div>  
            </div>-->
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
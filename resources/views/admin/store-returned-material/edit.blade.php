@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">        
        <form id="returnForm" data-toggle="validator" action="{{ route($modulePath.'update', [base64_encode(base64_encode($return_material->id))]) }}" method="post">
            <input type="hidden" name="_method" value="PUT">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue"> 
                Batch Code <span class="required">*</span></label>
                <select class="form-control my-select" id="batch_id" name="batch_id" required="" data-error="Batch Code field is required.">  
                <option value="{{$return_material->batch_id}}">{{ $return_material->hasBatch->batch_card_no }}</option>                  
                   <!--  <option value="">Select Batch</option>
                    @foreach($batchNos as $val){
                    <option value="{{$val['id']}}" @if($return_material->batch_id==$val['id']) selected @endif>{{ $val['batch_card_no']." ".$val['assignedProduct']['code']." (".$val['assignedProduct']['name'].")" }}</option>
                    @endforeach -->
                </select>                
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_batch_id"></li>
                    </ul>
                </span>
            </div>
            
            <div class="form-group col-md-6">
                <label class="theme-blue">Return Date
                    <span class="required">*</span></label>
                <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                <input 
                    type="text"
                    value="{{$freturn_date}}" 
                    name="return_date"                    
                    class="form-control acc_depreciation" 
                    required
                    readonly                                    
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


            <div class="with-border col-md-12">
                <h4 class="">Plan Material</h4>
            </div>
            <div class="col-md-12">
                <table class="table mb-0 border-none ">
                    <thead class="theme-bg-blue-light-opacity-15">
                        <tr>                            
                            <th class="w-160-px">Material Name</th>                            
                            <th class="w-160-px">Material Lot Number</th>
                            <th class="w-160-px">Quantity</th>
                            <th class="w-50-px"></th>
                        </tr>
                    </thead>
                    <tbody class="no-border">
                 @php
                    $k = 0;
                @endphp
                @foreach($return_material->hasReturnedMaterials as $material)
                    <tr class="inner-td add_plan_area plan">                    
                    <td>
                    <div class="form-group"> 
                        <select 
                            class="form-control my-select production_material" 
                            placeholder="All Materials"
                            name="returned[{{$k}}][material_id]"
                            id="material_{{$k}}"
                            
                            onchange="loadLot(this);"
                            data-error="Material Number field is required." 
                        >
                            <!-- <option value="">Select Material</option> -->
                             <option value="{{ $material->material->id }}"  selected >{{ $material->material->name }}</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_returned[{{$k}}][material_id][] err_production_material"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>

                        @php
                            $production_qty=0;
                           // dd($material->lot);
                            if(!empty($material->lot->hasProductionMaterial)){
                                foreach($material->lot->hasProductionMaterial as $production_material)
                                {
                                    if($production_material->lot_id==$material->lot->id)
                                    {
                                        $production_qty=$production_material->quantity;
                                    }
                                }
                            }

                        @endphp

                        <div class="form-group"> 
                        <select 
                            class="form-control my-select production_lot" 
                            placeholder="Material Lots"
                            name="returned[{{$k}}][lot_id]"
                            onchange="setQuantityLimit({{$k}});"
                            id="lot_material_{{$k}}"
                            
                            data-error="Material Lot field is required." 
                        >
                            <!-- <option value="">Select Lot</option> -->
                             <option data-qty="{{ $production_qty }}" value="{{ $material->lot->id }}"  selected >{{ $material->lot->lot_no }} ({{ $production_qty }})</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_returned[{{$k}}][lot_id][] err_production_lot"></li>
                            </ul>
                        </span>
                        </div>
                    </td>
                    <td>
                    <div class="add_quantity form-group">
                        <input 
                            type="number" 
                            class="form-control quantity"
                            name="returned[{{$k}}][quantity]" 
                            id="quantity_{{$k}}"
                            value="{{ $material->quantity }}" 
                            min="1"
                            max="{{$production_qty}}"
                            step="any" 
                            data-error="You can not select more than available quantity: {{$production_qty}}"
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_returned[{{$k}}][quantity][] err_quantity"></li>
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
        var batch_id = "{{ $return_material->batch_id }}";
        var index = "{{ $k }}";
        //var material_id = "{{ $return_material->material_id }}";
    </script>
    <script type="text/javascript" src="{{ url('assets/admin/js/returned-material/create-edit.js') }}"></script>    
@endsection
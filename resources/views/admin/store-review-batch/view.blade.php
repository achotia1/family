@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection

@section('style')
<style>
    .trExpense{
        text-align: center;
        /*color: maroon;*/
        /*background: aliceblue;*/
    }
    .trExpenseTotal{
        text-align: right;
    }
    .trCategory{
        /*background-color: azure;*/
        /*color: darkred;*/
    }
    .trSubCategory{
        width: 30%;
        text-align: right;
    }
    table,tbody ,tr, td {
        border: 1px solid darkgray;
    }
    tbody:first-child{
        border-top: 3px solid darkgray;   
    }
    #l_search_month{
        float: left;
        margin-right: 1%;
    }
    .l_search_month{
        float: left;
        margin-right: 1%;
    }
    #search_month{
        width: 15%;
        display: inline-block;
    }
    #search_state{
        width: 16%;
        display: inline-block;
    }
    #search_year{
        width: 10%;
        display: inline-block;
    }
    .title{
        text-align: center; 
        font-size: 20px;
    }
    .yeild{
        text-align: center; 
        font-size: 20px;
        color: #550000;
        font-weight: bold;
    }
</style>
@endsection
@section('content')
<section class="content">        
    <div class="box">
        <div class="box-body">
        	<div class="table-responsive"  id="tblPrint">
                <table class="table" border="1px;">
                    <tbody>
	                    <tr class="trExpense">
	                        <td colspan="5" class="title"><b>Batch Card Details</b></td>
	                    </tr>
	                    <tr>	                    	
	                    	<td><b>Batch Card :</b></td>
	                    	<td colspan="4">{{$object->batch_card_no}}</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td><b>Unit :</b></td>
	                    	<td colspan="4">{{$object->assignedProduct->name}}</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td><b>Product Code :</b></td>
	                    	<td colspan="4">{{$object->assignedProduct->code}}</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td><b>Planned Material :</b></td>
	                    	<td colspan="4"><span id="planned-material"></span></td>
	                    </tr>
                        @if(!$materials->isEmpty())
	                    <tr>
                            <td colspan="5"></td>
                        </tr>
                        <tr class="trExpense">
                            <td colspan="5" class="title"><b>Raw Material Details</b></td>
                        </tr>

                        <tr>
	                    	<td><b>Sr.No</b></td>
	                    	<td><b>Raw Material Name</b></td>
	                    	<td><b>Final Weight</b></td>
	                    	<td><b>Planned Material</b></td>
	                    	<td><b>Returned Material</b></td>
	                    </tr>
	                    @php $total = $finalWeightTotal = 0;  @endphp
	                    @foreach($materials as $key => $material)
	                    @php
	                    $returnedQty = $finalWeight = 0;
        				$total = $total + $material->quantity;
        				$returnedQty = isset($returnedData[$material->material_id]) ? $returnedData[$material->material_id] : 0;
        				$finalWeight = $material->quantity - $returnedQty;		
        				$finalWeightTotal = $finalWeightTotal + $finalWeight;
    					@endphp
	                    <tr>
	                    	<td>{{$key+1}}</td>
	                    	<td>{{$material->associatedMateials->name}}</td>
	                    	<td>{{$finalWeight}}</td>      	
	                    	<td>{{$material->quantity}}</td>
	                    	<td>{{$returnedQty}}</td>	                    	                  	
	                    </tr>
	                    @endforeach
	                    <tr>
	                    	<td></td>
	                    	<td></td>
	                    	<td><b>{{$finalWeightTotal}}</b></td>
	                    	<td><b><span id="planned-weight">{{$total}}</span></b></td>
	                    	<td></td>
	                    </tr>
	                    
	                    <tr>
                            <td colspan="5"></td>
                        </tr>
                        <tr class="trExpense">
                            <td colspan="5" class="title"><b>Made By Material</b></td>
                        </tr>
                        @php
                        $yeild = round(($finalWeightTotal/$total)*100, 2);
                        @endphp
                        <tr>
                            <td colspan="5" class="yeild">{{$yeild}}%</td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="5" class="yeild">No any material is planned for this Batch.</td>
                        </tr>
                        @endif

                    </tbody>
                </table>
            </div>
            @if(!$materials->isEmpty())
			<form id="reviewBatchForm" action="{{ route($modulePath.'send-to-billing', [base64_encode(base64_encode($object->id))]) }}" method="POST">
			{{ csrf_field() }}
			<input type="hidden" name="id" value="{{$object->id}}">
			<div class="form-group col-md-6">
                <label class="theme-blue">Sell Cost
                    <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="sell_cost" 
                    value="{{$object->sell_cost}}" 
                    class="form-control" 
                    required
                    step="any"                                    
                    data-error="Sell Cost field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_sell_cost"></li>
                    </ul>
	            </span>
            </div>            
            <div class="form-group col-md-6">
                <label class="theme-blue">Send to Sales Department?</label>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="is_reviewed" checked value="yes">
                      Yes
                    </label>
                </div>  
            </div>
            <div class="form-group col-md-12">            
            <button type="submit" class="btn btn-success pull-right">Send</button>
            </div>
           </form> 
		  @endif
        </div>
    </div>
</section>
@endsection
@section('scripts')    
    <script type="text/javascript" src="{{ url('assets/admin/js/review-batch-card/create-edit.js') }}"></script>    

    <script type="text/javascript">
    	$(document).ready(function() {
    		var planned_weight = $('#planned-weight').text();
    		$('#planned-material').text(planned_weight);
    	});
    </script>
@endsection
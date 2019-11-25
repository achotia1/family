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
	                    	<td class="w-90-px"><b>Batch Card :</b></td>
	                    	<td colspan="4">	                    	
	                    	{{$object->assignedPlan->assignedBatch->batch_card_no}}
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td><b>Unit :</b></td>
	                    	<td colspan="4">
	                    	{{$object->assignedPlan->assignedBatch->assignedProduct->name}}	
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td><b>Product Code :</b></td>
	                    	<td colspan="4">
	                    	{{$object->assignedPlan->assignedBatch->assignedProduct->code}}	
	                    	</td>
	                    </tr>	                    
	                    <tr>
                            <td colspan="5"></td>
                        </tr>
                        <tr class="trExpense">
                            <td colspan="5" class="title"><b>Raw Material</b></td>
                        </tr>
                        <tr>
	                    	<td><b>Sr.No</b></td>
	                    	<td><b>Raw Material Name</b></td>
	                    	<td><b>Final Weight</b></td>
	                    	<td><b>Planned Material</b></td>
	                    	<td><b>Returned Material</b></td>                  	
	                    </tr>
	                    @php 
	                    $key = $rawTotal = 0;
	                    $finalTotal = $plannedTotal = $returnedTotal = 0;
	                    @endphp
	                    @foreach($object->assignedPlan->hasProductionMaterials as $material)
	                    @php
	                    
	                    if($material->mateialName->material_type == 'Raw'){
	                    	$key = $key + 1;
	                    	$finalWeight = $material->quantity - $material->returned_quantity;	                    	
	                    	$finalTotal = $finalTotal + $finalWeight;
	                    	
	                    	$plannedTotal = $plannedTotal + $material->quantity;
	                    	$returnedTotal = $returnedTotal + $material->returned_quantity;
	                    	
	                    	$finalWeight = number_format($finalWeight, 2, '.', '')." ".$material->mateialName->unit;
	                    	$planned = number_format($material->quantity, 2, '.', '')." ".$material->mateialName->unit;;
	                    	$returned = number_format($material->returned_quantity, 2, '.', '')." ".$material->mateialName->unit;;
	                    	
	                    @endphp
	                    <tr>
	                    	<td>{{$key}}</td>
	                    	<td>{{$material->mateialName->name}}</td>
	                    	<td>{{$finalWeight}}</td>      	
	                    	<td>{{$planned}}</td>
	                    	<td>{{$returned}}</td>
	                    </tr>
	                    @php
	                    }
	                    @endphp
	                    @endforeach
	                    @php	                    
	                    $finalTotal = number_format($finalTotal, 2, '.', '');
	                    $plannedTotal = number_format($plannedTotal, 2, '.', '');
	                    $returnedTotal = number_format($returnedTotal, 2, '.', '');
	                    @endphp
                        <tr>	                    	
	                    	<td colspan="2"></td>
	                    	<td><b>{{$finalTotal}}</b></td>
	                    	<td><b>{{$plannedTotal}}</b></td>
	                    	<td><b>{{$returnedTotal}}</b></td>
	                    </tr>
	                    <tr>
                            <td colspan="5"></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="title"><b>Made By Material</b></td>
                        </tr>
                        @php
                        $sellableQty = number_format($object->sellable_qty, 2, '.', '');
                        $coursePowder = number_format($object->course_powder, 2, '.', '');
	                    $rejection = number_format($object->rejection, 2, '.', '');
	                    $dustProduct = number_format($object->dust_product, 2, '.', '');	                    
	                    $wasteageWeight = $object->sellable_qty + $object->course_powder + $object->rejection + $object->dust_product;
	                    $lossMaterial = $finalTotal - $wasteageWeight;
            			$yield = ($object->sellable_qty/$finalTotal) * 100;
            			$lossMaterial = number_format($lossMaterial, 2, '.', '');
						$yield = number_format($yield, 2, '.', '');
                        @endphp
                        <tr>	                    	
	                    	<td class="w-90-px"><b>Sellable Quantity :</b></td>
	                    	<td colspan="4">	                    	
	                    	{{$sellableQty}}
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Corse Powder :</b></td>
	                    	<td colspan="4">	                    	
	                    	{{$coursePowder}}
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Rejection :</b></td>
	                    	<td colspan="4">	                    	
	                    	{{$rejection}}
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Dust Product :</b></td>
	                    	<td colspan="4">	                    	
	                    	{{$dustProduct}}
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Loss Material :</b></td>
	                    	<td colspan="4">	                    	
	                    	{{$lossMaterial}}
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Yield :</b></td>
	                    	<td colspan="4">	                    	
	                    	<b>{{$yield}}%</b>
	                    	</td>
	                    </tr>
                    </tbody>
                </table>
            </div>           
        </div>
    </div>
</section>
@endsection
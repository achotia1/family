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
	                        <td colspan="7" class="title"><b>Batch Card Details</b>
	                        <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
	                        </td>
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Batch Card :</b></td>
	                    	<td>	                    	
	                    	{{$object->assignedPlan->assignedBatch->batch_card_no}}
	                    	</td>
	                    	<td class="w-90-px"><b>Product :</b></td>
	                    	<td colspan="4">	                    	
	                    	{{$object->assignedPlan->assignedBatch->assignedProduct->code}} ({{$object->assignedPlan->assignedBatch->assignedProduct->name}})	
	                    	</td>
	                    </tr>	                                     
	                    <tr>
                            <td colspan="7"></td>
                        </tr>
                        <tr class="trExpense">
                            <td colspan="7" class="title"><b>Raw Material</b></td>
                        </tr>
                        <tr>
	                    	<td><b>Sr.No</b></td>
	                    	<td><b>Raw Material Name</b></td>
	                    	<td><b>Price Per Unit (INR)</b></td>
	                    	<td><b>Final Weight</b></td>
	                    	<td><b>Planned Material</b></td>
	                    	<td><b>Returned Material</b></td>
	                    	<td><b>Amount (INR)</b></td>                 	
	                    </tr>
	                    @php 
	                    $key = $rawTotal = 0;
	                    $otherMaterial = array();  
	                    $i = 1;
	                    $finalTotal = $plannedTotal = $returnedTotal = $amountTotal = 0;
	                    @endphp
	                    @foreach($object->assignedPlan->hasProductionMaterials as $material)
	                    @php	                    
	                    if($material->mateialName->material_type == 'Raw'){
	                    	$key = $key + 1;
	                    	$returned = 0;
	                    	if(isset($object->assignedPlan->hasReturnMaterial->hasReturnedMaterials))
	                    	{
								
							
		                    	foreach($object->assignedPlan->hasReturnMaterial->hasReturnedMaterials as $returnedMaterial){
									if( $material->lot_id == $returnedMaterial->lot_id)
										$returned = $returnedMaterial->quantity;								}
							}
	                    	$finalWeight = $material->quantity - $returned;	                    	
	                    	$finalTotal = $finalTotal + $finalWeight;
	                    	
	                    	$plannedTotal = $plannedTotal + $material->quantity;
	                    	$returnedTotal = $returnedTotal + $returned;
	                    	$returned =  number_format($returned, 2, '.', '');
	                    	$amount = ($finalWeight * $material->hasLot->price_per_unit);
	                    	$amountTotal = $amountTotal + $amount;
	                    	$finalWeight = number_format($finalWeight, 2, '.', '')." ".$material->mateialName->unit;
	                    	$planned = number_format($material->quantity, 2, '.', '')." ".$material->mateialName->unit;
	                    	$pricePerUnit = number_format($material->hasLot->price_per_unit, 2, '.', '');
	                    	$formattedAmount =  number_format($amount, 2, '.', '');
	                    	
							
	                    	
	                    @endphp
	                    <tr>
	                    	<td>{{$key}}</td>
	                    	<td>{{$material->mateialName->name}} ({{$material->hasLot->lot_no}})</td>
	                    	<td class="text-right">{{$pricePerUnit}}</td>
	                    	<td class="text-right">{{$finalWeight}}</td>      	
	                    	<td class="text-right">{{$planned}}</td>
	                    	<td class="text-right">{{$returned}}</td>	                    	
	                    	<td class="text-right">{{$formattedAmount}}</td>
	                    </tr>
	                    @php
	                    } else {
							$preturned = 0;
	                    	if(isset($object->assignedPlan->hasReturnMaterial->hasReturnedMaterials))
	                    	{
								
							
		                    	foreach($object->assignedPlan->hasReturnMaterial->hasReturnedMaterials as $returnedMaterial){
									if( $material->lot_id == $returnedMaterial->lot_id)
										$preturned = $returnedMaterial->quantity;									}
							}
							
							$pfinalWeight = $material->quantity - $preturned;
							
							$pamount = ($pfinalWeight * $material->hasLot->price_per_unit);							
							$otherMaterial[$i]['name'] = $material->mateialName->name;
							$otherMaterial[$i]['lot_no'] = $material->hasLot->lot_no;
							$otherMaterial[$i]['quantity'] = number_format($material->quantity, 2, '.', '');
							$otherMaterial[$i]['returned_quantity'] = number_format($preturned, 2, '.', '');
							$otherMaterial[$i]['final_weight'] = number_format($pfinalWeight, 2, '.', '');
							$otherMaterial[$i]['pamount'] = number_format($pamount, 2, '.', '');
							$otherMaterial[$i]['price_per_unit'] = number_format($material->hasLot->price_per_unit, 2, '.', '');							
							
							$i++;
						}
	                    @endphp
	                    @endforeach
	                    @php	                    
	                    $finalTotal = number_format($finalTotal, 2, '.', '');
	                    $plannedTotal = number_format($plannedTotal, 2, '.', '');
	                    $returnedTotal = number_format($returnedTotal, 2, '.', '');
	                    $amountTotal = number_format($amountTotal, 2, '.', '');
	                    @endphp
                        <tr>	                    	
	                    	<td colspan="3"></td>
	                    	<td class="text-right"><b>{{$finalTotal}} Kg</b></td>
	                    	<td class="text-right"><b>{{$plannedTotal}} Kg</b></td>
	                    	<td class="text-right"><b>{{$returnedTotal}}</b></td>
	                    	<td class="text-right"><b>{{$amountTotal}}</b></td>
	                    </tr>

	                    @php 
	                    $pamountTotal = 0;                       
                        if(!empty($otherMaterial)){
                        @endphp
	                    <tr>
                            <td colspan="7"></td>
                        </tr>
                        
                        <tr class="trExpense">
                            <td colspan="7" class="title"><b>Planned Packaging Material</b></td>             </tr>
                        @php 
	                    $packTotal = $packFinalTotal = $returnedTotal = 0;
	                    
	                    @endphp
                        @foreach($otherMaterial as $oKey=>$oMaterial)
                        @php
                        $packTotal = $packTotal + $oMaterial['quantity'];
                        $returnedTotal = $returnedTotal + $oMaterial['returned_quantity'];
                        $packFinalTotal = $packFinalTotal + $oMaterial['final_weight'];
                        $pamountTotal = $pamountTotal + $oMaterial['pamount'];
                        @endphp
                        <tr>
	                    	<td>{{$oKey}}</td>
	                    	<td>{{$oMaterial['name']}} ({{$oMaterial['lot_no']}})</td>
	                    	<td class="text-right">{{$oMaterial['price_per_unit']}}</td>
	                    	<td class="text-right">{{$oMaterial['final_weight']}}</td>     	
	                    	<td class="text-right">{{$oMaterial['quantity']}}</td>
	                    	<td class="text-right">{{$oMaterial['returned_quantity']}}</td>
	                    	<td class="text-right">{{$oMaterial['pamount']}}</td>
	                    </tr>
                        @endforeach
                        @php
                        $packFinalTotal = number_format($packFinalTotal, 2, '.', '');
                        $packTotal = number_format($packTotal, 2, '.', '');
                        $returnedTotal = number_format($returnedTotal, 2, '.', '');
                        $pamountTotal = number_format($pamountTotal, 2, '.', '');
                        @endphp
                        <tr>	                    	
	                    	<td colspan="3"></td>
	                    	<td class="text-right"><b>{{$packFinalTotal}} Kg</b></td>
	                    	<td class="text-right"><b>{{$packTotal}} Kg</b></td>
	                    	<td class="text-right"><b>{{$returnedTotal}}</b></td>                    	
	                    	<td class="text-right"><b>{{$pamountTotal}}</b></td>
	                    </tr>
	                    @php
	                    }
	                    @endphp
	                    <tr>
                            <td colspan="7"></td>
                        </tr>
                        <tr>
                            <td colspan="7" class="title"><b>Made By Material</b></td>
                        </tr>
                        @php
                        
                        $sellableQty = number_format($object->sellable_qty, 2, '.', '');
                        $cost_per_unit = ($amountTotal + $pamountTotal)/$sellableQty;
                        $cost_per_unit = number_format($cost_per_unit, 2, '.', '');
                        $coursePowder = number_format($object->course_powder, 2, '.', '');
	                    $rejection = number_format($object->rejection, 2, '.', '');
	                    $dustProduct = number_format($object->dust_product, 2, '.', '');	                    $looseProduct = number_format($object->loose_material, 2, '.', '');
	                    
	                    $wasteageWeight = $object->sellable_qty + $object->course_powder + $object->rejection + $object->dust_product + $object->loose_material;
	                    $lossMaterial = $finalTotal - $wasteageWeight;
            			$lossPer = ($lossMaterial/$finalTotal) * 100;
						$lossPer = number_format($lossPer, 2, '.', '');
						
            			$yield = ($object->sellable_qty/$finalTotal) * 100;
            			$lossMaterial = number_format($lossMaterial, 2, '.', '');
						$yield = number_format($yield, 2, '.', '');
						
						$coursePer = ($object->course_powder/$finalTotal) * 100;
						$coursePer = number_format($coursePer, 2, '.', '');
						$rejectionPer = ($object->rejection/$finalTotal) * 100;
						$rejectionPer = number_format($rejectionPer, 2, '.', '');
						$dustPer = ($object->dust_product/$finalTotal) * 100;
						$dustPer = number_format($dustPer, 2, '.', '');
						$loosePer = ($object->loose_material/$finalTotal) * 100;
						$loosePer = number_format($loosePer, 2, '.', '');
                        @endphp
                        <tr>	                    	
	                    	<td class="w-90-px"><b>Sellable Quantity :</b></td>
	                    	<td class="text-green">	                    	
	                    	<h4><b>{{$sellableQty}}</b></h4>
	                    	</td>
	                    	<td>	                    	
	                    	<b>Yield :</b>
	                    	</td>
	                    	<td colspan="4" class="text-green">
	                    	<h4><b>{{$yield}}%</b></h4>
	                    	</td>
	                    	
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Corse Powder :</b></td>
	                    	<td>	                    	
	                    	{{$coursePowder}}
	                    	</td>
	                    	<td>	                    	
	                    	<b>Course  powder Percentage:</b>
	                    	</td>
	                    	<td colspan="4">
	                    	{{$coursePer}}%
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Rejection :</b></td>
	                    	<td>	                    	
	                    	{{$rejection}}
	                    	</td>
	                    	<td>	                    	
	                    	<b>Rejection Percentage:</b>
	                    	</td>
	                    	<td colspan="4">
	                    	{{$rejectionPer}}%
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Dust Product :</b></td>
	                    	<td>	                    	
	                    	{{$dustProduct}}
	                    	</td>
	                    	<td>	                    	
	                    	<b>Dust Product Percentage:</b>
	                    	</td>
	                    	<td colspan="4">
	                    	{{$dustPer}}%
	                    	</td>
	                    </tr>
	                     <tr>	                    	
	                    	<td class="w-90-px"><b>Loose Material :</b></td>
	                    	<td>	                    	
	                    	{{$looseProduct}}
	                    	</td>
	                    	<td>	                    	
	                    	<b>Loose Material Percentage:</b>
	                    	</td>
	                    	<td colspan="4">
	                    	{{$loosePer}}%
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Loss Material :</b></td>
	                    	<td>	                    	
	                    	{{$lossMaterial}}
	                    	</td>
	                    	<td>	                    	
	                    	<b>Loss Material Percentage:</b>
	                    	</td>
	                    	<td colspan="4">
	                    	{{$lossPer}}%
	                    	</td>
	                    </tr>
	                    <tr>
	                    	<td colspan="7" class="text-aqua"><b>Manufacturing Cost Per Unit : {{$cost_per_unit}}</b> </td>     </tr>	                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')    
    <script type="text/javascript" src="{{ url('assets/admin/js/materials-out/view.js') }}"></script>
@endsection
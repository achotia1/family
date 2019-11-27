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
	                        <td colspan="5" class="title"><b>Batch Card Details</b>
	                        <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
	                        </td>
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
	                    $otherMaterial = array();  
	                    $i = 1;
	                    $finalTotal = $plannedTotal = $returnedTotal = 0;
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
	                    	
	                    	$finalWeight = number_format($finalWeight, 2, '.', '')." ".$material->mateialName->unit;
	                    	$planned = number_format($material->quantity, 2, '.', '')." ".$material->mateialName->unit;
	                    	
							
	                    	
	                    @endphp
	                    <tr>
	                    	<td>{{$key}}</td>
	                    	<td>{{$material->mateialName->name}}</td>
	                    	<td>{{$finalWeight}}</td>      	
	                    	<td>{{$planned}}</td>
	                    	<td>{{$returned}}</td>
	                    </tr>
	                    @php
	                    } else {
							$otherMaterial[$i]['name'] = $material->mateialName->name;
							$otherMaterial[$i]['lot_no'] = $material->hasLot->lot_no;
							$otherMaterial[$i]['quantity'] = $material->quantity;
							$otherMaterial[$i]['returned_quantity'] = $material->returned_quantity;
							$i++;
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

	                    @php                        
                        if(!empty($otherMaterial)){
                        @endphp
	                    <tr>
                            <td colspan="5"></td>
                        </tr>
                        
                        <tr class="trExpense">
                            <td colspan="5" class="title"><b>Planned Packaging Material</b></td>             </tr>
                        @php 
	                    $packTotal = 0;
	                    $returnedTotal = 0;
	                    @endphp
                        @foreach($otherMaterial as $oKey=>$oMaterial)
                        @php
                        $packTotal = $packTotal + $oMaterial['quantity'];
                        $returnedTotal = $returnedTotal + $oMaterial['returned_quantity'];
                        @endphp
                        <tr>
	                    	<td>{{$oKey}}</td>
	                    	<td>{{$oMaterial['name']}}</td>
	                    	<td>{{$oMaterial['lot_no']}}</td>      	
	                    	<td>{{$oMaterial['quantity']}}</td>
	                    	<td>{{$oMaterial['returned_quantity']}}</td>
	                    </tr>
                        @endforeach
                        <tr>	                    	
	                    	<td colspan="3"></td>
	                    	<td><b><span id="planned-pweight">{{$packTotal}}</span></b></td>	                    	
	                    	<td><b><span id="planned-pweight">{{$returnedTotal}}</span></b></td>	                    	
	                    </tr>
	                    @php
	                    }
	                    @endphp  






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
                        @endphp
                        <tr>	                    	
	                    	<td class="w-90-px"><b>Sellable Quantity :</b></td>
	                    	<td>	                    	
	                    	{{$sellableQty}}
	                    	</td>
	                    	<td style="color: green">	                    	
	                    	<b>Yield :</b>
	                    	</td>
	                    	<td colspan="2" style="color: green">
	                    	<b>{{$yield}}%</b>
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
	                    	<td colspan="2">
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
	                    	<td colspan="2">
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
	                    	<td colspan="2">
	                    	{{$dustPer}}%
	                    	</td>
	                    </tr>
	                    <tr>	                    	
	                    	<td class="w-90-px"><b>Loss Material :</b></td>
	                    	<td>	                    	
	                    	{{$lossMaterial}}
	                    	</td>
	                    	<td style="color: red">	                    	
	                    	<b>Loss Material Percentage:</b>
	                    	</td>
	                    	<td colspan="2" style="color: red">
	                    	<b>{{$lossPer}}%</b>
	                    	</td>
	                    </tr>	                    
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
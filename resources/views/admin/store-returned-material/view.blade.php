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
                            <td colspan="4" class="title"><b>Batch Card Details</b>
                            <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
                            </td>

                        </tr>
                        <tr>                            
                            <td class="w-90-px"><b>Batch Card :</b></td>
                            <td colspan="3">                            
                            {{$object->assignedProductionPlan->assignedBatch->batch_card_no??""}}
                            </td>
                        </tr>
                        <tr>                            
                            <td><b>Unit :</b></td>
                            <td colspan="3">
                                {{$object->assignedProductionPlan->assignedBatch->assignedProduct->name}}
                            </td>
                        </tr>
                        <tr>                            
                            <td><b>Product Code :</b></td>
                            <td colspan="3">
                                {{$object->assignedProductionPlan->assignedBatch->assignedProduct->code}}
                            </td>
                        </tr>
                        <tr>                            
                            <td><b>Total Returned Raw Material :</b></td>
                            <td colspan="3"><span id="planned-material"></span></td>
                        </tr>
                        <tr class="cls-pmaterial">                          
                            <td><b>Total Packaging Material :</b></td>
                            <td colspan="3"><span id="planned-pmaterial"></span></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr class="trExpense">
                            <td colspan="4" class="title"><b>Returned Raw Material</b></td>
                        </tr>
                        <tr>
                            <td><b>Sr.No</b></td>
                            <td><b>Raw Material Name</b></td>
                            <td><b>Material Lot</b></td>
                            <td><b>Quantity</b></td>                            
                        </tr>
                        @php 
                        $key = $rawTotal = 0;
                        $i = 1;
                        $otherMaterial = array();
                        @endphp
                        @foreach($object->hasReturnedMaterials as $material)
                        @php
                        if($material->material->material_type == 'Raw'){
                        $key = $key + 1;
                        $rawTotal = $rawTotal + $material->quantity;
                        @endphp
                        <tr>
                            <td>{{$key}}</td>
                            <td>{{$material->material->name}}</td>
                            <td>{{$material->lot->lot_no}}</td>          
                            <td>{{$material->quantity}}</td>
                        </tr>
                        @php
                        } else {
                            $otherMaterial[$i]['name'] = $material->material->name;
                            $otherMaterial[$i]['lot_no'] = $material->lot->lot_no;
                            $otherMaterial[$i]['quantity'] = $material->quantity;
                        $i++;
                        }
                        @endphp
                        @endforeach                     
                        <tr>                            
                            <td colspan="3"></td>
                            <td><b><span id="planned-weight">{{$rawTotal}}</span></b></td>                          
                        </tr>
                        @php                        
                        if(!empty($otherMaterial) && count($otherMaterial)>0){
                        @endphp
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        
                        <tr class="trExpense">
                            <td colspan="4" class="title"><b>Returned Packaging Material</b></td>             </tr>
                        @php 
                        $packTotal = 0;
                        @endphp
                        @foreach($otherMaterial as $oKey=>$oMaterial)
                        @php
                        $packTotal = $packTotal + $oMaterial['quantity'];
                        @endphp
                        <tr>
                            <td>{{$oKey}}</td>
                            <td>{{$oMaterial['name']}}</td>
                            <td>{{$oMaterial['lot_no']}}</td>       
                            <td>{{$oMaterial['quantity']}}</td>
                        </tr>
                        @endforeach
                        <tr>                            
                            <td colspan="3"></td>
                            <td><b><span id="planned-pweight">{{$packTotal}}</span></b></td>                            
                        </tr>
                        @php
                        }
                        @endphp  
                    </tbody>
                </table>
            </div>           
        </div>
    </div>
</section>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var planned_weight = $('#planned-weight').text();
            $('#planned-material').text(planned_weight+" kg");
            @if(!empty($otherMaterial))
                $('#planned-pmaterial').text($('#planned-pweight').text());
            @else
                $('table tr.cls-pmaterial').remove();   
            @endif
        });
    </script>
@endsection
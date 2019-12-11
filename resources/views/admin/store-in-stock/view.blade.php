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
	                        <td colspan="4" class="title"><b>Stock Details</b>
	                        <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
	                        </td>
	                    </tr>
	                   	<tr>	                    	
	                    	<td><b>Batch Code :</b></td>
	                    	<td>{{$object->assignedBatch->batch_card_no}}</td>
	                    	<td><b>Product Code:</b></td>
	                    	<td>{{$object->assignedProduct->code}} ({{$object->assignedProduct->name}})</td>
	                    	
	                    </tr>
	                    @php
	                    $quantity = number_format($object->quantity, 2, '.', '');
	                    $balanceQuantity = number_format($object->balance_quantity, 2, '.', '');
	                    $manufacturingCost = number_format($object->manufacturing_cost, 2, '.', '');
	                    $createdAt = date('d M Y', strtotime($object->created_at));
	                    @endphp
	                    <tr>	                    	
	                    	<td><b>Quantity :</b></td>
	                    	<td>{{$quantity}}</td>
	                    	<td><b>Balance Stock</b></td>
	                    	<td>{{$balanceQuantity}}</td>
	                    	
	                    </tr>
	                    <tr>	                    	
	                    	<td><b>Manufacturing Cost :</b></td>
	                    	<td>{{$manufacturingCost}}</td>
	                    	<td><b>Created At:</b></td>
	                    	<td>{{$createdAt}}</td>
	                    	
	                    </tr>                   
                    </tbody>
                </table>
            </div>
                 
                
        </div>
    </div>
</section>
@endsection

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
                            <td colspan="6" class="title"><b>Sale Invoice</b>
                            <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
                            </td>

                        </tr>
                        <tr>                            
                            <td class="w-90-px"><b>Invoice Number:</b></td>
                            <td colspan="2">                            
                            {{$object->invoice_no??""}}
                            </td>

                            <td><b>Invoice Date:</b></td>
                            <td colspan="2">
                                {{ date('d-m-Y', strtotime($object->invoice_date))}}
                            </td>
                        </tr>
                        <tr>                            
                            <td><b>Customer Name:</b></td>
                            <td colspan="5">
                                {{$object->hasCustomer->contact_name}} ({{$object->hasCustomer->company_name}})
                            </td>
                        </tr>
                        <!-- <tr>                            
                            <td><b>Total Returned Raw Material :</b></td>
                            <td colspan="3"><span id="planned-material"></span></td>
                        </tr>
                        <tr class="cls-pmaterial">                          
                            <td><b>Total Packaging Material :</b></td>
                            <td colspan="3"><span id="planned-pmaterial"></span></td>
                        </tr> -->
                        <tr>
                            <td colspan="6"></td>
                        </tr>
                        <tr class="trExpense">
                            <td colspan="6" class="title"><b>Product Details</b></td>
                        </tr>
                        <tr>
                            <td><b>Sr.No</b></td>
                            <td><b>Product Name</b></td>
                            <td><b>Batch Code</b></td>
                            <td><b>Quantity</b></td>                            
                            <td><b>Rate</b></td>                            
                            <td><b>Amount</b></td>                            
                        </tr>
                        @php
                            $index=1;
                            $pId=[];
                            $total_qty=0;
                            $total_amount=0;
                        @endphp
                        @foreach($productBatch_data as $key=>$products)
                            @foreach($products as $product)
                                @php
                                    $total_qty=$total_qty+$product->quantity;
                                    $total_amount=$total_amount+$product->total_basic;
                                @endphp 
                            <tr>
                                @if(!in_array($product->product_id,$pId))
                                    @php
                                        $pId[]=$product->product_id;
                                    @endphp 
                                <td>{{ $index }}</td>
                                <td>{{ $product->assignedProduct->name }} ({{ $product->assignedProduct->code }})</td>
                                @else
                                <td></td>
                                <td></td>
                                @endif
                                <td>{{ $product->assignedBatch->batch_card_no }}</td>          
                                <td>{{ number_format($product->quantity,2) }} kg</td>          
                                <td>{{ number_format($product->rate,2) }}</td>
                                <td>{{ number_format($product->total_basic,2) }}</td>
                            </tr>
                            @endforeach 
                        @php
                            $index++;
                        @endphp
                        @endforeach 
                        <tr>
                             <td colspan="3" align="right"><strong>Total</strong></td>
                             <td colspan="2"><strong> {{ number_format($total_qty,2) }} kg </strong></td>
                             
                             <td><strong> {{ number_format($total_amount,2) }} </strong></td>
                        </tr>
                       
                    </tbody>
                </table>
            </div>           
        </div>
    </div>
</section>
@endsection
@section('scripts')
    <script type="text/javascript">
        //$(document).ready(function() {
            // var planned_weight = $('#planned-weight').text();
            // $('#planned-material').text(planned_weight+" kg");
            // @if(!empty($otherMaterial))
            //     $('#planned-pmaterial').text($('#planned-pweight').text());
            // @else
            //     $('table tr.cls-pmaterial').remove();   
            // @endif
        //});
    </script>
@endsection
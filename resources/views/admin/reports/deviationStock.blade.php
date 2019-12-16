@extends('admin.layout.master')

@section('title')
{{ $moduleTitle }}
@endsection

@section('style')
<style>
    .interval-lable{
        float: left;
        margin-right: 1%;
        font-size: 16px !important;
        font-weight: normal !important;
        margin-top: 10px !important;
    }
     .l-interval{
        float: left;
        margin-right: 1%;
        margin-top: 10px !important;
    }
    #interval-time{
        width: 15%;
        display: inline-block;        
    }
    .ltime-interval{
        /*width: 18%;*/
        display: inline-block;        
    }
    .stime-interval{
        /*width: 3%;*/
        display: inline-block;        
    }
</style>
@endsection

@section('content')


<section class="content">
    <div class="box">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <label class="theme-blue f-14">From Date</label>
                    <input id="from-date" type="text" class="form-control">
                 </div>   
                 <div class="col-md-3">
                     <label class="theme-blue f-14">To Date</label>
                     <input id="to-date" type="text" class="form-control">
                </div>
                 <div class="col-md-3">
                     <label class="theme-blue">Product</label>
                    <select class="form-control select2" 
                         id="product-id"
                         name="product_id" 
                        >                    
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->code }} ({{$product->name}})</option>
                        @endforeach
                    </select>  
                </div>
                 <div class="col-md-3" style="margin-top: 24px !important;">
                    <button type="button" class="btn btn-primary" onclick="doSearch(this)">Search</button>
                </div>
            </div>
        </div>   
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>                        
                        <th class="w-10">Batch Code</th>
                        <th class="w-20">Product</th>                        
                        <th class="w-5">Last Corrected Date</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
@section('scripts')

<script type="text/javascript" src="{{ url('assets/admin/js/reports/deviationStock.js') }}"></script>

@endsection

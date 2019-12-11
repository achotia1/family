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
        <div class="box-header align-right">
        	<span class="ltime-interval interval-lable">Product not used for more than : </span>
        	<input id="interval-time" type="number" class="form-control l-interval">
        	
        	<span class="interval-lable stime-interval"> days </span>
        	<button type="button" class="btn btn-primary l-interval" onclick="doSearch(this)">Search</button>
        </div>    
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>                        
                        <th class="w-10">Batch Name</th>
                        <th class="w-20">Product</th>                        
                        <th class="w-5">Stock Balance</th>
                        <th class="w-10">Last Used Date</th>
                        <th class="w-10">Stock In Date</th>
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

<script type="text/javascript" src="{{ url('assets/admin/js/reports/agedProduct.js') }}"></script>

@endsection

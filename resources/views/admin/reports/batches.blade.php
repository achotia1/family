@extends('admin.layout.master')

@section('title')
{{ $moduleTitle }}
@endsection

@section('styles')

@endsection

@section('content')
<style type="text/css">
    
    /*.w-110 {
    width: 110px !important;
}*/
</style>

<section class="content">
    <div class="box">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4">
                    <label class="theme-blue f-14">From Date</label>
                    <input id="from-date" type="text" class="form-control">
                 </div>   
                 <div class="col-md-4">
                     <label class="theme-blue f-14">To Date</label>
                     <input id="to-date" type="text" class="form-control">
                </div>
                 <div class="col-md-4" style="margin-top: 24px !important;">
                    <button type="button" class="btn btn-primary" onclick="doSearch(this)">Search</button>
                </div>
            </div>
        </div>
            
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>
                        <!-- <th class="w-10">Select</th> -->
                        <th class="w-10">Batch Code</th>
                        <th class="w-20">Product</th>                        
                        <th class="w-5">Sellable Quantity</th>
                        <th class="w-10">Loss Material</th>
                        <th class="w-10">Yield</th>                        
                       <!--  <th class="w-20">Actions</th> -->
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

<script type="text/javascript" src="{{ url('assets/admin/js/reports/batches.js') }}"></script>

@endsection

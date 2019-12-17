@extends('admin.layout.master')

@section('title')
{{ $moduleTitle }}
@endsection

@section('style')
<style>
</style>
@endsection

@section('content')


<section class="content">
    <div class="box">
    	<div class="box-header with-border">              
        	<button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
        </div>
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>                        
                        <th class="w-10">Batch No</th>
                        <th class="w-20">Previous Balance</th>                        
                        <th class="w-20">Corrected Balance</th>                        
                        <th class="w-5">Correction Date</th>
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
<script type="text/javascript">
    var stockId = "{{ $stockId }}";
</script>
<script type="text/javascript" src="{{ url('assets/admin/js/reports/deviationStockHistory.js') }}"></script>

@endsection

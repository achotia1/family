@extends('admin.layout.master')

@section('title')
{{ $moduleTitle }}
@endsection

@section('style')
<style>
.batch-closed{	
	color:#5EC43C;
	font-weight: 600;	
}
</style>
@endsection

@section('content')

<section class="content">
    <div class="box">
        
        <div class="box-header align-right">     
        @can('store-material-output-add')             
            <a href="{{ route($modulePath.'create') }}" class="btn btn-primary pull-right" >Add Out Material</a>            
            <a href="javascript:void(0)" class="btn btn-danger" onclick="return deleteCollections(this)">Delete Selected</a>
        @endcan
        </div>
        
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>
                        <th class="w-10">Select</th>
                        <th class="w-10">Batch Code</th>
                        <th class="w-20">Product</th>                        
                        <th class="w-5">Sellable Quantity</th>
                        <th class="w-10">Loss Material</th>
                        <th class="w-10">Yield</th>
                        <th>Batch Status</th>                        
                        <th class="w-20">Actions</th>
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

<script type="text/javascript" src="{{ url('assets/admin/js/materials-out/index.js') }}"></script>

@endsection

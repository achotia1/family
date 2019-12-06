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
.no-planned{	
	color:#1f7cef;
}

</style>
@endsection

@section('content')

<section class="content">
    <div class="box">
        
        <div class="box-header align-right"> 
        @can('store-batches-add')              
            <a href="{{ route($modulePath.'create') }}" class="btn btn-primary pull-right" >Add Batch</a>            
            <a href="javascript:void(0)" class="btn btn-danger" onclick="return deleteCollections(this)">Delete Selected</a>
        @endcan
        </div>
        
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>
                        <th class="w-90-px">Select</th>
                        <th>Product Code</th>
                        <th class="w-100-px">Batch Code</th>
                        <th class="w-100-px">Batch Quantity</th>
                        <th class="w-100-px">Is Plan Added?</th>                        						<th class="w-100-px">Status</th>
                        <th class="w-180-px">Actions</th>                        
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

<script type="text/javascript" src="{{ url('assets/admin/js/rms-store/index.js') }}"></script>

@endsection

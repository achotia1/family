@extends('admin.layout.master')

@section('title')
{{ $moduleTitle }}
@endsection

@section('style')
<style>
.batch-closed{	
	color:#41882a;
	font-weight: 600;	
}
</style>
@endsection
@section('content')

<section class="content">
    <div class="box">
        <div class="box-header align-right">           
            @can('store-returned-material-add')
            <a href="{{ route($modulePath.'create') }}" class="btn btn-primary pull-right" >Add Returned Material</a>            
            <!-- <a href="javascript:void(0)" class="btn btn-danger" onclick="return deleteCollections(this)">Delete Selected</a> -->
            @endcan
        </div>
        
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>
                        <!-- <th class="w-90-px">Select</th> -->
                        <th class="w-100-px">Batch Number</th>
                        <th class="w-100-px">Product</th>
                        <th class="w-100-px">Returned Date</th>
                        <th class="w-100-px">Batch Status</th>              
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

<script type="text/javascript" src="{{ url('assets/admin/js/returned-material/index.js') }}"></script>

@endsection

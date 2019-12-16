@extends('admin.layout.master')

@section('title')
{{ $moduleTitle }}
@endsection

@section('style')
<style>
.batch-closed{	
	color:#41882a;
	font-weight: 500;	
}
</style>
@endsection
@section('content')

<section class="content">
    <div class="box">
        
        <div class="box-header align-right">           
            <!--<a href="{{ route($modulePath.'create') }}" class="btn btn-primary pull-right" >Add Plan</a>-->            
            <!--<a href="javascript:void(0)" class="btn btn-danger" onclick="return deleteCollections(this)">Delete Selected</a>-->
        </div>
        
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>                        
                        <th class="w-160-px">Batch Code</th>
                        <th class="w-160-px">Product</th>
                        <th class="w-100-px">Course Stock</th>
                        <th class="w-100-px">Rejection Stock</th>
                        <th class="w-100-px">Dust Stock</th>
                        <th class="w-100-px">Loose Stock</th>                        
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

<script type="text/javascript" src="{{ url('assets/admin/js/store-waste-stock/index.js') }}"></script>

@endsection

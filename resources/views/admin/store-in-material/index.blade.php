@extends('admin.layout.master')

@section('title')
{{ $moduleTitle }}
@endsection

@section('styles')
@endsection

@section('content')

<section class="content">
    <div class="box">
        
        <div class="box-header align-right">    
        @can('store-material-in-add')         
            <a href="{{ route($modulePath.'create') }}" class="btn btn-primary pull-right" >Add In Material</a>            
            <a href="javascript:void(0)" class="btn btn-danger" onclick="return deleteCollections(this)">Delete Selected</a>
        @endcan
        </div>
        
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>
                        <th class="w-90-px">Select</th>
                        <th class="w-100-px">Lot Number</th>
                        <th class="w-60-px">Material Name</th>                        
                        <th class="w-60-px">Lot Quantity</th>
                        <th class="w-100-px">Lot Balance</th>
                        <th class="w-100-px">Status</th>                        
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

<script type="text/javascript" src="{{ url('assets/admin/js/materials-in/index.js') }}"></script>

@endsection

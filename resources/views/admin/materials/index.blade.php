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
            <a href="{{ route($modulePath.'create') }}" class="btn btn-primary pull-right" >Add Raw Material</a>            
            <a href="javascript:void(0)" class="btn btn-danger" onclick="return deleteCollections(this)">Delete Selected</a>
        </div>
        
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>
                        <th class="w-90-px">Select</th>
                        <th>Material</th>
                        <th class="w-100-px">Total Quantity</th>
                        <th class="w-100-px">Price Per Unit</th>
                        <th class="w-100-px">Trigger Quantity</th>
                        <th class="w-100-px">Opening Stock</th>
                        <th class="w-100-px">Received Quantity</th>
                        <th class="w-100-px">Issued Quantity</th>
                        <th class="w-100-px">Return Quantity</th>
                        <th class="w-100-px">Balance Quantity</th>
                        <th class="w-100-px">MOQ</th>
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

<script type="text/javascript" src="{{ url('assets/admin/js/materials/index.js') }}"></script>

@endsection

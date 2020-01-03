@extends('admin.layout.master')

@section('title')
{{ $moduleTitle }}
@endsection

@section('styles')

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
                     <label class="theme-blue">Material</label>
                    <select class="form-control select2" 
                         id="material-id"
                         name="material_id" 
                        >                    
                        <option value="">Select Material</option>
                        @foreach($materials as $material)
                            <option value="{{ $material->id }}">{{ $material->name }}</option>
                        @endforeach
                    </select>  
                </div>
                 <div class="col-md-3" style="margin-top: 36px !important;">
                    <button type="button" class="btn btn-primary" onclick="doSearch(this)">Search</button>
                </div>
            </div>
        </div>
            
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>                        
                        <th>Raw material</th>
                        <th>Units</th>                        
                        <th class="w-10">Opening Stock</th>
                        <th>Recived Quantity</th>
                        <th>Issued Quantity</th>
                        <th>Return Quantity</th>
                        <th >Balance Quantity</th>
                        <th >MOQ</th>
                        <th >Status</th>                        
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
<script type="text/javascript" src="{{ url('assets/admin/js/reports/rawMaterial.js') }}"></script>
@endsection

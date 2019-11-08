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
           
        </div>
        
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>
                        <th class="w-90-px">Select</th>
                        <th>Product Code</th>
                        <th class="w-100-px">Batch Card Number</th>
                        <th class="w-100-px">Batch Quantity</th>
                        <th class="w-100-px">Review Status</th>                        
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

<script type="text/javascript" src="{{ url('assets/admin/js/review-batch-card/index.js') }}"></script>

@endsection

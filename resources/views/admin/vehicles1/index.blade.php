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
            <a href="{{ route($modulePath.'create') }}" class="btn btn-primary pull-right" >Add Vehicle</a>
            <a href="javascript:void(0)" class="btn btn-danger" onclick="return deleteCollections(this)"
                              >Delete Selected</a>
        </div>
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="display: none"></th>
                        <th class="w-90-px">Select</th>
                        <th>Chassis Number</th>
                        <th class="w-280-px">Registration Number</th>
                        <th class="w-100-px">OL Number</th>
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

<script type="text/javascript" src="{{ url('assets/admin/js/vehicles/index.js') }}"></script>

@endsection
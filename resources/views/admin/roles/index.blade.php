@extends('admin.layout.master')

@section('title')
{{ $moduleTitle }}
@endsection

@section('content')
<section class="content">
    <div class="box">
        <div class="box-header align-right">
            <a onclick="return addRole(this)" data-href="{{ route('admin.roles.store') }}"  data-toggle="modal" class="btn btn-primary pull-right" >Add Role</a>
        </div>
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="">
                    <tr>
                        <th class="">Name</th>
                        <th class="w-130-px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</section>

@section('model')
    @include('admin.roles.create-role-model')
@show 
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('assets/admin/js/roles/index.js') }}"></script>
@stop
@extends('admin.layout.master')

@section('title')
{{ $moduleAction ?? 'Manage Users' }}
@endsection

@section('styles')
@endsection

@section('content')
<section class="content">
    <div class="box">
        <div class="box-header align-right">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary pull-right" >Add User</a>
        </div>
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th style="visibility: hidden;"></th>
                        <th class="w-140-px">Name</th>
                       <!--  <th class="w-140-px">Company Name</th> -->
                        <th class="w-200-px">Email</th>
                        <th class="w-100-px">Mobile</th>
                        <th class="w-100-px">Role</th>
                        <!-- <th class="text-center w-100-px">Status</th> -->
                        <th class="text-center w-130-px">Actions</th>
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
    <script type="text/javascript" src="{{ asset('/assets/admin/js/users/index.js') }}"></script>
@endsection
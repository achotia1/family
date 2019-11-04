@extends('admin.layout.master')

@section('title')
{{ $moduleAction ?? 'Manage Permissions' }}
@endsection

@section('styles')
@endsection

@section('content')
<section class="content">
    <div class="box">
        <form action="{{ route('admin.permissions.store') }}" id="permissionsForm" data-toggle="validator" >
        <div class="box-body">
            <div class="d-flex flex-column w-25 form-group mb-0">
                <label class="theme-blue">Select Role</label>
                <select class="form-control my-select" name="role" placeholder="All State" onchange="return getPermissions(this)" required data-error="Role field is required." >
                    <option value="">Select Role</option>
                    @if(!empty($roles) && sizeof($roles) > 0)
                        @foreach($roles as $key => $role)
                            <option value="{{ base64_encode(base64_encode($role->id)) }}">{{ ucfirst(str_replace('-', ' ', $role->name) ?? '--') }}</option>
                        @endforeach
                    @endif
                </select>
                <span class="help-block with-errors">
                  <ul class="list-unstyled">
                     <li class="err_role"></li>
                  </ul>
               </span>

            </div>
            <div class="panel-group toggle-group" id="accordion1">
                <!-- third -->
                @section('permissions')
                    @include('admin.permissions.role-permissions')
                @show

            </div>
            <div class="card-body pt-0 d-flex">
                <button class="btn btn-success pull-right" id="submitButton" type="submit">Save Changes</button>
            </div>
        </div>
        </form>
    </div>
</section>

   
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ url('assets/admin/js/permissions/index.js') }}"></script>
@endsection
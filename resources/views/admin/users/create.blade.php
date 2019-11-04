@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
        <form id="customerForm" data-toggle="validator" action="{{ route($modulePath.'.store') }}">
            <div class="box-header with-border">
              <h1 class="box-title">User Information</h1>
            </div>
            
            <div class="form-group">
                <label class="theme-blue"> 
                Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-control" 
                    required
                    maxlength="250" 
                    data-error="Name field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_name"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Mobile 
                Number <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="mobile_number" 
                    class="form-control" 
                    required
                    maxlength="12" 
                    data-error="Mobile Number field is required" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_mobile_number"></li>
                    </ul>
                </span>
            </div>

            <div class="d-flex flex-column mb-25 form-group">
                <label class="theme-blue">User 
                Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="username"
                    class="form-control" 
                    required
                    maxlength="250" 
                    data-error="User Name field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_username"></li>
                    </ul>
                </span>
            </div>

            <div class="f-col-6 p-0 form-group">
                <label class="theme-blue">Email Id <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="email" 
                    class="form-control" 
                    required
                    data-error="Email field is required." 
                    pattern='^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$'
                    data-pattern-error="@lang('admin.ERR_EMAIL_FORMAT')">
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_email"></li>
                    </ul>
                </span>
            </div>

              <div class="f-col-6 form-group">
                 <label class="theme-blue">@lang('admin.TITLE_PASS') <span class="required">*</span></label>
                 <input class="form-control" type="password" name="password" id="password" required
                    data-error="@lang('admin.ERR_PASS')">
                 <span class="help-block with-errors">
                    <ul class="list-unstyled">
                       <li class="err_password"></li>
                    </ul>
                 </span>
              </div>
              <div class="f-col-6 form-group">
                 <label class="theme-blue">@lang('admin.TITLE_CONFIRM_PASS') <span
                    class="required">*</span></label>
                 <input class="form-control" type="password" name="confirm_password" required
                    data-error="@lang('admin.ERR_CONFIRM_PASS')">
                 <span class="help-block with-errors">
                    <ul class="list-unstyled">
                       <li class="err_confirm_password"></li>
                    </ul>
                 </span>
              </div> 

            <div class="f-col-6 p-0 form-group">
                <label class="theme-blue">@lang('admin.TITLE_SELECT_ROLE') <span
                    class="required">*</span></label>
                <select class="form-control my-select" name="role" required
                    data-error="@lang('admin.ERR_ROLE')">
                    <option value="">@lang('admin.TITLE_SELECT_ROLE')</option>
                    @if(!empty($roles) && sizeof($roles) > 0)
                        @foreach($roles as $key => $role)
                        <option value="{{ base64_encode(base64_encode($role->id)) }}">
                           {{ ucfirst(str_replace('-', ' ',$role->name)) }}
                        </option>
                        @endforeach 
                    @endif
                </select>
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                       <li class="err_role"></li>
                    </ul>
                </span>
            </div>

            <div class="box-footer">
                <button type="reset" class="btn btn-danger">Reset</button>
                <button type="submit" class="btn btn-success pull-right">Save</button>
            </div>
        </form>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script type="text/javascript" src="{{ asset('assets/plugins/input-mask/mask.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/admin/js/users/create-edit.js') }}"></script>
@endsection
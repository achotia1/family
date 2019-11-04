@extends('admin.layout.web')
@section('title')
{{ $moduleAction ?? 'Log In' }}
@endsection
@section('content')
    
    <div class="d-flex pb-5">
        <div class="login-wrapper">
            <div class="card border-0 shadow">
                <h1 class="title blue-border-bottom">
                    {{ $moduleAction }}
                </h1>
               
                <form id="forgotPasswordForm" action="{{ route($modulePath) }}" data-toggle="validator">
                    {{ csrf_field() }}
                    <div class="card-body d-flex flex-column">
                        <div class="form-group">
                            <label for="username" class="theme-blue bold">Username</label>
                            <input type="text" 
                                class="form-control" 
                                name="username"
                                required
                                data-error="@lang('admin.ERR_USERNAME_REQUIRED')" 
                            >
                            <span class="help-block  with-errors">
                                <ul class="list-unstyled">
                                    <li class="err_username"></li>
                                </ul>
                            </span>
                        </div>
                        <div class="form-group text-center mt-3">
                            <button type="submit" name="submit" class="btn btn-primary" >Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- /.login-box -->
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('assets/admin/js/auth/forgot-password.js') }}"></script>
@endsection
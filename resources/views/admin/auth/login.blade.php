@extends('admin.layout.web')
@section('title')
{{ $moduleAction ?? 'Log In' }}
@endsection
@section('content')
 @php
    $error_msg = "";
@endphp
@if(session()->has('message'))
    @php
        $error_msg = session()->get('message');
    @endphp
@endif

  <!-- /.login-logo -->
  <div class="login-box-body card">
    <p class="login-box-msg">Sign in to start your session</p>

        <form id='loginForm' method="post" action="" data-toggle="validator">
        {{ csrf_field() }}
        <div class="form-group  has-feedback">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>  
            <input type="text" 
                class="form-control" 
                name="username" 
                placeholder="Username or Email" 
                value="{{ $user->username ?? '' }}"
                required
                data-error="@lang('admin.ERR_USERNAME_REQUIRED')" 
            >
            <span class="help-block  with-errors">
                <ul class="list-unstyled">
                    <li class="err_username" ></li>
                </ul>
            </span>
        </div>

        <div class="form-group has-feedback">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>  
            <input type="password" 
                class="form-control" 
                name="password" 
                placeholder="Password" 
                value="{{ $user->password ?? '' }}"
                required
                data-error="Password field is required." 
            >
            <span class="help-block  with-errors">
                <ul class="list-unstyled">
                    <li class="err_password" ></li>
                </ul>
            </span>
        </div>

        
        <div class="row">
            <div class="col-xs-8">
                <div class="checkbox">
                    <label>
                        <input type="checkbox"
                                class="" 
                                name="remember" 
                                @if(!empty($user))
                                    checked
                                @endif 
                            >
                            <span class="checkmark"></span>
                            Remember Me
                        </label>
                    </label>
                </div>
            </div>
            <div class="col-xs-4">
                <button type="submit" id="btnLogin" value="Login" class="btn btn-primary btn-block btn-flat">Sign In</button>
            </div>
        </div>
        </form>
       

    <!-- /.social-auth-links -->

    <a href="{{ route('admin.auth.forgot.password') }}">I forgot my password</a><br>
    <!-- <a href="register.html" class="text-center">Register a new membership</a> -->

  </div>
  <!-- /.login-box-body -->

@endsection
@section('scripts')
<script type="text/javascript">
    var error_msg = "{{ $error_msg }}";
    if(error_msg){
       toastr.error(error_msg);
    }
    //var company_id = "{{ \Request::segment(3) }}";
</script>
<script type="text/javascript" src="{{ asset('assets/admin/js/auth/login.js') }}"></script>
@endsection

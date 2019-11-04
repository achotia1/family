@extends('admin.layout.web')

@section('title')
    {{ $moduleTitle ?? 'Reset Password' }}
@endsection

@section('styles')

@endsection

@section('content')
    <div class="d-flex pb-5">
    <div class="login-wrapper">
        <div class="card border-0 shadow">

            <h1 class="title blue-border-bottom">
                {{ $moduleAction }}
            </h1>

            <form id='resetPasswordForm' action="{{ route($modulePath,[$token]) }}" data-toggle="validator">

                {{ csrf_field() }}

                <div class="card-body d-flex flex-column">

                    <div class="form-group">
                        <label for="usr" class="theme-blue bold">Username</label>
                        <input type="text" 
                            class="form-control" 
                            name="username"
                            value="{{ $email ?? '' }}" 
                            disabled="true"
                            required
                            data-error="Email field is required" 
                        >
                        <span class=" help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_username"></li>
                            </ul>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="password" class="theme-blue bold">Password</label>
                        <input type="password" 
                            class="form-control" 
                            name="password"
                            required
                            data-error="Password field is required." 
                        >
                        <span class="help-block  with-errors">
                            <ul class="list-unstyled">
                                <li class="err_password"></li>
                            </ul>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="theme-blue bold">Confirm Password</label>
                        <input type="password" 
                            class="form-control" 
                            name="confirm_password" 
                            required
                            data-error="Confirm password field is required." 
                        >
                        <span class="help-block  with-errors">
                            <ul class="list-unstyled">
                                <li class="err_confirm_password"></li>
                            </ul>
                        </span>
                    </div>

                    <div class="form-group text-center mt-3">
                        <button type="submit" class="btn btn-primary" >Submit</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ url('assets/admin/js/auth/reset-password.js') }}"></script>
@endsection
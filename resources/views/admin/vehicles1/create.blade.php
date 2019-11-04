@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
        <form id="vehicleForm" data-toggle="validator" action="{{ route($modulePath.'store') }}">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
            </div>
            
            <div class="form-group">
                <label class="theme-blue"> 
                Chassis Number <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="chassis_number" 
                    class="form-control" 
                    required
                    maxlength="12" 
                    data-error="Chassis Number field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_chassis_number"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Registration 
                Number <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="registration_number" 
                    class="form-control" 
                    required
                    maxlength="12" 
                    data-error="Registration Number field is required" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_registration_number"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">OL 
                Number <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="ol_number" 
                    class="form-control" 
                    required
                    maxlength="12" 
                    data-error="OL Number field is required" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_ol_number"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Capacity <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="capacity" 
                    class="form-control" 
                    required
                    maxlength="12" 
                    data-error="Capacity field is required" 
                    pattern='^([0-9]+|\s+)$'
                    data-pattern-error="Capacity field should accept numbers only"
                    >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_capacity"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">OL 
                Status <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="ol_status" 
                    class="form-control" 
                    required
                    maxlength="12" 
                    data-error="OL Status field is required" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_ol_status"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Id Number <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="id_number" 
                    class="form-control" 
                    required
                    maxlength="12" 
                    data-error="Id Number field is required" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_id_number"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Issue Number <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="issue_number" 
                    class="form-control" 
                    required
                    maxlength="12" 
                    data-error="Issue Number field is required" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_issue_number"></li>
                    </ul>
                </span>
            </div>

            <!-- Date -->
            <div class="form-group">
                <label>Permit Start Date:</label>

                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" name="permit_start_date" class="form-control pull-right datepicker">
                </div>
                <!-- /.input group -->
            </div>

            <div class="form-group">
                <label>Permit End Date:</label>

                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" name="permit_end_date" class="form-control pull-right datepicker">
                </div>
                <!-- /.input group -->
            </div>


            <div class="form-group">
                <label class="theme-blue">Vehicle Type <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="type" 
                    class="form-control" 
                    required
                    maxlength="12" 
                    data-error="Vehicle Type field is required" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_type"></li>
                    </ul>
                </span>
            </div>

            
            <div class="form-group">
                <label class="theme-blue">Vehicle Status</label>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="status" checked value="1">
                      Active
                    </label>
                </div>  
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
    <script type="text/javascript" src="{{ url('assets/admin/js/vehicles/create-edit.js') }}"></script>
@endsection
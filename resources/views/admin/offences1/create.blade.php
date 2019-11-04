@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
        <form id="offenceForm" data-toggle="validator" action="{{ route($modulePath.'store') }}">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
            </div>
            
            <div class="form-group">
                <label class="theme-blue"> 
                Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-control" 
                    required
                    maxlength="50" 
                    data-error="@lang('admin.ERR_OFFENCE_NAME')" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_name"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Description <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="description" 
                    class="form-control" 
                    required
                    data-error="@lang('admin.ERR_OFFENCE_DESC')" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_description"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Penalty <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="penalty" 
                    class="form-control" 
                    required
                    maxlength="20" 
                    pattern='^\d+(\.\d{0,2})?$'
                    data-pattern-error="@lang('admin.ERR_OFFENCE_PENALTY_FORMAT')"
                    data-error="@lang('admin.ERR_OFFENCE_PENALTY')" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_penalty"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group">
                <label class="theme-blue">Offence Status</label>
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
    <script type="text/javascript" src="{{ url('assets/admin/js/Offences/create-edit.js') }}"></script>
@endsection
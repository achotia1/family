@extends('web.layout.main')

@section('title')
   {{ $moduleTitle ?? '' }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
        <div class="box-header with-border">
          <h1 class="box-title">{{ $moduleTitle ?? ''}}</h1>
          <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
        </div>
        <form id="familyHeadForm" data-toggle="validator" action="{{ route($modulePath.'.member.store') }}" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <label class="theme-blue"> 
                        Family Head <span class="required">*</span></label>
                        <select class="form-control my-select select2" id="family_head_id" name="family_head_id" required="" data-error="Family head field is required.">
                            <option value="">Select Family Head</option>
                            @if( !empty($familyHeads) && count($familyHeads) > 0 )
                            @foreach($familyHeads as $familyHead){
                                <option value="{{ $familyHead->id }}">{{ $familyHead->first_name .' '. $familyHead->last_name }}</option>
                            @endforeach
                            @endif
                         </select>                
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_family_head_id"></li>
                            </ul>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <label class="theme-blue"> 
                        First Name <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="first_name" 
                            class="form-control" 
                            required
                            maxlength="250" 
                            data-error="First name field is required." 
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_first_name"></li>
                            </ul>
                        </span>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="theme-blue"> 
                        Last Name <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="last_name" 
                            class="form-control" 
                            required
                            maxlength="250" 
                            data-error="Last name field is required." 
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_last_name"></li>
                            </ul>
                        </span>
                    </div>
                </div>
            </div>

             <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <label class="theme-blue">Birth Date
                            <span class="required">*</span></label>
                        <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                        <input type="text" name="birth_date" class="form-control" required="" data-error="Birth Date field is required.">
                        <span class="input-group-addon">
                             <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_birth_date"></li>
                            </ul>
                        </span>
                    </div>
                    
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <label class="theme-blue"> Martial Status <span class="required">*</span></label>
                        <div class="radio">
                            <label>
                              <input type="radio" name="martial_status" value="1" class="martial_status_married">
                              Is Married
                            </label>
                            <label>
                              <input type="radio" name="martial_status" value="0" class="martial_status_unmarried">
                               Is Unmarried
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-md-6 hide" id="wedding_div">
                        <label class="theme-blue">Wedding Date</label>
                        <div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
                        <input type="text" name="wedding_date" class="form-control">
                        <span class="input-group-addon">
                             <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_wedding_date"></li>
                            </ul>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group col-md-6">
                          <label for="photo">Upload Photo</label>
                          <input type="file" id="photo" accept="image/*" name='photo'>
                    </div>
                </div>
               
                <div class="form-group col-md-6">
                    <label class="theme-blue"> 
                    Education <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="education" 
                        class="form-control" 
                        required
                        data-error="Education field is required." 
                    >
                    <span class="help-block with-errors">
                        <ul class="list-unstyled">
                            <li class="err_education"></li>
                        </ul>
                    </span>
                </div>
            </div>

            
            <input type="hidden" name="member_type" value="2">

            <div class="box-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="reset" class="btn btn-danger">Reset</button>
            </div>
        </form>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script type="text/javascript">

</script>
<script type="text/javascript" src="{{ asset('assets/plugins/input-mask/mask.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/web/js/family-head/create-edit.js') }}"></script>
@endsection
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
        <form id="familyHeadForm" data-toggle="validator" action="{{ route($modulePath.'.store') }}" autocomplete="off">
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
                    <div class="form-group col-md-6">
                        <label class="theme-blue">Mobile 
                Number <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="mobile_number" 
                    class="form-control" 
                    required
                    maxlength="10" 
                    data-error="Mobile Number field is required" 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_mobile_number"></li>
                    </ul>
                </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                      <label>Address</label>
                      <textarea 
                       class="form-control"
                       name="address" 
                       rows="3"
                       data-error="Address field is required"
                       ></textarea>
                       <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_address"></li>
                            </ul>
                        </span>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="theme-blue"> 
                        Pincode <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="pincode" 
                            class="form-control" 
                            required
                            data-error="Pincode field is required." 
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_pincode"></li>
                            </ul>
                        </span>
                    </div>
                </div>
            </div>

             <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <label class="theme-blue"> 
                        States <span class="required">*</span></label>
                        <select class="form-control my-select select2" id="state_id" name="state_id"required="" onchange="getStateCities(this);" data-error="State field is required.">
                            <option value="">Select State</option>
                            @if( !empty($states) && count($states) > 0 )
                            @foreach($states as $state){
                                <option value="{{ $state['id'] }}">{{ $state['name'] }}</option>
                            @endforeach
                            @endif
                         </select>                
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_state_id"></li>
                            </ul>
                        </span>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="theme-blue"> 
                        Cities <span class="required">*</span></label>
                        <select class="form-control my-select select2" id="city_id" name="city_id" required="" data-error="City field is required.">
                            <option value="">Select City</option>
                            @if( !empty($cities) && count($cities) > 0 )
                            @foreach($cities as $city){
                                <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                            @endforeach
                            @endif
                         </select>                
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_city_id"></li>
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
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                          <label for="photo">Upload Photo</label>
                          <input type="file" id="photo" accept="image/*" name='photo'>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group col-md-12 select_hobby">
                        <label class="theme-blue"> 
                        Hobbies <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="hobbies[]" 
                            class="form-control" 
                            required
                            data-error="Hobby field is required." 
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_hobbies[]"></li>
                            </ul>
                        </span>
                        
                    </div>
                    <div class="col-md-4">
                        <a href="javascript:void(0)" class="btn btn-primary " onclick="return addHobby()" style="cursor: pointer;">
                        <i class="fa fa-plus"></i> Add Hobby
                        </a>
                    </div>
                </div>

            </div>
            <input type="hidden" name="member_type" value="1">

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
var hobby =`<div class = "form-group col-md-12 select_hobby"><input 
    type="text" 
    name="hobbies[]" 
    class="form-control" 
    required
    data-error="Hobby field is required." 
></div>`;
</script>
<script type="text/javascript" src="{{ asset('assets/plugins/input-mask/mask.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/web/js/family-head/create-edit.js') }}"></script>
@endsection
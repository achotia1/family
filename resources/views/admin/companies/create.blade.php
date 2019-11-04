@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<div class="row mb-5">
        <div class="col-xs-12">
            <form id="companyForm" data-toggle="validator" action="{{ route($modulePath.'store') }}">
                <div class="card border-0 shadow">
                    <h1 class="title blue-border-bottom">
                        {{ strtoupper($moduleAction) }}
                    </h1>
                    <div class="card-footer d-flex theme-bg-blue-light blue-border-bottom">
                    	<button class="btn-normal ml-auto blue-btn-inverse" type="reset">Reset</button>
                        <button class="btn-normal ml-3 blue-btn-inverse" type="submit">Save</button>
                    </div>
                    <h1 class="card-subtitle blue-border-bottom text-capitalize">
                        Company Information
                    </h1>
                    <div class="card-body">
                        <div class="f-col-6 p-0">

                            <div class="d-flex flex-column mb-25 form-group">
                                <label class="theme-blue">Company 
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

                            <div class="f-row">
                            <div class="f-col-6 form-group">
                                <label class="theme-blue">Upload Company Logo</label>
                                <div class="theme-bg-blue-light py-4 px-2 f-row m-0 fileParentDiv">
                                    <div class="w-auto">
                                        <label class="mb-0 custom-file-upload blue-btn-inverse f-14 removefile" style="display: none" onclick="removeFile(this)" >Remove File</label>
                                        <label for="logo" class="mb-0 custom-file-upload blue-btn f-14 choosefile">Choose File </label>          
                                        <input 
                                            name='logo' 
                                            id="logo" 
                                            class="file-upload d-none" 
                                            accept="image/*"
                                            type="file"
                                        >
                                    </div>
                                    <div class="filename ml-2 old_name" id="image_name">
                                        <p class="mb-0 file-upload-filename">No file Selected.</p>
                                    </div>
                                </div>
                                <span class="help-block with-errors">
                                    <ul class="list-unstyled">
                                        <li class="err_image"></li>
                                    </ul>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex flex-column mb-25 form-group">
                                <label class="theme-blue">Company 
                                URL <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    name="company_url" 
                                    class="form-control" 
                                    required
                                    data-error="URL field is required." 
                                >
                                <span class="help-block with-errors">
                                    <ul class="list-unstyled">
                                        <li class="err_name"></li>
                                    </ul>
                                </span>
                            </div>

                            
                           <div class="d-flex flex-column">
                              <label class="theme-blue">Company Status</label>
                              <ul class="list-group list-checkbox clear mt-2 d-flex flex-wrap">
                                 <li class="d-inline-flex w-auto mb-3 mr-3">
                                    <label class="checkbox-container">
                                    <input type="checkbox" name="status" checked value="1">
                                    <span class="checkmark"></span>
                                    </label>
                                    <p class="text-capitalize mb-0 bold">Active </p>
                                 </li>
                              </ul>
                           </div>   

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ url('assets/admin/js/companies/create-edit.js') }}"></script>
@endsection
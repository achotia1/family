@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<div class="row mb-5">
        <div class="col-xs-12">
            <form id="productsForm" data-toggle="validator" action="">
                <div class="card border-0 shadow">
                    <h1 class="title blue-border-bottom">
                        {{ strtoupper($moduleAction) }}
                    </h1>
                    <div class="card-footer d-flex theme-bg-blue-light blue-border-bottom">
                        <button class="btn-normal ml-auto blue-btn-inverse" type="button" onclick="window.history.back()">Back</button>
                    	<!-- 
                        <button class="btn-normal ml-3 blue-btn-inverse" type="submit">Save</button> -->
                    </div>
                    <h1 class="card-subtitle blue-border-bottom text-capitalize">
                        Product Information
                    </h1>
                    <div class="card-body">
                        <div class="f-col-6 p-0">

                            <div class="d-flex flex-column mb-25 form-group">
                                <label class="theme-blue">Product 
                                Name <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    class="form-control" 
                                    value="{{ $product->name }}" 
                                    disabled
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

                            <div class="d-flex flex-column mb-25 form-group">
                                <label class="theme-blue">Product 
                                Code <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    name="code" 
                                    class="form-control" 
                                    value="{{ $product->code }}" 
                                    disabled
                                    required
                                    maxlength="250" 
                                    data-error="Code field is required." 
                                >
                                <span class="help-block with-errors">
                                    <ul class="list-unstyled">
                                        <li class="err_code"></li>
                                    </ul>
                                </span>
                            </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

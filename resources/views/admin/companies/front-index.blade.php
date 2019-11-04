<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="icon" href="{{ url('/favicon.ico') }}" sizes="16x16">
   <meta name="admin-path" content="{{ url('/admin') }}">
   <meta name="base-path" content="{{ url('/') }}">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <title> Companies | Orchid</title>
   <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i"
   rel="stylesheet">
   <script src="{{ asset('assets/common/js/jquery.min.js') }}"></script>
   <script src="{{ asset('assets/common/js/bootstrap.min.js') }}"></script>
   <script src="{{ asset('assets/plugins/axios/axios.min.js') }}"></script>
   <link rel="stylesheet" href="{{ asset('assets/common/css/bootstrap4-classes.css') }}">
   <link rel="stylesheet" href="{{ asset('assets/common/css/bootstrap.min.css') }}">
   <link rel="stylesheet" href="{{ asset('assets/common/css/bootstrap-datepicker3.min.css') }}">
   <link rel="stylesheet" href="{{ asset('assets/common/css/jquery.mCustomScrollbar.css') }}">
   <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
   <link rel="stylesheet" href="{{ asset('assets/admin/css/responsive.css') }}">
   <link rel="stylesheet" href="{{ asset('assets/admin/css/browser.css') }}">
   <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}">
   <link rel="stylesheet" type="text/css" href="{{ url('assets/plugins/sweetalert/sweetalert.css') }}">
   <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700&display=swap" rel="stylesheet">
   @yield('styles')
   <script src="{{ asset('assets/plugins/ckeditor/ckeditor.js') }}"></script>
   <script src="{{ asset('assets/plugins/datatable/jquery.dataTables.min.js') }}"></script>
   <style>
      *{
         font-family: 'Ubuntu', sans-serif;
      }
      .container {
         margin: 50px auto;
      }
      h1 {
         font-size: 45px;
         text-align: center;
         margin: 0 0 50px 0;
         font-weight: 600;
         line-height: 69px;
      }
      .company-border {
         border: 2px solid #285cad;
         background-color: #fff;
         margin-bottom: 0;
         display: flex;
         align-items: center;
         flex-direction: column;
         position: relative;
         border-radius: 20px;
         overflow: hidden;
      }
      .company-border h2 {
         vertical-align: middle !important;
         color: #feffff;
         padding-top: 0;
         text-align: center;
         margin: 0;
         font-size: 16px;
         font-weight: 400;
         font-family: 'Ubuntu', sans-serif;
      }
      .brand-logo {
         max-width: 250px;
         margin: 0 auto 0 auto;
         display: flex;
         align-items: center;
         justify-content: center;
         min-height: 150px;
         padding-bottom: 0;
         padding: 30px 0;
      }
      .brand-name {
         width: 100%;
         background-color: #285cad;
         padding: 12px 10px;
      }
      h1 span {
         color: #285cad;
         position: relative;
      }
      h1 small {
         font-size: 24px;
         letter-spacing: 1.2px; color: #000;
      }
      h1 span::after {
         content: "";
         width: 50px;
         height: 3px;
         background-color: #285cad;
         position: absolute;
         bottom: -20px;
         left: 50%;
         transform: translate(-50%,0);
      }
   </style>
</head>
<body>
   @section('styles')
   @endsection
   <div class="container">
      <div class="row">
         <h1><span>Welcome Super Admin!</span><br>
            <small>Please select the company in which you want to login.</small>
         </h1>
      </div>
      <div class="row">
         @if(!empty($companies) && sizeof($companies)>0)
         @foreach($companies as $company)
         @php
         $image_path = asset('/assets/admin/images/default.png');
         if(!empty($company->logo) && is_file(storage_path().'/app/'.$company->logo))
         {
            $image_path = url('/storage/app/'.$company->logo);
         }
         @endphp
         <div class="col-md-4 mb-5">
           <a href="{{ $company->store_company_url }}">
            <div class="company-border">
               <div class="brand-logo">
                  <img src="{{ $image_path }}" alt="logo" class="img-fluid img-responsive">
               </div>
               <div class="brand-name">
                
                  <h2>{{ $company->name }}</h2>
                  
               </div>
            </div>
         </a>
      </div>
      @endforeach
      @endif
   </div>
</div>
@section('scripts')
<!-- <script type="text/javascript" src="{{ url('assets/admin/js/auth/login.js') }}"></script> -->
@endsection
@include('admin.layout.partials.footer')
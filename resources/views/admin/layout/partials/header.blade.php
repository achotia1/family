<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('title') | {{ config('constants.SITENAME') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="admin-path" content="{{ url('/admin') }}">
  <meta name="base-path" content="{{ url('/') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}">

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('assets/adminLte/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/adminLte/bower_components/font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('assets/adminLte/bower_components/Ionicons/css/ionicons.min.css') }}">

   <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('assets/adminLte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('assets/adminLte/dist/css/AdminLTE.min.css') }}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset('assets/adminLte/dist/css/skins/_all-skins.min.css') }}">
  <!-- Morris chart -->
  <link rel="stylesheet" href="{{ asset('assets/adminLte/bower_components/morris.js/morris.css') }}">
  <!-- jvectormap -->
  <link rel="stylesheet" href="{{ asset('assets/adminLte/bower_components/jvectormap/jquery-jvectormap.css') }}">
  <!-- Date Picker -->
  <link rel="stylesheet" href="{{ asset('assets/adminLte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset('assets/adminLte/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ url('assets/plugins/sweetalert/sweetalert.css') }}">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <link rel="stylesheet" href="{{ asset('assets/adminLte/bower_components/select2/dist/css/select2.min.css') }}">
	@yield('style')
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
@php

        if(!empty($company->logo) && is_file(storage_path().'/app/'.$company->logo))
        {
          $logo = 'storage/app/'.$company->logo;
        }
        else
        {
          $logo = 'assets/admin/images/logo.jpg';
        }
        //dd($company);

      @endphp
  <header class="main-header">
    <!-- Logo -->
    <a href="{{ url('/admin') }}" class="logo">
      <div class="logo_img">
        <img src="{{ asset($logo) }}" class="company-image" alt="Company Image">
      </div>
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>{{ substr($company->name,0,1) }}</b>S</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg" style="@if(strlen($company->name)>8) font-size:15px @endif"><b>{{ $company->name }}</b>Store</span>

    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
             <!--  <img src="{{ asset('assets/adminLte/dist/img/user2-160x160.jpg') }}" class="user-image" alt="User Image"> -->
             
            @if(Auth::check())
            <span class="hidden-xs">{{ ucwords(auth()->user()->name) }} </span>
            @endif
           
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                 <!--  <a href="#">Profile</a> -->
                 <!-- <a href=""  class="btn btn-default btn-flat">Edit Profile</a> -->
                  <a href="#" data-toggle="modal" data-target="#updateUserPassword" onclick="document.getElementById('updateUserPasswordForm').reset()" class="btn btn-default btn-flat">Change Password</a>
                </div>
                <div class="pull-right">
                  <a href="{{ url('admin/logout') }}" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <!-- <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li> -->
        </ul>
      </div>
    </nav>
  </header>
@if(Auth::check())
   @include('admin.layout.partials.sidebar')
@endif
  <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>
        {{ $moduleAction??''}}
         <!-- Dashboard
         <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
         <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
         <li class="active">{{ $moduleAction??''}}</li>
      </ol>
   </section>
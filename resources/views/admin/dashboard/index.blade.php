@extends('admin.layout.master')
@section('title')
{{ $moduleAction ?? 'Manage Dashboard' }}
@endsection
@section('content')

   <!-- Main content -->
   <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
         <h1 class="dashboard-h1"><span>Welcome to {{ $company->name ?? '' }} </span>
         </h1>
      </div>
      <div class="row">

         @can('store-total-users')
         <!-- ./col -->
         <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
               <div class="inner">
                  <h3>{{ $count['customer'] }}</h3>
                  <p>User Registered</p>
               </div>
               <div class="icon">
                  <i class="ion ion-person-add"></i>
               </div>
               <a href="{{ route('admin.users.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
         </div>
         @endcan

         <!-- ./col -->
         <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
               <div class="inner">
                  <h3>{{ $count['rawMaterial'] }}</h3>
                  <p>Raw Materials</p>
               </div>
               <div class="icon">
                  <i class="ion ion-bag"></i>
               </div>
               <a href="{{ route('admin.materials.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
         </div>
        
         <!-- ./col -->
      </div>
      <!-- /.row -->
      
   </section>

@endsection
@section('scripts')
<!-- <script type="text/javascript" src="{{ url('assets/admin/js/dashboard/index.js') }}"></script> -->
@endsection
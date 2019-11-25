 <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <!-- <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset('assets/adminLte/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Alexander Pierce</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div> -->
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
     
      <ul class="sidebar-menu" data-widget="tree">
       <!--  <li class="header">MAIN NAVIGATION</li> -->
        @can('store-dashboard')
        <li class="{{ active(['admin/dashboard','admin/dashboard/*']) }}">
          <a href="{{ route('admin.dashboard') }}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            <!-- <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span> -->
          </a>
          <!-- <ul class="treeview-menu">
            <li class="active"><a href="index.html"><i class="fa fa-circle-o"></i> Dashboard v1</a></li>
            <li><a href="index2.html"><i class="fa fa-circle-o"></i> Dashboard v2</a></li>
          </ul> -->
        </li>
        @endcan

        <!-- Manage Users -->
        @can('store-manage-users')
        <li class="treeview {{ active(['admin/users','admin/roles','admin/permissions', 'admin/users/*']) }}">
          <a href="#">
            <i class="fa fa-user-plus"></i>
            <span>Manage Users</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @can('store-users-add')
            <li class="{{ active('admin/users/create') }}">
              <a href="{{ route('admin.users.create') }}"><i class="fa fa-hand-o-right"></i> Add User</a>
            </li>
            @endcan
            @can('store-users-listing')
            <li class="{{ active('admin/users') }}">
              <a href="{{ route('admin.users.index') }}"><i class="fa fa-hand-o-right"></i> View Users</a>
            </li>
            @endcan
            
            @can('store-manage-roles')
            <li class="{{ active('admin/roles') }}">
              <a href="{{ route('admin.roles.index') }}"><i class="fa fa-hand-o-right"></i> Manage Roles</a>
            </li>
            @endcan

            @can('store-manage-permissions')
            <li class="{{ active('admin/permissions') }}">
              <a href="{{ route('admin.permissions.index') }}"><i class="fa fa-hand-o-right"></i> Manage Permissions</a>
            </li>
            @endcan
          </ul>
        </li>
        @endcan   

        <!-- Manage Material -->        
        <li class="treeview {{ active(['admin/materials', 'admin/materials/*']) }}">
          <a href="#">
            <i class="fa fa-list-alt"></i>
            <span>Manage Material</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">            
            <li class="{{ active('admin/materials') }}">
              <a href="{{ route('admin.materials.index') }}"><i class="fa fa-hand-o-right"></i> View materials</a>
            </li>           
            
            <li class="{{ active('admin/materials/create') }}">
              <a href="{{ route('admin.materials.create') }}"><i class="fa fa-hand-o-right"></i> Add material</a>
            </li>
           
          </ul>
        </li>        
        <!-- Manage Material In -->        
        <li class="treeview {{ active(['admin/materials-in', 'admin/materials-in/*']) }}">
          <a href="#">
            <i class="fa fa-list-alt"></i>
            <span>Manage Material In</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">            
            <li class="{{ active('admin/materials-in') }}">
              <a href="{{ route('admin.materials-in.index') }}"><i class="fa fa-hand-o-right"></i> View Material In</a>
            </li>            
            <li class="{{ active('admin/materials-in/create') }}">
              <a href="{{ route('admin.materials-in.create') }}"><i class="fa fa-hand-o-right"></i> Add Material In</a>
            </li>           
          </ul>
        </li>

        <!-- Manage RMS -->
        
        <li class="treeview {{ active(['admin/rms-store', 'admin/rms-store/*']) }}">
          <a href="#">
            <i class="fa fa-user-plus"></i>
            <span>Manage Batches</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            
            <li class="{{ active('admin/rms-store') }}">
              <a href="{{ route('admin.rms-store.index') }}"><i class="fa fa-hand-o-right"></i> View Batches</a>
            </li>
           
            
            <li class="{{ active('admin/rms-store/create') }}">
              <a href="{{ route('admin.rms-store.create') }}"><i class="fa fa-hand-o-right"></i> Add Batch</a>
            </li>
            
          </ul>
        </li>
        <!-- production -->
        <li class="treeview {{ active(['admin/production', 'admin/production/*']) }}">
          <a href="#">
            <i class="fa fa-user-secret"></i>
            <span>Manage Material Plans</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="{{ active('admin/production') }}">
              <a href="{{ route('admin.production.index') }}"><i class="fa fa-hand-o-right"></i> View Material Plan</a>
            </li>
           
           
            <li class="{{ active('admin/production/create') }}">
              <a href="{{ route('admin.production.create') }}"><i class="fa fa-hand-o-right"></i> Add Material Plan</a>
            </li>
           
          </ul>
        </li>
        <!-- end production -->

        <!-- sales -->
        <!-- <li class="treeview {{ active(['admin/sales', 'admin/sales/*']) }}">
          <a href="#">
            <i class="fa fa-user-secret"></i>
            <span>Manage Issued Material</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="{{ active('admin/sales') }}">
              <a href="{{ route('admin.sales.index') }}"><i class="fa fa-hand-o-right"></i> View Issued Material</a>
            </li>
           
           
            <li class="{{ active('admin/sales/create') }}">
              <a href="{{ route('admin.sales.create') }}"><i class="fa fa-hand-o-right"></i> Add Issued Material</a>
            </li>
           
          </ul>
        </li> -->
        <!-- end sales -->
		
		<!-- return material -->
        <li class="treeview {{ active(['admin/return', 'admin/return/*']) }}">
          <a href="#">
            <i class="fa fa-user-secret"></i>
            <span>Manage Returned Material</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="{{ active('admin/return') }}">
              <a href="{{ route('admin.return.index') }}"><i class="fa fa-hand-o-right"></i> View Returned Material</a>
            </li>
           
           
            <li class="{{ active('admin/return/create') }}">
              <a href="{{ route('admin.return.create') }}"><i class="fa fa-hand-o-right"></i> Add Returned Material</a>
            </li>
           
          </ul>
        </li>
        <!-- return sales -->
        
        <!-- output material -->
        <li class="treeview {{ active(['admin/materials-out', 'admin/materials-out/*']) }}">
          <a href="#">
            <i class="fa fa-user-secret"></i>
            <span>Manage Material Output</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="{{ active('admin/materials-out') }}">
              <a href="{{ route('admin.materials-out.index') }}"><i class="fa fa-hand-o-right"></i> View Material Output</a>
            </li>
           
           
            <li class="{{ active('admin/materials-out/create') }}">
              <a href="{{ route('admin.materials-out.create') }}"><i class="fa fa-hand-o-right"></i> Add Material Output</a>
            </li>
           
          </ul>
        </li>
        <!-- end production -->
        <!-- Review Batch card -->
        
        <li class="{{ active(['admin/review-batch-card','admin/review-batch-card/*']) }}">
          <a href="{{ url('admin/review-batch-card') }}">
            <i class="fa fa-user-plus"></i> <span>Review Batch Cards</span>
          </a>
          
        </li>
        
        <!-- Manage Reports -->
        @can('manage-reports')
        <li class="treeview {{ active(['admin/report', 'admin/report/*']) }}">
          <a href="#">
            <i class="fa fa-file"></i>
            <span>Manage Reports</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @can('vehicle-history')
            <li>
              <a href="#"><i class="fa fa-hand-o-right"></i> Vehicle History Report</a>
            </li>
            @endcan
          </ul>
        </li>
        @endcan
      
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
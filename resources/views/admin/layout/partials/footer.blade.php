
   <!-- /.content -->
</div>
<!-- /.content-wrapper -->
  <footer class="main-footer">
    <!-- <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.18
    </div> -->
    <strong>Copyright &copy; 2019-{{ date("Y",time()) }} <a href="{{ url('/admin') }}">OrchidStore</a>.</strong> All rights
    reserved.
  </footer>

</div>
@section('models')
@include('admin.customers.change-password-model')
@show
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="{{ asset('assets/adminLte/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('assets/adminLte/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('assets/adminLte/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- Morris.js charts -->
<!-- <script src="{{ asset('assets/adminLte/bower_components/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('assets/adminLte/bower_components/morris.js/morris.min.js') }}"></script> -->
<!-- Sparkline -->
<script src="{{ asset('assets/adminLte/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap -->
<script src="{{ asset('assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('assets/adminLte/bower_components/jquery-knob/dist/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('assets/adminLte/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/adminLte/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<!-- datepicker -->
<script src="{{ asset('assets/adminLte/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<!-- Slimscroll -->
<script src="{{ asset('assets/adminLte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('assets/adminLte/bower_components/fastclick/lib/fastclick.js') }}"></script>
<script src="{{ asset('assets/adminLte/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/adminLte/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="{{ asset('assets/adminLte/dist/js/pages/dashboard.js') }}"></script>
 --><!-- AdminLTE for demo purposes -->
<!-- <script src="{{ asset('assets/adminLte/dist/js/demo.js') }}"></script> -->

<script src="{{ asset('assets/common/js/validator.min.js') }}"></script>

<script src="{{ asset('assets/plugins/lodingoverlay/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('assets/plugins/axios/axios.min.js') }}"></script>
<script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/plugins/toastr/toastr.options.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert/sweetalert.js') }}"></script>
<script src="{{ asset('assets/admin/js/users/model.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/plugins/input-mask/mask.js') }}"></script>
 <script type="text/javascript">
  const ADMINURL = $('meta[name="admin-path"]').attr('content');
  const BASEURL = $('meta[name="base-path"]').attr('content');
  const CSRFTOKEN = document.querySelector("meta[name=csrf-token]").content
  axios.defaults.headers.common['X-CSRF-Token'] = CSRFTOKEN;
   $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
</script>

<script src="{{ asset('assets/plugins/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/adminLte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
@yield('scripts')
</body>
</html>
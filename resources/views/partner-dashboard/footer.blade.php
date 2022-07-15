<footer class="footer text-center"> 2020 &copy; Royalty Inc </footer>

</div>
<!-- /#page-wrapper -->
</div>
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="{{asset('partner-dashboard/plugins/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{asset('partner-dashboard/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- Menu Plugin JavaScript -->
<script src="{{asset('partner-dashboard/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js')}}"></script>
<!--slimscroll JavaScript -->
<script src="{{asset('partner-dashboard/js/jquery.slimscroll.js')}}"></script>
<!--Wave Effects -->
<script src="{{asset('partner-dashboard/js/waves.js')}}"></script>
<!--Counter js -->
<script src="{{asset('partner-dashboard/plugins/bower_components/waypoints/lib/jquery.waypoints.js')}}"></script>
<script src="{{asset('partner-dashboard/plugins/bower_components/counterup/jquery.counterup.min.js')}}"></script>
<!-- chartist chart -->
<script src="{{asset('partner-dashboard/plugins/bower_components/chartist-js/dist/chartist.min.js')}}"></script>
<script src="{{asset('partner-dashboard/plugins/bower_components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js')}}"></script>
<!-- Sparkline chart JavaScript -->
<script src="{{asset('partner-dashboard/plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js')}}"></script>
<!-- Custom Theme JavaScript -->
<script src="{{asset('partner-dashboard/js/custom.min.js')}}"></script>
@if(url()->current() == url('partner/branch/dashboard'))
    <script src="{{asset('partner-dashboard/js/dashboard1.js')}}"></script>
@endif
<script src="{{asset('partner-dashboard/plugins/bower_components/toast-master/js/jquery.toast.js')}}"></script>
<script src="{{asset('partner-dashboard/plugins/bower_components/toast-master/js/jquery.toast.js')}}"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="{{asset('js/datepicker/moment.min.js')}}"></script>
<script src="{{asset('js/partner_dashboard/statistics.js')}}"></script>
@include('partner-dashboard.pusher_js')
<script>
    $("#side-menu li:first-child").css('padding-top', '70px');
</script>

{{--new request modal--}}
<div id="new_request_modal" class="modal" role="dialog">
    <div class="modal-dialog" style="width: 800px !important; overflow:auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times" style="color: #000 !important;"></i>
                </button>
                <h4 class="modal-title" style="color: #000 !important;">New Request</h4>
            </div>
            <div class="modal-body">
                <div class="no-info">
                    <ul class="list-group" id="new_tran_requests">
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
   function merchantLogout(){
    if (confirm('Are you sure you want to log out?')) {        
      var url = "{{ url('/partner_logout') }}";

      $('<form action="' + url + '" method="POST">' +
         '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
         '</form>').appendTo($(document.body)).submit();
    }
    return false;
   }
</script>

<script>
    var base_url = window.location.origin;
    var cur_url = window.location.href;
    if (base_url + '/partner/branch/requests' !== cur_url) {
        // update request status
        function updateStatus(notificationID, sourceID, status, posted_on) {
            $(".notification_"+notificationID).prop('disabled', true);
            var url =
                base_url +
                "/" +
                "partner/update_transaction_request/" +
                notificationID +
                "/" +
                sourceID +
                "/" +
                status;
            window.location = url;
        }
    }
</script>
{{--refresh page if remains idle for a while--}}
{{--<script type="text/javascript">--}}
{{--    var idleTime = 0;--}}
{{--    $(document).ready(function () {--}}
{{--        //Increment the idle time counter every minute.--}}
{{--        // var idleInterval = setInterval(timerIncrement, 2000); // 1 minute--}}

{{--        //Zero the idle timer on mouse movement.--}}
{{--        $(this).mousemove(function (e) {--}}
{{--            idleTime = 0;--}}
{{--        });--}}
{{--        $(this).keypress(function (e) {--}}
{{--            idleTime = 0;--}}
{{--        });--}}
{{--    });--}}

{{--    function timerIncrement() {--}}
{{--        idleTime++;--}}
{{--        if (idleTime > 10) { // 20 minutes--}}
{{--            window.location.reload();--}}
{{--        }--}}
{{--    }--}}
{{--</script>--}}

</body>

</html>
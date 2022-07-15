<!-- footer content -->
<footer>
    <div class="pull-right">
        Royalty - Admin Panel
    </div>
    <div class="clearfix"></div>
</footer>
<!-- /footer content -->
</div>
</div>

<!-- jQuery -->
<script src="{{ asset('admin/vendors/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('admin/vendors/fastclick/lib/fastclick.js') }}"></script>
<!-- NProgress -->
<script src="{{ asset('admin/vendors/nprogress/nprogress.js') }}"></script>
<!-- bootstrap-progressbar -->
<script src="{{ asset('admin/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script>

<!-- Custom Theme Scripts -->
<script src="{{ asset('admin/build/js/custom.min.js') }}"></script>

@include('admin.production.push_noti.pusher_js')

<script>
    function startPageLoader() {
        $(".page_loader").css('display', 'block');
        return true;
    }

    function stopPageLoader() {
        $(".page_loader").css('display', 'none');
        return true;
    }
</script>
{{--========================================================================================
================================ PARTNER ADD FORM BY ADMIN ==============================
=========================================================================================--}}

<script>
    $("#contact").focusout(function () {

        var phone = $("#contact").val();

        if (isNaN(phone) || (phone.length > 14) || phone.length == 0) {
            $(".error_phone").text('Invalid Phone Number');
            return false;
        }
        else {
            $(".error_phone").text('');
        }
    });

    $("#ownerContact").focusout(function () {

        var owner_phone = $("#ownerContact").val();

        if (isNaN(owner_phone) || (owner_phone.length > 14) || owner_phone.length == 0) {
            $(".error_ownerContact").text('Invalid Phone Number');
            return false;
        }
        else {
            $(".error_ownerContact").text('');
        }
    });

    $("#email").focusout(function () {

        var email = $("#email").val();

        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        if (!filter.test(email)) {
            $(".error_email").text('Invalid E-mail');
            return false;
        }
        else {
            $(".error_email").text('');
        }

    });

    $("#admin_code").focusout(function () {

        var admin_code = $("#admin_code").val();

        if (admin_code.length < 5) {
            $(".error_admin_code").text('Invalid Admin Code');
            return false;
        }
        else {
            $(".error_admin_code").text('');
        }

    });

    $("#latitude").focusout(function () {

        var latitude = $("#latitude").val();

        if (isNaN(latitude) || latitude.length == 0) {
            $(".error_latitude").text('Invalid Latitude');
            return false;
        }
        else {
            $(".error_latitude").text('');
        }

    });

    $("#longitude").focusout(function () {

        var longitude = $("#longitude").val();

        if (isNaN(longitude) || longitude.length == 0) {
            $(".error_longitude").text('Invalid Longitude');
            return false;
        }
        else {
            $(".error_longitude").text('');
        }

    });
</script>
</body>
</html>
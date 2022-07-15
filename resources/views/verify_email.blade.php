<script>
    //check if user directly trying to access signup page without validating phone
    if (localStorage.getItem("signup_phone") === null) {
        window.location = "{{ url('/login') }}";
    }
</script>
@if(!session('customer_id'))
    <script>window.location = "{{ url('/login') }}";</script>
@endif
<?php
//get last part of url to not show login button in login page
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$page = end($link_array);
?>
@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>E-mail verification</h2>
         <p>Verification Successful!</p>
      </div>
   </div>
</section>
<section>
<div class="row">
    <div class="col-md-6 col-md-offset-3 center">
        @if(session('error'))
            <p class="text-danger center">{{session('error')}}</p>
        @endif
        <form class="form-horizontal form-label-left" method="post" action="{{ url('registration/verify_email') }}">
            <br>
            @if(!session('error'))
                <p>A verification link & a code has been sent to <br> <b>{{ $reg_email }}. Please check all your email folders.</b> </p>
                <p>You can verify your email by clicking the link or entering the code below.</p>
            @endif
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-8 col-xs-offset-2 col-xs-8">
                    <label for="verifying_code"></label>
                    <input type="text" class="form-control" placeholder="Verification code"
                       name="verifying_code" id="verifying_code" minlength="6" maxlength="6">
                </div>
            </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="reg_email" value="{{ $reg_email }}">
            <div class="ln_solid"></div>
            OR
            <div class="form-group" style="margin-bottom: 24px">
                <div class="col-sm-offset-2 col-sm-8 col-xs-offset-2 col-xs-8">
                    <label for="verifying_email"></label>
                    <p>If you want to change your email then please enter your new email below and click verify.</p>
                    <input type="email" class="form-control" placeholder="Enter new email(optional)"
                       name="verifying_email" id="verifying_email">
                </div>
            </div>
            <div class="form-group">
                <p class="middle" style="display: contents">
                    <button type="submit" class="btn btn-primary verify_button">Verify</button>
                </p>
            </div>
        </form>
    </div>
</div>
</section>
@include('footer')
@if(url()->current() == url('registration/verify_email'))
    <script>
        var app_key = '{{env("PUSHER_APP_KEY")}}';
        var app_cluster = '{{env("PUSHER_APP_CLUSTER")}}';
        var pusher = new Pusher(app_key, {
            cluster: app_cluster,
            forceTLS: true
        });

        var checkEmailVerifyChannel = pusher.subscribe('check_email_verify_status');
        checkEmailVerifyChannel.bind('redirect_if_email_verified', function(response) {
            var data = response['data'];
            var customer_id = "<?php echo session('customer_id'); ?>";
            if(customer_id == data.customer_id && data.email_verified == 1){
                window.location.href = '{{url("users").'/'.session('customer_username')}}';
            }
        });
    </script>
@endif
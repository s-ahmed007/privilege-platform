@if(session()->has('customer_id') || session()->has('partner_id'))
   <script>
      window.location = "{{ url('/') }}";
   </script>
@endif
<?php
//get last part of url to not show login button in login page
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$page = end($link_array);
?>
@include('header')
<link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Royalty Login/Signup</h2>
      </div>
   </div>
</section>
<section id="counts" class="counts">
   <div class="container">
      <div class="row" data-aos="fade-left">
         <div class="login-box whitebox">
            <div class="login-box-element">
               <div class="login-box-inner">
                  <h4>
                     LOGIN/SIGNUP
                  </h4><hr>
                  <p class="mtb-10">Enter your phone number</p>
               </div>
               <div class="login-reg-phone-num">
                  <span class="number-bar">+880</span>
                  <label for="phone_number"></label>
                  <input class="input-field phonenumberinput" type="text" placeholder="1XXXXXXXXX" id="phone_number"
                     maxlength="11">
               </div>
               <div class="form-group user-pin" style="display: none;">
                  <p class="mtb-10">Enter you PIN</p>
                  <span class="wrong_pin"></span>
                  <input type="password" class="pinOrPass pininput" id="pinPass" maxlength="4">
                  <span toggle="#pinPass"
                     class="fa fa-fw fa-eye-slash field-icon toggle-pin"></span>
               </div>
               <input type="hidden" id="type">
               <input type="hidden" id="customer_id">
               <div class="group mtb-10">
                  <input type="button" value="Next" class="signinbtn btn btn-primary btn-block"
                         onclick="checkPhone()" id="signinbtn"/>
                  <p class="forgot-pass"></p>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
@include('footer')
{{--modal to show user reset message starts--}}
<div id="userResetModal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">    <h4 class="modal-title">Successful!</h4>
            <button type="button" class="close" data-dismiss="modal">
               <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <span>Your PIN has been reset.</span>
         </div>
      </div>
   </div>
</div>
<script>
   @if(session()->has('user_reset'))
   $('#userResetModal').modal('show');
   @endif
</script>
{{--PIN show or hide--}}
<script type="text/javascript">
   $(".toggle-pin").click(function () {
      $(this).toggleClass("fa-eye fa-eye-slash");
      var input = $($(this).attr("toggle"));
      if (input.attr("type") == "password") {
         input.attr("type", "text");
      } else {
         input.attr("type", "password");
      }
   });
</script>
{{--modal to deactive customer modal--}}
<div id="deactive_user_modal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">        <h4 class="modal-title">Sorry!</h4>
            <button type="button" class="close" data-dismiss="modal">
               <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="user_deactive_text">
            <p></p>
         </div>
      </div>
   </div>
</div>
{{--modal to show verification code entry--}}
<div id="verification_code_modal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">   <h4 class="modal-title">Verification Code</h4>
            <button type="button" class="close" data-dismiss="modal">
               <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="user_deactive_text">
            <p style="text-align: center;">We have sent you an OTP to the following number:</p>
            <p style="text-align: center;font-weight:bold;" class="verify_phone"></p>
            <p style="text-align: center;">Please enter the OTP below</p>
            {{--            <p class="phone_verify_msg"></p>--}}
            {{--            <p class="phone_verify_status"></p>--}}
            <div class="form-horizontal form-label-left">
               <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-8 col-xs-offset-2 col-xs-8">
                     <label for="phone_verifying_code"></label>
                     <input type="text" class="form-control" placeholder="Enter verification code"
                            name="phone_verifying_code" id="phone_verifying_code" minlength="6" maxlength="6" required>
                  </div>
               </div>
               <input type="hidden" name="_token" value="{{ csrf_token() }}">
               <input type="hidden" name="password_user" id="user_type">
               <div class="ln_solid"></div>
               <div class="form-group center">
                  <p style="display: contents">
                     <button class="btn btn-primary verify_button" onclick="return verifyOTP()">Verify</button>
                  </p>
                  <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif" alt="Royalty Loading GIF"
                       class="loading-gif" style="display: none; position: relative;" title="Royalty loading icon">
               </div>
               <div class="center">
               <a href="{{url()->current()}}">I want to change my number</a><br>
               <a style="    text-decoration: underline;
    cursor: pointer;
    font-size: 12px;" onclick="checkPhone()">Resend code</a>
            </div>
            </div>
         </div>
      </div>
   </div>
</div>
@include('footer-js.login-js')
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
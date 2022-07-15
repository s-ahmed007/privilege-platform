@if(session()->has('customer_id') || session()->has('partner_id'))
<style type="text/css">
   body {
   background: #eee;
   }
</style>
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
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Sign up</h2>
         <p>Get exclusive offers, discounts & rewards through our platform</p>
      </div>
   </div>
</section>
<section id="counts" class="counts">
   <div class="container">
      <div class="row" data-aos="fade-left">
         <div class="login-box whitebox">
            <div class="login-box-element">
               <div class="login-box-inner">
                  <h4>BECOME A MEMBER</h4>
                  <p class="mtb-10">Create an account to enter the world of boundless possibilities.</p>
               </div>
               <hr>
               <form action="{{ url('registration') }}" onsubmit="return checkFields();" method="post">
                  {{csrf_field()}}
                  <div class="signup-form">
                     <div class="mat-div">
                        @if ($errors->getBag('default')->first('phone_number'))
                        {{ $errors->getBag('default')->first('phone_number') }}
                        @endif
                        <div class="error_phone"></div>
                        <label for="phone_number" class="mat-label">PHONE NUMBER</label>
                        <input type="text" class="mat-input" id="phone_number">
                        <span class="correct_number">&#10004;</span>
                     </div>
                     <div class="mat-div">
                        @if ($errors->getBag('default')->first('full_name'))
                        {{ $errors->getBag('default')->first('full_name') }}
                        @endif
                        <label for="full_name" class="mat-label">FULL NAME</label>
                        <input type="text" class="mat-input" name="full_name" id="full_name">
                        <span class="correct_name"></span>
                        <div class="error_name"></div>
                     </div>
                     <div class="mat-div">
                        @if ($errors->getBag('default')->first('signup_email'))
                        {{ $errors->getBag('default')->first('signup_email') }}
                        @endif
                        <label for="signup_email" class="mat-label">E-MAIL ADDRESS</label>
                        <input type="text" class="mat-input" name="signup_email" id="signup_email">
                        <span class="correct_email"></span>
                        <div class="error_email"></div>
                     </div>
{{--                     <div class="mat-div">--}}
{{--                        @if ($errors->getBag('default')->first('pin'))--}}
{{--                        {{ $errors->getBag('default')->first('pin') }}--}}
{{--                        @endif--}}
{{--                        <label for="pin" class="mat-label">4-DIGIT PIN</label>--}}
{{--                        <input type="password" class="mat-input" name="pin" id="pin" maxlength="4">--}}
{{--                        <span toggle="#pin" class="fa fa-fw fa-eye-slash field-icon toggle-pin"--}}
{{--                              style="margin-right: 8px;margin-top: -18px;"></span>--}}
{{--                        <span class="correct_pin"></span>--}}
{{--                        <div class="error_pin"></div>--}}
{{--                     </div>--}}
                     <div class="mat-div">
                        @if ($errors->getBag('default')->first('refer_code'))
                        {{ $errors->getBag('default')->first('refer_code') }}
                        @endif
                        <label for="pin" class="mat-label">REFER CODE <small>(Optional)</small></label>
                        <input type="text" class="mat-input" name="refer_code" id="refer_code" maxlength="5">
                        <span class="correct_reg_refer"></span>
                        <div class="error_reg_refer"></div>
                     </div>
                  </div>
                  <br>
                  <div class="reg-form-join-btn center">
                     <input type="submit" value="JOIN" class="signinbtn btn btn-primary btn-block"/>
                     <div class="agreement">
                        <p>By joining, you agree to our
                           <a href="{{ url('terms&conditions') }}" target="_blank">Terms & Conditions</a>
                           and
                           <a href="{{ url('privacypolicy') }}" target="_blank">Privacy Policy</a>.
                        </p>
                     </div>
                  </div>
                  <input type="hidden" class="mat-input" name="phone_number" id="phone_number2">
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
@include('footer')
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
<script>
   $(".mat-input").focus(function(){
       $(this).parent().addClass("is-active is-completed");
   });
   
   $(".mat-input").focusout(function(){
       if($(this).val() === "")
           $(this).parent().removeClass("is-completed");
       $(this).parent().removeClass("is-active");
   })
</script>
@include('footer-js.signup-js')
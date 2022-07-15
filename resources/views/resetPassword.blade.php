@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Royalty Account PIN Reset</h2>
      </div>
   </div>
</section>
<section id="features" class="features">
<div class="container">
<div class="row" data-aos="fade-left">
    @if(isset($step) && $step == 1)
         <div class="login-box whitebox">
            <div class="login-box-element">
               <div class="login-box-inner">
                   @if(session('password sent'))
                    <div id="login-alert" class="alert alert-success">{{session('password sent')}}</div>
                   @elseif(session('server_error'))
                      <div id="login-alert" class="alert alert-danger">{{session('server_error')}}</div>
                   @elseif(session('phone not exist'))
                      <div id="login-alert" class="alert alert-danger">{{session('phone not exist')}}</div>
                   @endif
                   <h3>Reset Your PIN</h3><hr>
                   <p class="mtb-10">Enter your phone number and we'll send a code to reset your PIN.</p>
               </div>
               <div class="login-reg-phone-num">
                 @if(session('phone not exist'))
                     <script>
                         $(document).ready(function () {
                             // if E-mail doesn't exist then collapse email input form
                             $("#collapse2").addClass("in");
                         });
                     </script>
                     <div id="login-alert red">{{session('phone does not exist')}}</div>
                 @endif
                 <form id="loginform" class="form-horizontal" role="form" action="{{url('reset_pin/send-sms')}}"
                       method="post" style="width: inherit;">
                     <div class="login-reg-phone-num">
                         <span class="number-bar">+880</span>
                         <label for="phone_number"></label>
                         <input class="input-field phonenumberinput" type="text" placeholder="PHONE NUMBER" id="phone_number"
                            name="phone" maxlength="10" required>
                     </div>
                     <input type="hidden" name="_token" value="{{csrf_token()}}">
                     <div class="form-group">
                         <div class="col-sm-12">
                             <div class="group">
                                 <button type="submit" id="btn-login" class="btn btn-primary btn-block">NEXT</button>
                             </div>
                         </div>
                     </div>
                 </form>
               </div>
            </div>
         </div>
    @elseif(isset($step) && $step == 2)
        <div class="login-box whitebox">
            <div class="login-box-element">
                <div class="login-box-inner">
                    @if(session('did_not_match'))
                        <div id="login-alert" class="alert alert-danger">{{session('did_not_match')}}</div>
                    @endif
                    <h3>Reset Your PIN</h3><hr>
                    <p class="mtb-10">Please enter your 6 digit code here.</p>
                    @error('reset_otp')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="login-reg-phone-num">
                    @if(session('phone not exist'))
                        <script>
                            $(document).ready(function () {
                                // if E-mail doesn't exist then collapse email input form
                                $("#collapse2").addClass("in");
                            });
                        </script>
                        <div id="login-alert red">{{session('phone does not exist')}}</div>
                    @endif
                    <form id="loginform" class="form-horizontal" role="form" action="{{url('reset_pin/check-otp')}}"
                          method="post" style="width: inherit;">
                        <div class="login-reg-phone-num">
                            <label for="reset_otp"></label>
                            <input class="input-field phonenumberinput" type="text" placeholder="Enter OTP" id="reset_otp"
                                   name="reset_otp" maxlength="6" required>
                        </div>
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="otp_check_phone" value="{{$_GET['phone']}}">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="group">
                                    <button type="submit" id="btn-login" class="btn btn-primary btn-block">NEXT</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

</div>
</section>
@include('footer')
@if($step == 1)
    <script>
        var phone = localStorage.getItem('reset_phone');
        $(".phonenumberinput").val(phone);
    </script>
@endif

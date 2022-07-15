{{-- redirect dashboard page if already logged in --}}
@if(session('branch_user_id'))
<?php $url = url('partner/branch/requests');?>
<script>window.location.href = "{{$url}}";</script>
@endif
<link rel="icon" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo-merchant.png">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- Meta, title, CSS, favicons, etc. -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Merchant Dashboard</title>
<!-- Bootstrap -->
<link href="{{ asset('admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
<!-- Font Awesome -->
<link href="{{ asset('admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<!-- NProgress -->
<link href="{{ asset('admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
<!-- Animate.css') }} -->
<link href="{{ asset('admin/vendors/animate.css/animate.min.css') }}" rel="stylesheet">
<!-- Custom Theme Style -->
<!-- <link href="{{ asset('admin/build/css/custom.min.css') }}" rel="stylesheet"> -->
<link href="{{asset('css/stylenew.css')}}" rel="stylesheet">
<link href="{{asset('css/mq.css')}}" rel="stylesheet">
<!-- Template Main CSS File -->
<link href="{{asset('css/bootstrap.3.3.4.min.css')}}" rel="stylesheet">
<!-- Template Main MQ File -->
<link href="{{asset('css/mq.css')}}" rel="stylesheet">
<!-- Vendor CSS Files -->
<link href="{{asset('css/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
<section id="hero">
   <!-- <div class="container">
      <div class="section-title-hero">
         <h2>Royalty Login/Signup</h2>
      </div>
   </div> -->
</section>
<section id="features" class="features">
   <a class="hiddenanchor" id="signup"></a>
   <a class="hiddenanchor" id="signin"></a>
   <div class="container">
      <div class="row">
         <div class="login-box whitebox">
            <div class="login-box-element">
               <div class="login-box-inner">
                  <div class="center">
                     <p>ROYALTY PARTNER LOGIN
                  </div>
                  <hr>
                  </p>
                  <p class="mtb-10">Enter your number and PIN.</p>
               </div>
               @if (session('login_error'))
               <div style="text-shadow: none; color: red; font-size: 1.3em">
                  {{ session('login_error') }}
               </div>
               @endif
               <form action="{{ url('partner/branch_user_login') }}" method="post">
                  <div class="login-reg-phone-num">
                     <span class="number-bar">+88</span>
                     <label for="phone_number"></label>
                     <input class="input-field phonenumberinput" type="text" placeholder="01XXXXXXXXX"
                        name="phone" id="phone_number" maxlength="11" required>
                  </div>
                  <div class="form-group user-pin">
                     <input type="password" class="form-control pininput-partner" placeholder="PIN" name="pin" maxlength="4" id="pininput-p"
                        required/>
                     <span toggle="#pininput-p" class="fa fa-fw fa-eye-slash field-icon toggle-pin"></span>
                  </div>
                  <div class="group">
                     <input type="hidden" name="_token" value="{{ csrf_token() }}">
                     <button type="submit" name="submit" class="btn button btn-block" style="border-radius: unset;">Log in</button>
                  </div>
               </form>
               <br>
               <p>For any help call: 01312620202 (Khalid)</p>
            </div>
         </div>
      </div>
   </div>
</section>
<script src="{{asset('partner-dashboard/plugins/bower_components/jquery/dist/jquery.min.js')}}"></script>
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
{{-- redirect request page if already logged in --}}
@if(session('branch_user_id'))
  <?php $url = url('branch/requests'); ?>
  <script>window.location.href = "{{$url}}";</script>
@endif 
<!DOCTYPE html>
<html lang="en">
   <head>
   <link rel="icon" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo-merchant.png">
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <!-- Meta, title, CSS, favicons, etc. -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Admin Dashboard</title>
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
   </head>
   <body class="login">
      <div>
         <a class="hiddenanchor" id="signup"></a>
         <a class="hiddenanchor" id="signin"></a>
         <div class="container">
            <div class="login-box row">
               <div class="login-box-element">
                  <div class="login-box-inner">
                     <div class="center">
                        <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/icon/Royalty_logo.png" style="width:20%"/>
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
                  <form action="{{ url('branch_user_login') }}" method="post">
                     <div class="login-reg-phone-num">
                        <span class="number-bar">+880</span>
                        <label for="phone_number"></label>
                        <input class="input-field phonenumberinput" type="text" placeholder="PHONE NUMBER" 
                           name="phone" id="phone_number" maxlength="10" required>
                     </div>
                     <div class="form-group user-pin">
                        <input type="password" class="form-control" placeholder="PIN" name="pin" maxlength="4" 
                           required/>
                     </div>
                     <div class="group">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" name="submit" class="btn button btn-block">Log in</button>
                     </div>
                  </form>
                  <br>
                  <p>For any help call: 01312620202 (Khalid)</p>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>
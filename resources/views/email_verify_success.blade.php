<?php
   //get last part of url to not show login button in login page
   $link = $_SERVER['PHP_SELF'];
   $link_array = explode('/', $link);
   $dont_redirect_mobile_page = end($link_array);
?>
@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <p>EMAIL VERIFICATION</p>
      </div>
   </div>
</section>
<link rel="stylesheet" href="{{asset('css/fail-success.css')}}">
<div class="container">
   <div class="content-body">
      <div class="image-body">
         <div class="row">
            <div class="col-md-3 col-sm-2 col-xs-12">
            </div>
            <div class="col-md-6 col-sm-8 col-xs-12">
               <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/payment/payment-success.png"
                  class="logo-img" alt="Royalty Logo">
            </div>
            <div class="col-md-3 col-sm-2 col-xs-12">
            </div>
         </div>
      </div>
      <div class="text-body">
         <h1 class="heading">E-mail verification successful!</h1>
         <p class="para">You have successfully verified your E-mail address!
         </p>
      </div>
      <div class="row">
         <div class="col-md-4"></div>
         <div class="col-md-4 center">
            @if(session('customer_username'))
            <a href="{{url('/users/'.session('customer_username'))}}">
               <p class="btn btn-primary">Go to your Account</p>
            </a>
            @else
            <a href="{{url('/login')}}">
               <p class="btn btn-primary">Log In</p>
            </a>
            @endif
         </div>
         <div class="col-md-4"></div>
      </div>
   </div>
</div>
@include('footer')
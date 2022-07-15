<?php session_start(); ?>
<!DOCTYPE html>
<html class="no-js" lang="en">
   <head>
      <meta charset="utf-8">
      <title>Royalty - Discover offers, discounts & rewards</title>
      <meta name="description" content="Royalty Mobile page">
      <meta name="author" content="">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
      <link href="{{asset('css/bootstrap.3.3.4.min.css')}}" rel="stylesheet">
      <link rel="stylesheet" href="{{asset('css/mobiledevice/base.css')}}">
      <link rel="stylesheet" href="{{asset('css/mobiledevice/vendor.css')}}">
      <link rel="stylesheet" href="{{asset('css/mobiledevice/main.css')}}">
      <script src="https://kit.fontawesome.com/9e60b11f48.js" crossorigin="anonymous" defer></script>
      <link rel="stylesheet" href="{{asset('css/mainv1.0.1.css')}}">
      <script src="{{asset('js/mobiledevice/modernizr.js')}}"></script>
      <script src="{{asset('js/mobiledevice/pace.min.js')}}"></script>
      <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
      <link rel="icon" href="favicon.ico" type="image/x-icon">
   </head>
   <body id="top" class="home">
      <header>
      </header>
      <section id="home">
         <div class="overlay"></div>
         <div class="home-content-table">
            <div class="home-content-tablecell">
               <div class="row">
                  <div class="col-six">
                     <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/apppage/appimage.png"/>    
                  </div>
                  <div class="col-six" style="margin-top: 2.1rem;">
                     <h1 class="animate-intro">Your Lifestyle Partner</h1>
                     <!-- <h3 class="animate-intro">
                        Register now to avail a 30 days free trial with access to 500+ deals from 100+ partners!
                        </h3> -->
                     <h3 class="animate-intro">
                        Get your Royalty Membership to avail up to <span>75%</span> discount at over <span>200</span> places in Dhaka!
                     </h3>
                     <div class="more animate-intro">
                        <a href="{{url('/rbdapp')}}">
                           <p class="button stroke" style="cursor: unset">
                              Download Now!
                           </p>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <div id="userResetModal" class="modal" role="dialog" style="top: 10%">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" style="height: 0px;">
                  <i class="cross-icon"></i>
                  </button>
                  <h4 class="modal-title" style="color: #fff !important;">Successful!</h4>
               </div>
               <div class="modal-body" id="profile_modal">
                  <span>Your PIN has been reset.</span>
               </div>
            </div>
         </div>
      </div>
      <div id="welcome_popup1">
         <div id="popup2" class="popup">
            <i class="cross-icon popup-closed" id="close" style="color: black"></i>
            <a href="{{url('/donate')}}">
            <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/home-page/donation-popup.png"
               alt="Royalty Home Popup" class="lazyload" style="width: 100%">
            </a>
         </div>
      </div>
      <script src="{{asset('js/mobiledevice/jquery-2.1.3.min.js')}}"></script>
      <script src="{{asset('js/mobiledevice/plugins.js')}}"></script>
      <script src="{{asset('js/mobiledevice/main.js')}}"></script>
      <script src="{{asset('js/bootstrap.min.js')}}"></script>
      <script>
         function Links(){
             //Get all Anchor elements
             var a=document.getElementsByTagName('a');
             //Loop through each anchor element found.
             for(var i=0; i<a.length; i++){
                 //Set on click event for the anchor element
                 a[i].addEventListener('click',Anchors,false);
             }
         }
         function Anchors(){
         //Set new window location using the anchor href that triggers this function.
         window.location.href=this.href;
         }
         window.onload=Links;
      </script>
      @if(\Illuminate\Support\Facades\Session::has('pin_updated_from_phone'))
      <script>
         $("#userResetModal").modal('show');
      </script>
      <?php
         \Illuminate\Support\Facades\Session::flush('pin_updated_from_phone');
         ?>
      @endif
       <script defer>
         $(document).ready(function() {
             setTimeout(function(){
                var welcome = sessionStorage.getItem("welcome_today");
                var today = new Date();
                var date_val = today.getDate();
                if(welcome==date_val){
                    {{--nothing to do--}}
                }else{
                    sessionStorage.setItem("welcome_today", date_val);
                    $("#welcome_popup1").removeAttr("style");
                    $("#welcome_popup1").show();
                }
             }, 3000);//show modal after 3 minutes
         
            $(".popup-closed").on("click", function (e) {
                $("#popup2").css("display", "none");
            });
         });
      </script>
   </body>
</html>
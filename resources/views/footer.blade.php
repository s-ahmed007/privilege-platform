<!-- ======= Footer ======= -->
<footer id="footer">
   <div class="footer-top">
      <div class="container">
         <div class="row">
            <div class="col-lg-4 col-md-6">
               <div class="footer-info">
                  <h3>Keep in touch</h3>
                  <p>
                     Bashundhara Rd, Bashundhara R/A,<br>Dhaka 1229.<br><br>
                     <strong>Phone:</strong> <a href="tel:+8809638620202">+880-963-862-0202</a> (10am-6pm) <br>
                     <strong>Email:</strong> <a href="mailto:support@royaltybd.com">support@royaltybd.com</a><br>
                  </p>
                  <div class="social-links mt-3">
                     <a href="{{ url('https://www.facebook.com/RoyaltyBD/') }}" class="facebook">
                         <i class="bx bxl-facebook"></i></a>
                     <a href="{{ url('https://www.instagram.com/RoyaltyBD/') }}" class="instagram">
                         <i class="bx bxl-instagram"></i></a>
                     <a href="{{ url('https://www.youtube.com/channel/UCKFicIPvXBA-_a04LNsurhA') }}" class="youtube">
                         <i class="bx bxl-youtube"></i></a>
                     <a href="{{ url('https://twitter.com/RoyaltyBD') }}" class="twitter"><i class="bx bxl-twitter"></i>
                     </a>
                     <a href="{{ url('https://www.linkedin.com/company/royalty-bangladesh/')}}" class="linkedin">
                         <i class="bx bxl-linkedin"></i></a>
                     <a href="{{ url('https://www.snapchat.com/add/royalty.bd')}}" class="snapchat">
                         <i class="bx bxl-snapchat"></i></a>
                  </div>
                  <div class="row download-logo">
                     <div class="col-sm-6 col-md-6 col-xs-6">
                        <a href="{{url('http://bit.ly/RBDIOSAPP')}}" target="_blank">
                        <img class="lazyload footer-apple" style="width: 100%;"
                           src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/appstore.png"
                           alt="Royalty Applestore Icon"/>
                        </a>
                     </div>
                     <div class="col-sm-6 col-md-6 col-xs-6">
                        <a href="{{url('http://bit.ly/RBDANDROID')}}" target="_blank">
                        <img class="lazyload footer-play" style="width: 100%;"
                           src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/playstore.png"
                           alt="Royalty Playstore Icon"/>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-lg-2 col-md-6 footer-links">
               <h4>Company</h4>
               <ul>
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('about-us') }}" target="_blank">About us</a>
                  </li>
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('/faq') }}" target="_blank">FAQs</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('careers') }}" target="_blank">Careers</a>
                  </li>
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('blog') }}" target="_blank">Blog</a></li>
                  <li><i class="bx bx-chevron-right"></i>  <a href="{{ url('terms&conditions') }}" target="_blank">
                          Terms & Conditions</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('privacypolicy') }}" target="_blank">
                          Privacy Policy</a></li>
               </ul>
            </div>
            <div class="col-lg-2 col-md-6 footer-links">
               <h4>Partners</h4>
               <ul>
                  <li><i class="bx bx-chevron-right"></i> <a href="{{url('partner-join') }}" target="_blank">
                          Become a partner</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('contact') }}" target="_blank">Partner Contact
                      </a></li>
               </ul>
            </div>
            <div class="col-lg-2 col-md-6 footer-links">
               <h4>More</h4>
               <ul>
                  <!-- <li><i class="bx bx-chevron-right"></i> <a href="{{ url('top-referrals') }}" target="_blank">Top Referrals</a></li>
                     <li><i class="bx bx-chevron-right"></i> <a href="{{ url('results') }}" target="_blank">Results</a></li> -->
                  @if(Session::has('customer_id'))
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('royaltyrewards') }}" target="_blank">
                          Royalty Rewards</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('yourWish') }}" target="_blank">Make a wish
                      </a></li>
                  @endif
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('press') }}" target="_blank">Press</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('influencer-program') }}" target="_blank">
                          Influencer Program</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="{{ url('how_it_works') }}" target="_blank">
                          How it works?</a></li>
               </ul>
            </div>
         </div>
      </div>
   </div>
   <div class="container">
      
      <div class="copyright">
         &copy; Copyright 2020 <strong><span>Royalty Inc</span></strong>. All Rights Reserved
      </div>
      <div class="credits"> Designed by <a href="{{url('/')}}">Royalty Inc.</a>
      </div>
   </div>
</footer>
<!-- End Footer -->

<div id="preloader"></div>
<!-- Vendor JS Files -->
<script src="{{asset('js/jquery/jquery.min.js')}}"></script>
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('js/jquery.easing/jquery.easing.min.js')}}"></script>
<script src="{{asset('js/venobox.min.js')}}"></script>
<script src="{{asset('js/waypoints/jquery.waypoints.min.js')}}"></script>
<script src="{{asset('js/counterup/counterup.min.js')}}"></script>
<script src="{{asset('js/owl.carousel2.min.js')}}"></script>
<script src="{{asset('js/aos.js')}}"></script>
<!-- Template Main JS File -->
<script src="{{asset('js/mainnew.js')}}"></script>
{{-- ================================================================================================
================================Message after payment cleared,  modal======================
=================================================================================================== --}}
@if(session('payment_clear'))
<script>
   $('#payment_modal').modal('show');
</script>
<div id="payment_modal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">            <h4 class="modal-title">Payment Successful</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>

         </div>
         <div class="modal-body">
            <p>{{session('payment_clear')}}</p>
            <p>Login here.</p>
            <a href="#" data-toggle="modal" data-target="#myModal">Login</a>
         </div>
      </div>
   </div>
</div>
@endif
{{-- modal to show beta version message --}}
<div id="beta_version" class="modal fade" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-body" style="padding: 0 !important;">
            <div class="row">
               <!-- <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/images/beta-version.png"
               alt="Royalty beta" class="lazyload" style="width:100%; height: 500px"> -->
            </div>
         </div>
      </div>
   </div>
</div>
{{-- ==================== Invalid FOLLOWERS Modal====================== --}}
<div id="invalidFollow" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header"><h4 class="modal-title">Sorry!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
            
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <p>You need to have a Royalty user account to be able to follow.</p>
         </div>
      </div>
   </div>
</div>
{{--partner admin control panel login modal--}}
<div id="partner-admin-modal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">   <h4 class="modal-title">Partner Admin Dashboard Login</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         
         </div>
         <div class="modal-body">
            <div class="no-info">
               <p style="font-size: 12px;margin-bottom: 10px;">
                  By default, your browser might block pop-ups from automatically showing up on your screen. When a
                  pop-up is blocked, the address bar will be marked Pop-up blocked. You have to allow pop-ups to
                  access the control panel.
               </p>
               <form id="partner-admin-login">
                  <input type="text" id="partner_admin_code" title="partner admin">
                  <button type="button" class="btn btn-primary" id="partnerAdminDashboard" style="margin-top: -3px;">Go
                  </button>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
{{-- modal to show birthday gift expired message --}}
<div id="birthdayGiftExpiredModal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">   <h4 class="modal-title">Sorry!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <div class="no-info">
               <p>{{session('birthdayGiftExpired')}}</p>
            </div>
         </div>
      </div>
   </div>
</div>
{{-- End modal to show beta version message  --}}
{{--modal to show while verifying account E-mail--}}
<div id="email_verify_modal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header"> <h4 class="modal-title" id="email_edit_title">Update Your E-mail</h4>
            <button type="button" class="close customerEmailEditModalCross" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <div class="no-info">
               <p id="email_edit_text">Please verify your E-mail address associated to this account. You can edit if
                  this isn't your correct E-mail.
               </p>
               <span id="email_error"></span>
            </div>
            <form class="form-horizontal form-label-left" method="post" onsubmit="return verifySubmitMail();"
               action="{{ url('send_edit_email_verification') }}">
               <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-8 col-xs-offset-2 col-xs-8">
                     <?php
                        $email = '';
                        if(isset($customer_data)){
                            $email = $customer_data->customer_email;
                        }elseif (isset($profileInfo)){
                            $email = $profileInfo->customer_email;
                        }
                        ?>
                     <label for="verifying_mail"></label>
                     <input type="email" class="form-control" placeholder="Enter your E-mail address"
                        name="verifying_mail" value="{{ session('customer_email') }}" id="verifying_mail"
                        onkeyup="validateVerificationEmail()" onmouseup="validateVerificationEmail()" required>
                  </div>
               </div>
               <input type="hidden" name="_token" value="{{ csrf_token() }}">
               <input type="hidden" name="from_page" id="email_edit_or_verify" value="verify">
               <div class="ln_solid"></div>
               <div class="form-group center">
                  <p style="display: contents">
                     <button type="submit" class="btn btn-primary verify_button">Verify</button>
                  </p>
                  <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif"
                       alt="Royalty Loading GIF" class="loading-gif" style="display: none; position: relative;"
                       title="Royalty loading icon">
               </div>
            </form>
            {{--</p>--}}
         </div>
      </div>
   </div>
</div>
{{--modal to show while changing DOB--}}
<div id="dob_update_modal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
            <h4 class="modal-title" id="dob_edit_title">Update Profile</h4>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <div class="no-info">
               <p id="dob_edit_text">You can only change your birthday once.
               </p>
            </div>
            <form class="form-horizontal form-label-left" method="post"
               action="{{ url('edit-dob') }}"
               enctype="multipart/form-data">
               <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-8 col-xs-offset-2 col-xs-8">
                     <div class="birthday-select">
                        <?php
                           $days = range(1, 31);
                           $months = range(1, 12);
                           $years = range(1930, date('Y'));
                           ?>
                        <label for="birth_day"></label>
                        <select name="birth_day" id="birth_day" style="border-radius:5px;padding: 0 0 3px 5px">
                           <option selected disabled>Day</option>
                           <?php
                              foreach($days as $day) {
                              ?>
                           <option value="<?php echo($day) ?>"><?php echo($day) ?></option>
                           <?php
                              }
                              ?>
                        </select>
                        <label for="birth_month"></label>
                        <select name="birth_month" id="birth_month" style="border-radius:5px;padding: 0 0 3px 5px">
                           <option selected disabled>Month</option>
                           <?php foreach($months as $month) { ?>
                           <option value="<?php echo($month) ?>"><?php echo($month) ?></option>
                           <?php } ?>
                        </select>
                        <label for="birth_year"></label>
                        <select name="birth_year" id="birth_year" style="border-radius:5px;padding: 0 0 3px 5px">
                           <option selected disabled>Year</option>
                           <?php foreach($years as $year) { ?>
                           <option value="<?php echo($year) ?>"><?php echo($year) ?></option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>
               </div>
               <input type="hidden" name="_token" value="{{ csrf_token() }}">
               <div class="ln_solid"></div>
               <div class="form-group">
                  <p class="middle"><button type="submit" class="btn btn-primary">Update</button></p>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<div id="expired_user_alert" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
         <h4 class="modal-title">Renew Membership</h4>
            <button type="button" class="close" data-dismiss="modal" id="modalClose">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body center">
         <p>
               Renew your membership today to enjoy up to 75% discount in more than {{session('total_branch_count')}} partners!
            </p>
            <br>
            <br>
            <a href="{{url('renew_subscription')}}" class="btn btn-success">Renew Membership</a>
            <!-- Or
               <button type="button" class="btn btn-primary" data-dismiss="modal">Skip</button> -->
         </div>
      </div>
   </div>
</div>
<script async>
   {{--check mail verification fields onsubmit--}}
   function validateVerificationEmail() {
       var email = $("#verifying_mail").val();
       var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
       if (!filter.test(email)) {
           $("#email_error").html("Please enter a valid E-mail").css({"display": "block", 'color': 'red'});
           $('.verify_button').prop('disabled', true);
       } else {
           $("#email_error").css("display", "none");
           var url = "{{ url('/checkDuplicateEmail') }}";
           $.ajax({
               type: "POST",
               url: url,
               data: {'_token': '<?php echo csrf_token(); ?>', 'email': email},
               success: function (data) {
                   if(data == 0){
                       $('.verify_button').prop('disabled', false);
                   }else if(data == 1){
                       $("#email_error").html("Email already exists").css({"display": "block", 'color': 'red'});
                       $('.verify_button').prop('disabled', true);
                   }
               }
           });
       }
   }
</script>
<!-- Essential jQuery Plugins
   ================================================== -->
<!-- Main jQuery -->
@include('footer-js.pusher_js')
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/index.js')}}" defer></script>
<script src="https://cdn.jsdelivr.net/npm/lazyload@2.0.0-beta.2/lazyload.js"></script>
<script src="{{asset('js/classie.js')}}" defer></script>
<script src="{{asset('js/owl.carousel.min.js')}}" defer></script>
@if(strpos(url()->current(), request()->root().'/users/') !== false )<!--load if its user account page-->
<!-- Begin emoji-picker JavaScript -->
<script src="{{asset('emoji/js/config.js')}}" defer></script>
<script src="{{asset('emoji/js/util.js')}}" defer></script>
<script src="{{asset('emoji/js/jquery.emojiarea.js')}}" defer></script>
<script src="{{asset('emoji/js/emoji-picker.js')}}" defer></script>
<!-- End emoji-picker JavaScript -->
<script defer>
   $(function() {
      // Initializes and creates emoji set from sprite sheet
      window.emojiPicker = new EmojiPicker({
         emojiable_selector: '[data-emojiable=true]',
         assetsPath: '{{asset('emoji/img/')}}',
         popupButtonClasses: 'smile-icon'
      });
      // Finds all elements with `emojiable_selector` and converts them to rich emoji input fields
      // You may want to delay this step if you have dynamically created input fields that appear later in the loading process
      // It can be called as many times as necessary; previously converted input fields will not be converted again
      window.emojiPicker.discover();
   });
</script>
@endif
{{--modal to show subscribe status--}}
@if(!empty(Session::get('subscribe_status')))
<script>
   $(function () {
       $('#subscribe_message_modal').modal('show');
   });
</script>
{{-- when someone successfully subscribes to the newsletter --}}
<div id="subscribe_message_modal" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">        <h4 class="modal-title">Thank you!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
    
         </div>
         <div class="modal-body">
            <p>{{ session('subscribe_status') }}</p>
         </div>
      </div>
   </div>
</div>
@endif
@if(session('edit_email_verify_fail'))
<script>
   $("#email_edit_text").html('{{session('edit_email_verify_fail')}}');
   $("#email_verify_modal").modal('show');
</script>
@endif
{{--Partner or non user cannot like a review--}}
<div id="nonClickableLike" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">         <h4 class="modal-title">Sorry!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
   
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <div>
               @if(Session::has('partner_id'))
               <p>You are not allowed to like other partner's review</p>
               @else
               <p>You need to be a Royalty Member to be able to like reviews. <a href="{{ url('login') }}">Sign up/Login</a> to like this review.</p>
               @endif   
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   function myNavFunction(id) {
       $("#date-popover").hide();
       var nav = $("#" + id).data("navigation");
       var to = $("#" + id).data("to");
   }
</script>
{{--for partner account ends--}}
{{--script for owl carousel--}}
<script>
   $(document).ready(function (c) {
       $('.alert-close').on('click', function (c) {
           $(this).parent().fadeOut('slow', function (c) {
           });
       });
   });
</script>
<script async>
   $(document).ready(function () {
      var owl = $('.owl-carousel');
      owl.owlCarousel({
          loop: true,
          margin: 24,
          responsiveClass: true,
          responsive: {
              0: {
                  items: 2,
                  nav: true,
                  loop: false
              },
              600: {
                  items: 2,
                  nav: false,
                  loop: false
              },
              1000: {
                  items: 4,
                  nav: true,
                  loop: false
              }
          }
      })
   });
</script>
{{--========================================================================= --}}
{{--jaquery to fade out Preloader--}}
{{--=========================================================================--}}
<script>
   jQuery(window).on('load', function () {
       $("#preloader").fadeOut("slow");
   });
</script>
{{--function to call lazy load of image (when user scroll)--}}
<script>
   lazyload();
</script>
{{-- ============================================================================================================
========================search suggestion with JavaScript & Ajax====================
============================================================================================================= --}}
<script>
   $(document).ready(function () {
       var $window = $(window);
       if ($window.width() < 992) {
           $("#searchForm").removeClass('active');
           $("#small-search").addClass('active');
       } else {
           $("#small-search").removeClass('active');
           $("#searchForm").addClass('active');
       }

       var _changeInterval = null;
       var search_id;
       var search_hint;
       if($("#searchForm.active").length > 0) {//large screen
           search_id = 'searchname';
           search_hint = 'txtHint';
       }else if($("#small-search.active").length > 0){//small screen
           search_id = 'small_searchname';
           search_hint = 'small_txtHint';
       }
       var launchSearch = function () {
           $('#'+search_id).unbind().on('keyup', function () {
               $value = $(this).val();
               clearInterval(_changeInterval)
               _changeInterval = setInterval(function() {
                   var format = /[!@#$%^~*()_+\-=\[\]{};:"\\|,.<>\/?]/;
                   if (format.test($value)) {
                       return false;
                   }
                   $.ajax({
                       type: 'get',
                       url: '{{ url('autocomplete') }}',
                       // send data to function through autocomplete route
                       data: {'search': $value},
                       success: function (data) {
                           $('#'+search_hint).html(data).show();
                       }
                   });
                   clearInterval(_changeInterval)
               }, 1000);
   
           });
       };
       //show again suggestion box after clicking anywhere
       $("#"+search_id).keyup(launchSearch).click(launchSearch);
       // hide when click anywhere in the body
       $('body').click(function () {
           $("#"+search_hint).hide();
       });
   });
</script>
{{-- ============================================================================================================
========================follow user functionality with JavaScript & Ajax====================
============================================================================================================ --}}
<script defer>
   $(document).on('click', '#follow-user', function () {
       var url = "{{url('/follow-user')}}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id': this.value},
           success: function (data) {
               //nothing
           }
       });
   });
</script>
{{-- ============================================================================================================
========================Unfollow user functionality with JavaScript & Ajax====================
============================================================================================================ --}}
<script defer>
   $(document).on('click', '#unfollow-user', function () {
       var url = "{{ url('/unfollow-user') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id': this.value},
           success: function (data) {
               $('.unfollow-user-' + data).hide();
               //elements by class always return an array. For this I had to write below code
               var className = document.getElementsByClassName('follow-user-' + data);
               for (var idx = 0; idx < className.length; idx++) {
                   className[idx].style.display = 'unset';
                   className[idx].style.visibility = 'visible';
               }
           }
       });
   });
</script>
{{-- ============================================================================================================
========================cancel user follow request with JavaScript & Ajax====================
============================================================================================================ --}}
<script defer>
   $(document).on('click', '#cancel-follow-request', function () {
       var url = "{{ url('/cancel-follow-request') }}";
   
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id': this.value},
           success: function (data) {
               //nothing
               var className = document.getElementsByClassName('follow-requested-' + data);
               for (var idx = 0; idx < className.length; idx++) {
                   className[idx].style.display = 'none';
               }
               //elements by class always return an array. For this I had to write below code
               var className = document.getElementsByClassName('follow-user-' + data);
               for (var idx = 0; idx < className.length; idx++) {
                   className[idx].style.display = 'unset';
                   className[idx].style.visibility = 'visible';
               }
           }
       });
   });
</script>
{{-- ============================================================================================================
========================Follow partner functionality with JavaScript & Ajax==================
============================================================================================================ --}}
<script defer>
   $(document).on('click', '#follow-partner', function () {
       var url = "{{ url('/follow-partner') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id': this.value},
           success: function (data) {
               //nothing
               $('.follow-partner-' + data).hide();
               //elements by class always return an array. For this I had to write below code
               var className = document.getElementsByClassName('unfollow-partner-' + data);
               for (var idx = 0; idx < className.length; idx++) {
                   className[idx].style.display = 'unset';
                   className[idx].style.visibility = 'visible';
               }
           }
       });
   });
</script>
{{-- ============================================================================================================
========================Unfollow partner functionality with JavaScript & Ajax====================
============================================================================================================ --}}
<script defer>
   $(document).on('click', '#unfollow-partner', function () {
       var url = "{{ url('/unfollow-partner') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id': this.value},
           success: function (data) {
               //nothing
               $('.unfollow-partner-' + data['partner_id']).hide();
               //elements by class always return an array. For this I had to write below code
               var className = document.getElementsByClassName('follow-partner-' + data['partner_id']);
               for (var idx = 0; idx < className.length; idx++) {
                   className[idx].style.display = 'unset';
                   className[idx].style.visibility = 'visible';
               }
               // update total followers number
               if (document.getElementById('total_followers_of_partner')) {
                   if (data['total_followers'] > 1) {
                       var followers = data['total_followers'] + ' Followers';
                   } else {
                       var followers = data['total_followers'] + ' Follower';
                   }
                   document.getElementById('total_followers_of_partner').innerHTML = followers;
               }
               //update followers list of this partner
               if (document.getElementById('followersModal')) {
                   document.getElementById('followersModal').innerHTML = data['followers_list'];
               }
           }
       });
   });
</script>
{{-- ============================================================================================================
========================Like & Unlike functionality in review section with JavaScript & Ajax====================
============================================================================================================ --}}
<script async>
   $(document).on('click', '.like-review', function () {
       //initiate url
       var url = "{{ url('/like') }}";
       //get value to pass
       var value = this.value;
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id': value},
           success: function (data) {
               // nothing
           }
       });
   });
   
   $(document).on('click', '.unlike-review', function () {
       var source_id = $(this).attr('data-source');
       //initiate url
       var url = "{{ url('/unlike') }}";
       //get value to pass
       var value = this.value;
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id': value, 'source_id': source_id},
           success: function (data) {

           }
       });
   });
</script>
{{-- ============================================================================================================
========================Like post functionality with JavaScript & Ajax====================
============================================================================================================ --}}
<script async>
   $(document).on('click', '.like-post', function () {
       var post_id = this.value;
       var url = "{{ url('/likePost') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'post_id': post_id},
           success: function (data) {
               // add animation to like button
               $("#postLike-"+data['post_id']).html('<i class="love-f-icon"></i>');
               $("#postLike-"+data['post_id']).children('.love-f-icon').addClass('animate-like');
   
               //add code to unlike
               $("#postLike-"+data['post_id']).addClass('unlike-post').removeClass('like-post');
               $("#postLike-"+data['post_id']).attr("data-source", data['like_id']);
               var like_text = (data['total_likes_of_post'] > 1 ? " likes" : " like");
               document.getElementById('likes_of_post_' + data['post_id']).innerHTML = data['total_likes_of_post'] +like_text;
           }
       });
   });
   $(document).on('click', '.unlike-post', function () {
       var source_id = $(this).attr('data-source');
       var value = this.value;
       var url = "{{ url('/unLikePost') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id':value, 'source_id': source_id},
           success: function (data) {
               // add animation to like button
               $("#postLike-"+data['post_id']).html('<i class="love-e-icon"></i>');
   
               //add code to unlike
               $("#postLike-"+data['post_id']).addClass('like-post').removeClass('unlike-post');
               $("#postLike-"+data['post_id']).attr("data-source", data['like_id']);
               var like_text = (data['total_likes_of_post'] > 1 ? " likes" : " like");
               document.getElementById('likes_of_post_' + data['post_id']).innerHTML = data['total_likes_of_post'] +like_text;
           }
       });
   });
</script>
{{--Copy to clipboard refer code--}}
<script defer>
   function copyToClipboard(text, el) {
       var copyTest = document.queryCommandSupported('copy');
       var elOriginalText = el.attr('data-original-title');
   
       if (copyTest === true) {
           var copyTextArea = document.createElement("textarea");
           copyTextArea.value = text;
           document.body.appendChild(copyTextArea);
           copyTextArea.select();
           try {
               var successful = document.execCommand('copy');
               var msg = successful ? 'Copied!' : 'Whoops, not copied!';
               el.attr('data-original-title', msg).tooltip('show');
           } catch (err) {
               //
           }
           document.body.removeChild(copyTextArea);
           el.attr('data-original-title', elOriginalText);
       } else {
           // Fallback if browser doesn't support .execCommand('copy')
           window.prompt("Copy to clipboard: Ctrl+C or Command+C, Enter", text);
       }
   }
   
   $(document).ready(function () {
       // Requires Bootstrap 3 for functionality
       $('.js-tooltip').tooltip();
       $('.js-copy').click(function () {
         var text = $(this).attr('data-copy');
         var el = $(this);
         copyToClipboard(text, el);
         toastr.success('Your refer code has been copied');
       });
   });
</script>
{{--Copy to clipboard refer code ends--}}

{{-- ============================================================================================================
========================partner admin login with JavaScript & Ajax====================
============================================================================================================= --}}
<script defer>
   function partnerAdminLogin(e){
       var url = "{{ url('/partner/admin') }}";
       var code = $("#partner_admin_code").val();
       $.ajax({
           type: "POST",
           url: url,
           async: false,
           data: {'_token': '<?php echo csrf_token(); ?>', 'code': code},
           success: function (data) {
               //nothing
               if (data == '1') {
                   window.open('<?php echo url('/partner/adminDashboard/' . session("partner_username")); ?>', '_blank');
                   e.preventDefault();
               } else {
                   alert('didn\'t match');
                   e.preventDefault();
               }
           }
       })
   }
   
   $("#partner-admin-login").submit(function(e){
       partnerAdminLogin(e);
   });
   
   $(document).on('click', '#partnerAdminDashboard', function (e) {
       partnerAdminLogin(e);
   });
</script>
{{--password eye--}}
<script defer>
   $(".toggle-password").click(function () {
       $(this).toggleClass("fa-eye-slash fa-eye");
       var input = $($(this).attr("toggle"));
       if (input.attr("type") == "password") {
           input.attr("type", "text");
       } else {
           input.attr("type", "password");
       }
   });
</script>
{{--avail coupon modal--}}
<div id="coupon-availed-modal" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">     <h4 class="modal-title">Coupon availed</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
       
         </div>
         <div class="modal-body">
            <div class="no-info">
               <p>{{session('coupon-selected')}}</p>
            </div>
         </div>
      </div>
   </div>
</div>
@if(session('coupon-selected'))
<script>
   $('#coupon-availed-modal').modal('show');
</script>
@endif
{{--Try Again Modal For Any Backend Operation Failure--}}
<div id="try-again-modal" class="modal" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">Sorry!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
          
         </div>
         <div class="modal-body">
            <p>{{ session('try_again') }}</p>
         </div>
      </div>
   </div>
</div>
@if(session('try_again'))
<script>
   $('#try-again-modal').modal('show');
</script>
@endif
{{--Review Delete Modal For Successful Operation--}}
<div id="review-delete-modal" class="modal" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">          <h4 class="modal-title">Successful!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
  
         </div>
         <div class="modal-body">
            <div class="no-info">
               <p>{{ session('review_deleted') }}</p>
            </div>
         </div>
      </div>
   </div>
</div>
@if(session('review_deleted'))
<script>
   $('#review-delete-modal').modal('show');
</script>
@endif
{{--Refer Bonus Redeem Modal For Any Backend Operation Failure--}}
<div id="refer-bonus-modal" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">   <h4 class="modal-title">Congratulations!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         
         </div>
         <div class="modal-body">
            <div class="no-info">
               <?php
                  $pname = str_replace("'", "", session('bonus_redeemed'));
                  ?>
               <p>You have successfully redeemed a cash coupon worth ৳250 for
                  <a href="{{url('partner-profile/'.$pname.'/'.session('branch_id'))}}">{{$pname}}</a>.
                  This coupon will expire in 7 days.
               </p>
            </div>
         </div>
      </div>
   </div>
</div>
@if (session('bonus_redeemed'))
<script>
   $('#refer-bonus-modal').modal('show');
</script>
@endif
{{--mail verified modal--}}
<div id="mail_verified_modal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">Successful!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
          
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <div class="no-info">
               <p>
                  {{session('email_verified')}}
                  @if(session('customer_username'))
                  Continue to your <a href="{{url("users/". Session::get('customer_username'))}}">Account</a>.
                  @else
                  Please <a href="{{url("login")}}">Login</a> to continue
                  @endif
               </p>
            </div>
         </div>
      </div>
   </div>
</div>
@if(session('email_verified'))
<script>
   $('#mail_verified_modal').modal('show');
</script>
@endif
{{--For Confirmation of E-mail Verification Mail STARTS--}}
<div id="email_verification_sent" class="modal" role="dialog">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h4 class="modal-title">Mail Sent</h4>
               <button type="button" class="close" data-dismiss="modal">
                <i class="cross-icon"></i>
                </button>
            </div>
         <div class="modal-body">
            <div>
               @if(session('email verification sent'))
               <div class="center">
                  <p>A verification link & a code has been sent to <br> <b>{{ session('reg_change_email') }}. Please check all your email folders.</b></p>
                  <p>You can verify your email by clicking the link or entering the code below.</p>
               </div>
               @elseif(session('email_verify_fail'))
               <p class="text-danger middle">{{session('email_verify_fail')}}</p>
               @elseif(session('code_already_sent'))
               <p class="middle">{{session('code_already_sent')}}</p>
               @endif
               <form class="form-horizontal form-label-left text-center" method="post" action="{{ url('useracc/verify_email') }}">
                  <div class="form-group">
                     <div class="col-sm-offset-2 col-sm-8 col-xs-offset-2 col-xs-8">
                        <label for="verifying_code"></label>
                        <input type="text" class="form-control" placeholder="Enter verification code"
                           name="verifying_code" id="verifying_code" minlength="6" maxlength="6" required>
                     </div>
                  </div>
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <div class="ln_solid"></div>
                  <div class="form-group center">
                     <p style="display: contents">
                        <button type="submit" class="btn btn-primary verify_button">Verify</button>
                     </p>
                  </div>
                  <a href="#" style="font-size: 12px;" onclick="showEmailEditModalAgain()">I want to edit my email</a>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
{{--********************turned off paid membership************************--}}
{{--@if(Session::has('customer_email_verified') && Session::get('customer_email_verified') ==0)--}}
{{--@if(session('email verification sent'))--}}
{{--@else--}}
{{--<script>--}}
{{--   $("#email_verify_modal").modal({backdrop: 'static', keyboard: false});--}}
{{--</script>--}}
{{--@endif--}}
{{--@endif--}}
<script>
   function showEmailEditModalAgain() {
       $('#email_verification_sent').css('display', 'none');
       $("#email_verify_modal").css('display', 'block').modal('show');
   }
</script>
@if(session('email verification sent') || session('email_verify_fail') || session('code_already_sent'))
<script>
   $("#email_verify_modal").css('display', 'none');
   $('#email_verification_sent').modal('toggle');
   // $('#email_verification_sent').modal({backdrop: 'static', keyboard: false});
</script>
@endif
<div id="email_verify_success" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">   <h4 class="modal-title">Successful</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         
         </div>
         <div class="modal-body">
            <div>
               <p>{{session('email_verify_success')}}</p>
            </div>
         </div>
      </div>
   </div>
</div>
@if(session('email_verify_success'))
<script>
   $('#email_verify_success').modal('show');
</script>
@endif
@if(session('customer_id') && url()->current() != url('registration/verify_email'))
<script async>
   //update email verified status & refresh cur page
   var checkEmailVerifyChannel = pusher.subscribe('check_email_verify_status');
   checkEmailVerifyChannel.bind('redirect_if_email_verified', function(response) {
       var data = response['data'];
       var customer_id = "<?php echo session('customer_id'); ?>";
       if(customer_id == data.customer_id && data.email_verified == 1){
           <?php session(['customer_email_verified' => 1]); ?>
           window.location = '{{url()->current()}}';
       }
   });
   
   //user force logout
   var userForceLogoutChannel = pusher.subscribe('user_logout');
   userForceLogoutChannel.bind('user_force_logout', function(response) {
       var data = response['data'];
       var customer_id = "<?php echo session('customer_id'); ?>";
       if(customer_id == data.customer_id){
         var url = "{{ url('/customer_logout') }}";

         $('<form action="' + url + '" method="POST">' +
         '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
         '</form>').appendTo($(document.body)).submit();
       }
   });
</script>
@endif
{{-- if server error --}}
<div id="server_error" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">Mail Failed</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
          
         </div>
         <div class="modal-body">
            <div class="no-info">
               <p>{{session('server_error')}}</p>
            </div>
         </div>
      </div>
   </div>
</div>
@if(session('server_error'))
<script>
   $('#server_error').modal('show');
</script>
@endif
{{-- If E-mail is invalid --}}
<div id="email_not_exist" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">Mail Failed</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
          
         </div>
         <div class="modal-body">
            <div class="no-info">
               <p>{{session('email not exist')}}</p>
            </div>
         </div>
      </div>
   </div>
</div>
{{--modal to show branch list of a partner--}}
<div id="profile-modal" class="modal" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header"> <h4 class="modal-title partner-name-in-modal"></h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
           
         </div>
         <div class="modal-body">
            <div class="partner-branches">
               <ul id="branch_list"></ul>
            </div>
         </div>
      </div>
   </div>
</div>
{{--branch list modal ends--}}
@if(session('email not exist'))
<script>
   $('#email_not_exist').modal('show');
</script>
@endif
{{--For Confirmation of E-mail Verification Mail ENDS--}}
{{-- for mobile devices show this modal --}}
<div id="iammobiledevice" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background-color: #f5f5f5; color: #333333">     <h4 class="modal-title">Browsing from phone? Download our app!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
       
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12 col-sm-12 col-xs-12" id="device_ios">
                  <a href="{{url('http://bit.ly/RBDIOSAPP')}}" target="_blank">
                  <img class="lazyload footer-apple"
                     src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/appstore.png"
                     alt="Royalty Applestore Icon"/>
                  </a>
               </div>
               <div class="col-md-12 col-sm-12 col-xs-12" id="device_android">
                  <a rel="noopener" href="{{url('http://bit.ly/RBDANDROID')}}" target="_blank">
                  <img class="lazyload footer-play"
                     src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/playstore.png"
                     alt="Royalty Playstore Icon"/>
                  </a>
               </div>
            </div>
         </div>
         <div class="modal-footer" style="background-color: #f5f5f5; color: #333333">
            <button class="btn btn-primary close" data-dismiss="modal">Continue to website</button>
         </div>
      </div>
   </div>
</div>
<!-- liker modal -->
<div id="likerModal" class="modal" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header"> <h4 class="modal-title">                
               Likes
            </h4>
            <button type="button" class="close" data-dismiss="modal"><i class="cross-icon"></i></button>
           
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <ul class="likerList"></ul>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
{{-- DOB success modal --}}
<div id="birthdayUpdateModal" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header"> <h4 class="modal-title">Successful</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
           
         </div>
         <div class="modal-body">
            <div>
               <p>{{session('dob_updated')}}</p>
            </div>
         </div>
      </div>
   </div>
</div>

{{--=================================================================================================
==============================show something at first visit with cookie value ends========================
=================================================================================================--}}
{{--pop up script for all offers --}}
<script>
   $(document).ready(function () {
       //select the POPUP FRAME and show it
       $("#popup").hide().fadeIn(1000);
   
       //close the POPUP if the button with id="close" is clicked
       $("#close").on("click", function (e) {
           e.preventDefault();
           $("#popup").fadeOut(1000);
       });
   });
</script>
{{--validate search field with javascript--}}
<script async>
   $(document).ready(function () {
       var search_id;
       var search_form;
       if($("#searchname").length) {//large screen
           search_id = 'searchname';
           search_form = 'searchForm';
       }else if($("#small_searchname").length){//small screen
           search_id = 'small_searchname';
           search_form = 'small-search';
       }
       $('#'+search_form).submit(function () {
           var val = document.getElementById(search_id).value;
           var format = /[!@#$%^~*()_+\-=\[\]{};':"\\|,.<>\/?]/;
           if (format.test(val)) {
               return false;
           } else {
               //check if field contains nothing or only spaces
               if (val === '' || !val.replace(/\s/g, '').length || val == 0) {
                   return false;//stops form from being submitted
               } else {
                   return true;//allow form to be submitted
               }
           }
       });
   });
</script>
{{--For the navbar added on 24.6.2018--}}
<script>
   $(function () {
       $('[data-tooltip="tooltip"]').tooltip()
   });
</script>
{{--For the navbar added on 24.6.2018--}}
{{--save current url to redirect this page after login--}}
<script>
   function prev_login() {
       var cur_url = '{{url()->current()}}';
       localStorage.setItem('prev_url_at_login', cur_url);
       $('#partnerReviewModal').modal('toggle');
   }
</script>
<script>
   @if(session()->has('birthdayGiftExpired'))
   $('#birthdayGiftExpiredModal').modal('show');
   @endif
</script>
<script>
   @if(session()->has('dob_updated'))
   $('#birthdayUpdateModal').modal('show');
   @endif
</script>
{{--=================================================================================================
==============================show pop up at first visit with cookie value========================
=================================================================================================--}}
{{-- @if(Session::has('customer_id') || Session::has('partner_id')) --}}
{{--nothing to do--}}
{{-- @else --}}
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
   
      $(".popup-close").on("click", function (e) {
          $("#popup1").css("display", "none");
      });
   });
</script>
{{-- @endif --}}
@if(session('review_does_not_exist'))
<script>
   $('#reviewDoesNotExist1').modal('show');
</script>
@endif
{{--=================================================================================================
==============================SOCIAL POST SHARE AND COUNT========================
=================================================================================================--}}
<script defer>
   function update_facebook_count(post_id, post_url) {
       var url = "{{ url('/post-share-count') }}";
       $.ajax({
       type: "POST",
       url: url,
       data: {'_token': '<?php echo csrf_token(); ?>', 'post_id': post_id},
       success: function (data) {
           window.open(post_url);
       }
       });
   }
   
   function update_twitter_count(post_id, post_text) {
       var url = "{{ url('/post-share-count') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'post_id': post_id},
           success: function (data) {
           window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(post_text));
           }
       });
   }
</script>
<script async>
   function showLocationModal(param) {
       var url = "{{ url('/partner-locations-for-modal') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {
               '_token': '<?php echo csrf_token(); ?>',
               'partner_id' : param
           },
           success: function (data) {
               $(".partner-name-in-modal").text(data['name']);//set modal title
               $("#branch_list").html(data['locations']);//set modal body
               $('#profile-modal').modal('toggle');//show modal
           }
       });
   }
   
   function getReviewLikerList(review_id) {
       var url = "{{ url('/review_liker_list') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {
               '_token': '<?php echo csrf_token(); ?>', 'review_id' : review_id},
           success: function (data) {
               if(data.length !== 0){
                   $("#likerModal").modal('toggle');
                   var output = '';
                   var i;
                   for (i = 0; i < data.length; i++) {
                       output += "<li class='liker'>";
                       output += "<img class='liker_img' src='"+data[i]['liker_image']+"' width='100px' height='100px' alt='Profile Image'>";
                       output += "<p class='liker_name'>"+data[i]['liker_name']+"</p>";
                       output += "</li>";
                   }
                   $(".likerList").hide().html(output).fadeIn('slow');
               }
           }
       });
   }
   function getNewsFeedLikerList(news_id) {
       var url = "{{ url('/news_feed_liker_list') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {
               '_token': '<?php echo csrf_token(); ?>',
               'post_id' : news_id
           },
           success: function (data) {
               if(data.length !== 0){
                   $("#likerModal").modal('toggle');
                   var output = '';
                   var i;
                   for (i = 0; i < data.length; i++) {
                       output += "<li class='liker'>";
                       output += "<img class='liker_img' src='"+data[i]['liker_image']+"' width='100px' height='100px' alt='Profile Image'>";
                       output += "<p class='liker_name'>"+data[i]['liker_name']+"</p>";
                       output += "</li>";
                   }
                   $(".likerList").hide().html(output).fadeIn('slow');
               }
           }
       });
   }
   
   function verifySubmitMail() {
       var page = $("#email_edit_or_verify").val();
       if(page === 'edit'){
           var email = $("#verifying_mail").val();
           var existing_email = '{{session("customer_email")}}';
           if(email === existing_email){
               return false;
           }
       }
       validateVerificationEmail();
       $(".loading-gif").css('display', 'inline-block');
       $('.verify_button').prop('disabled', true);
       return true;
   }
</script>
<!-- CUSTOMER LOGOUT -->
<script type="text/javascript">
   function customerLogout(){
      var url = "{{ url('/customer_logout') }}";

      $('<form action="' + url + '" method="POST">' +
         '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
         '</form>').appendTo($(document.body)).submit();
   }
</script>
{{-- <script>
   function getMobileOperatingSystem() {
      var userAgent = navigator.userAgent || navigator.vendor || window.opera;
   
      if( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) )
      {
          $("#welcome_popup1").hide();
          $("#device_android").hide();
         $("#iammobiledevice").modal('toggle');
     }
      else if( userAgent.match( /Android/i ) )
     {
          $("#welcome_popup1").hide();
          $("#device_ios").hide();
          $("#iammobiledevice").modal('toggle');
       }
    }
    getMobileOperatingSystem();
</script> --}}
{{--load body after loading all contents--}}

<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>

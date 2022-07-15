@include('header')
<style>
   .shareUrl-input {
   cursor: pointer;
   }
   .shareUrl-headerText {
   margin-top: 0;
   margin-bottom: 10px;
   font-size: 22px;
   }
   .shareUrl-input {
   width: 100%;
   border: 2px solid rgba(0, 0, 0, 0.09);
   text-align: center;
   font-weight: bold;
   -webkit-transition: all 300ms ease;
   transition: all 300ms ease;
   }
   .shareUrl-input:hover, .shareUrl-input:focus, .shareUrl-input:active {
   border-color: rgba(0, 0, 0, 0.3);
   background: rgba(255, 255, 255, 0.1);
   }
   @media (min-width: 568px) {
   .shareUrl-headerText {
   font-size: 32px;
   }
   }
   .u-flexCenterHorizontal {
   display: -webkit-box;
   display: flex;
   -webkit-box-pack: center !important;
   justify-content: center !important;
   }
   #copied-tooltip {
      display: none;
      padding: 5px 12px;
      background-color: #000000df;
      border-radius: 4px;
      color: #fff;
      position: absolute;
      right: 10px;
      bottom: 14px;
   }
   #copied-tooltip::after {
      content: "";
      position: absolute;
      bottom: 100%;
      left: 50%;
      margin-left: -5px;
      border-width: 5px;
      border-style: solid;
      border-color: transparent transparent #000;
   }
</style>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <!-- <h2>Find your profile details, usages, rewards all together here</h2> -->
         <p>PERSONAL INFORMATION</p>
      </div>
   </div>
</section>
<section class="counts">
   <div class="container">
      <div class="row">
         <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="sidebar mb-3 shadow">
               @include('useracc.sidebar')
            </div>
         </div>
         <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
            <div class="row">
               <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                  <div data-aos="zoom-in" data-aos-delay="200">
                     <h3 class="graybox-head">
                        PERSONAL INFO
                     </h3>
                     <div class="graybox center">
                     <ul>
                        <li>  <?php if($customer_data->customer_email == '') {
                           echo 'No E-mail';
                           }
                           else
                           {?>
                           ✉️ {{ $customer_data->customer_email }}
                           <?php }?>
                        </li>
                        <li>&#x1F4DE; {{ $customer_data->customer_contact_number }}</li>
                        @if($customer_data->card_active > 1)
                        <li>        <?php $date = date_create($customer_data->expiry_date);?>
                           @if($customer_data->exp_status == 'expired')
                           📅 <span style="color: red;">Expired on: {{date_format($date, 'M d, Y')}}</span>
                           @else
                           Expiry Date: {{date_format($date, 'M d, Y')}}
                           @endif
                        </li>
                        @endif
                        <!-- @if($customer_data->card_active > 1)
                        <li>📇 {{ $customer_data->customerID }}</li>
                        @endif -->
                        @if ($customer_data->customer_dob != null)
                        <li>  🎂 {{date("d F, Y", strtotime($customer_data->customer_dob))}}</li>
                        @endif
                     </ul>
                  </div>
               </div>
               </div><br>
               <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                  <div id="text-carousel" class="carousel slide" data-ride="carousel">
                     <div class="row">
                        <div class="col-xs-12">
                           <div class="carousel-inner">
                              <div class="item active">
                                 <div class="carousel-content">
                                    <div>
                                       <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/banner/b-1.png"
                                          style="width: 100%" class="lazyload" alt="Royalty User Banner">
                                    </div>
                                 </div>
                              </div>
                              <div class="item">
                                 <div class="carousel-content">
                                    <div>
                                       <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/banner/b-3.jpg"
                                          style="width: 100%" class="lazyload" alt="Royalty User Banner">
                                    </div>
                                 </div>
                              </div>
                              <div class="item">
                                 <div class="carousel-content">
                                    <div>
                                       <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/banner/b-4.png"
                                          style="width: 100%" class="lazyload" alt="Royalty User Banner">
                                    </div>
                                 </div>
                              </div>
                              <div class="item">
                                 <div class="carousel-content">
                                    <div>
                                       <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/banner/b-5.jpg"
                                          style="width: 100%" class="lazyload" alt="Royalty User Banner">
                                    </div>
                                 </div>
                              </div>
                              <div class="item">
                                 <div class="carousel-content">
                                    <div>
                                       <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/banner/b-6.png"
                                          style="width: 100%" class="lazyload" alt="Royalty User Banner">
                                    </div>
                                 </div>
                              </div>
                              <div class="item">
                                 <div class="carousel-content">
                                    <div>
                                       <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/banner/b-7.png"
                                          style="width: 100%" class="lazyload" alt="Royalty User Banner">
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <a class="left carousel-control" href="#text-carousel" data-slide="prev"
                        style="background-image: none">
                     <span class="glyphicon glyphicon-chevron-left"></span>
                     </a>
                     <a class="right carousel-control" href="#text-carousel" data-slide="next"
                        style="background-image: none">
                     <span class="glyphicon glyphicon-chevron-right"></span>
                     </a>
                  </div>
               </div>
            </div>
            <br>
            @if($customer_data->profile_completed < 100)
            <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <div data-aos="zoom-in" data-aos-delay="200">
               <h3 class="graybox-head">
                     COMPLETE YOUR PROFILE
                  </h3>
                        <div class="profile-completation-bar graybox center" id="profile_completion">
                           <div class="progress-bar-container mtb-10">
                              <div class="progress" style="margin: unset">
                                 <div class="progress-bar progress-bar-success progress-bar-striped"
                                    role="progressbar"
                                    aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"
                                    style="width:{{$customer_data->profile_completed}}%;">
                                 </div>
                              </div>
                              <span class="completed_percentage">{{$customer_data->profile_completed}}%</span>
                           </div>
                           More Credits, More Rewards.
                           <p>Complete your profile to earn 10 Royalty Credits.</p>
                        </div>
               </div>
            </div>
            </div>
            <br>
            @endif
            
            @if($customer_data->customer_gender == null)
            <div class="row" id="hide-gender">
               <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div data-aos="zoom-in" data-aos-delay="200">
                     <h3 class="graybox-head">TELL US A BIT ABOUT YOURSELF</h3>
                        <div class="graybox center">
                           <p>Hey {{ session('customer_full_name') }},</p>
                           <p>Choose your team!</p>
                           <div class="gender-container mtb-10">
                              <div class="gender-box mlr">
                                 <input type="button" class="gender-type male" onclick="updateGender('male')" style="background-image: url(https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/Male.png);">
                                 <br>
                                 <span class="mf-text">Male</span>
                              </div>
                              <div class="gender-box mlr">
                                 <input type="button" class="gender-type female" onclick="updateGender('female')" style="background-image: url(https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/Female.png);">
                                 <br>
                                 <span class="mf-text">Female</span>
                              </div>
                           </div>
                        </div>
                  </div>
               </div>
            </div>
            <br>
            @endif
            
            @if($customer_data->customer_dob == null)
            <div class="row" id="hide-dob">
               <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div data-aos="zoom-in" data-aos-delay="200">
                     <h3 class="graybox-head">
                        <b>WE WANT TO KNOW MORE ABOUT YOU</b>
                     </h3>
                           <div class="missing-info-body graybox center">
                              <p>Time to give you a surprise on:</p>
                              <div class="birthday-select">
                                 <?php
                                    //Day
                                    echo '<label for="birth_day"></label>';
                                    echo '<select name="birth_day" id="birth_day" style="border-radius:5px;padding: 0 0 3px 5px">';
                                    echo '<option selected disabled>Day</option>';
                                    for ($i = 1; $i <= 31; $i++) {
                                       $i = str_pad($i, 2, 0, STR_PAD_LEFT);
                                       echo "<option value='$i'>$i</option>";
                                    }
                                    echo '</select>-';
                                    //Month
                                    echo '<label for="birth_month"></label>';
                                    echo '<select name="birth_month" id="birth_month" style="border-radius:5px;padding: 0 0 3px 5px">';
                                    echo '<option selected disabled>Month</option>';
                                    for ($i = 1; $i <= 12; $i++) {
                                       $i = str_pad($i, 2, 0, STR_PAD_LEFT);
                                       echo "<option value='$i'>$i</option>";
                                    }
                                    echo '</select>-';
                                    //Year
                                    echo '<label for="birth_year"></label>';
                                    echo '<select name="birth_year" id="birth_year" style="border-radius:5px;padding: 0 0 3px 5px">';
                                    echo '<option selected disabled>Year</option>';
                                    for ($i = date('Y'); $i >= date('Y', strtotime('-100 years')); $i--) {
                                       echo "<option value='$i'>$i</option>";
                                    }
                                    echo '</select>';
                                    ?>
                                 <span class="invalid_dob" style="display: none; margin-left: 10px"></span>
                              </div>
                              <button class="btn-green-round" onclick="updateDOB()">Confirm</button>
                           </div>
                  </div>
               </div>
            </div>
            <br>
            @endif
            
            @if($customer_data->customer_profile_image == 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png')
            <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <div data-aos="zoom-in" data-aos-delay="200">
                  <h3 class="graybox-head">
                     PLEASE UPLOAD YOUR IMAGE
                  </h3>
                        <div class="missing-info-body graybox center" id="hide-dob">
                           <p></p>
                           <div class="birthday-select">
                              <button onclick="location.href='{{url("edit-profile")}}'" class="btn-green-round">Upload Image</button>
                           </div>
                     </div>
               </div>
            </div>
            </div>
            <br>
            @endif
            
            <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <div data-aos="zoom-in" data-aos-delay="200">
                  <h3 class="graybox-head">
                     REFERRAL CODE
                  </h3>
                        <div class="missing-info-body graybox center" id="hide-dob">
                           <div class="shareUrl u-verticalGrid u-marginAuto u-size1040">
                              <div class="shareUrl-header">
                                 <p>Your unique referral code: {{$customer_data->referral_number}}</p>
                                 <p class="shareUrl-headerSubtext">Total Referral Credits Earned: {{$customer_data->refer_credits}} </p>
                              </div>
                              <div class="shareUrl-body">
                                 <div>
                                    <span id="copied-tooltip">Copied!</span>
                                    <input class="shareUrl-input js-shareUrl" type="text" readonly="readonly" />
                                    <p>Click above to share the code with your Friends & Family.</p>
                                 </div>
                              </div>
                              <footer class="shareUrl-footer">
                              </footer>
                              <a href="{{url('refer_leaderboard')}}"><u>See Leaderboard</u></a>
                           </div>
                        </div>
               </div>
               </div>
            </div>
            <br>
            
            <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <div data-aos="zoom-in" data-aos-delay="200">
                  <h3 class="graybox-head">
                    E-MAIL SUBSCRIPTION
                  </h3>
                  <div class="graybox center">
                     @if($customer_data->subscribed != null)
                     <div id="subscribed">
                        <p>Tired of our newsletters? You can un-subscribe from here by pressing the button below.</p>
                        <button class="btn btn-secondary" id="unsubscribe"
                           value="{{$customer_data->customer_email}}">
                        Unsubscribe
                        </button>
                     </div>
                     <div id="unsubscribed" style="display: none;">
                        <p>Want to know more about our offers? Get exclusive updates by pressing the button below.</p>
                        <button class="btn-green-round" id="subscribeAgain"
                           value="{{$customer_data->customer_email}}">
                        Subscribe
                        </button>
                     </div>
                     @else
                     <div id="unsubscribed">
                        <p>Want to know more about our offers? Get our exclusive updates by pressing the button below</p>
                        <button class="btn-green-round" id="subscribeAgain"
                           value="{{$customer_data->customer_email}}">Subscribe
                        </button>
                     </div>
                     <div id="subscribed" style="display: none;">
                        <p>Tired of our newsletters? You can un-subscribe from here by pressing the button below.</p>
                        <button class="btn btn-secondary" id="unsubscribe"
                           value="{{$customer_data->customer_email}}">Unsubscribe
                        </button>
                     </div>
                     @endif
                  </div>
               </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- reward redeem success Modal-->
<div id="profileCompletedModal" class="modal fade" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header"> <h4 class="modal-title">Success</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
           
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <p class="profile_complete_success_msg">Congratulations! You have earned 10 Royalty Credits by completing
               your profile.
            </p>
         </div>
      </div>
   </div>
</div>
@include('useracc.commonDivs')
@include('footer')
@include('footer-js.user-account-js')
<script>
   (function() {
    
    // Create reusable copy fn
    function copy(element) {
        
        return function() {
           $("#copied-tooltip").css('display','inline-block');
          document.execCommand('copy', false, element.select());
           setTimeout( function() {
              $("#copied-tooltip").css('display','none');
           }, 1000);
        }
    }
    
    // Grab shareUrl element
    var shareUrl = document.querySelector('.js-shareUrl');
   
    // Create new instance of copy, passing in shareUrl element
    var copyShareUrl = copy(shareUrl);
    
    // Set value via markup or JS
    shareUrl.value = "Hey! I have just signed up for Royalty! Use my code: {{$customer_data->referral_number}} to earn credit once you sign up!";
   
    // Click listener with copyShareUrl handler
    shareUrl.addEventListener('click', copyShareUrl, false);
   
   }());
</script>
{{--check if profile complete then show point availed modal--}}
@if($customer_data->profile_completed >= 100 && session('img_updated'))
<script>
   $(document).ready(function() {
      $("#profileCompletedModal").modal('show');
   });
</script>
@endif
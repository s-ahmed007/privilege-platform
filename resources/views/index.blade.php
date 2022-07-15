<?php use \App\Http\Controllers\functionController; ?>
<?php
   if(Session::has('customer_id')){
      $user_exp_stat = \App\CustomerInfo::where('customer_id', session('customer_id'))->first();
      session(['user_type' => $user_exp_stat->customer_type]);
      session(['expiry_status' => (new \App\Http\Controllers\functionController2())->getExpStatusOfCustomer($user_exp_stat->expiry_date)]);
   }
   
   $mem_plan_renew = $prices_for_faq->where('platform', \App\Http\Controllers\Enum\PlatformType::web)
       ->where('type', \App\Http\Controllers\Enum\MembershipPriceType::renew)
       ->where('month', 1)->first();
   $mem_plans_buy = $prices_for_faq->where('platform', \App\Http\Controllers\Enum\PlatformType::web)
       ->where('type', \App\Http\Controllers\Enum\MembershipPriceType::buy)
       ->where('month', '!=', 1)
       ->sortBy('month');
   ?>
@include("header")
<link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
<!-- ======= Top Banner Section ======= -->
<section id="hero">
   <div class="container">
      <div class="row">
         <div class="col-lg-5 col-md-push-7 hero-img" data-aos="zoom-out" data-aos-delay="300">
            <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/hero-img.png" class="img-fluid animated" alt="Royalty Hero Image">
         </div>
         <div class="col-lg-7 col-md-pull-5 pt-5 pt-lg-0 d-flex align-items-center">
            <div data-aos="zoom-out">
               <h1>Get up to 75% discount with <span>Royalty</span></h1>
               <h2>One platform for amazing offers and discounts in Dhaka</h2>
               <div class="text-center text-lg-left" style="display: inline;">
                  <a href="https://play.google.com/store/apps/details?id=com.royalty.bd" class="btn-get-started" target="_blank"><i class='bx bxl-android' ></i>Android</a>
               </div>
               <div class="text-center text-lg-left" style="display: inline;">
                  <a href="https://apps.apple.com/tt/app/royalty-bd/id1300476271" class="btn-get-started" target="_blank"><i class='bx bxl-apple'></i>iOS</a>
               </div>
               <br>
               <br>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- End Top Banner -->
<main id="main">
   <!-- ========================Home Slider ============================= -->
   <!-- <div class="container-fluid home-carousal">
      <div id="myCarousel" class="carousel slide" data-ride="carousel">
         <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
            <li data-target="#myCarousel" data-slide-to="3"></li>
            <li data-target="#myCarousel" data-slide-to="4"></li>
            <li data-target="#myCarousel" data-slide-to="5"></li>
            <li data-target="#myCarousel" data-slide-to="6"></li>
         </ol>
         <div class="carousel-inner">
            @foreach($carousel_images as $key => $carousel)
            @if($key == 0)
            <div class="item active">
            <a href="{{url('/donate')}}">
               <img src="{{$carousel}}" alt="Royalty Banner" class="lazyload homepage-banner">
               </a>
            </div>
            @elseif($key == 1)
            <div class="item">
               <a href="{{ url('offers/food_and_drinks') }}">
               <img src="{{$carousel}}" alt="Royalty Category" class="lazyload homepage-banner">
               </a>
            </div>
            @elseif($key == 2)
            <div class="item">
               <a href="{{ url('offers/health_and_fitness') }}">
               <img src="{{$carousel}}" alt="Royalty Category" class="lazyload homepage-banner">
               </a>
            </div>
            @elseif($key == 3)
            <div class="item">
               <a href="{{ url('offers/lifestyle') }}">
               <img src="{{$carousel}}" alt="Royalty Category" class="lazyload homepage-banner">
               </a>
            </div>
            @elseif($key == 4)
            <div class="item">
               <a href="{{ url('offers/beauty_and_spa') }}">
               <img src="{{$carousel}}" alt="Royalty Category" class="lazyload homepage-banner">
               </a>
            </div>
            @elseif($key == 5)
            <div class="item">
               <a href="{{ url('offers/entertainment') }}">
               <img src="{{$carousel}}" alt="Royalty Category" class="lazyload homepage-banner">
               </a>
            </div>
            @elseif($key == 6)
            <div class="item">
               <a href="{{ url('offers/getaways') }}">
               <img src="{{$carousel}}" alt="Royalty Category" class="lazyload homepage-banner">
               </a>
            </div>
            @endif
            @endforeach
         </div>
         <a class="left carousel-control" href="#myCarousel" data-slide="prev">
         <span class="glyphicon glyphicon-chevron-left"></span>
         <span class="sr-only">Previous</span>
         </a>
         <a class="right carousel-control" href="#myCarousel" data-slide="next">
         <span class="glyphicon glyphicon-chevron-right"></span>
         <span class="sr-only">Next</span>
         </a>
      </div>
      </div> -->
   <!-- ======= About Section ======= -->
   <section id="about" class="about">
      <div class="container-fluid">
         <div class="row">
            <div class="col-xl-5 col-lg-6 video-box d-flex justify-content-center align-items-stretch" data-aos="fade-right">
               <a href="https://www.youtube.com/watch?v=m2B8x43cKW4" class="venobox play-btn mb-4" data-vbtype="video" data-autoplay="true"></a>
            </div>
            <div class="col-xl-7 col-lg-6 icon-boxes d-flex flex-column align-items-stretch justify-content-center py-5 px-lg-5" data-aos="fade-left">
               <h3>What we are?</h3>
               <p>The first-ever dedicated privilege platform of the country, where you can avail amazing discounts & offers at your desired places with our membership plan. Make us your lifestyle partner today!</p>
               <div class="icon-box" data-aos="zoom-in" data-aos-delay="100">
                  <div class="icon"><i class="bx bx-scan"></i></div>
                  <h4 class="title">SCAN/OPEN PARTNER PROFILE</h4>
                  <p class="description">Visit our partner outlets and scan their QR stand or open partner profile on the app to see current offers.</p>
               </div>
               <div class="icon-box" data-aos="zoom-in" data-aos-delay="200">
                  <div class="icon"><i class="bx bx-select-multiple"></i></div>
                  <h4 class="title">SELECT</h4>
                  <p class="description">Select the partner offers to send request and ask the merchant to accept it.</p>
               </div>
               <div class="icon-box" data-aos="zoom-in" data-aos-delay="300">
                  <div class="icon"><i class='bx bxs-discount'></i></div>
                  <h4 class="title">SAVE MONEY & GET REWARDS</h4>
                  <p class="description">After every successful transaction, earn Royalty Credits which can be used to redeem further rewards!</p>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- End About Section -->
   <!-- ======= Categories Section ======= -->
   <section id="features" class="features">
      <div class="container">
         <div class="section-title" data-aos="fade-up">
            <h2>Categories</h2>
            <p>Royalty Premium Categories</p>
         </div>
         <?php
            $cat_div_1 = $categories->take(7);
            $cat_div_2 = $categories->slice(7);
            ?>
         <div class="row" data-aos="fade-left">
            <div class="col-lg-3 col-md-4 mt-4 mt-md-0">
               <a href="{{ url('offers/all') }}">
                  <div class="icon-box" data-aos="zoom-in" data-aos-delay="100">
                     <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/home-page/category/0.png" alt="Royalty category" class="lazyload">
                     <h3>Offers</h3>
                  </div>
               </a>
            </div>
            @foreach($cat_div_1 as $category)
            <div class="col-lg-3 col-md-4 mt-4 mt-md-0">
               <a href="{{ url('offers/'.$category->type) }}">
                  <div class="icon-box" data-aos="zoom-in" data-aos-delay="150">
                     <img src="{{$category->icon}}" alt="Royalty category" class="lazyload">
                     <h3>{{$category->name}}</h3>
                  </div>
               </a>
            </div>
            @endforeach
            @foreach($cat_div_2 as $category)
            <div class="col-lg-3 col-md-4 mt-4 mt-md-0 hide_cat_div" style="display:none;">
               <a href="{{ url('offers/'.$category->type) }}">
                  <div class="icon-box" data-aos="zoom-in" data-aos-delay="150">
                     <img src="{{$category->icon}}" alt="Royalty category" class="lazyload">
                     <h3>{{$category->name}}</h3>
                  </div>
               </a>
            </div>
            @endforeach
         </div>
         @if(count($categories) > 7)
         <br>
         <div class="row">
            <div class="col-md-12 col-sm-11 col-xs-12">
               <button class="btn btn-primary" style="float: right;" onclick="hideCategory(this)"><i class="fa fa-angle-down"></i></button>
            </div>
         </div>
         @endif
      </div>
   </section>
   <!-- End Categories Section -->
   <!-- ======= Counts Section ======= -->
   <section id="counts" class="counts">
      <div class="container">
         <div class="row" data-aos="fade-up">
            <div class="col-lg-3 col-md-6">
               <div class="count-box">
                  <i class="bx bx-happy"></i>
                  <span data-toggle="counter-up">{{$counters['user_saved']}}</span>
                  <p>Saved by customers</p>
               </div>
            </div>
            <div class="col-lg-3 col-md-6 mt-5 mt-md-0">
               <div class="count-box">
                  <i class="bx bx-store"></i>
                  <span data-toggle="counter-up">{{$counters['branch_count']}}</span>
                  <p>Partner Outlets</p>
               </div>
            </div>
            <div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
               <div class="count-box">
                  <i class="bx bxs-offer"></i>
                  <span data-toggle="counter-up">{{$counters['offer_availed']}}</span>
                  <p>Offers Availed</p>
               </div>
            </div>
            <div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
               <div class="count-box">
                  <i class="bx bxs-discount"></i>
                  <span data-toggle="counter-up">{{$counters['all_reviews']}}</span>
                  <p>Reviews</p>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- End Counts Section -->
   <!-- TRIAL PROMPT -->
   @if(Session::has('customer_id') && Session::get('user_type') == 2)
   @else
   <!-- <div class="royalty-earn-container mb-30">
      <div class="center trial-prompt">
         <p>UP TO 75% OFF AT 200+ PARTNER OUTLETS.
         </p>
         <p>START YOUR FREE TRIAL TODAY!
         </p>
         <a href="{{url('select-card')}}">
            <button class="btn btn-success">
               <p>
                  TRY 1 MONTH FOR FREE
               </p>
            </button>
         </a>
      </div>
      </div> -->
   @endif
{{--   <section id="details" class="details">--}}
{{--      <div class="container">--}}
{{--         <div class="section-title" data-aos="fade-up">--}}
{{--            <h2>Pricing</h2>--}}
{{--            <p>Affordable Membership Prices</p>--}}
{{--         </div>--}}
{{--      </div>--}}
{{--      @if(Session::has('customer_id'))--}}
{{--      @if(Session::get('user_type') ==3)--}}
{{--      --}}{{--if guest user--}}
{{--      <div class="container">--}}
{{--         <div class="public-guest-card">--}}
{{--            <div class="price-card">--}}
{{--               <div class="row">--}}
{{--                  <div class="col-md-6 col-sm-12 col-xs-12">--}}
{{--                     <div class="price-card-info-head">--}}
{{--                        <h3>GET your membership TODAY</h3>--}}
{{--                        <h5>OFFERS | DISCOUNTS | REWARDS</h5>--}}
{{--                     </div>--}}
{{--                     <div class="price-card-info-box">--}}
{{--                        <div class="price-card-info">--}}
{{--                           <ul class="no-list">--}}
{{--                              <li class="li-icon">Find all the great offers, discounts & rewards from top brands!--}}
{{--                              </li>--}}
{{--                              <li class="li-icon">--}}
{{--                                 Earn Credits with every scan.--}}
{{--                              </li>--}}
{{--                              <li class="li-icon">--}}
{{--                                 Review partners with your authentic experience.--}}
{{--                              </li>--}}
{{--                              <li class="li-icon">--}}
{{--                                 Earn credits from your activity on the platform. Redeem credits for greater rewards.--}}
{{--                              </li>--}}
{{--                           </ul>--}}
{{--                        </div>--}}
{{--                     </div>--}}
{{--                  </div>--}}
{{--                  <div class="col-md-6 col-sm-12 col-xs-12">--}}
{{--                     <div class="price-card-holder">--}}
{{--                        @foreach($card_prices as $card)--}}
{{--                        <?php--}}
{{--                           if($card->month == 3){--}}
{{--                              $period = 'QUARTERLY';--}}
{{--                           }elseif($card->month == 6){--}}
{{--                              $period = 'HALF-YEARLY';--}}
{{--                           }elseif($card->month == 12){--}}
{{--                              $period = 'YEARLY';--}}
{{--                           }--}}
{{--                           ?>--}}
{{--                        <div class="price-card-box row {{$card->month == 12 ? 'price-card-box-highlight' : ''}}">--}}
{{--                           <div class="price-card-box-line col-md-10 col-sm-10 col-xs-10">--}}
{{--                              <div class="price-card-box-type">--}}
{{--                                 @if($card->price == 0)--}}
{{--                                 <b>{{$card->month.' - '}}{{$card->month > 1 ? 'MONTHS':'MONTH'}} TRIAL</b>--}}
{{--                                 <!-- <p>{{$card->month}} {{$card->month > 1 ? 'months':'month'}} of Royalty Membership - New users</p> -->--}}
{{--                                 <p>For NEW users only</p>--}}
{{--                                 @else--}}
{{--                                 <b>{{$card->month}} - {{$card->month > 1 ? 'MONTHS':'MONTH'}}</b>--}}
{{--                                 <p>Royalty Premium Membership</p>--}}
{{--                                 @endif--}}
{{--                              </div>--}}
{{--                              <div class="price-card-box-price">{{$card->price == 0 ? 'FREE' : $card->price.'tk'}}</div>--}}
{{--                           </div>--}}
{{--                           <div class="col-md-2 col-sm-2 col-xs-2">--}}
{{--                              <div class="pricing-list-btn">--}}
{{--                                 <a href="{{ url('select-card') }}">--}}
{{--                                    <div class="btn btn-success">{{$card->price == 0 ? 'GET' : 'Buy'}}</div>--}}
{{--                                 </a>--}}
{{--                              </div>--}}
{{--                           </div>--}}
{{--                        </div>--}}
{{--                        @endforeach--}}
{{--                     </div>--}}
{{--                  </div>--}}
{{--               </div>--}}
{{--            </div>--}}
{{--         </div>--}}
{{--      </div>--}}
{{--      @elseif(session('expiry_status') == 'expired')--}}
{{--      --}}{{--if expired user--}}
{{--      <div class="container">--}}
{{--         <div class="public-guest-card">--}}
{{--            <div class="price-card">--}}
{{--               <div class="row">--}}
{{--                  <div class="col-md-6 col-sm-12 col-xs-12">--}}
{{--                     <div class="price-card-info-head">--}}
{{--                        <h3>GET your Royalty Membership TODAY</h3>--}}
{{--                        <h5>OFFERS | DISCOUNTS | REWARDS</h5>--}}
{{--                     </div>--}}
{{--                     <div class="price-card-info-box">--}}
{{--                        <div class="price-card-info">--}}
{{--                           <ul class="no-list">--}}
{{--                              <li class="li-icon">Find all the great offers, discounts & rewards from top brands in just one single platform - Royalty!--}}
{{--                              </li>--}}
{{--                              <li class="li-icon">--}}
{{--                                 Earn Credits with every scan when you avail an offer at our partner outlets.--}}
{{--                              </li>--}}
{{--                              <li class="li-icon">--}}
{{--                                 Rate & review partners with your authentic experience. A review platform made only for you from real customers.--}}
{{--                              </li>--}}
{{--                              <li class="li-icon">--}}
{{--                                 Earn credits from your activity on the platform. Redeem credits for greater rewards.--}}
{{--                              </li>--}}
{{--                           </ul>--}}
{{--                        </div>--}}
{{--                     </div>--}}
{{--                  </div>--}}
{{--                  <div class="col-md-6 col-sm-12 col-xs-12">--}}
{{--                     <div class="price-card-holder">--}}
{{--                        @foreach($card_prices as $card)--}}
{{--                        <?php--}}
{{--                           if($card->month == 3){--}}
{{--                              $period = 'QUARTERLY';--}}
{{--                           }elseif($card->month == 6){--}}
{{--                              $period = 'HALF-YEARLY';--}}
{{--                           }elseif($card->month == 12){--}}
{{--                              $period = 'YEARLY';--}}
{{--                           }--}}
{{--                           ?>--}}
{{--                        <div class="price-card-box row {{$card->month == 12 ? 'price-card-box-highlight' : ''}}">--}}
{{--                           <div class="price-card-box-line col-md-10 col-sm-10 col-xs-10">--}}
{{--                              <div class="price-card-box-type">--}}
{{--                                 {{$card->month}} - {{$card->month > 1 ? 'MONTHS':'MONTH'}}--}}
{{--                                 <p>{{$card->month}} {{$card->month > 1 ? ' months':' month'}} of Royalty Premium Membership</p>--}}
{{--                              </div>--}}
{{--                              <div class="price-card-box-price">{{$card->price}}tk</div>--}}
{{--                           </div>--}}
{{--                           <div class="col-md-2 col-sm-2 col-xs-2">--}}
{{--                              <div class="pricing-list-btn">--}}
{{--                                 <a href="{{ url('renew_subscription') }}">--}}
{{--                                    <div class="btn btn-success">Renew</div>--}}
{{--                                 </a>--}}
{{--                              </div>--}}
{{--                           </div>--}}
{{--                        </div>--}}
{{--                        @endforeach--}}
{{--                     </div>--}}
{{--                  </div>--}}
{{--               </div>--}}
{{--            </div>--}}
{{--         </div>--}}
{{--      </div>--}}
{{--      @endif--}}
{{--      @else--}}
{{--      --}}{{--if public--}}
{{--      <div class="container">--}}
{{--         <div class="public-guest-card">--}}
{{--            <div class="price-card">--}}
{{--               <div class="row">--}}
{{--                  <div class="col-md-6 col-sm-12 col-xs-12">--}}
{{--                     <div class="price-card-info-head">--}}
{{--                        <h3>GET your Royalty Membership TODAY</h3>--}}
{{--                        <h5>OFFERS | DISCOUNTS | REWARDS</h5>--}}
{{--                     </div>--}}
{{--                     <div class="price-card-info-box">--}}
{{--                        <div class="price-card-info">--}}
{{--                           <ul class="no-list">--}}
{{--                              <li class="li-icon">Find all the great offers, discounts & rewards from top brands in just one single platform - Royalty!--}}
{{--                              </li>--}}
{{--                              <li class="li-icon">--}}
{{--                                 Earn Credits with every scan when you avail an offer at our partner outlets.--}}
{{--                              </li>--}}
{{--                              <li class="li-icon">--}}
{{--                                 Rate & review partners with your authentic experience. A review platform made only for you from real customers.--}}
{{--                              </li>--}}
{{--                              <li class="li-icon">--}}
{{--                                 Earn credits from your activity on the platform. Redeem credits for greater rewards.--}}
{{--                              </li>--}}
{{--                           </ul>--}}
{{--                        </div>--}}
{{--                     </div>--}}
{{--                  </div>--}}
{{--                  <div class="col-md-6 col-sm-12 col-xs-12">--}}
{{--                     <div class="price-card-holder">--}}
{{--                        @foreach($card_prices as $card)--}}
{{--                        <?php--}}
{{--                           if($card->month == 3){--}}
{{--                              $period = 'QUARTERLY';--}}
{{--                           }elseif($card->month == 6){--}}
{{--                              $period = 'HALF-YEARLY';--}}
{{--                           }elseif($card->month == 12){--}}
{{--                              $period = 'YEARLY';--}}
{{--                           }--}}
{{--                           ?>--}}
{{--                        <div class="price-card-box row {{$card->month == 12 ? 'price-card-box-highlight' : ''}}">--}}
{{--                           <div class="price-card-box-line col-md-10 col-sm-10 col-xs-10">--}}
{{--                              <div class="price-card-box-type">--}}
{{--                                 @if($card->price == 0)--}}
{{--                                 <b>{{$card->month.' - '}}{{$card->month > 1 ? 'MONTHS':'MONTH'}} TRIAL</b>--}}
{{--                                 <!-- <p>{{$card->month}} {{$card->month > 1 ? 'months':'month'}} For NEW users only</p> -->--}}
{{--                                 <p>For NEW users only</p>--}}
{{--                                 @else--}}
{{--                                 <b>{{$card->month}} - {{$card->month > 1 ? 'MONTHS':'MONTH'}}</b>--}}
{{--                                 <p>Royalty Premium Membership</p>--}}
{{--                                 @endif--}}
{{--                              </div>--}}
{{--                              <div class="price-card-box-price">{{$card->price == 0 ? 'FREE' : $card->price.'tk'}}</div>--}}
{{--                           </div>--}}
{{--                           <div class="col-md-2 col-sm-2 col-xs-2">--}}
{{--                              <div class="pricing-list-btn">--}}
{{--                                 <a href="{{ url('select-card') }}">--}}
{{--                                    <div class="btn btn-success">{{$card->price == 0 ? 'GET' : 'Buy'}}</div>--}}
{{--                                 </a>--}}
{{--                              </div>--}}
{{--                           </div>--}}
{{--                        </div>--}}
{{--                        @endforeach--}}
{{--                     </div>--}}
{{--                  </div>--}}
{{--               </div>--}}
{{--            </div>--}}
{{--         </div>--}}
{{--      </div>--}}
{{--      @endif--}}
{{--   </section>--}}
   <!-- ======= Pricing Section ======= -->
   <!-- <section id="pricing" class="pricing">
      <div class="container">
         <div class="section-title" data-aos="fade-up">
            <h2>Pricing</h2>
            <p>Affordable Membership Prices</p>
         </div>
         <div class="row" data-aos="fade-left">
            <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
               <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
                  <h3>
                     <b>100000</b>
                     <p>Royalty Premium Membership</p>
                  </h3>
                  <h4><sup>৳</sup>100tk</h4>
                  <ul>
                     <li>Access to 1000 offers</li>
                     <li>Earn Rewards</li>
                     <li>Earn Credits</li>
                  </ul>
                  <div class="btn-wrap">
                     <a href=""></a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      </section> -->
   <!-- End Pricing Section -->
   <!-- ======= Details Section ======= -->
   <section id="details" class="details">
      <div class="container">
         <div class="row content">
            <div class="col-md-4" data-aos="fade-right">
               <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/details-1.png" class="img-fluid" alt="Royalty Home About">
            </div>
            <div class="col-md-8 pt-4" data-aos="fade-up">
               <h3>Royalty App - Your lifestyle partner</h3>
               <p class="font-italic bold">
                  Offers, discounts, rewards and what not?
               </p>
               <ul>
                  <li><i class="bx bx-check"></i> Find all the great offers, discounts & rewards.</li>
                  <li><i class="bx bx-check"></i> Find all the coolest places to visit near you from the nearby section.</li>
                  <li><i class="bx bx-check"></i> Stay up to dated with all the latest offers, partners and exciting events in the newsfeed section.</li>
                  <li><i class="bx bx-check"></i> Earn Credits every time when you avail an offer at our partner outlets.</li>
                  <li><i class="bx bx-check"></i> Rate & review partners with your authentic experience. A review platform made only for you from real customers.</li>
                  <li><i class="bx bx-check"></i> Earn credits from your activity on the platform. Redeem credits for greater rewards.</li>
               </ul>
               <p>Sign up today to unlock world of boundless possibilities.</p>
            </div>
         </div>
      </div>
   </section>
   <!-- End Details Section -->
   <!-- ======= Gallery Section ======= -->
   <!-- <section id="gallery" class="gallery">
      <div class="container">
         <div class="section-title" data-aos="fade-up">
            <h2>Gallery</h2>
            <p>Check our Gallery</p>
         </div>
         <div class="row no-gutters" data-aos="fade-left">
            <div class="col-lg-3 col-md-4">
               <div class="gallery-item" data-aos="zoom-in" data-aos-delay="100">
                  <a href="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-1.jpg" class="venobox" data-gall="gallery-item">
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-1.jpg" alt="" class="img-fluid">
                  </a>
               </div>
            </div>
            <div class="col-lg-3 col-md-4">
               <div class="gallery-item" data-aos="zoom-in" data-aos-delay="150">
                  <a href="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-2.jpg" class="venobox" data-gall="gallery-item">
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-2.jpg" alt="" class="img-fluid">
                  </a>
               </div>
            </div>
            <div class="col-lg-3 col-md-4">
               <div class="gallery-item" data-aos="zoom-in" data-aos-delay="200">
                  <a href="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-3.jpg" class="venobox" data-gall="gallery-item">
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-3.jpg" alt="" class="img-fluid">
                  </a>
               </div>
            </div>
            <div class="col-lg-3 col-md-4">
               <div class="gallery-item" data-aos="zoom-in" data-aos-delay="250">
                  <a href="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-4.jpg" class="venobox" data-gall="gallery-item">
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-4.jpg" alt="" class="img-fluid">
                  </a>
               </div>
            </div>
            <div class="col-lg-3 col-md-4">
               <div class="gallery-item" data-aos="zoom-in" data-aos-delay="300">
                  <a href="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-5.jpg" class="venobox" data-gall="gallery-item">
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-5.jpg" alt="" class="img-fluid">
                  </a>
               </div>
            </div>
            <div class="col-lg-3 col-md-4">
               <div class="gallery-item" data-aos="zoom-in" data-aos-delay="350">
                  <a href="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-6.jpg" class="venobox" data-gall="gallery-item">
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-6.jpg" alt="" class="img-fluid">
                  </a>
               </div>
            </div>
            <div class="col-lg-3 col-md-4">
               <div class="gallery-item" data-aos="zoom-in" data-aos-delay="400">
                  <a href="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-7.jpg" class="venobox" data-gall="gallery-item">
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-7.jpg" alt="" class="img-fluid">
                  </a>
               </div>
            </div>
            <div class="col-lg-3 col-md-4">
               <div class="gallery-item" data-aos="zoom-in" data-aos-delay="450">
                  <a href="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-8.jpg" class="venobox" data-gall="gallery-item">
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/newimg/gallery/gallery-8.jpg" alt="" class="img-fluid">
                  </a>
               </div>
            </div>
         </div>
      </div>
      </section> -->
   <!-- End Gallery Section -->
   <!-- ======= New Partners Section ======= -->
   <section id="team" class="team">
      <div class="container">
         <div class="section-title" data-aos="fade-up">
            <h2>NEW PARTNERS</h2>
            <p>National & International Brands</p>
         </div>
         <div class="row" data-aos="fade-left">
            <div class="col-md-12">
               <div class="owl-carousel owl-theme">
                  @if(isset($topBrands))
                  @foreach($topBrands as $topBrand)
                  <?php $pname = str_replace("'", "", $topBrand->partner_name); ?>
                  @if($topBrand->branch_number == 1)
                  <a href="{{ url('partner-profile/'. $pname .'/'.$topBrand->main_branch_id)}}" class="carousal-anchor">
                     @else
                     <div class="item" onclick="showLocationModal( '{{$topBrand->partner_account_id}}' )">
                        @endif
                        <div class="card card-inverse card-info">
                              
                           <img src="{{ $topBrand->partner_profile_image }}" class="lazyload card-img-top" alt="
                              Royalty Profile Image">
                           <div class="card-block">
                              <h4 class="card-title card-partner-name">{{$topBrand->partner_name}}</h4>
                              <div class="card-text">
                                 <p>
                                    {{$topBrand->location}} - @if(isset($topBrand->average_rating))
                              <?php $ratings = [1,2,3,4,5]; ?>
                              @if($topBrand->average_rating == 0)
                              <span class="partner-box-info-rating">new</span>
                              @elseif(in_array($topBrand->average_rating, $ratings))
                              <i class="bx bxs-star yellow"></i>
                              <span class="partner-box-info-rating">{{round($topBrand->average_rating).'.0'}}</span>
                              @else
                              <i class="bx bxs-star yellow"></i>
                              <span class="partner-box-info-rating">{{round($topBrand->average_rating, 1)}}</span>
                              @endif
                              @endif
                                 </p>
                              </div>
                           </div>
                           <div class="card-footer">
                              <label class="label-tag-small">OFFER</label>
                              <small class="bold black"> {{$topBrand->offer_heading}}</small>
                           </div>
                        </div>
                        @if($topBrand->branch_number == 1)
                  </a>
                  @else
                  </div>
                  @endif
                  @endforeach
                  @endif
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- End New Partners Section -->
   <!-- ======= Banner Section ======= -->
   <section id="team" class="team">
      <div class="container">
         <div class="row" data-aos="fade-left">
            <div class="col-md-12">
               {{--Card Customization--}}
               {{--If partner is logged in--}}
               @if(Session::has('customer_id'))
               @if(Session::get('user_type') !=3)
               @if(Session::get('customer_delivery_type') != 5)
               <!-- <div class="royalty-earn-container mb-30">
                  <div class="row royalty-earn">      
                     <div class="col-md-12 col-sm-12 col-xs-12">
                        <p>Card Customization is here!</p>
                        <p>Want to see your name on your Royalty Card? Shoot an E-mail at support@royaltybd.com or
                           call us at +880-963-862-0202 to customize your card.
                        </p>       
                     </div>
                  </div>
                  </div> -->
               {{--when card holder logged in but didnt customize card--}}
               <div class="royalty-earn-container">
                  <img src ="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png" style="width:100%" alt="Royalty Home Banner"/>
               </div>
               @else
               {{--when card holder logged in and customize card--}}
               <div class="royalty-earn-container">
                  <img src ="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png" style="width:100%" alt="Royalty Home Banner"/>
               </div>
               @endif
               @else
               {{--If guest is logged in--}}
               <div class="royalty-earn-container">
                  <a href="{{url('select-card')}}">
                  <img src ="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/guest.png" style="width:100%" alt="Royalty Home Banner"/>
                  </a>
               </div>
               @endif
               @else
               {{--If no one is logged in--}}
               <div class="royalty-earn-container">
                  <a href="{{url('login')}}">
                  <img src ="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/public.png" style="width:100%" alt="Royalty Home Banner"/>
                  </a>
               </div>
               @endif
            </div>
         </div>
      </div>
   </section>
   <!-- End Banner Section -->
   <!-- ======= Trending Offers Section ======= -->
   <section id="team" class="team">
      <div class="container">
         <div class="section-title" data-aos="fade-up">
            <h2>TRENDING</h2>
            <p>National & International Brands</p>
         </div>
         <div class="row" data-aos="fade-left">
            <div class="col-md-12">
               {{--Trending Offers--}}
               <div class="owl-carousel owl-theme">
                  @if(isset($trendingOffers))
                  @foreach($trendingOffers as $profileImage)
                  <?php $pname = str_replace("'", "", $profileImage->partner_name); ?>
                  @if($profileImage->branch_number == 1)
                  <a href="{{ url('partner-profile/'. $pname .'/'.$profileImage->main_branch_id)}}" class="carousal-anchor">
                     @else
                     <div class="item" onclick="showLocationModal( '{{$profileImage->partner_account_id}}' )">
                        @endif
                        <div class="card card-inverse card-info">
                           <img src="{{ $profileImage->partner_profile_image }}" class="lazyload card-img-top" alt="
                              Royalty Profile Image">
                           <div class="card-block">
                              <h4 class="card-title card-partner-name">{{$profileImage->partner_name}}</h4>
                              <div class="card-text">
                                 <p>
                                    {{$profileImage->location}} - @if(isset($profileImage->average_rating))
                              <?php $ratings = [1,2,3,4,5]; ?>
                              @if($profileImage->average_rating == 0)
                              <span class="partner-box-info-rating">new</span>
                              @elseif(in_array($profileImage->average_rating, $ratings))
                              <i class="bx bxs-star yellow"></i>
                              <span class="partner-box-info-rating">
                              &nbsp;{{round($profileImage->average_rating).'.0'}}
                              </span>
                              @else
                              <i class="bx bxs-star yellow"></i>
                              <span class="partner-box-info-rating">{{round($profileImage->average_rating, 1)}}
                              </span>
                              @endif
                              @endif
                                 </p>
                              </div>
                           </div>
                           <div class="card-footer">
                              <label class="label-tag-small">OFFER</label>
                              <small class="bold black"> {{$profileImage->offer_heading}}</small>
                           </div>
                        </div>
                        @if($profileImage->branch_number == 1)
                  </a>
                  <!-- end of profile link -->
                  @else
                  </div><!-- end of profile location modal -->
                  @endif
                  @endforeach
                  @endif
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- End Trending Offers Section -->
   <!-- ======= Banner Section ======= -->
   <section id="team" class="team">
      <div class="container">
         <div class="row" data-aos="fade-left">
            <div class="col-md-12">
               {{--Refer cash--}}
               @if(Session::has('customer_id'))
               <div class="refer-container">
                  <img class="refer-img" src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/refercode.png" width="100%" alt="Royalty Refer">
                  <button type="button" class="btn-refer js-tooltip js-copy refer-btn"
                     data-toggle="tooltip" data-placement="bottom"
                     data-copy="{{session('referral_number')}}" title="Copy">
                  {{session('referral_number')}}
                  <i class="bx bxs-copy"></i>
                  </button>
               </div>
               @else
               {{--If no one is logged in--}}
               <div class="refer-container">
                  <a href="{{url('login')}}">
                  <img class="refer-img" src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/refer.png" width="100%" alt="Royalty Refer">
                  </a>
               </div>
               @endif
            </div>
         </div>
      </div>
   </section>
   <!-- End Banner Section -->
   <!-- ======= POPULAR Section ======= -->
   <section id="team" class="team">
      <div class="container">
         <div class="section-title" data-aos="fade-up">
            <h2>POPULAR</h2>
            <p>National & International Brands</p>
         </div>
         <div class="row" data-aos="fade-left">
            <div class="col-md-12">
               <div class="owl-carousel owl-theme">
                  @if(isset($topPartners))
                  @foreach($topPartners as $profileImage)
                  <?php $pname = str_replace("'", "", $profileImage['info']['partner_name']); ?>
                  @if(count($profileImage['branches']) == 1)
                  <a href="{{ url('partner-profile/'. $pname .'/'.$profileImage['main_branch_id'])}}" class="carousal-anchor">
                     @else
                     <div class="item" onclick="showLocationModal( '{{$profileImage['partner_account_id']}}' )">
                        @endif
                        <div class="card card-inverse card-info">
                           <img src="{{ $profileImage['info']['profile_image']['partner_profile_image'] }}" class="lazyload card-img-top" alt="
                              Royalty Profile Image">
                           <div class="card-block">
                              <h4 class="card-title card-partner-name">{{$profileImage['info']['partner_name']}}</h4>
                              <div class="card-text">
                                 <p>
                                    {{$profileImage['location']}} - 
                                    @if(isset($profileImage['average_rating']))
                           <?php $ratings = [1,2,3,4,5]; ?>
                           @if($profileImage['average_rating'] == 0)
                           <span class="partner-box-info-rating">new</span>
                           @elseif(in_array($profileImage['average_rating'], $ratings))
                           <i class="bx bxs-star yellow"></i>
                           <span class="partner-box-info-rating">
                           {{round($profileImage['average_rating']).'.0'}}</span>
                           @else
                           <i class="bx bxs-star yellow"></i>
                           <span class="partner-box-info-rating">
                           {{round($profileImage['average_rating'], 1)}}</span>
                           @endif
                           @endif
                                 </p>
                              </div>
                           </div>
                           <div class="card-footer">
                              <label class="label-tag-small">OFFER</label>
                              <small class="bold black"> {{$profileImage['offer_heading']}}</small>
                           </div>
                        </div>
                        @if(count($profileImage['branches']) == 1)
                  </a>
                  @else
                  </div>
                  @endif
                  @endforeach
                  @endif
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- End Popular Section -->
   <!-- ======= Recently visited Section ======= -->
   @if(Session::has('customer_id') && count($visited_profile) > 0)
   <section id="team" class="team">
      <div class="container">
      <div class="section-title" data-aos="fade-up">
         <h2>Recent</h2>
         <p>RECENTLY VISITED PARTNERS</p>
      </div>
      <div class="row" data-aos="fade-left">
         <div class="col-md-12">
            <div class="container">
               <div class="row">
                  <div class="col-md-12 columns">
                     <div class="owl-carousel owl-theme">
                        @foreach($visited_profile as $row)
                        <?php $pname = str_replace("'", "", $row->partner->info->partner_name);
                           $branch_id = (new functionController)->mainBranchOfPartner($row->partner_id);
                           ?>
                        <a href="{{ url('partner-profile/'. $pname .'/'.$branch_id[0]['id']) }}" class="carousal-anchor">
                           <div class="item">
                              <div class="card card-inverse card-info" style="box-shadow: unset;">
                                 <div class="partner-box-img-container">
                                    <img src="{{ $row->partner->info->profileImage->partner_profile_image }}" class="lazyload pp-img" alt="Royalty Profile Image">
                                 </div>
                                 <div class="partner-box-info-container">
                                    <div class="partner-box-info-container-l">
                                       <p class="card-title card-partner-name center">
                                          {{ $row->partner->info->partner_name }}
                                       </p>
                                       </p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </a>
                        @endforeach
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   @endif
   <!-- End recently visited Section -->
   <!-- ======= F.A.Q Section ======= -->
   <section id="faq" class="faq section-bg">
      <div class="container">
         <div class="section-title" data-aos="fade-up">
            <h2>F.A.Q</h2>
            <p>Frequently Asked Questions</p>
         </div>
         <div class="faq-list">
            <ul>
               <li data-aos="fade-up">
                  <i class="bx bx-help-circle icon-help"></i>
                  <a data-toggle="collapse" class="collapsed" href="#faq-list-1">
                     What is Royalty? How much does Royalty Premium Membership cost ?
                     <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i>
                  </a>
                  <div id="faq-list-1" class="collapse" data-parent=".faq-list">
                     <p>
                        Royalty is a discount, offer and reward platform.
                        The regular price of Royalty Premium Membership is BDT
                        {{$mem_plan_renew->price}} for {{$mem_plan_renew->month}}
                        {{$mem_plan_renew->month > 1 ? 'months':'month'}} {{$mem_plan_renew->price == 0 ? '(FREE for new users),':','}}
                        <?php $i=1; ?>
                        @foreach($mem_plans_buy as $mem_plan)
                        @if($i ==count($mem_plans_buy))
                        {{'BDT '.$mem_plan->price}} for {{$mem_plan->month}} {{$mem_plan->month > 1 ? 'months.':'month.'}}
                        @elseif(++$i ==count($mem_plans_buy))
                        {{'BDT '.$mem_plan->price}} for {{$mem_plan->month}} {{$mem_plan->month > 1 ? 'months':'month'}} and
                        @else
                        {{'BDT '.$mem_plan->price}} for {{$mem_plan->month}} {{$mem_plan->month > 1 ? 'months':'month'}},
                        @endif
                        @endforeach
                        <!-- FREE trial is applicable for new users only.  -->
                        More on how to get the membership can be found <span><a href="{{url('/blog')}}" target="_blank" class="faq-blog-link">on our blog</a></span>.
                     </p>
                  </div>
               </li>
               <li data-aos="fade-up" data-aos-delay="100">
                  <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-2" class="collapsed"> How to use the app to avail offers? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                  <div id="faq-list-2" class="collapse" data-parent=".faq-list">
                     <p>
                        Royalty is quick and easy to use. Simply show your QR on the app to the waiter or the manager to scan and process the discount. Once the partner completes processing the discount from their end by scanning
                        your QR, you will get a notification on your account regarding the transaction. At the same time Credits will be added to your account. Regarding Credits, check the ‘Rewards’ section.
                     </p>
                  </div>
               </li>
               <!-- <li data-aos="fade-up" data-aos-delay="200">
                  <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-3" class="collapsed">What are Royalty Deals? How does it work? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                  <div id="faq-list-3" class="collapse" data-parent=".faq-list">
                     <p>
                        Deals is an initiative taken by Royalty to financially support the restaurants and other businesses at this crucial time since they have been temporarily closed to help control the spread of COVID-19. The deals can be pre-paid from the Royalty app or web for any participating partners. These deals can be used at the specific partner outlet in the next 6 months when things get back to normal. The deals are offered at a discounted price that’s worth a higher amount and can be availed at the partner once they are operational again.
                     </p>
                  </div>
                  </li> -->
               <li data-aos="fade-up" data-aos-delay="300">
                  <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-4" class="collapsed">What are Royalty Credits? What can I do with the Credits? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                  <div id="faq-list-4" class="collapse" data-parent=".faq-list">
                     <p>
                        As you keep on using our service, you will earn credits on the go! You can use the credit to redeem rewards. Free credits get you free rewards!
                     </p>
                  </div>
               </li>
               <li data-aos="fade-up" data-aos-delay="400">
                  <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-5" class="collapsed">Why should I download the Royalty Mobile App? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                  <div id="faq-list-5" class="collapse" data-parent=".faq-list">
                     <p>
                        Royalty mobile app is a light-weight and easy to use app, that would give you the freedom to do what you love, anywhere you are. You can download it on both Google Play Store and iOS App Store. Get it now.
                     </p>
                  </div>
               </li>
            </ul>
         </div>
      </div>
   </section>
   <!-- End F.A.Q Section -->
   <!-- ======= features and selected Section ======= -->
   <section id="team" class="team">
      <div class="container">
         <div class="section-title" data-aos="fade-up">
            <h2>Achievement</h2>
            <p>FEATURED AND SELECTED</p>
         </div>
         <div class="row" data-aos="fade-left">
            <div class="col-md-12">
               <div class="center m-10-a">
                  <div class="col-md-3 col-sm-3 col-xs-6">
                     <a href="{{ url('https://www.thedailystar.net/next-step/news/making-privilege-programs-accessible-1811989') }}">
                     <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/feature/RBD-TDS.png" style="width:100%" alt="Royalty in the daily star"/>
                     </a>
                  </div>
                  <div class="col-md-3 col-sm-3 col-xs-6">
                     <a href="{{ url('https://today.thefinancialexpress.com.bd/stock-corporate/fintech-agrotech-edutech-to-thrive-in-near-future-1556986491') }}">
                     <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/feature/RBD-TFE.png" style="width:100%" alt="Royalty in the financial express"/>
                     </a>
                  </div>
                  <div class="col-md-3 col-sm-3 col-xs-6">
                     <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/feature/RBD-TIA.png" style="width:100%" alt="Royalty in tech in asia"/>
                  </div>
                  <div class="col-md-3 col-sm-3 col-xs-6">
                     <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/feature/RBD-WST.jpg" style="width:100%" alt="Royalty in web summit"/>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- End features and selected Section -->
   <!-- ======= Testimonials Section ======= -->
   <section id="testimonials" class="testimonials">
      <div class="container">
         <div class="row">
            <div class="col-sm-12">
               <div id="myCarousel" class="carousel slide" data-ride="carousel">
                  <!-- Carousel indicators -->
                  <ol class="carousel-indicators">
                     <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                     <li data-target="#myCarousel" data-slide-to="1"></li>
                  </ol>
                  <!-- Wrapper for carousel items -->
                  <div class="carousel-inner">
                     <div class="item carousel-item active">
                        <div class="row">
                           <div class="col-sm-6">
                              <div class="media">
                                 <div class="media-left d-flex mr-3">
                                    <a href="#">
                                    <img src="https://royalty-bd.s3.ap-southeast-1.amazonaws.com/static-images/images/users/review1.1.jpg" alt="Royalty Testimonial">
                                    </a>
                                 </div>
                                 <div class="media-body">
                                    <div class="testimonial">
                                       <p>Love the IOS app after using the android app for a long time. Great stuff! Saves me a lot of money every week. I have been referring my friends to join the platform too! Thanks for making such an amazing product!</p>
                                       <p class="overview"><b>Hussam</b>, Student</p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-sm-6">
                              <div class="media">
                                 <div class="media-left d-flex mr-3">
                                    <a href="#">
                                    <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/images/users/review1.2.jpg" alt="Royalty Testimonial">
                                    </a>
                                 </div>
                                 <div class="media-body">
                                    <div class="testimonial">
                                       <p>Great app and very handy! A lifesaver for me. The best part is that the new partners and offers keep adding up every month and I get to save more money, the more I spend! Been using it for past two years Love it totally!</p>
                                       <p class="overview"><b>Shahriar Rabbani</b>, Ex., ACI Limited</p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="item carousel-item">
                        <div class="row">
                           <div class="col-sm-6">
                              <div class="media">
                                 <div class="media-left d-flex mr-3">
                                    <a href="#">
                                    <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/images/users/review2.1.png" alt="Royalty Testimonial">
                                    </a>
                                 </div>
                                 <div class="media-body">
                                    <div class="testimonial">
                                       <p>I know the team and think they've got a lot of promising offers worth looking into. I did not need to get a credit card or anything to get it complete. I just signed up for the membership and started saving.</p>
                                       <p class="overview"><b>Sadika Islam Eva</b>, Student, NSU</p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-sm-6">
                              <div class="media">
                                 <div class="media-left d-flex mr-3">
                                    <a href="#">
                                    <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/images/users/review2.2.png" alt="Royalty Testimonial">
                                    </a>
                                 </div>
                                 <div class="media-body">
                                    <div class="testimonial">
                                       <p>The app is very useful especially for the people who love to explore food, new places, spas, and salons. It allows for great savings and also authentic reviews from people visiting partners.</p>
                                       <p class="overview"><b>Mohammad Salman</b>, Owner, Treehouse</p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- End Testimonials Section -->
   <!-- ======= Blog Section ======= -->
   <section id="team" class="team">
      <div class="container">
         <div class="section-title" data-aos="fade-up">
            <h2>Blog</h2>
            <p>Royalty Blogs</p>
         </div>
         <div class="row" data-aos="fade-left">
            <div class="col-md-12">
               {{--BLOG--}}
               @if(isset($blogPosts))
               <div class="container">
                  <div class="row">
                     <div class="col-md-12 columns">
                        <div class="owl-carousel owl-theme">
                           @foreach($blogPosts as $blogPost)
                           <div class="item">
                              <a href="{{ url('/blog/'.$blogPost['heading']) }}">
                                 <div class="card card-inverse card-info">
                                    <div class="blog-img">
                                       <img src="{{ $blogPost['image_url'] }}"
                                          alt="Royalty Blog" class="lazyload h-115"/>
                                    </div>
                                    <div class="partner-box-info-container">
                                       <p class="home-news-head dots">
                                          {{ $blogPost['heading'] }}
                                       </p>
                                       <p class="home-news-summary dots3">
                                          {{strip_tags($blogPost['details']).'....'}}
                                       <p>
                              <a href="{{ url('/blog/'.$blogPost['heading']) }}">
                              <p class="f-s bold">
                              Read more
                              </p>
                              </p>
                              </div>
                              </div>
                              </a>
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
               </div>
               @endif
            </div>
         </div>
      </div>
   </section>
   <!-- End Blog Section -->
</main>
<a href="#" class="back-to-top"><i class='bx bxs-chevron-up'></i></a>
<!-- <a href="{{url('/royaltyrewards')}}">
   <div class="referral-popup show">
      <span id='close-refer'
         onclick='this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode); return false;'>x
      </span>
      <div class="referral-popup__image">
         <img class="lazyload img-responsive" src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/home-page/refer-pop.png" alt="Royalty Refer">
      </div>
   </div>
   </a> -->
@if (session('payment_clear'))
<div class="alert alert-success">
   {{ session('payment_clear') }}
</div>
@endif
{{--Welcome POPUP--}}
<!-- <div id="welcome_popup1">
   <div id="popup1" class="popup">
      <i class="cross-icon popup-close" id="close" style="color: black"></i>
      <a href="{{url('/donate')}}">
      <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/home-page/donation-popup.png"
         alt="Royalty Home Popup" class="lazyload" style="width: 100%">
      </a>
   </div>
   </div> -->
{{--========================================================================================
================================ EMAIL VALIDATION FOR SUBSCRIPTION =====================
=========================================================================================--}}
<script>
   function validateEmail() {
       $(".error_email").empty();
       var email = $("#subscribe").val();
   
       var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
   
       if (!filter.test(email)) {
           $(".error_email").text('Invalid E-mail!');
           return false;
       } else {
           return true;
       }
   }
</script>
@include("footer")
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@if(Session::has('customer_id') && session('user_type') != 3 && session('expiry_status') == 'expired')
<script>
   $(document).ready(function() {
      setTimeout(function(){
         var date1 = localStorage.getItem("expired_user_alert");
         var date2 = new Date();
         var diff = date2.getTime() - date1;
         var hh = Math.floor(diff / 1000 / 60 / 60);
         diff -= hh * 1000 * 60 * 60;
         var mm = Math.floor(diff / 1000 / 60);
         diff -= mm * 1000 * 60;
   
         if(localStorage.getItem("expired_user_alert") === null){
            localStorage.setItem("expired_user_alert", date2.getTime());
            $("#expired_user_alert").removeAttr("style").show();
         }else{
            if(hh >= 24 && mm > 0){
               localStorage.setItem("expired_user_alert", date2.getTime());
               $("#expired_user_alert").removeAttr("style").show();
            }
         }
      }, 300);//show modal after 300 milliseconds
   });
   //force close for this modal as normally not closing
   $(function () {
      $('#modalClose').on('click', function () {
         $('#expired_user_alert').hide();
      })
   })
</script>
@endif
<script>
   function hideCategory(elem) {
      var cur_elem = $(elem).children('i');
      if (cur_elem.hasClass('fa-angle-down')) {
         cur_elem.removeClass('fa-angle-down').addClass('fa-angle-up');
      } else {
         cur_elem.removeClass('fa-angle-up').addClass('fa-angle-down');
      }
      $(".hide_cat_div").toggle();
   }
</script>
<script>
   $(document).ready(function() {
      var owl = $('.owl-carousel');
      owl.owlCarousel({
         loop: true,
         nav: true,
         margin: 10,
         responsive: {
            0: {items: 1},
            600: {items: 3},
            960: {items: 5},
            1200: {items: 6}
         }
      });
      owl.on('mousewheel', '.owl-stage', function(e) {
         if (e.deltaY > 0) {
            owl.trigger('next.owl');
         } else {
            owl.trigger('prev.owl');
         }
         e.preventDefault();
      });
   })
</script>
<script>
   mixpanel.track(
           "PageView",
           {
              "Page Slug": 'Home'
           }
   );
</script>
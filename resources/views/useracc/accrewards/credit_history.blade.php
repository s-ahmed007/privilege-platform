@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <!-- <h2> </h2> -->
         <p>CREDIT HISTORY</p>
      </div>
   </div>
</section>
<section class="counts">
<div class="container">
<div class="row">
   <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                   <a href="{{url('/users/'.session('customer_username').'/rewards')}}">Rewards</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><a href="">Credit History</a></li>
            </ol>
        </nav>
    </div>
</div>
   <div class="row">
      <div class="col-md-3">
         <div class="sidebar mb-3 shadow">
            @include('useracc.sidebar')
         </div>
      </div>
      <div class="col-md-9">
            <div class="row">
               <div class="col-md-12 col-sm-12 col-xs-12 mb-3">
                  <h3 class="graybox-head">ROYALTY CREDIT</h3>
                  <div class="row graybox">
                     <div class="col-md-8 col-sm-12 col-xs-12">
                        <div class="reward-point-summary">
                        <p>Your Lifetime Total Royalty Credits: </p>
                        <p> You can earn credits from many activities on the platform, such as: rate, review, scans etc. Use credits to redeem greater rewards!</p>
                        <div class="row" style="float:right">
                           <div class="col-md-6">
                              <a href="{{ url('royaltyrewards') }}">
                              <button class="btn btn-primary">
                              FIND OUT MORE
                              </button>
                              </a>
                           </div>
                           <div class="col-md-6">
                              <a href="{{url('users/'.session('customer_username').'/credit_history')}}" class="btn btn-primary">
                              CREDIT HISTORY
                              </a>
                           </div>
                        </div>
                        </div>
                     </div>
                     <div class="col-md-4 col-sm-12 col-xs-12">
                        <div class="user-acc-point-box">
                           <span class="big-point-text">{{$all_points['royalty_points']}}</span>
                           <span class="big-point-point">credit.</span>
                           <span class="tooltip1 user-tooltip" style="color: #ffc107; z-index: 0">
                           <i class="question-icon"></i>
                           <span class="tooltiptext1">Earn Royalty credit from rate, review, refer, activity.</span>
                           </span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-12 mb-3">
                  <h4><b>LIFETIME CREDITS EARNED</b></h4>
                  <div class="row">
                     <div class="col-md-4">
                        <span>Offer
                        </span>
                        <span>..................................................
                        </span>
                        <span class="credit-h-total">{{$all_points['transaction_points']}}
                        </span> <br>
{{--                        <span>Deal--}}
{{--                        </span>--}}
{{--                        <span>..................................................--}}
{{--                        </span>--}}
{{--                        <span class="credit-h-total">{{$all_points['deal_redeemed_points']}}--}}
{{--                        </span> <br>--}}
                        <span>Rating
                        </span>
                        <span>....................................................
                        </span>
                        <span class="credit-h-total">{{$all_points['rating_points']}}
                        </span> <br>
{{--                        <span>Deal Refund--}}
{{--                        </span>--}}
{{--                        <span>.........................................--}}
{{--                        </span>--}}
{{--                        <span class="credit-h-total">{{$all_points['deal_refund_points']}}--}}
{{--                        </span>--}}
                     </div>
                     <div class="col-md-4">
                        <span>Review
                        </span>
                        <span>..................................................
                        </span>
                        <span class="credit-h-total">{{$all_points['review_points']}}
                        </span> <br>
                        <span>Refer
                        </span>
                        <span>.....................................................
                        </span>
                        <span class="credit-h-total">{{$all_points['refer_points']}}
                        </span>
                    </div>
                    <div class="col-md-4">
                        <span>Activity
                        </span>
                        <span>..................................................
                        </span>
                        <span class="credit-h-total">{{$all_points['activity_points']}}
                        </span> <br>
                        <span>Profile Completation
                        </span>
                        <span>............................
                        </span>
                        <span class="credit-h-total">{{$all_points['profile_complete_points']}}
                        </span>
                    </div>
                  </div>
               </div>
               <div class="col-md-12">
               <h4><b>CREDITS HISTORY</b></h4>
                  @foreach($all_earn_history as $history)
                     <div class="row whitebox" style="margin: 10px 0;">
                        <div class="col-md-8 col-sm-8 col-xs-8" style="display: inline-flex;">
                           <img src="{{$history['icon']}}" alt="Credit History" width="50" height="50" class="img-circle">
                           <div class="reward-list-text my-3">
                              <h4 class="mt-2">{{$history['activity']}}</h4>
                              <p>{{$history['date']}}</p>
                           </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4">
                           <div class="center" style="float: right;">
                              <h4 class="mt-2" style="color: green"> +{{$history['point']}}</h4>
                              <p> {{$history['time']}}</p>
                           </div>
                        </div>
                     </div>
                  @endforeach
               </div>
            </div>
      </div>
   </div>
</div>
</section>
@include('useracc.commonDivs')
@include('footer')
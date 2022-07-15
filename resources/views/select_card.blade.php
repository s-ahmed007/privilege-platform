@include('header')
<style type="text/css">
   .active_price_btn{background-color: #128E62 !important; color: #fff !important}
</style>
<link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Dhaka (200+ Partners)</h2>
         <p>PURCHASE MEMBERSHIP</p>
      </div>
   </div>
</section>
<!-- ======= Pricing Section ======= -->
<section id="pricing" class="pricing">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Pricing</h2>
         <p>Check our Pricing</p>
      </div>
      <div class="row" data-aos="fade-left">
         <?php $trial = $cards->where('platform', \App\Http\Controllers\Enum\PlatformType::web)->where('price', 0)->first();?>
         @foreach($cards as $card)
         <div class="col-lg-4 col-md-6 mt-4 mt-md-0">
            <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
               <h3>{{$card->price == 0 ? 'TRIAL':'PREMIUM'}}</h3>
               <h4>{{$card->price == 0 ? 'FREE':'৳ '.$card->price}}<span> / {{$card->month}}
                     {{$card->month > 1 ? 'MONTHS':'MONTH'}}</span>
               </h4>
               <ul>
                  <li><span class="{{$card->price == 0 ?'uncheck-sign':'check-sign'}}">
                        {!! $card->price == 0 ? '&#10060;':'&#10004;' !!}</span> Access to {{$active_offers}}+ offers
                  </li>
                  <li><span class="{{$card->price == 0 ?'uncheck-sign':'check-sign'}}">
                        {!! $card->price == 0 ? '&#10060;':'&#10004;' !!}</span> Earn credits
                  </li>
                  <li><span class="{{$card->price == 0 ?'uncheck-sign':'check-sign'}}">
                        {!! $card->price == 0 ? '&#10060;':'&#10004;' !!}</span> Redeem Rewards
                  </li>
               </ul>
               @if($card->price == 0)
                  <div class="btn-wrap" onclick="upgradeSubscription('trial', '0')">
                     <a style="cursor: pointer;" class="btn-buy">Get</a>
                  </div>
               @else
                  <div class="btn-wrap {{$card->month == 12 ? ' is-featured' : ''}}"
                       onclick="upgradeSubscription('{{$card->month}}', '{{$card->price}}', this)">
                     <a style="cursor: pointer;" class="btn-buy">Choose</a>
                  </div>
               @endif
            </div>
         </div>
         @endforeach
      </div>
   </div>
</section>
<!-- End Pricing Section -->
<section class="counts">
   <div class="container buy-card-background" id="payment_box" style="display: none;">
   <h3>Subscription length - <span id="selected_duration">0</span></h3>
   <form class="form-vertical cus_reg_form" action="{{ url('/confirm_buy_card') }}" method="post"
         onsubmit="return checkFields();">
      <div class="row">
         <div class="col-md-6 col-sm-12 col-xs-12">
            <p>Promo Code (Optional)</p>
            @if ($errors->has('card_promo'))
            <div style="color: red;">
               <ul id="card_promo_error">
                  <li>{{ $errors->first('card_promo') }}</li>
               </ul>
            </div>
            @elseif (session('card_promo'))
            <span style="color: red">{{ session('card_promo') }}</span>
            @endif
            <div class="form-group">
               <input type="text" name="card_promo" placeholder="Enter Code" class="form-control" id="card_promo"
                      style="width: 50%;">
            </div>
            <span class="error_card_promo" id="error_card_promo"></span>
            <span class="correct_card_promo" id="correct_card_promo"></span>
            <p class="promo-head">Bill Summary:</p>
            <div class="row bill-box">
               <div class="card-bill-price-l col-xs-8">Premium Membership Price</div>
               <div class="card-bill-price col-xs-4"></div>
               <div class="card-bill-promo-l col-xs-8">Promo Code Discount</div>
               <div class="card-bill-promo col-xs-4"></div>
               <div class="card-bill-final-l col-xs-8" style="border: unset">Final Bill</div>
               <div class="card-bill-final col-xs-4"></div>
            </div>
            <input type="hidden" class="promotion-discount" value="not_set">
            <input type="hidden" name="subscription_price" class="subscription_price">
            <input type="hidden" name="subscription" class="subscription">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
         </div>
         <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="center">
               <button type="submit" class="btn btn-primary" name="submit" style="float: right; margin: 0 0 0 10px;">Continue to payment
               <i class="fas fa-arrow-circle-right"></i>
               </button>
               <button class="btn btn-secondary" id="spot_purchase_btn" style="float: right; cursor:default;
                  pointer-events: none">Spot Purchase</button>
            </div>
         </div>
      </div>
   </form>
   <input type="hidden" value="{{\App\Http\Controllers\Enum\PromoType::CARD_PURCHASE}}" id="mem_type">
   </div>
   <div id="OTPSentModal" class="modal" role="dialog" style="top: 10%">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">  <h4 class="modal-title">Verification Code</h4>
               <button type="button" class="close" data-dismiss="modal">
               <i class="cross-icon"></i>
               </button>
             
            </div>
            <div class="modal-body" id="user_deactive_text">
               <p style="text-align: center;">We have sent an OTP to the sales agent's number.</p>
               <p style="text-align: center;font-weight:bold;" class="verify_phone"></p>
               <p style="text-align: center;">Enter the sales agents OTP below:</p>
               <div class="form-horizontal form-label-left">
                  <div class="form-group">
                     <div class="col-sm-offset-2 col-sm-8 col-xs-offset-2 col-xs-8">
                        <label for="phone_verifying_code"></label>
                        <input type="text" class="form-control" placeholder="Enter verification code"
                           name="phone_verifying_code" id="phone_verifying_code" minlength="6" maxlength="6" required>
                     </div>
                  </div>
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="password_user" id="user_type">
                  <div class="ln_solid"></div>
                  <div class="form-group">
                     <p class="middle" style="display: contents">
                        <button class="btn btn-primary verify_button">Verify</button>
                     </p>
                     <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif"
                          alt="Royalty Loading GIF" class="loading-gif" style="display: none; position: relative;"
                          title="Royalty loading icon">
                  </div>
                  <a id="resendSpotOTP">Resend code</a>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div id="spotFinalPriceModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header"> <h4 class="modal-title">Royalty Membership Fee</h4>
               <button type="button" class="close" data-dismiss="modal"><i class="cross-icon"></i></button>
              
            </div>
            <div class="modal-body" style="text-align: center">
               <h3 class="spot_final_price"></h3>
               <hr>
               <ul>
                  <li>
                     <span>Membership price</span>
                     <span class="spot_card_price"></span>
                  </li>
                  <li>
                     <span style="color: forestgreen;">Promo Code Discount</span>
                     <span style="color: forestgreen;" class="spot_promo_discount"></span>
                  </li>
               </ul>
               <form action="{{url('spot_purchase_from_user')}}" method="post">
                  {{csrf_field()}}
                  <input type="hidden" id="spot_continue_price" name="price">
                  <input type="hidden" id="spot_continue_customer" name="customer">
                  <input type="hidden" id="spot_continue_month" name="month">
                  <input type="hidden" id="spot_continue_promo" name="promo">
                  <input type="hidden" id="spot_continue_agent" name="seller">
                  <button type="submit" class="btn btn-success">Continue</button>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- 1 month trial modal -->
@if($trial)
<div id="1MonthTrialModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">          <h4 class="modal-title">Royalty Membership Trial</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
  
         </div>
         <div class="modal-body" style="text-align: center">
            <p>You are about to activate your {{$trial->month.'-'}}{{$trial->month > 1 ? 'Months' : 'Month'}} free trial and it will expire on </p>
            <p class="exp_date"></p>
            <!-- <p style="text-align: right"><a  class="invite_code_link" onclick="return openInvitationField(this)">I have an invitation code</a></p> -->
            <!-- <span class="error_trial_promo"></span>
               <span class="correct_trial_promo"></span>
               <input type="text" class="form-control" id="trial_promo_code" placeholder="Invitation code (If any)"
                   style="display: none" onkeyup="checkFieldValue()"> -->
            <button class="btn btn-success" onclick="checkTrialPromo()">Activate for FREE</button>
         </div>
      </div>
   </div>
</div>
@endif
@include('footer')
@include('footer-js.select-card-js')
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
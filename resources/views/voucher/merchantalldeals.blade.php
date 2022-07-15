<?php 
   use App\Http\Controllers\Enum\ValidFor;
   $customer_email_verified = \App\CustomerInfo::where('customer_id', session('customer_id'))->select('email_verified')->first();
   if ($customer_email_verified) {
     $customer_email_verified = $customer_email_verified->email_verified;
   }else{
     $customer_email_verified = 0;
   }
   ?>
@include('header')
<style type="text/css">
   .remaining_point_label{display: inline-block}
</style>
<link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>
            @foreach($categories as $category)
            @if($category->id == $partnerInfo->partner_category)
            <a href="{{ url('royaltydeals/'.$category->type) }}"
               target="_blank"> {{ $category->name }}</a>
            @endif
            @endforeach
         </h2>
         <div class="partner-details">
            <img src="{{$partnerInfo->profileImage->partner_profile_image}}" class="img-100"/>
            <h3 class="partner-profile-name">
               {{$partnerInfo->partner_name}}
               <span style="font-size: 12px">
               {{$partnerInfo->partner_type}}
               </span>
               <span style="font-size: 12px">
               {{$partnerInfo->branches[0]->partner_address}}
               </span>
               <span class="floating">
                  @if($partnerInfo->rating != NULL)
                  @if($partnerInfo->rating->average_rating == 1)
                     <i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating == 2)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating == 3)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating == 4)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating == 5)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating > 1.0 && $partnerInfo->rating->average_rating <=1.5)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating >1.5 && $partnerInfo->rating->average_rating < 2.0)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating > 2.0 && $partnerInfo->rating->average_rating <=2.5)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating > 2.5 && $partnerInfo->rating->average_rating < 3.0)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating > 3 && $partnerInfo->rating->average_rating <= 3.5)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i><i class="bx bx-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating > 3.5 && $partnerInfo->rating->average_rating < 4.0)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i>
                  @elseif($partnerInfo->rating->average_rating > 4.0 && $partnerInfo->rating->average_rating <= 4.5)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i>
                  @elseif($partnerInfo->rating->average_rating > 4.5 && $partnerInfo->rating->average_rating <= 5.0)
                     <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                  @else
                     <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                  @endif
                  @endif
               </span>
            </h3>
         </div>
      </div>
   </div>
</section>
<div class="container">
<div class="row">
   <div class="col-xs-12 col-sm-12 col-md-12">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item">
               <a href="{{url('/')}}">Home</a>
            </li>
            <li class="breadcrumb-item">
               <a href="{{url('/offers/'.$partnerInfo->category->type)}}">{{$partnerInfo->category->name}}</a>
            </li>
            <?php $pname = str_replace("'", "", $partnerInfo->partner_name); ?>
            <li class="breadcrumb-item">
               <a href="{{ url('partner-profile/'. $pname .'/'.$partnerInfo->branches[0]->id)}}">{{$partnerInfo->partner_name}}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">All Deals</li>
         </ol>
      </nav>
   </div>
</div>
</div>
<section class="counts">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-8">
            @foreach($partnerInfo->branches[0]->vouchers as $voucher)
               <?php $dates = $voucher->date_duration[0]; ?>
               <div class="whitebox-inner-box">
                  <div class="whitebox-inner-box-name">
                     <p>{{$voucher->heading}}</p>
                     @if($voucher->point != null && $voucher->point != 0)
                        <span class="savings-label">
                           EARN {{$voucher->point}}{{$voucher->point > 1 ? ' CREDITS':' CREDIT'}}  
                        </span>
                     @endif
                     <p class="deals-left bold"><span class="available_reward_{{$voucher->id}}">{{$voucher->counter_limit - $voucher->purchased}}</span> left</p>
                     <div class="partner-offer-timings">
                        <p>Valid for - <span>
                           {{$voucher->valid_for == ValidFor::ALL_MEMBERS ? 'Everyone':'Premium Members'}}</span>
                        </p>
                        <p>Valid on - 
                           <?php $weekdays = $voucher->weekdays[0]; ?>
                           @if($weekdays['sat'] == '1' && $weekdays['sun'] == '1' && $weekdays['mon'] == '1' && $weekdays['tue'] == '1' &&
                           $weekdays['wed'] == '1' && $weekdays['thu'] == '1' && $weekdays['fri'] == '1')
                           All days
                           @else
                           @if($weekdays['sun'] == '1') Sun @endif
                           @if($weekdays['mon'] == '1') Mon @endif
                           @if($weekdays['tue'] == '1') Tue @endif
                           @if($weekdays['wed'] == '1') Wed @endif
                           @if($weekdays['thu'] == '1') Thu @endif
                           @if($weekdays['fri'] == '1') Fri @endif
                           @if($weekdays['sat'] == '1') Sat @endif
                           @endif
                        </p>
                        @if(count($voucher->time_duration) != 0)
                        <p>Timing - <span>
                           <?php $i=0; ?>
                           @foreach($voucher->time_duration as $duration)
                           {{date('h:i A', strtotime($duration['from'])).' - '.date('h:i A', strtotime($duration['to']))}}
                           <?php
                              echo $i != count($voucher->time_duration)-1 ? ',' : '';
                              $i++; ?>
                           @endforeach
                           </span>
                        </p>
                        @endif
                        
                        <div class="pp-offer-btn">
                        <button class="btn btn-primary-thin offerDetails" data-offer-id="{{$voucher->id}}" data-offer-tab="details">Details</button>
                        <button class="btn btn-primary-thin offerDetails" data-offer-id="{{$voucher->id}}" data-offer-tab="tnc">T&C</button>
                     </div>
                     <br>
                     @if(!session('customer_id'))
                        <button class="btn btn-primary m-0" onclick="location.href='{{url("login")}}'">Get Premium Membership</button>
                        @else
                        @if(session('user_type') == 3 && $voucher->valid_for == ValidFor::PREMIUM_MEMBERS)
                        <button class="btn btn-primary m-0" onclick="buyMembershipBeforeDealPurchase()">Get Premium Membership</button>
                        @endif
                        @endif
                     </div>
                    
                  </div>
                  <div class="whitebox-inner-box-buy">
                     <p>
                        <span class="original-price">&#x9f3;{{intval($voucher->actual_price)}}</span>
                        <span class="discounted-price">&#x9f3;{{intval($voucher->selling_price)}}</span>
                     </p>
                     <div>
                        @if(!session('customer_id'))
                        <a href="{{url('login')}}"><button class="btn btn-success">ADD</button></a>
                        @else
                        @if($voucher->valid_for == ValidFor::PREMIUM_MEMBERS && session('user_type') != 2)
                        <button class="btn btn-success" onclick="alert('This deal is available for premium members only.')">ADD</button>
                        @else
                        @if($voucher->scan_limit == 0 && $voucher->scan_limit != null)
                        <button class="btn btn-success" style="background-color: #adaca7" onclick="alert('You can not purchase this deal.')">ADD</button>
                        @else
                        <button class="btn btn-success add_reward_{{$voucher->id}}" onclick="addRewardToCart({{$voucher}})">ADD</button>
                        @endif
                        @endif
                        @endif
                        <div class="inc_dec_{{$voucher->id}}" style="display: none;">
                           <button class="btn btn-reward-add counter_decrement_{{$voucher->id}}" style="color: #fff;
                              background-color: #28a745;
                              border-color: #28a745;" 
                              onclick="rewardCounter({{$voucher}},false)">-</button>
                           <button class="btn btn-reward-add reward_counter_{{$voucher->id}}" style="cursor:default;"></button>
                           <button class="btn btn-reward-add counter_increment_{{$voucher->id}}" style="color: #fff;
                              background-color: #28a745;
                              border-color: #28a745;"
                              onclick="rewardCounter({{$voucher}},true)">+</button>
                           <input type="hidden" class="redeemed_counter_{{$voucher->id}}">
                        </div>
                     </div>
                     <br>
                     <span class="deal-discount-amount">
                     @if($voucher->discount_type == 2)
                     {{intval($voucher->discount).'%'.' off'}}
                     @else
                     {{'&#x9f3;'.intval($voucher->discount).' off'}}
                     @endif
                     </span>
                  </div>
               </div>
               <div class="modal" id="offerDetails_{{$voucher->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h4 class="modal-title">{{ $partnerInfo->partner_name }}</h4>
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="cross-icon"></i>
                           </button>
                        </div>
                        <div class="modal-body">
                           <div role="tabpanel">
                              <!-- Nav tabs -->
                              <ul class="nav nav-tabs" role="tablist">
                                 <li role="presentation" class="active"><a href="#detailsTab{{$voucher->id}}" aria-controls="detailsTab{{$voucher->id}}" role="tab" data-toggle="tab">Details</a>
                                 </li>
                                 <li role="presentation"><a href="#tncTab{{$voucher->id}}" aria-controls="tncTab{{$voucher->id}}" role="tab" data-toggle="tab">T&C</a>
                                 </li>
                              </ul>
                              <!-- Tab panes -->
                              <div class="tab-content offer-tab-content">
                                 <div role="tabpanel" class="tab-pane active" id="detailsTab{{$voucher->id}}">
                                    {!! html_entity_decode($voucher->description) !!}
                                 </div>
                                 <div role="tabpanel" class="tab-pane" id="tncTab{{$voucher->id}}">
                                    {!! html_entity_decode($voucher->tnc) !!}
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            @endforeach
         </div>
         <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="online-deal-price-box">
               <div class="reward-cart-head center">
                  <p>Your Order</p>
               </div>
               <p class="pls_add mtb-10 center">Please add an option</p>
               <div id="reward_redeem_summery"></div>
               <input type="hidden" id="canRedeemReward" value="{{session('customer_username') == null ? 'false':session('customer_username')}}">
               <input type="hidden" id="total_deal_price">
               <input type="hidden" id="customer_email_verified" value="{{$customer_email_verified == 0 ? 'false':'true'}}">
               <div class="center">
               @if($cur_credit > 0)
                  @if(session('user_type') != 3 && session('expiry_status') != 'expired')
                     <label class="remaining_point_label filter">Credits Remaining: <span class="remaining_point"> {{$cur_credit}}</span>
                     <input type="checkbox" id="user_credits_checkbox" name="user_credits" value="{{$cur_credit}}">
                     <span class="checkmark user_credits_checkbox" style="display: none;"></span>
                     </label>
                  @else
                     <label class="remaining_point_label filter">Credits Remaining: <span class="remaining_point"> {{$cur_credit}}</span>
                     <input type="checkbox" onclick="premiumMembershipModal()" name="user_credits">
                     <span class="checkmark user_credits_checkbox" style="display: none;"></span>
                     </label>
                  @endif
               @endif
               <div class="center">
               <button class="btn btn-block btn-success" id="redeem_confirm_button" disabled>BUY NOW</button>
               </div>
         </div>
      </div>
   </div>
</section>
<div id="premiumMembersipModal" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Premium Membership</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <div style="text-align: center;">
               <p>You need to be a premium member to use credits for deals.</p>
               @if (session('user_type') == 2 && session('expiry_status') == 'expired')
                  <a href="{{url('renew_subscription')}}" class="btn btn-primary">Buy Premium Membership</a>
               @else
                  <a href="{{url('select-card')}}" class="btn btn-primary">Buy Premium Membership</a>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
<div id="dealVerifyEmailModal" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Sorry!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <div style="text-align: center;">
               <p>You need to verify your email for purchasing deals.</p>
               <a href="{{url('users/'.session('customer_username').'/info')}}" class="btn btn-primary">Go to your profile to verify email</a>
            </div>
         </div>
      </div>
   </div>
</div>
@include('footer')
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/customer/voucher.js')}}"></script>
<script type="text/javascript" async>
   function buyMembershipBeforeDealPurchase() {
      var cur_url = '{{url()->current()}}';
      localStorage.setItem('take_to_deal_page_after_buy_membership', cur_url);
      window.location.href = '{{url("select-card")}}';
   }
   localStorage.removeItem("take_to_deal_page_after_buy_membership");
</script>
@include('partner-dashboard.header')
<div class="container-fluid">
   <div class="row bg-title">
      <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
         <h3 class="d-inline-block">{{__('partner/common.customer_rewards')}}</h3>
         <h5 class="d-inline-block float-right">{{__('partner/rewards.reward_that_can_be_availed')}}</h5>
      </div>
   </div>
   <div class="row">
      <div class="col-lg-4 col-sm-6 col-xs-12">
         <div class="white-box">
            <p style="color: red;font-weight:bold;">{{__('partner/rewards.payment_due')}}: {{$payment_info['due']}} BDT </p>
         </div>
      </div>
      <div class="col-lg-4 col-sm-6 col-xs-12">
         <div class="white-box">
            <p style="color: green;font-weight:bold;">{{__('partner/rewards.payment_paid')}}: {{$payment_info['paid']}} BDT </p>
         </div>
      </div>
      <div class="col-lg-4 col-sm-6 col-xs-12">
         <div class="white-box">
            <p style="color: #007bff;font-weight:bold;">{{__('partner/rewards.last_paid')}}: {{$payment_info['last_paid']}} </p>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-12">
         @if(count($sorted_rewards) > 0)
         @foreach($sorted_rewards as $offer)
         <div class="column">
            <div class="row m-z-a">
               <div class="partner-offer-box-l">
                  <div class="col-md-3">
                     <img src="{{$offer->image}}" alt="Reward Image" class="reward-list-img my-3 border" style="width:80px;height:80px">
                  </div>
                  <div class="col-md-9">
                     <p>{{$offer->offer_description}}</p>
                     <?php
                        if($offer->actual_price != 0){
                            $deducted_price = $offer->actual_price - $offer->price;
                            $percentage = floor(($deducted_price * 100)/$offer->actual_price);
                        }else{
                            $percentage = 0;
                        }
                        ?>
                     <div class="partner-offer-timings">
                        <p>Valid till -
                           <span>
                           {{date("F d, Y", strtotime($offer->date_duration[0]['to']))}}
                           </span>
                        </p>
                     </div>
                     <div>
                        @if($offer->expired)
                        <span class="badge badge-danger">Expired</span>
                        @else
                        <span class="badge badge-success">Available {{$offer->counter_limit - $offer->offer_use_count}}</span>
                        @endif
                        <span>Used: <b>{{$offer->offer_use_count}}{{$offer->offer_use_count > 1 ? ' times':' time'}}</b></span>
                        <span>Cost: <b>{{$offer->offer_use_count * $offer->actual_price}}</b></span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         @endforeach
         @else
         <p style="padding: 10px 20px;">No rewards available currently. To give rewards to customers please contact: 01312620202.</p>
         @endif
      </div>
   </div>
</div>
@include('partner-dashboard.footer')
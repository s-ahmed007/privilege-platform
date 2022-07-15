@include('header')
<link href="{{ asset('css/onlinedeals.css') }}" rel="stylesheet">
<style>
   .req_phone_error,.req_email_error,.req_del_add_error,.req_others_error{
   float: right;
   color: darkred;
   }
</style>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <p> 
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background-color: unset;margin-bottom: unset;">
               <li class="breadcrumb-item">
                  <a href="{{url('/users/'.session('customer_username').'/rewards')}}">Rewards</a>
               </li>
               <li class="breadcrumb-item active" aria-current="page"><a href="">Reward Redeem</a></li>
            </ol>
         </nav>
         </p>
      </div>
   </div>
</section>
<section class="counts">
   <div class="container">
      <div class="row">
         <div class="col-md-3">
            <div class="sidebar mb-3 shadow">
               @include('useracc.sidebar')
            </div>
         </div>
         <div class="col-md-9">
               <div class="row m-0">
                  @if($reward)
                  <div class="col-xs-12 col-sm-12 col-md-8">
                     <div class="whitebox-inner-box">
                        <div class="whitebox-inner-box-name">
                           <img src="{{$reward->image}}" alt="Reward Image" class="reward-list-img my-3 border">
                           <div style="display:inline-block;vertical-align: middle;">
                           <p>{{$reward->offer_description}}</p>
                           @if($reward->branch_id != \App\Http\Controllers\Enum\AdminScannerType::royalty_branch_id)
                           <p class="deals-left bold">Partner Details: {{$reward->branch->info->partner_name
                              .', '.$reward->branch->partner_area}}
                           </p>
                           @endif
                           </div>
                           @if($reward->selling_point > $all_points['royalty_points'])
                           <p>Credits required to redeem: 
                           <span class="reward-credit-label-inactive" style="background-color: #adaca7">
                           {{$reward->selling_point}} credits</span>
                           </p>
                           @else
                           <p>
                           Credits required to redeem: 
                           <span class="reward-credit-label-active">{{$reward->selling_point}} credits</span>
                           </p>
                           @endif
                           <br>
                           @if($reward->counter_limit)
                           Remaining amount: <span class="available_reward">
                           {{$reward->counter_limit - $reward->rewardRedeems->sum('quantity')}} </span><br>
                           <input type="hidden" id="reward_available" value="{{$reward->counter_limit}}">
                           @else
                           <span class="available_reward" style="display: none;">100 </span>
                           @endif
                           <p><b>Reward expires on:</b> <span>{{date("d F, Y", strtotime($reward->date_duration[0]['to']))}}</span></p>
                           <br>
                           {!! html_entity_decode($reward->offer_full_description) !!}
                           <br>
                           {!! html_entity_decode($reward->tnc) !!}
                        </div>
                        <div class="whitebox-inner-box-buy">
                           <div>
                              <button class="btn btn-success add_reward_{{$reward->id}}" onclick="addRewardToCart({{$reward}})">ADD</button>
                              <div class="inc_dec_{{$reward->id}}" style="display: none;">
                                 <button class="btn btn-reward-add counter_decrement" style="color: #fff;
                                    background-color: #28a745;border-color: #28a745;"
                                    onclick="rewardCounter({{$reward}},false)">-</button>
                                 <button class="btn btn-reward-add reward_counter_{{$reward->id}}" style="cursor:default;"></button>
                                 <button class="btn btn-reward-add counter_increment" style="color: #fff;
                                    background-color: #28a745;border-color: #28a745;"
                                    onclick="rewardCounter({{$reward}},true)">+</button>
                                 <input type="hidden" class="redeemed_counter">
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="modal fade" id="offerDetails_{{$reward->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <h4 class="modal-title">{{ $reward->offer_description }}</h4>
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="cross-icon"></i>
                                 </button>
                              </div>
                              <div class="modal-body">
                                 <div role="tabpanel">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" role="tablist">
                                       <li role="presentation" class="active"><a href="#detailsTab{{$reward->id}}" aria-controls="detailsTab{{$reward->id}}"
                                          role="tab" data-toggle="tab">Details</a>
                                       </li>
                                       <li role="presentation"><a href="#tncTab{{$reward->id}}" aria-controls="tncTab{{$reward->id}}" role="tab"
                                          data-toggle="tab">T&C</a>
                                       </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content offer-tab-content">
                                       <div role="tabpanel" class="tab-pane active" id="detailsTab{{$reward->id}}">
                                          {!! html_entity_decode($reward->offer_full_description) !!}
                                       </div>
                                       <div role="tabpanel" class="tab-pane" id="tncTab{{$reward->id}}">
                                          {!! html_entity_decode($reward->tnc) !!}
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-4">
                     <div class="online-deal-price-box">
                        <div class="reward-cart-head center">
                           <p>Your Order</p>
                        </div>
                        <p class="pls_add mtb-10 center">Please add reward(s)</p>
                        <div id="reward_redeem_summery"></div>
                        <input type="hidden" id="canRedeemReward" value="[]">
                        <div class="center">
                        <label for="user_credits">Credits Remaining: <span class="remaining_point">{{$all_points['royalty_points']}}</span></label>
                        </div>
                        @if($reward->selling_point > $all_points['royalty_points'])
                        <button class="btn btn-block btn-secondary" disabled>Redeem Now</button>
                        @else
                        <button class="btn btn-block btn-secondary" id="redeem_confirm_button" disabled>Redeem Now</button>
                        @endif
                     </div>
                  </div>
                  @endif
                  <input type="hidden" class="reward_ids">
                  <input type="hidden" class="rewards">
                  <input type="hidden" class="rewards_of" value="rbd">
               </div>
         </div>
      </div>
   </div>
</section>
<!-- address modal for royalty redeem reward -->
<div id="redeemAddressModal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Required Information</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            @if($req_phone)
            <label for="req_phone">Phone</label> <span class="req_phone_error"></span>
            <input type="text" class="form-control" name="req_phone" id="req_phone" placeholder="{{$req_phone}}"
               value="{{$customer_data->customer_contact_number}}" maxlength="14">
            @endif
            @if($req_email)
            <label for="req_email">Email</label><span class="req_email_error"></span>
            <input type="text" class="form-control" name="req_email" id="req_email" placeholder="{{$req_email}}"
               value="{{$customer_data->customer_email}}">
            @endif
            @if($req_del_add)
            <label for="req_del_add">Delivery Address</label><span class="req_del_add_error"></span>
            <input type="text" class="form-control" name="req_del_add" id="req_del_add" placeholder="{{$req_del_add}}"
               value="{{$customer_data->customer_address}}">
            @endif
            @if($req_others)
            <label for="req_others">Others</label><span class="req_others_error"></span>
            <input type="text" class="form-control" name="req_others" id="req_others" placeholder="{{$req_others}}">
            @endif
            <input type="submit" class="btn btn-success" onclick="redeemRewardConfirm()">
         </div>
      </div>
   </div>
</div>
{{--can not redeeem reward modal--}}
<div id="canNotRedeemRewardModal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Sorry!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal" style="text-align: center;">
            @if($customer_data->exp_status == 'expired')
            <p>Your membership has expired. Renew now to use your credits and unlock exciting gifts and rewards.</p>
            <button class="btn btn-default" data-dismiss="modal">Later</button>
            <a href="{{url('renew_subscription')}}" class="btn btn-success">Renew Now</a>
            @elseif($customer_data->card_active == 2 && $customer_data->delivery_type == 11)
            <p>You need to be a premium member to use your credits and unlock exciting gifts and rewards.</p>
            <button class="btn btn-default" data-dismiss="modal">Later</button>
            <a href="{{url('renew_subscription')}}" class="btn btn-success">Upgrade Now</a>
            @endif
         </div>
      </div>
   </div>
</div>
@include('footer')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/customer/rewards.js')}}"></script>
<script>
   localStorage.setItem('my_branch_point', '{{$all_points['royalty_points']}}');
   //set can redeem reward field value
   var canRedeemValues = $("#canRedeemReward");
   @if($customer_data->exp_status == 'expired')
      canRedeemValues.val(false);
   @elseif($customer_data->card_active == 2 && $customer_data->delivery_type == 11)
      canRedeemValues.val(false);
   @else
      canRedeemValues.val(true);
   @endif
</script>
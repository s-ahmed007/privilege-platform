@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <p>REWARDS</p>
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
               <div class="col-md-12 col-sm-12 col-xs-12 mb-10">
                  <h3 class="graybox-head">ROYALTY CREDIT</h3>
                  <div class="row graybox">
                     <div class="col-md-8 col-sm-12 col-xs-12">
                     <div class="reward-point-summary">
                        <p>Your available Royalty credits: </p>
                        <p> You can earn credits from many activities on the platform, such as: rate, review, scans etc.
                           Use credits to redeem greater rewards!</p>
                     </div>
                        <div class="row" style="float:right">
                           <div class="col-md-6">
                              <a href="{{ url('royaltyrewards') }}">
                              <button class="btn btn-primary">
                              FIND OUT MORE
                              </button>
                              </a>
                           </div>
                           <div class="col-md-6">
                              <a href="{{url('users/'.session('customer_username').'/credit_history')}}"
                                 class="btn btn-primary">
                              CREDIT HISTORY
                              </a>
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
                           {{--                           @if($customer_data->total_points > 0)--}}
                           {{--
                           <p>You will be able to avail your Royalty Credits very soon!</p>
                           --}}
                           {{--                           @endif--}}
                        </div>
                     </div>
                  </div>
               </div>
            </div><br>
            <div class="row">
               <div class="col-md-12">
                  <div class="tabbable-panel">
                     <div class="tabbable-line">
                        <ul class="nav nav-tabs ">
                           <li class="active">
                              <a href="#tab_default_1" data-toggle="tab">
                              REWARDS </a>
                           </li>
                           <li>
                              <a href="#tab_default_2" data-toggle="tab">
                              REDEEMED </a>
                           </li>
                        </ul>
                        <div class="tab-content">
                           <div class="tab-pane active" id="tab_default_1">
                              <div class="row">
                                 @if(count($rewards) > 0)
                                 <div class="col-md-12 col-sm-12 col-xs-12">
                                    @foreach($rewards as $reward)
                                    <div class="row whitebox">
                                       <div class="col-md-10 col-sm-10 col-xs-10" style="display: inline-flex;">
                                          <img src="{{$reward->image}}" alt="Reward Image" class="reward-list-img my-3 border">
                                          <div class="reward-list-text">
                                             <p class="bold">{{$reward->offer_description}},  @if($reward->branch_id != \App\Http\Controllers\Enum\AdminScannerType::royalty_branch_id)
                                                <span class="reward-partner-branch">{{$reward->branch->info->partner_name
                                                .', '.$reward->branch->partner_area}}</span>
                                             @endif</p>
                                             @if($reward->selling_point > $all_points['royalty_points'])
                                             <p>
                                                <span class="reward-credit-label-inactive" style="background-color: #adaca7">
                                                   {{$reward->selling_point}} credits</span></p>
                                             @else<p>
                                                <span class="reward-credit-label-active">{{$reward->selling_point}} credits</span></p>
                                             @endif
                                             @if($reward->counter_limit)
                                                <p>Remaining: {{$reward->counter_limit - $reward->rewardRedeems->sum('quantity')}} </p>
                                             @endif
                                             <p>Expires on: <span class="red">{{date("d F, Y", strtotime($reward->date_duration['to']))}}</span></p>
                                          </div>
                                       </div>
                                       <div class="col-md-2 col-sm-2 col-xs-2">
                                          @if(session('user_type') != 3)
                                             <a href="{{url('users/'.session('customer_username').'/reward/'.$reward->id)}}">
                                                <div class="reward-details-icon">
                                                   <i class="fas fa-chevron-circle-right"></i>
                                                </div>
                                             </a>
                                          @else
                                             <div class="reward-details-icon"
                                                data-toggle="modal" data-target="#guestCantRedeemRewardModal">
                                                <i class="fas fa-chevron-circle-right"></i>
                                             </div>
                                          @endif
                                       </div>
                                    </div>
                                    @endforeach
                                    {{$rewards->links()}}
                                 </div>
                                 @else
                                 <h4>No rewards available.</h4>
                                 @endif
                              </div>
                           </div>
                           <div class="tab-pane" id="tab_default_2">
                              @if(count($redeemedRewards) > 0)
                                 @foreach($redeemedRewards as $redeemed)
                                    <div class="row whitebox">
                                       <div class="col-md-10 col-sm-10 col-xs-10" style="display: inline-flex;">
                                          <img src="{{$redeemed->reward->image}}" alt="Reward Image" class="reward-list-img my-3 border">
                                          <div class="reward-list-text">
                                             <p><b>{{$redeemed->reward->offer_description}}</b></p>
                                             <p> Quantity: {{$redeemed->quantity}}</p>
                                             @if($redeemed->reward->branch_id != \App\Http\Controllers\Enum\AdminScannerType::royalty_branch_id)
                                                <p class="reward-partner-branch">@ {{$redeemed->reward->branch->info->partner_name
                                                .', '.$redeemed->reward->branch->partner_area}}</p>
                                             @endif
                                             <span class="reward-credit-label-active">{{$redeemed->reward->selling_point}}
                                                credits</span><br>
                                             <?php
                                             $posted_on = date("Y-M-d H:i:s", strtotime($redeemed->created_at));
                                             $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                             $availed = date("Y-M-d H:i:s", strtotime($redeemed->updated_at));
                                             $availed = \Carbon\Carbon::createFromTimeStamp(strtotime($availed));
                                             ?>
                                             <p><b>Redeemed:</b> {{$created->diffForHumans()}}</p>
                                             @if($redeemed->used == 1)
                                                <p><b>Used on:</b> {{$availed->diffForHumans()}}</p>
                                             @endif
                                             <p>
                                             @if($redeemed->used != 1)
                                                @if(date('Y-m-d', strtotime($redeemed->reward->date_duration['to'])) < date('Y-m-d'))
                                                   <b>Expired on:</b> {{date("d F, Y", strtotime($redeemed->reward->date_duration['to']))}}
                                                @else
                                                   <?php $status = (new \App\Http\Controllers\Reward\functionController())
                                                           ->getExpStatusOfRedeemedReward($redeemed->reward->date_duration['to'], 3);?>
                                                   @if($status == 1)
                                                      <b>Expiring on:</b> {{date("d F, Y", strtotime($redeemed->reward->date_duration['to']))}}
                                                   @else
                                                      <b>Expires on:</b> {{date("d F, Y", strtotime($redeemed->reward->date_duration['to']))}}
                                                   @endif
                                                @endif
                                             @endif
                                             </p>
                                             <div class="pp-offer-btn mtb-10">
                                                @if($redeemed->reward->offer_full_description != null)
                                                <button class="btn btn-primary-thin offerDetails" data-offer-id="{{$redeemed->reward->id}}"
                                                        data-offer-tab="details">Details</button>
                                                @endif
                                                  <button class="btn btn-primary-thin offerDetails" data-offer-id="{{$redeemed->reward->id}}"
                                                          data-offer-tab="tnc">T&C</button>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-md-2 col-sm-2 col-xs-2">
                                          <div class="reward-details-icon">
                                          @if($redeemed->used == 1)
                                             <span class="redeemed-reward-used"> USED</span>
                                          @else
                                             @if(date('Y-m-d', strtotime($redeemed->reward->date_duration['to'])) < date('Y-m-d'))
                                                <span class="redeemed-reward-expired">EXPIRED</span>
                                             @else
                                                <?php $status = (new \App\Http\Controllers\Reward\functionController())
                                                           ->getExpStatusOfRedeemedReward($redeemed->reward->date_duration['to'], 3);?>
                                                @if($status == 1)
                                                   <span class="redeemed-reward-expiring"> EXPIRING</span>
                                                @else
                                                   <span class="redeemed-reward-notused">NOT USED</span>
                                                @endif
                                             @endif
                                          @endif
                                          </div>
                                       </div>
                                    </div>
                                    <div class="modal" id="offerDetails_{{$redeemed->reward->id}}" tabindex="-1"
                                         role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                       <div class="modal-dialog">
                                          <div class="modal-content">
                                             <div class="modal-header"> <h4 class="modal-title">{{ $redeemed->reward->offer_description }}</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><i class="cross-icon"></i>
                                                </button>

                                             </div>
                                             <div class="modal-body">
                                                <div role="tabpanel">
                                                   <!-- Nav tabs -->
                                                   <ul class="nav nav-tabs" role="tablist">
                                                      <li role="presentation" class="active">
                                                         <a href="#detailsTab{{$redeemed->reward->id}}"
                                                            aria-controls="detailsTab{{$redeemed->reward->id}}"
                                                            role="tab" data-toggle="tab">Details</a>
                                                      </li>
                                                      <li role="presentation">
                                                         <a href="#tncTab{{$redeemed->reward->id}}"
                                                            aria-controls="tncTab{{$redeemed->reward->id}}" role="tab"
                                                            data-toggle="tab">T&C</a>
                                                      </li>
                                                   </ul>
                                                   <!-- Tab panes -->
                                                   <div class="tab-content offer-tab-content">
                                                      <div role="tabpanel" class="tab-pane active" id="detailsTab{{$redeemed->reward->id}}">
                                                         {!! html_entity_decode($redeemed->reward->offer_full_description) !!}
                                                      </div>
                                                      <div role="tabpanel" class="tab-pane" id="tncTab{{$redeemed->reward->id}}">
                                                         {!! html_entity_decode($redeemed->reward->tnc) !!}
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 @endforeach
                              @else
                                 <h4 class="no-data-found">No rewards redeemed.</h4>
                              @endif
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
<!-- reward redeem success Modal-->
<div id="rewardRedeemSuccessModal" class="modal fade" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">Success</h4>
            <button type="button" class="close" data-dismiss="modal">
               <i class="cross-icon"></i>
            </button>
          
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <p class="redeem_success_msg">You have successfully redeemed your reward.</p>
         </div>
      </div>
   </div>
</div>
<div id="guestCantRedeemRewardModal" class="modal fade" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header"> <h4 class="modal-title">Sorry!</h4>
            <button type="button" class="close" data-dismiss="modal">
               <i class="cross-icon"></i>
            </button>
           
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <p class="redeem_success_msg">You need to be a Royalty Premium Member to redeem reward.<br>
               <a href="{{ url('select-card') }}">Buy Premium Membership</a> to redeem reward.
            </p>
         </div>
      </div>
   </div>
</div>
@include('useracc.commonDivs')
@include('footer')
<script>
   // script to show individual offer details modal & open specific tab
   $(document).ready(function() {
      $(".offerDetails").click(function() {
         var offer_id = $(this).data("offer-id");
         var offer_tab = $(this).data("offer-tab");
         if (offer_tab === "details") {
            $('a[href^="#tncTab' + offer_id + '"]').parent().removeClass("active");
            $('a[href^="#detailsTab' + offer_id + '"]').parent().addClass("active");
            $("#tncTab" + offer_id).removeClass("active");
            $("#detailsTab" + offer_id).addClass("active");
         } else if (offer_tab === "tnc") {
            $('a[href^="#detailsTab' + offer_id + '"]').parent().removeClass("active");
            $('a[href^="#tncTab' + offer_id + '"]').parent().addClass("active");
            $("#detailsTab" + offer_id).removeClass("active");
            $("#tncTab" + offer_id).addClass("active");
         }
         $("#offerDetails_" + offer_id).modal("show");
      });

      //check if user redeemed reward then show success msg
      if (localStorage.getItem("reward_redeemed_success_msg") !== null) {
         $(".redeem_success_msg").text(localStorage.getItem("reward_redeemed_success_msg"));
         $("#rewardRedeemSuccessModal").modal("toggle");
         localStorage.removeItem('reward_redeemed_success_msg');
      }
   });
</script>
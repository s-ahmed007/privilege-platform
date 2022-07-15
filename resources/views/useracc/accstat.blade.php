@include('header')
<?php use \App\Http\Controllers\functionController; ?>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <p>Stats</p>
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
         <div class="col-md-8 col-sm-12 col-xs-12 mb-10">
            <h3 class="graybox-head">STATS</h3>
            <div class="row center graybox m-0">
               <div class="col-md-3 col-sm-3 col-xs-3">
                  <div class="acc-stat-image-container">
                     <a href="{{url('users/'.session('customer_username').'/offers')}}">
                        <div>
                           <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/card_used.png"
                                width="100%" alt="Royalty card-used" class="user-acc-stat-img lazyload">
                        </div>
                        <h4 class="acc-stats-heading mt-2">
                           {{ $card_used }}
                        </h4>
                        <span class="bold">
                        {{$card_used > 1 ? 'Usages' : 'Usage'}}
                        </span>
                     </a>
                  </div>
                  <p class="dashboard-col-text center">Your total usage</p>
               </div>
               <div class="col-md-3 col-sm-3 col-xs-3">
                  <a href="" data-toggle="modal"
                     data-target="#visitedPartnerModal">
                     <div class="acc-stat-image-container">
                        <div>
                           <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/partner_visited.png" width="100%"
                              alt="Royalty user"
                              class="user-acc-stat-img lazyload">
                        </div>
                        <h4 class="acc-stats-heading mt-2">
                           {{ $partner_number }}
                        </h4>
                        <span class="bold">
                        {{$partner_number > 1 ? 'Partners' : 'Partner'}}
                        </span>
                     </div>
                  </a>
                  <p class="dashboard-col-text center">Total partners visited</p>
               </div>
               <div class="col-md-3 col-sm-3 col-xs-3">
                  <div class="acc-stat-image-container">
                     <a href="{{url('users/'.session('customer_username').'/reviews')}}">
                        <div>
                           <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/dashboard_review.png" width="100%"
                              alt="Royalty user"
                              class="user-acc-stat-img lazyload">
                        </div>
                        <h4 class="acc-stats-heading mt-2">
                           {{ $review_number }}
                        </h4>
                        <span class="bold">
                        {{$review_number > 1 ? 'Reviews' : 'Review'}}
                        </span>
                     </a>
                  </div>
                  <p class="dashboard-col-text center">Your total reviews</p>
               </div>
               <div class="col-md-3 col-sm-3 col-xs-3">
                  <div class="acc-stat-image-container">
                     <div>
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/heart.png" width="100%"
                           alt="Royalty user likes"
                           class="user-acc-stat-img lazyload">
                     </div>
                     <h4 class="acc-stats-heading mt-2">
                        {{ functionController::likeNumber(session('customer_id')) }}
                     </h4>
                     <span class="bold">
                     <?php echo functionController::likeNumber(session('customer_id')) > 1 ? 'Likes' : 'Like';?>
                     </span>
                  </div>
                  <p class="dashboard-col-text center">Total likes on reviews</p>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-12 col-xs-12 recent_trans_followers">
                     <h3 class="graybox-head">RECENT OFFERS AVAILED</h3>
                     <div class="recent-transactions-body">
                        @if(isset($transactionHistory['transactions']) && count($transactionHistory['transactions']) == 0 )
                        <div class="no-info padding">
                           <p>No offers availed</p>
                        </div>
                        @elseif(isset($transactionHistory['transactions']) && count($transactionHistory['transactions']) <= 5)
                        @foreach($transactionHistory['transactions'] as $tr)
                        <div class="transactions-line">
                           <p><i class="transaction-icon"></i></p>
                           <p class="transaction-history-time">
                              <?php
                                 $posted_on = date("Y-M-d H:i:s", strtotime($tr['posted_on']));
                                 $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                 ?>
                              {{$created->diffForHumans()}}
                           </p>
                           <p class="transaction-history-details">
                              <?php $pname = str_replace("'", "", $tr['branch']['info']['partner_name']); ?>
                              You availed an offer at
                              <b><a
                                 href="{{ url('partner-profile/'. $pname.'/'.$tr['branch']['id']) }}"
                                 target="_blank">
                              {{$tr['branch']['info']['partner_name'].' ('.$tr['branch']['partner_area'].')'}}
                              </a></b>
                           </p>
                        </div>
                        @endforeach
                        @elseif(isset($transactionHistory['transactions']) && count($transactionHistory['transactions']) > 5)
                        @for($i=0; $i < 5; $i++)
                        <div class="transactions-line">
                           <p><i class="transaction-icon"></i></p>
                           <p class="transaction-history-time">
                              <?php
                                 $posted_on = date("Y-M-d H:i:s", strtotime($transactionHistory['transactions'][$i]['posted_on']));
                                 $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                 ?>
                              {{$created->diffForHumans()}}
                           </p>
                           <p class="transaction-history-details">
                              <?php $pname = str_replace("'", "", $transactionHistory['transactions'][$i]['branch']['info']['partner_name']); ?>
                              You availed an offer at
                              <b><a
                                 href="{{url('partner-profile/'. $pname.'/'.$transactionHistory['transactions'][$i]['branch']['id'])}}"
                                 style="font-weight: bold" target="_blank">
                              {{ $transactionHistory['transactions'][$i]['branch']['info']['partner_name'].' ('.$transactionHistory['transactions'][$i]['branch']['partner_area'].')' }}
                              </a>
                              </b>
                           </p>
                        </div>
                        @endfor
                        @else
                        <div class="no-info padding">
                           <p>No offer availed</p>
                        </div>
                        @endif
                     </div>
                  </div>
         </div>
         </div>
      </div>
   </div>
</section>

<!-- Places customer has visited Modal-->
<div id="visitedPartnerModal" class="modal fade" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">Partners you have visited</h4>
            <button type="button" class="close" data-dismiss="modal">
               <i class="cross-icon"></i>
            </button>
          
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            @if($visited_partners && count($visited_partners) != 0)
               <div class="row" style="padding: 1%">
                  @foreach($visited_partners as $visited_partner)
                     <div class="col-md-6 col-sm-6 col-xs-12" style="padding-bottom: 5px;">
                        <?php $pname = str_replace("'", "", $visited_partner->partner_name); ?>
                        <a href="{{url('partner-profile/'.$pname.'/'.$visited_partner->branch_id)}}"
                           target="_blank">
                           <div class="table" style="margin-bottom: 5px;">
                              <div class="row table_row coupon-table m-0">
                                 <div class="cell col-md-10 col-sm-8 col-xs-8 coupon-details">
                                    <img src="{{ $visited_partner->partner_profile_image }}"
                                         alt="Royalty partner" class="testborder img-40 lazyload">
                                    <p class="lboy dots d-block">{{$visited_partner->partner_name.' ('.
                                    $visited_partner->partner_area.')'}}</p>
                                 </div>
                                 <div class="cell col-md-2 col-sm-4 col-xs-4"
                                      style="text-align: center;margin-top: 10px;">
                                    <span style="font-weight: bold">{{$visited_partner->total_visit}}</span>
                                 </div>
                              </div>
                           </div>
                        </a>
                     </div>
                  @endforeach
               </div>
            @else
               <div>
                  <p>You did not visit any of our partners yet.</p>
               </div>
            @endif
         </div>
      </div>
   </div>
</div>
@include('useracc.commonDivs')
@include('footer')
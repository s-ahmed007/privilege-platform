{{--modal to show total promo used of influencer --}}
<div id="total_promo_used" class="modal" role="dialog" style="top: 10%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">   <h4 class="modal-title">Promo Usage</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="cross-icon"></i>
                </button>
             
            </div>
            <div class="modal-body" id="profile_modal" class="profile_modal">
                <div class="no-info">
                    @if($promo_used['user'] != null)
                        <p>Your Promo Code:
                        <p>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-copy js-tooltip js-copy"
                                    data-toggle="tooltip" data-placement="bottom"
                                    data-copy="{{$promo_used['user']->code}}" title="Copy">
                                {{$promo_used['user']->code}}
                                <i class="bx bxs-copy"></i>
                            </button>
                        </div>
                    @endif
                    <p>Your promo code has been used {{$promo_used['usage']}} {{$promo_used['usage'] > 1 ? 'times.' : 'time.'}}</p>
                </div>

                <div class="table" style="text-align: center">
                    <div class="row header table_row t-his-row">
                        <div class="cell">Date</div>
                        <div class="cell">Type of Card</div>
                        <div class="cell">Commission</div>
                    </div>
                    <?php
                    $total_commission = 0;
                    $total_cards = 0;
                    ?>
                    @if($promo_used['user'] != null)
                        @foreach($promo_used['user']['promoUsage'] as $history)
                            <div class="row table_row t-his-row">
                                <div class="cell" data-title="Date">
                                    <?php
                                    echo $history->ssl->tran_date;
                                    ?>
                                </div>
                                <div class="cell" data-title="Type of Card">
                                    <b>
                                        <p>
                                            @if($history->customerInfo->customer_type == 2)
                                                {{'Royalty card'.' - '.$history->customerInfo->month.' months'}}
                                            @endif
                                        </p>
                                    </b>
                                </div>
                                <div class="cell" data-title="Commission">
                                    <?php
                                    $commission = round(($history->ssl->amount * \App\Http\Controllers\Enum\InfluencerPercentage::percentage)/100);
                                    $total_commission += $commission;
                                    $total_cards += 1;
                                    echo $commission;
                                    ?>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="row table_row">
                        <div class="cell total" data-title="">
                            Total
                        </div>
                        <div class="cell" data-title="Sold Cards">
                            <b>{{$total_cards}}</b>
                        </div>
                        <div class="cell" data-title="Commission">
                            <b>{{$total_commission}}</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- user Create a Review Modal--}}
<div id="reviewModal" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Write a review</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal" style="text-align: center">
            <form name="reviewForm" method="post" id="reviewForm">
               <span id="star_error"></span><br>
               <p>Rating</p>
               <div class="rate_row">
                  <span class="rate_star" data-value="1"><i class="bx bxs-star "></i></span>
                  <span class="rate_star" data-value="2"><i class="bx bxs-star "></i></span>
                  <span class="rate_star" data-value="3"><i class="bx bxs-star "></i></span>
                  <span class="rate_star" data-value="4"><i class="bx bxs-star "></i></span>
                  <span class="rate_star" data-value="5"><i class="bx bxs-star "></i></span>
                  <input type="hidden" class="get_star" id="get_star" name="rate_star">
               </div>
               <div>
                  <span id="heading_error"></span><br>
                  <label>Title of your review</label>
                  <br>
                  <input type="text" name="heading" id="heading review_heading"
                     maxlength="50" class="form-control review_title" style="width: 100%">
               </div>
               <p align="right" style="font-size: small;">
                  <span id="titleChars">0/50</span>
               </p>
               <div>
                  <span id="comment_error"></span><br>
                  <label>Your review</label><br>
                  <textarea name="content" rows="8" cols="60" id="comment review_comment"
                     data-emojiable="true" class="form-control" maxlength="500"></textarea><br>
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="review_type" id="review_type">
               </div>
               <p align="right" style="font-size: small; margin-top: -10px">
                  <span id="revChars">0/500</span>
               </p>
               <button  class="btn btn-primary review_submit_succeed" style="display: none; background: rgb(28,194,43);margin-left: unset;">Successfully submitted!</button>
               <button type="submit" class="btn btn-primary submit_review" id="review_submit"
                  onclick="return validate();">Submit your review
               </button>
            </form>
         </div>
      </div>
   </div>
</div>
{{-- modal to show review post success message --}}
@if(session('reviewSubmitted'))
<div id="reviewSubmitted" class="modal" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">   <h4 class="modal-title">
               Review submitted successfully!
            </h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         
         </div>
         <div class="modal-body">
            <p>{{ session('reviewSubmitted') }}</p>
         </div>
      </div>
   </div>
</div>
@endif
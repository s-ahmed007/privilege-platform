<?php use App\Http\Controllers\Enum\ReviewType; ?>
@include('header')
<!-- Begin emoji-picker Stylesheets -->
<link href="{{asset('emoji/css/emoji.css')}}" rel="stylesheet">
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <!-- <h2>Find your profile details, usages, rewards all together here</h2> -->
         <p>OFFERS AVAILED</p>
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
               @if($customer_data->customer_type != 3)
               <form class="form-horizontal form-label-left">
                  {{csrf_field()}}
                  <div class="form-group" style="text-align: center;margin: 0 auto;">
                     <div class="col-md-4 sort-head">Sort by Month/Year</div>
                     <div class="col-md-4 sort-year">
                        <select class="form-control" id="sortCusTranHisYear" onchange="SortCusTranHis()">
                           <option value="all">Year</option>
                           <?php
                            for ($i = 2018; $i <= date('Y'); $i++) {
                                  echo "<option value='$i'>$i</option>";
                            }
                            ?>
                        </select>
                     </div>
                     <div class="col-md-4 sort-month">
                        <select class="form-control" id="sortCusTranHisMonth" onchange="SortCusTranHis()">
                           <option value="all">Month</option>
                           <option value="01">January</option>
                           <option value="02">February</option>
                           <option value="03">March</option>
                           <option value="04">April</option>
                           <option value="05">May</option>
                           <option value="06">June</option>
                           <option value="07">July</option>
                           <option value="08">August</option>
                           <option value="09">September</option>
                           <option value="10">October</option>
                           <option value="11">November</option>
                           <option value="12">December</option>
                        </select>
                     </div>
                  </div>
               </form>
               <div id="customer_tran_his">
                  @if(isset($transactionHistory['transactions']) && count($transactionHistory['transactions']) > 0)
                  <div class="table-responsive whitebox">
                     <table class="table">
                        <thead>
                           <tr>
                              <td>Date & Time</td>
                              <td>Partners Visited</td>
                              <td>Credits</td>
                              <td>Offers Availed</td>
                              <td>Review</td>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($transactionHistory['transactions'] as $history)
                           <tr>
                              <th>
                                <?php
                                 $posted_on = date("Y-M-d H:i:s", strtotime($history['posted_on']));
                                 $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                 echo date_format($created, "h:i A d-m-y");
                                ?>
                              </th>
                              <th>
                                 {{$history['branch']['info']['partner_name'].' - '.$history['branch']['partner_area']}}
                              </th>
                              <th>
                                 @if($history['redeem_id'] == null)
                                 {{$history['transaction_point']}}
                                 @else
                                 Reward
                                 @endif
                              </th>
                              <th>                           @if($history['offer'] != null)
                                 {{$history['offer']['offer_description']}}
                                 @else
                                 {{"Discount Availed"}}
                                 @endif
                              </th>
                              <th>
                                  @if($history['review_id'] == null)
                                 <?php $review_submit_url = url('createReview/'.$history['branch']['partner_account_id']
                                          .'/'.$history['id']); ?>
                                 <button class="btn btn-green" onclick="createReview('{{ $review_submit_url }}',
                                         '{{ReviewType::OFFER}}')">Review</button>
                                 @else
                                 <span class="btn" style="color:#fff; background-color: #ffc107;cursor: default;
                                  padding: 0px 5px;">Reviewed</span>
                                 @endif
                              </th>
                           </tr>
                           @endforeach
                           <tr>
                           <td><i class="minus-icon"></i></td>
                              <td><i class="minus-icon"></i></td>
                              <td>{{$transactionHistory['total_point']}}</td>
                              <td><i class="minus-icon"></i></td>
                              <td><i class="minus-icon"></i></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  @else
                  <div class="no-info">
                     <h4>No offers availed yet.</h4>
                  </div>
                  @endif
               </div>
               @else
               @if($customer_data->card_active == 0)
               @if(!empty($info_at_buy_card) && $info_at_buy_card->delivery_type == 4)
               <h4 class="no-info" style="margin: 10px 50px 50px 60px;">
                  Your Royalty Membership is being processed. Please activate once you receive a
                  confirmation E-mail.
               </h4>
               @else
               <div class="no-info">
                  <h4>
                     <a href="{{url('/select-card')}}">Get Royalty Premium Membership</a>
                      to make transaction in our partner stores.
                  </h4>
               </div>
               @endif
               @endif
               @endif
         </div>
      </div>
   </div>
</section>
{{-- user Create a Review Modal--}}
{{-- 
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
               <span id="star_error"></span>
               <p>Rating</p>
               <div class="rate_row">
                  <span class="rate_star" data-value="1"><i class="bx bxs-star yellow"></i></span>
                  <span class="rate_star" data-value="2"><i class="bx bxs-star yellow"></i></span>
                  <span class="rate_star" data-value="3"><i class="bx bxs-star yellow"></i></span>
                  <span class="rate_star" data-value="4"><i class="bx bxs-star yellow"></i></span>
                  <span class="rate_star" data-value="5"><i class="bx bxs-star yellow"></i></span>
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
--}}
{{-- modal to show review post success message --}}
{{-- 
<div id="reviewSubmitted" class="modal" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header"> <h4 class="modal-title">
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
--}}
<script>
   function SortCusTranHis() {
       var url = "{{ url('/sort-customer-transaction-history') }}";
       var year = $("#sortCusTranHisYear").val();
       var month = $("#sortCusTranHisMonth").val();
       if (year === 'all' && month !== 'all') {
           alert('Please select a year to see result');
           return false;
       }
       $('.page_loader').fadeIn();//show loading gif
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'year': year, 'month': month},
           success: function (data) {
               $("#customer_tran_his").hide().html(data).fadeIn('slow');
               $('.page_loader').fadeOut();//hide loading gif
           }
       })
   }
</script>
@include('useracc.commonDivs')
@include('footer')
@include('footer-js.user-account-js')
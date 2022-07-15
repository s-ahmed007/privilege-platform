@include('header')
<?php use \App\Http\Controllers\functionController; ?>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Review details</h2>
      </div>
   </div>
</section>
<section id="features" class="features">
   <div class="container">
   <!-- THIS PORTION TO DISPLAY SINGLE REVIEW DETAILS -->
   @if(isset($singleReviewDetails['review']) && isset($singleReviewDetails['review']['id']))
   <div class="whitebox-inner-box-inner" id="review-id-{{$singleReviewDetails['review']['id']}}">
      <p class="reviews-head">Review about {{$singleReviewDetails['review']['partner_name'].', '.
      $singleReviewDetails['review']['partner_area']}} </p>
      <!-- user review -->
      <div class="row">
         <div class="col-md-2 col-sm-2 col-xs-3">
            <!-- User profile picture -->
            <div class="comment-avatar center">
               {{--<a href="{{url('user-profile/'.$singleReviewDetails['customer_username'])}}">--}}
               <a>
               <img src="{{ asset($singleReviewDetails['review']['customer_profile_image'])}}"
                  class="img-circle img-40 primary-border lazyload" alt="Royalty user-pic">
               </a>
               <p class="comment-name reviewer-name mt">
                  {{-- <a> --}}
                  {{$singleReviewDetails['review']['customer_first_name'].' '.$singleReviewDetails['review']['customer_last_name']}}
               {{-- </a> --}}
               </p>
               {{--<a href="{{url('user-profile/'.$singleReviewDetails['customer_username'])}}" target="_blank">--}}               
               <i class="bx bx-edit user-total-reviews">
                 <span>{{ functionController::reviewNumber($singleReviewDetails['review']['customer_id']) }}</span> 
               </i>
              {{--  </a> --}}
               <i class="bx bx-like likes_of_user_{{$singleReviewDetails['review']['customer_id']}}">
                  <span>{{ functionController::likeNumber($singleReviewDetails['review']['customer_id']) }}</span>               
               </i>
            </div>
         </div>
         <div class="col-md-10 col-sm-10 col-xs-9">
            <!-- User Review box -->
            <div class="comment-box">
               <div class="comment-head">
                  {{--social media buttons--}}
                  <?php
                     if ($singleReviewDetails['review']['heading'] != null && $singleReviewDetails['review']['heading'] != 'n/a') {
                         $heading = str_replace("'", "", $singleReviewDetails['review']['heading']);
                         $heading = str_replace('"', "", $heading);
                         $heading = trim(preg_replace('/\s+/', ' ', $heading));
                     } else {
                         $heading = '';
                     }
                     if ($singleReviewDetails['review']['heading'] != null && $singleReviewDetails['review']['body'] != 'n/a') {
                         $body = str_replace("'", "", $singleReviewDetails['review']['body']);
                         $body = str_replace('"', "", $body);
                         $body = trim(preg_replace('/\s+/', ' ', $body));
                     } else {
                         $body = '';
                     }
                     $newline = '\n';
                     $pretext = 'Review On';
                     $partner_name = " " . str_replace("'", "\'", $singleReviewDetails['review']['partner_name']);
                     $posttext = 'on royaltybd.com';
                     $review_body = $body;
                     $review_head = $heading;
                     $enc_review_id = (new functionController)->socialShareEncryption('encrypt', $singleReviewDetails['review']['id']);
                     $review_url = url('/review/' . $enc_review_id);
                     ?>
                  <div class="social-buttons">
                     <!-- Twitter share button code -->
                     <span onclick="window.open('https://twitter.com/intent/tweet?text=' +
                        encodeURIComponent('<?php echo $pretext . $partner_name . $newline . $newline . substr($review_head,0, 30).'...' . $newline . substr($review_body,0, 130).'...' . $newline . $newline . $review_url;?>')); return false;">
                     <a href="#"><i class="bx bxl-twitter"></i></a>
                     </span>
                     <!-- Facebook share button code -->
                     <span>
                     <?php $review_url = 'https://www.facebook.com/sharer.php?href=https%3A%2F%2F'.url('/').'%2Freview-share%2F' . $enc_review_id; ?>
                     <a href="<?php echo $review_url;?>" target="_blank">
                     <i class="bx bxl-facebook-circle"></i>
                     </a>
                     </span>
                     @if(Session::get('customer_id') == $singleReviewDetails['review']['customer_id'])
                     <p class="center">
                        <a class="btn btn-danger btn-xs" href="{{url('/reviewDelete/'.$singleReviewDetails['review']['id'])}}"
                           onclick="return confirm('Are you sure you want to delete this review?')">
                        <i class="delete-icon">
                        </i>
                        </a>
                     </p>
                     @endif
                  </div>
                  <!-- social media buttons END -->
               </div>
               <div class="comment-content">
                  <div>
                     <span>
                        <?php $pname = str_replace("'", "", $singleReviewDetails['review']['partner_name']);
                           $main_branch = (new functionController)->mainBranchOfPartner($singleReviewDetails['review']['partner_account_id']);
                           ?>
                        <a href="{{ url('/partner-profile/'.$pname.'/'.$main_branch[0]->id) }}">
                     </span>
                     <span>
                     <a href="{{url('/partner-profile/'.$pname.'/'.$main_branch[0]->id)}}"
                       >
                     <p class="partner-name"
                        style="display: inherit">{{$singleReviewDetails['review']['partner_name']}}</p>
                     </a>
                     </span>
                  </div>
                  <div class="review-star">
                     @if($singleReviewDetails['review']['rating'] == 1)
                     <div class="reviewer-star-rating-div">
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                     </div>
                     @elseif($singleReviewDetails['review']['rating'] == 2)
                     <div class="reviewer-star-rating-div">
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                     </div>
                     @elseif($singleReviewDetails['review']['rating'] == 3)
                     <div class="reviewer-star-rating-div">
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                     </div>
                     @elseif($singleReviewDetails['review']['rating'] == 4)
                     <div class="reviewer-star-rating-div">
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                     </div>
                     @else
                     <div class="reviewer-star-rating-div">
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                     </div>
                     @endif
                  </div>
                  <span class="review-post-date">{{date('d-m-y', strtotime($singleReviewDetails['review']['posted_on'])) }}</span>
                  <h4 class="review-head bold">{{$review_head}}</h4>
                  <p class="review-description">{{$review_body}} </p>
                   <?php if($singleReviewDetails['review']['total_likes_of_a_review'] > 0){
                       $onclick = 'onclick="getReviewLikerList('.$singleReviewDetails['review']['id'].')"';
                   }else{
                       $onclick = '';
                   }
                   ?>
                  <div class="like-button">
                     {{--Like option--}}
                     @if((Session::has('customer_id') || Session::has('partner_id')) && $singleReviewDetails['review']['liked'] == 1)
                        <div class="like-content">
                           <button class="btn-like unlike-review" id="principalSelect-{{$singleReviewDetails['review']['id']}}"
                              value="{{$singleReviewDetails['review']['id']}}" data-source="{{$singleReviewDetails['review']['source_id']}}">
                           <i class="love-f-icon"></i>
                           </button>
                        </div>
                        <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$singleReviewDetails['review']['id']}}">
                           {{$singleReviewDetails['review']['total_likes_of_a_review']}}
                           {{ $singleReviewDetails['review']['total_likes_of_a_review'] > 1 ? ' likes' : ' like'}}
                        </p>
                     @elseif(Session::has('customer_id') && $singleReviewDetails['review']['liked'] == 0 && Session::get('customer_id') != $singleReviewDetails['review']['customer_id'])
                        <div class="like-content">
                           <button class="btn-like like-review" id="principalSelect-{{$singleReviewDetails['review']['id']}}"
                              value="{{ $singleReviewDetails['review']['id'] }}" data-source="{{ $singleReviewDetails['review']['source_id'] }}">
                           <i class="love-e-icon"></i>
                           </button>
                        </div>
                        <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$singleReviewDetails['review']['id']}}">
                           {{ $singleReviewDetails['review']['total_likes_of_a_review'] }}
                           {{ $singleReviewDetails['review']['total_likes_of_a_review'] > 1 ? ' likes' : ' like' }}
                        </p>
                     @elseif(Session::has('customer_id') && Session::get('customer_id') == $singleReviewDetails['review']['customer_id'])
                        <!-- {{--
                           <div class="like-content">--}}
                              {{--<button class="btn-like">--}}
                              {{--<i class="love-e-icon"></i>--}}
                              {{--</button>--}}
                              {{--
                           </div>
                           --}} -->
                        <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{ $singleReviewDetails['review']['id'] }}">
                           {{$singleReviewDetails['review']['total_likes_of_a_review']}}
                           {{ $singleReviewDetails['review']['total_likes_of_a_review'] > 1 ? ' likes' : ' like'}}
                        </p>
                     @elseif(Session::has('partner_id') && $singleReviewDetails['review']['liked'] == 0 && Session::get('partner_id') == $singleReviewDetails['review']['partner_account_id'])
                        <div class="like-content">
                           <button class="btn-like like-review" id="principalSelect-{{$singleReviewDetails['review']['id']}}"
                              value="{{ $singleReviewDetails['review']['id'] }}" data-source="{{$singleReviewDetails['review']['source_id']}}">
                           <i class="love-e-icon"></i>
                           </button>
                        </div>
                        <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$singleReviewDetails['review']['id']}}">
                           {{$singleReviewDetails['review']['total_likes_of_a_review']}}
                           {{ $singleReviewDetails['review']['total_likes_of_a_review'] > 1 ? ' likes' : ' like'}}
                        </p>
                     @else
                        <div class="like-content">
                           <button class="btn-like" data-toggle="modal" data-target="#nonClickableLike">
                           <i class="love-e-icon"></i>
                           </button>
                        </div>
                        <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$singleReviewDetails['review']['id']}}">
                           {{ $singleReviewDetails['review']['total_likes_of_a_review'] }}
                           {{ $singleReviewDetails['review']['total_likes_of_a_review'] > 1 ? ' likes' : ' like' }}
                        </p>
                     @endif{{--Like option ends--}}
                  </div>
                  <p class="review-liability">
                     This review is the subjective opinion of a Royalty member and not of Royalty.
                              </p>
               </div>
            </div>
         </div>
      </div>
      <!-- Partner reply -->
      @if(isset($singleReviewDetails['review']['comments'][0]))
      <div class="row m-0 pull-right">
         <div class="">
            <!-- Partner reply box -->
            <div class="whitebox comment-box-partner">
               <div class="comment-content comment-content-partner">
                  <p class="partner-reply">
                  <p class="comment-name partner-response">
                     <a href="{{url('/partner-profile/'.$pname.'/'.$main_branch[0]->id)}}">
                     <b>{{ $singleReviewDetails['review']['partner_name'] }}</b>
                     </a>
                     <span>responded to this review</span>
                  </p>
                  <span class="partner-reply-date">{{date('d-m-y', strtotime($singleReviewDetails['review']['comments'][0]['posted_on'])) }}</span><br>
                  <p class="partner-reply">{{$singleReviewDetails['review']['comments'][0]['comment']}}</p>
                  </p>
               </div>
            </div>
         </div>
      </div>
      @endif
      <!-- if there is no reply and partner will see reply box -->
      @if(Session::has('partner_id') && $singleReviewDetails['review']['partner_account_id'] == Session::get('partner_id') && empty($singleReviewDetails['review']['comments'][0]['comment']))
      <div class="row">
         <div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-9 col-xs-offset-3">
            <!-- Partner reply box -->
            <div class="whitebox partner-color">
               <form action="{{url('replyReview/'.$singleReviewDetails['review']['id'])}}" method="post">
                  {{csrf_field()}}
                  <div class="form-group">
                     <textarea name="reply" id="singleReviewDetails{{0}}" cols="78" rows="4"
                        placeholder="Your reply goes here..."
                        style="float: left;"
                        required class="form-control"
                        maxlength="500" onkeyup="replyChars();"></textarea>
                  </div>
                  <p align="right" style="font-size: small; margin-top: -10px">
                     <span id="charNum">0/500</span>
                  </p>
                  <input type="hidden" name="customerID"
                     value="{{$singleReviewDetails['review']['customer_id']}}">
                  <input type="hidden" name="review_id"
                     value="{{  $singleReviewDetails['review']['id']}}">
                  <br>
                  <button type="submit" class="btn btn-primary reply_btn"
                     style="margin-top: 10px;float: right;margin-right: unset">Reply
                  </button>
               </form>
            </div>
         </div>
      </div>
      @endif
   </div>
   @else
   <!-- when the review doesn't exist -->
   <div class="row">
      <div class="col-md-12">
         <div style="text-align: center;padding: 50px;">
            <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/not-found.png" class="lazyload" alt="Royalty not-found">
            <h3> SORRY! PAGE NOT FOUND!
            </h3>
         </div>
      </div>
   </div>
   @endif
</div>
</section>
<!-- ENDS :: THIS PORTION TO DISPLAY SINGLE REVIEW DETAILS -->
@if(isset($singleReviewDetails['recent_reviews']) && count($singleReviewDetails['recent_reviews']) > 0)
<div class="container" style="padding: 50px;">
   <h4 class="f-xl single-latest-h">
      Meanwhile check out the latest reviews about {{$singleReviewDetails['partner_name']}}
   </h4>
   <div class="row">
      @foreach($singleReviewDetails['recent_reviews'] as $value)
      <div class="col-md-4 col-sm-4 col-xs-12">
         <p class="single-p-review-text">
            <span style="font-weight: bold">
            <i class="quote-o-icon"></i>
            {{$value['heading']}}
            <i class="quote-c-icon"></i>
            </span>
            <br>
            <span class="dots3">
            {{$value['body']}}
            </span>
         </p>
      </div>
      @endforeach
   </div>
</div>
@endif
@include('footer')
<script>
    function replyChars() {
        var no_of_chars = $("#singleReviewDetails0").val();
        $("#charNum").text(no_of_chars.length+'/500');
    }
</script>
@include('header')
<?php use \App\Http\Controllers\functionController; ?>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <!-- <h2>Find your profile details, usages, rewards all together here</h2> -->
         <p>REVIEWS</p>
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
            @if(isset($reviews) && count($reviews) > 0)
            <div class="user-review-total">
               <p>Total Reviews given  <i class="bx bx-edit user-total-reviews">
                  <span>{{ count($reviews) }}</span>
                  </i>
               </p>
               <p>Total Likes Earned <i class="bx bx-like">
                  <span>{{ functionController::likeNumber($customer_data->customerID) }}</span>
                  </i>
               </p>
            </div>
            <br>
            <?php $row = count($reviews); ?>
            @for($i=0; $i < $row; $i++)
                     <div class="whitebox">
                        <div class="comment-head">
                           <?php
                              if ($reviews[$i]['heading'] != null && $reviews[$i]['heading'] != 'n/a') {
                                  $heading = str_replace("'", "", $reviews[$i]['heading']);
                                  $heading = str_replace('"', "", $heading);
                                  $heading = trim(preg_replace('/\s+/', ' ', $heading));
                              } else {
                                  $heading = '';
                              }
                              if ($reviews[$i]['body'] != null && $reviews[$i]['body'] != 'n/a') {
                                  $body = str_replace("'", "", $reviews[$i]['body']);
                                  $body = str_replace('"', "", $body);
                                  $body = trim(preg_replace('/\s+/', ' ', $body));
                              } else {
                                  $body = '';
                              }
                              $newline = '\n';
                              $pretext = 'Review On';
                              $partner_name = " " . str_replace("'", "\'", $reviews[$i]['partner_name']);
                              $posttext = 'on royaltybd.com';
                              $review_body = $body;
                              $review_head = $heading;
                              $enc_review_id = (new functionController)->socialShareEncryption('encrypt', $reviews[$i]['id']);
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

                           </div>
                        </div>
                        <?php $main_branch_details = (new functionController)->mainBranchOfPartner($reviews[$i]['partner_account_id']) ?>
                        <div class="comment-content">
                           <div>
                                 <?php $pname = str_replace("'", "", $reviews[$i]['partner_name']); ?>
                                <a href="{{url('partner-profile/'.$pname.'/'.$main_branch_details[0]['id'])}}"
                                    target="_blank">
                                    <p class="partner-name">{{'@'.$reviews[$i]['partner_name']}}</p>
                                 </a>
                           </div>
                           <div class="review-star">
                              @if($reviews[$i]['rating'] == 1)
                              <div class="reviewer-star-rating-div">
                                 <i class="bx bxs-star yellow"></i>
                                 <i class="bx bx-star yellow"></i>
                                 <i class="bx bx-star yellow"></i>
                                 <i class="bx bx-star yellow"></i>
                                 <i class="bx bx-star yellow"></i>
                              </div>
                              @elseif($reviews[$i]['rating'] == 2)
                              <div class="reviewer-star-rating-div">
                                 <i class="bx bxs-star yellow"></i>
                                 <i class="bx bxs-star yellow"></i>
                                 <i class="bx bx-star yellow"></i>
                                 <i class="bx bx-star yellow"></i>
                                 <i class="bx bx-star yellow"></i>
                              </div>
                              @elseif($reviews[$i]['rating'] == 3)
                              <div class="reviewer-star-rating-div">
                                 <i class="bx bxs-star yellow"></i>
                                 <i class="bx bxs-star yellow"></i>
                                 <i class="bx bxs-star yellow"></i>
                                 <i class="bx bx-star yellow"></i>
                                 <i class="bx bx-star yellow"></i>               
                              </div>
                              @elseif($reviews[$i]['rating'] == 4)
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
                           <?php
                           $posted_on = date("Y-M-d H:i:s", strtotime($reviews[$i]['posted_on']));
                           $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                           ?>
                           <span class="review-post-date">
                                Posted on: {{$created->diffForHumans()}}
                           </span>
                           <h4 class="review-head bold">{{$review_head}}</h4>
                           @if(isset($review_body))
                           <p class="review-description">{{$review_body}}</p>
                           @endif
                           <?php if($reviews[$i]['total_likes_of_a_review'] > 0){
                              $onclick = 'onclick="getReviewLikerList('.$reviews[$i]['id'].')"';
                              }else{
                              $onclick = '';
                              }
                              ?>
                           <p class="likes-on-review" {!! $onclick !!}>{{$reviews[$i]['total_likes_of_a_review']}}
                           {{ $reviews[$i]['total_likes_of_a_review'] > 1 ? ' likes' : ' like'}}
                           </p>
                        </div>
                        <p class="review-liability">
                           This review is the subjective opinion of a Royalty member and not of Royalty.
                           </p>
                     </div>
               <!-- partner reply -->
               @if(isset($reviews[$i]['comments'][0]))
                  @if($reviews[$i]['comments'][0]['moderation_status'] == 1)
                     <div class="row m-0 pull-right">
                        <div class="">
                           <!-- Partner reply box -->
                           <div class="whitebox comment-box-partner">
                              <div class="comment-content comment-content-partner">
                                 <p class="comment-name partner-response">
                                    <a href="{{url('partner-profile/'.$pname.'/'.$main_branch_details[0]['id'])}}"
                                       target="_blank"><b>
                                    {{$reviews[$i]['partner_name']}}</b>
                                    </a>
                                    <span>responded to this review</span>
                                 </p>
                                 <span class="partner-reply-date">{{date('d-m-y', strtotime($reviews[$i]['comments'][0]['posted_on'])) }}</span>
                                 <p class="partner-reply">{{$reviews[$i]['comments'][0]['comment']}}</p>
                              </div>
                           </div>
                        </div>
                     </div>
                     @endif
               @endif

            @endfor
            @else
            <!-- If cardholders review is empty -->
            @if($customer_data->customer_type != 3)
            <div class="no-info">
               <h4>
                  You have not written any reviews yet.
               </h4>
            </div>
            @else
            <!-- If guest review is empty -->
            <div class="no-info">
               <h4>
                  <a href="{{url('/select-card')}}">Get Royalty Premium Membership</a> to write reviews.
               </h4>
            </div>
            @endif
            @endif
         </div>
      </div>
   </div>
</section>
@include('useracc.commonDivs')
@include('footer')
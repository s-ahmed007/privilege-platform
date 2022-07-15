@include('header')
<?php use \App\Http\Controllers\functionController; ?>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Royalty News</h2>
      </div>
   </div>
</section>
<section id="features" class="features">
   <div class="container">
   <!-- THIS PORTION TO DISPLAY SINGLE post DETAILS -->
   @if(isset($singlePostDetails['post_details']) && isset($singlePostDetails['post_details']['id']))
   <div class="row" data-aos="fade-left">
      <div class="col-md-8">
         <div id="newsfeed_dashboard">
            <div class="news-feed" id="post-id-{{$singlePostDetails['post_details']['id']}}">
               <?php
                  $posted_on = date("Y-M-d H:i:s", strtotime($singlePostDetails['post_details']['posted_on']));
                  $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                  ?>
               <section class="whitebox">
                  <?php
                     if ($singlePostDetails['post_details']['heading'] != null) {
                         $heading = str_replace("'", "", $singlePostDetails['post_details']['heading']);
                         $heading = str_replace('"', "", $heading);
                         $heading = trim(preg_replace('/\s+/', ' ', $heading));
                     } else {
                         $heading = '';
                     }
                     if ($singlePostDetails['post_details']['caption'] != null) {
                         $caption = str_replace("'", "",$singlePostDetails['post_details']['caption']);
                         $caption = str_replace('"', "", $caption);
                         $caption = trim(preg_replace('/\s+/', ' ', $caption));
                     } else {
                         $caption = '';
                     }
                     $newline = '\n';
                     $pretext = 'Posted ';
                     if($singlePostDetails['post_details']['poster_type'] == \App\Http\Controllers\Enum\PostType::partner){
                     $PartnerInfo = (new functionController)->partnerInfoById($singlePostDetails['post_details']['poster_id']);
                     $partner_name = "by " . str_replace("'", "\'", $PartnerInfo['partner_name'])." ";
                     } else {
                     $partner_name = '';
                     }
                     $posttext = 'on royaltybd.com\n';
                     $post_body = '\n'.$caption.'\n';
                     if($heading != ''){
                     $post_head = '\n'.$heading;
                     } else {
                     $post_head = '';
                     }
                     $enc_post_id = (new functionController)->postShareEncryption('encrypt', $singlePostDetails['post_details']['id']);
                     $post_url = url('/post-share/' . $enc_post_id);
                     ?>
                  <div class="social-buttons">
                     <!-- Twitter share button code -->
                     <?php $twit_post_text = $pretext . $partner_name . $posttext . substr($post_head,0, 30).'...' . substr($post_body,0, 130).'...' . $newline . $post_url; ?>
                     <span onclick="update_twitter_count('{{$singlePostDetails['post_details']['id']}}','{{$twit_post_text}}')" class="shareCursor">
                     <i class="bx bxl-twitter"></i>
                     </span>
                     <!-- Facebook share button code -->
                     <?php $fb_post_url = 'https://www.facebook.com/sharer.php?href=https%3A%2F%2F'.url('/').'%2Fpost-share%2F' . $enc_post_id; ?>
                     <span onclick="update_facebook_count('{{$singlePostDetails['post_details']['id']}}','{{$fb_post_url}}')" class="shareCursor">
                     <i class="bx bxl-facebook-circle"></i>
                     </span>
                  </div>
                  <div>
                     <p style="font-weight: bold;">Post time: {{$created->diffForHumans()}} </p>
                  </div>
                  <div class="news-img">
                     <img src="{{asset($singlePostDetails['post_details']['image_url'])}}" class="lazyload" alt="Royalty single posted image" style="width: 100%;" />
                  </div>
                  <div>
                     <p class="news_header">
                        <i class="announce-icon"></i>
                        &nbsp;<b>{{$singlePostDetails['post_details']['header']}}</b>
                     </p>
                     <p class="news_caption mtb-10">
                        <i class='bx bx-news' ></i> &nbsp;{{$singlePostDetails['post_details']['caption']}}
                     </p>
                  </div>
                  <div>
                     @if(Session::has('customer_id') && $singlePostDetails['post_details']['previous_like'] == 1)
                        <div class="like-button">
                           <div class="like-content">
                              <button class="btn-like unlike-post" data-source="{{$singlePostDetails['post_details']['like'][0]['id']}}"
                                      id="postLike-{{$singlePostDetails['post_details']['id']}}"
                                      value="{{$singlePostDetails['post_details']['id']}}">
                                 <i class="love-f-icon"></i>
                              </button>
                           </div>
                        </div>
                        <p class="likes-on-post" id="likes_of_post_{{$singlePostDetails['post_details']['id']}}">
                           {{$singlePostDetails['post_details']['total_likes']}} people liked this
                        </p>
                     @elseif(Session::has('customer_id') && $singlePostDetails['post_details']['previous_like'] == 0)
                        <div class="like-button">
                           <div class="like-content">
                              <button class="btn-like like-post pull-left"
                                      id="postLike-{{$singlePostDetails['post_details']['id']}}"
                                      value="{{$singlePostDetails['post_details']['id']}}">
                                 <i class="love-e-icon"></i>
                              </button>
                           </div>
                        </div>
                        <p class="likes-on-post" id="likes_of_post_{{$singlePostDetails['post_details']['id']}}">
                           {{$singlePostDetails['post_details']['total_likes']}} people liked this
                        </p>
                     @elseif(!Session::has('customer_id') && !Session::has('partner_id'))
                        <div class="like-button">
                           <div class="like-content">

                           </div>
                        </div>
                        <p class="likes-on-post" id="likes_of_post_{{$singlePostDetails['post_details']['id']}}">
                           {{$singlePostDetails['post_details']['total_likes']}} people liked this
                        </p>
                     @endif
                  </div>
                  <div style="overflow: hidden;">
                     @if($singlePostDetails['post_details']['post_link'] != null)
                     <button onclick="window.open('{{$singlePostDetails['post_details']['post_link']}}')" class="btn btn-primary" style="float: right;">
                     Read more
                     </button>
                     @endif
                  </div>
               </section>
            </div>
         </div>
      </div>
      <div class="col-md-4">
          <div>
            <p class="card-header">New Offers Everyday!</p>
            {{--If partner is logged in--}}
            @if(Session::has('partner_id'))
            {{--show nothing--}}
            {{--If cardholder/guest is logged in--}}
            @elseif(Session::has('customer_id'))
            <div class="card-body">
               <p>
                  Royalty signs up the best partners in town!
                  <br><br>
                  See all our latest partners of six different categories
                  <a href="{{url('offers/all')}}">
                  here
                  </a>.
               </p>
            </div>
            @else
            {{--If no one is logged in--}}
            <div class="card-body">
               <p>
                  We offer you the best offers in town. Our subscribed members can enjoy up
                  to 75% discount in our partner outlets.
                  <br><br>
                  Refer your friends & family to earn Royalty Credit which you can redeem for greater rewards!
               </p>
               <a href="{{url('login')}}">
               <button class="btn btn-primary" style="float: right;">SIGN UP</button>
               </a>
            </div>
            @endif        
         </div>
      </div>
   </div>
   @else
   @endif
</div>
</section>
@include('footer')
@include('header')
<?php use \App\Http\Controllers\functionController; ?>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <!-- <h2>Find your profile details, usages, rewards all together here</h2> -->
         <p>NEWS FEED</p>
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
        <div class="news-feed">
               @if(isset($newsFeed) && $newsFeed != null)
                  @foreach($newsFeed as $news)
                     <?php
                        $created = (new \App\Http\Controllers\functionController2())->createdAt($news['posted_on']);
                     ?>
                     <section class="whitebox">
                        <?php
                        if ($news['header'] != null) {
                           $heading = str_replace("'", "", $news['header']);
                           $heading = str_replace('"', "", $heading);
                           $heading = trim(preg_replace('/\s+/', ' ', $heading));
                        } else {
                           $heading = '';
                        }
                        if ($news['caption'] != null) {
                           $caption = str_replace("'", "",$news['caption']);
                           $caption = str_replace('"', "", $caption);
                           $caption = trim(preg_replace('/\s+/', ' ', $caption));
                        } else {
                           $caption = '';
                        }
                        $newline = '\n';
                        $pretext = 'Posted ';
                        if($news['poster_type'] == \App\Http\Controllers\Enum\PostType::partner){
                           $PartnerInfo = (new functionController)->partnerInfoById($news['poster_id']);
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
                        $enc_post_id = (new functionController)->postShareEncryption('encrypt', $news['id']);
                        $post_url = url('/post-share/' . $enc_post_id);
                        ?>
                         <div class="social-buttons">
                         <?php if($news['pinned_post'] == 1) echo "<span style='color: #007bff'><i class=\"bx bxs-bookmark-star\" aria-hidden=\"true\"></i></span>";?>
                         <!-- Twitter share button code -->
                           <?php $twit_post_text = $pretext . $partner_name . $posttext . substr($post_head,0, 30).'...' . substr($post_body,0, 130).'...' . $newline . $post_url; ?>
                           <span onclick="update_twitter_count('{{$news['id']}}','{{$twit_post_text}}')" class="shareCursor">
                                 <i class="bx bxl-twitter"></i>
                                 </span>
                           <!-- Facebook share button code -->
                           <?php $fb_post_url = 'https://www.facebook.com/sharer.php?href=https%3A%2F%2F'.url('/').'%2Fpost-share%2F' . $enc_post_id; ?>
                           <span onclick="update_facebook_count('{{$news['id']}}','{{$fb_post_url}}')" class="shareCursor">
                                 <i class="bx bxl-facebook-circle"></i>
                                 </span>
                        </div>
                        <div>
                           @if($news['poster_type'] == \App\Http\Controllers\Enum\PostType::partner)
                              <?php $pname = str_replace("'", "", $news['poster_name']); ?>
                              <a href="{{url('partner-profile/'. $pname.'/'.$news['poster_main_branch'])}}"
                                 target="_blank">
                                 <img src="{{ $news['poster_image'] }}"
                                      class="img-circle img-40 img-left lazyload" alt="Royalty news">
                                 <p class="text-top-right"> {{$news['poster_name']}} </p>
                                 @if(isset($news['poster_category']))
                                    <p class="text-top-right">
                                       {{$news['poster_category']}}<span style="font-size: 10px"> {{$created}}</span>
                                    </p>
                                 @endif
                              </a>
                           @elseif($news['poster_type'] == \App\Http\Controllers\Enum\PostType::b2b2c)
                              <img src="{{ $news['poster_image'] }}" alt="Royalty Client"
                                   class="img-circle img-40 img-left lazyload">
                              <p class="text-top-right"> {{$news['poster_name']}} </p>
                              <p class="text-btm-right"> {{$created}} </p>
                           @else
                              <img src="{{ $news['poster_image'] }}" alt="Royalty partner"
                                   class="img-circle img-40 img-left lazyload">
                              <p class="text-top-right"> {{$news['poster_name']}} </p>
                              <p class="text-btm-right"> {{$created}} </p>
                           @endif
                        </div>
                        <div class="news-img" style="text-align: center">
                            @if($news['media_type'] == \App\Http\Controllers\Enum\MediaType::IMAGE)
                                <img class="news_image" src="{{asset($news['image_url'])}}" alt="Royalty news">
                            @else
                                <iframe width="555" height="315" src="{{$news['image_url']}}"
                                        allow="accelerometer; encrypted-media; gyroscope;" allowfullscreen></iframe>
                            @endif
                        </div>
                        <p class="news_header"> <b>{{$news['header']}}</b> </p>
                         @if($news['caption'])
                            <p class="news_caption mtb-10"> <i class='bx bx-news' ></i> {{$news['caption']}} </p>
                         @endif
                        <!-- share starts -->
                        <div style="overflow: hidden;">
                           @if($news['post_link'] != null)
                              <button onclick="window.open('{{$news['post_link']}}')" class="btn btn-primary"
                                      style="float: right;">Read more
                              </button>
                        @endif
                        <!-- like starts -->
                           <div>
                              <?php
                                $onclick = $news['total_likes'] > 0 ? 'onclick="getNewsFeedLikerList('.$news['id'].')"'
                                   : '';
                                ?>
                              @if($news['previous_like']== 1)
                                 <div class="like-button">
                                    <div class="like-content">
                                       <button class="btn-like unlike-post pull-left"
                                               data-source="{{$news['previous_like_id']}}"
                                               id="postLike-{{$news['id']}}" value="{{$news['id']}}">
                                          <i class="love-f-icon"></i>
                                       </button>
                                    </div>
                                 </div>
                                 <p class="likes-on-post" id="likes_of_post_{{$news['id']}}" {!! $onclick !!}>
                                    {{$news['total_likes']}} {{ $news['total_likes'] > 1 ? ' likes' : ' like'}}
                                 </p>
                              @else
                                 <div class="like-button">
                                    <div class="like-content">
                                       <button class="btn-like like-post pull-left" id="postLike-{{$news['id']}}"
                                               value="{{$news['id']}}">
                                          <i class="love-e-icon"></i>
                                       </button>
                                    </div>
                                 </div>
                                 <p class="likes-on-post" id="likes_of_post_{{$news['id']}}" {!! $onclick !!}>
                                    {{$news['total_likes']}} {{ $news['total_likes'] > 1 ? ' likes' : ' like'}}
                                 </p>
                              @endif
                           </div>
                        </div>
                     </section>
                  @endforeach
                  {{$newsFeed->links()}}
               @else
                  <div class="no-info">
                     <h4>
                        There is no exciting news at the moment!
                     </h4>
                  </div>
               @endif
            </div>
      </div>
   </div>
</div>
</section>
{{--modal to show pin set form --}}
<div id="setPinModal" class="modal" role="dialog" style="top: 10%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Set PIN</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <p>Royalty always tries to make your lifestyle easy!</p>
                            <form method="post" action="{{url('updateUserPin')}}" onsubmit="return checkPin()">
                                {{csrf_field()}}
                                <label for="pin">Please set a 4-Digit PIN. This PIN will be used to log in and avail offers</label>
                                <div class="row">
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <span class="set_pin_error" style="color: red"></span>
                                        <input type="password" id="new_pin" class="form-control" name="new_pin" maxlength="4">
                                        <span toggle="#new_pin" style="margin-top:-24px !important"
                                              class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                                    </div>
                                    <div class="col-md-3 col-sm-3 col-xs-12 edit-pin-btn">
                                        <button type="submit" class="btn btn-primary" style="margin: unset;">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{--modal to show your card approval is pending--}}
<div id="pinSetSuccess" class="modal" role="dialog" style="top: 10%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">  <h4 class="modal-title">Success!</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="cross-icon"></i>
                </button>
              
            </div>
            <div class="modal-body" id="profile_modal" class="profile_modal" style="text-align: unset">
                <div class="no-info">
                    <p>PIN successfully updated.</p>
                </div>
            </div>
        </div>
    </div>
</div>
{{--modal to show free trial success message--}}
<div id="freeTrialSuccess" class="modal" role="dialog" style="top: 10%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="cross-icon"></i>
                </button>
                <h4 class="modal-title">Success!</h4>
            </div>
            <div class="modal-body" id="profile_modal" class="profile_modal" style="text-align: unset">
                <p class="freeTrialSuccess"></p>
            </div>
        </div>
    </div>
</div>
{{--modal to show registration success message--}}
{{--<div id="regSuccessModal" class="modal" role="dialog" style="top: 10%">--}}
{{--    <div class="modal-dialog">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}{{--                <h4 class="modal-title">Success!</h4>--}}
{{--                <button type="button" class="close" data-dismiss="modal">--}}
{{--                    <i class="cross-icon"></i>--}}
{{--                </button>--}}

{{--            </div>--}}
{{--            <div class="modal-body" id="profile_modal" class="profile_modal" style="text-align: unset">--}}
{{--                <p>Welcome to Royalty – Your world of boundless possibilities. Start saving now!</p>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

@include('useracc.commonDivs')
@include('footer')
<script async>
    @if($customer_data->pin == null)
      $('#setPinModal').modal({backdrop: 'static', keyboard: false});
    @endif
    @if (session('pin_updated'))
      $("#pinSetSuccess").modal('toggle');
    @endif
    @if (session('free_trial_success'))
      $(".freeTrialSuccess").text("{{session('free_trial_success')}}");
      $("#freeTrialSuccess").modal('toggle');
    @endif
{{--    @if(session('reg_succeeded'))--}}
{{--    $("#regSuccessModal").modal('toggle');--}}
{{--    @endif--}}
</script>
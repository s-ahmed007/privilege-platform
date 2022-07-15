<?php use \App\Http\Controllers\functionController; ?>
@include('header')
<link href="{{ asset('css/user-profile-view.css') }}" rel="stylesheet">
<div class="container user-profile-box">
   <div class="row">
      <div class="col-md-3">
         <div class="user-profile-pic">
            <img src="{{asset($userInfo['customer_profile_image'] != '' ?
               $userInfo['customer_profile_image'] : 'images/user.png')}}" alt="Royalty user"
               class="user-profile-picture img-circle lazyload">
         </div>
      </div>
      <div class="col-md-9">
         <div class="user-profile-info">
            <span class="user-profile-name">{{ $userInfo['customer_first_name'].' '.$userInfo['customer_last_name'] }}</span>
            <br>
            @if($userInfo['customer_type'] == 2)
            <span class="user-profile-platinum">Royalty Member</span>
            @else
            <span class="user-profile-guest"> Guest</span>
            @endif
            <?php $time = strtotime($userInfo['member_since']);
               $month = date("F", $time);/*get month from date*/
               $year = date("Y", $time);/*get year from date*/
               ?>
            <p class="member_since">member since {{$month.', '.$year}}</p>
            {{--<span class="follow-button">--}}
            {{--when user not logged in or partner logged in--}}
            {{--@if(!Session::has('customer_id') || Session::has('partner_id'))--}}
            {{--<button class="btn" data-toggle="modal" data-target="#invalidFollow"--}}
            {{--style="float: right;">Follow--}}
            {{--</button>--}}
            {{--When user logged in and in his own profile--}}
            {{--@elseif(Session::has('customer_id') && Session::get('customer_id') == $userInfo['customer_id'])--}}
            {{--nothing to show--}}
            {{--When user logged in and not following--}}
            {{--@elseif(Session::has('customer_id') && !isset($follower_list['following']))--}}
            {{--<button class="btn follow-user-{{$userInfo['customer_id']}}"--}}
            {{--id="follow-user"--}}
            {{--value="{{$userInfo['customer_id']}}"--}}
            {{--style="float: right;">Follow--}}
            {{--</button>--}}
            {{--<button style="display: none; float: right;"--}}
            {{--class="btn follow-requested-{{$userInfo['customer_id']}}"--}}
            {{--id="cancel-follow-request" value="{{$userInfo['customer_id']}}"--}}
            {{--title="Follow request sent">--}}
            {{--Requested--}}
            {{--</button>--}}
            {{--When user logged in and following--}}
            {{--@elseif(Session::has('customer_id') && isset($follower_list['following']) &&$follower_list['following'] == 1)--}}
            {{--<button class="btn unfollow-user-{{$userInfo['customer_id']}}"--}}
            {{--id="unfollow-user" value="{{$userInfo['customer_id']}}"--}}
            {{--title="Unfollow" style="float: right;">--}}
            {{--Unfollow--}}
            {{--</button>--}}
            {{--<button class="btn follow-user-{{$userInfo['customer_id']}}"--}}
            {{--id="follow-user"--}}
            {{--value="{{$userInfo['customer_id']}}"--}}
            {{--style="float: right;display: none;">Follow--}}
            {{--</button>--}}
            {{--<button style="display: none; float: right;"--}}
            {{--class="btn follow-requested-{{$userInfo['customer_id']}}"--}}
            {{--id="cancel-follow-request" value="{{$userInfo['customer_id']}}"--}}
            {{--title="Follow request sent">--}}
            {{--Requested--}}
            {{--</button>--}}
            {{--When user logged in and follow request sent--}}
            {{--@elseif(Session::has('customer_id') && isset($follower_list['following']) && $follower_list['following'] == 0)--}}
            {{--<button style="float: right;"--}}
            {{--class="btn follow-requested-{{$userInfo['customer_id']}}"--}}
            {{--id="cancel-follow-request" value="{{$userInfo['customer_id']}}"--}}
            {{--title="Follow request sent">--}}
            {{--Requested--}}
            {{--</button>--}}
            {{--<button class="btn follow-user-{{$userInfo['customer_id']}}"--}}
            {{--id="follow-user"--}}
            {{--value="{{$userInfo['customer_id']}}" style="float: right;display: none;">--}}
            {{--Follow--}}
            {{--</button>--}}
            {{--@endif--}}
            {{--</span>--}}
            {{--
            <p>{{ $userInfo['customer_username'] }}</p>
            --}}
            <div class="row user-stat-box">
               <div class="col-md-3 col-sm-3 col-xs-6 stat">
                  <p class="userstat-l">{{count($reviews)}}</p>
                  <p class="userstat-r"> Reviews</p>
               </div>
               <div class="col-md-3 col-sm-3 col-xs-6 stat">
                  <p class="userstat-l">{{$totalLike}}</p>
                  <p class="userstat-r"> Likes</p>
               </div>
               <div class="col-md-3 col-sm-3 col-xs-6 stat">
                  <p class="userstat-l">{{(count($following_list['partner'])+count($following_list['customer']))}}</p>
                  <p class="userstat-r"> Following</p>
               </div>
               <div class="col-md-3 col-sm-3 col-xs-6 stat">
                  <p class="userstat-l">{{count($follower_list['follower'])}}</p>
                  <p class="userstat-r"> Followers</p>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="panel with-nav-tabs panel-default">
            <div class="panel-heading single-project-nav">
               <ul class="nav nav-tabs">
                  <li class="active">
                     <a href="#recent-activity" data-toggle="tab">Recent Activity</a>
                  </li>
                  <li>
                     <a href="#reviews" data-toggle="tab">Reviews</a>
                  </li>
                  <li>
                     <a href="#following" data-toggle="tab">Following</a>
                  </li>
                  <li>
                     <a href="#followers" data-toggle="tab" style="border-right: unset">Followers</a>
                  </li>
               </ul>
            </div>
            <div class="panel-body">
               <div class="tab-content">
                  {{--RECENT ACTIVITY--}}
                  <div class="tab-pane fade in active" id="recent-activity">
                     @if(isset($recentActivity) && count($recentActivity) == 0)
                     @if(Session('customer_id')==$userInfo['customer_id'])
                     <div class="banner-btm">
                        <div class="no-info">
                           <h4>You don't have any recent activity.</h4>
                        </div>
                     </div>
                     @else
                     <div class="banner-btm">
                        <div class="no-info">
                           <h4>{{$userInfo['customer_first_name'].' '.$userInfo['customer_last_name']}}
                              doesn't
                              have any recent activity.
                           </h4>
                        </div>
                     </div>
                     @endif
                     @else
                     <ol class="activity-feed">
                        {{--review--}}
                        @foreach($recentActivity as $recent)
                        @if($recent['type'] == 1)
                        <?php
                           $posted_on = date("Y-M-d H:i:s", strtotime($recent['posted_on']));
                           $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                           ?>
                        <li class="feed-item" data-content="&#xf040;"
                           data-time="{{$created->diffForHumans()}}"
                           data-color="darkblue">
                           <section>
                              <label>
                              <?php
                                 $enc_review_id = (new functionController)->socialShareEncryption('encrypt', $recent['id']);
                                 ?>
                              {{$userInfo['customer_first_name'].' reviewed '}}
                              <?php $pname = str_replace("'", "", $recent['partner_name']); ?>
                              <a href="{{url('partner-profile/'.$pname.'/'.$recent['main_branch_id'])}}">
                              {{$recent['partner_name']}}
                              </a>. See the <a
                                 href="{{ url('/review/' . $enc_review_id) }}">review</a>.
                              </label>
                           </section>
                        </li>
                        {{--visited--}}
                        @elseif($recent['type'] == 2)
                        <?php
                           $posted_on = date("Y-M-d H:i:s", strtotime($recent['posted_on']));
                           $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                           ?>
                        <li class="feed-item" data-content="&#xf090;"
                           data-time="{{$created->diffForHumans()}}"
                           data-color="darkblue">
                           <section>
                              <label>
                              <img src="{{$recent['partner_profile_image']}}"
                                 class="img-40 img-circle primary-border lazyload"  Royalty partner">
                              <?php $pname = str_replace("'", "", $recent['partner_name']); ?>
                              {{$userInfo['customer_first_name'].' visited '}}
                              <a href="{{url('partner-profile/'.$pname.'/'.$recent['main_branch_id'])}}">
                              {{$recent['partner_name'] }}</a>.
                              </label>
                           </section>
                        </li>
                        {{--like--}}
                        @else
                        <?php
                           $posted_on = date("Y-M-d H:i:s", strtotime($recent['posted_on']));
                           $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                           ?>
                        <li class="feed-item" data-content="&#xf004;"
                           data-time="{{$created->diffForHumans()}}" data-color="red">
                           <section>
                              <label>
                              <?php
                                 $enc_review_id = (new functionController)->socialShareEncryption('encrypt', $recent['id']);
                                 $pname = str_replace("'", "", $recent['partner_name']);
                                 ?>
                              {{$userInfo['customer_first_name'].' liked a'}}
                              <a href="{{url('/review/' . $enc_review_id)}}">
                              review</a> on
                              <a href="{{url('/partner-profile/' .$pname.'/'.$recent['main_branch_id'])}}">
                              {{$recent['partner_name']}}
                              </a>.
                              </label>
                           </section>
                        </li>
                        @endif
                        @endforeach
                        {{--pagination starts here--}}
                        <div class="col-md-12 col-sm-12 col-xs-12 pagination">
                           {!! $recentActivity->appends(Request::except('page'))->render() !!}
                        </div>
                        {{--pagination ends here--}}
                     </ol>
                     @endif
                  </div>
                  {{--REVIEWS--}}
                  <div class="tab-pane fade" id="reviews">
                     @if(isset($reviews) && count($reviews) > 0){{-- variable comes from "profilefromoffer" function in homeController --}}
                     <?php $row = count($reviews); ?>
                     @for($i=0; $i < $row; $i++)
                     <div class="whitebox-inner-box-inner" id="review-id-{{$reviews[$i]['id']}}">
                        <!-- User review -->
                        <div class="row">
                           <div class="col-md-2 col-sm-2 col-xs-3">
                              <!-- User profile picture -->
                              <div class="comment-avatar center">
                                 <img src="{{ asset($reviews[$i]['customer_profile_image'])}}"
                                    class="img-circle img-40 primary-border lazyload" Royalty user">
                                 <p class="comment-name reviewer-name mt">
                                    {{$reviews[$i]['customer_first_name'].' '.$reviews[$i]['customer_last_name']}}
                                 </p>
                                 {{-- <a> --}}
                                 <i class="bx bx-edit user-total-reviews">
                                    <span>{{ functionController::reviewNumber($reviews[$i]['customer_id']) }}</span>
                                 </i>
                                 {{-- </a> --}}
                                 <i class="bx bx-like likes_of_user_{{$reviews[$i]['customer_id']}}">
                                 <span>{{ functionController::likeNumber($reviews[$i]['customer_id']) }}</span>
                                 </i>
                              </div>
                           </div>
                           <div class="col-md-10 col-sm-10 col-xs-9">
                              <!-- User Review box -->
                              <div class="whitebox">
                                 <div class="comment-head">
                                    {{--social media buttons--}}
                                    <?php
                                       if ($reviews[$i]['heading'] != 'n/a') {
                                           $heading = str_replace("'", "", $reviews[$i]['heading']);
                                           $heading = str_replace('"', "", $heading);
                                           $heading = trim(preg_replace('/\s+/', ' ', $heading));
                                       } else {
                                           $heading = '';
                                       }
                                       if ($reviews[$i]['body'] != 'n/a') {
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
                                       <?php $review_url = 'https://www.facebook.com/sharer.php?u=https%3A%2F%2Fwww.royaltybd.com%2Freview-share%2F' . $enc_review_id; ?>
                                       <a href="<?php echo $review_url;?>" target="_blank">
                                       <i class="bx bxl-facebook-circle"></i></a>
                                       </span>
                                       @if(Session::get('customer_id') == $reviews[$i]['customer_id'])
                                       <p class="middle">
                                          <a class="btn btn-danger btn-xs" href="{{url('/reviewDelete/'.$reviews[$i]['id'])}}"
                                             onclick="return confirm('Are you sure you want to delete this review?')">
                                          <i class="delete-icon"></i>
                                          </a>
                                       </p>
                                       @endif
                                    </div>
                                    {{--social media buttons END--}}
                                 </div>
                                 <div class="comment-content">
                                    <div>
                                       <?php $pname = str_replace("'", "", $reviews[$i]['partner_name']); ?>
                                       <a href="{{url('/partner-profile/'.$pname.'/'.$reviews[$i]['main_branch_id'])}}"
                                         >
                                          <span>
                                          <img src="{{ $reviews[$i]['partner_profile_image'] }}"
                                             class="img-circle primary-border img-30 lazyload" Royalty partner">
                                          </span>
                                          <p class="partner-name">{{$reviews[$i]['partner_name']}}</p>
                                       </a>
                                    </div>
                                    <div>
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
                                    <h4 class="review-head bold">{{$heading}}</h4>
                                    <p class="review-post-date">{{date('d-m-y', strtotime($reviews[$i]['posted_on'])) }}
                                    </p>
                                    @if(isset($body))
                                    <p class="review-description">{{$body}}
                                    </p>
                                    @endif
                                    <div class="like-button">
                                       {{--Logged in and liked--}}
                                       @if(Session::get('customer_id') && $reviews[$i]['liked'] == 1)
                                       <div class="like-content">
                                          <button class="btn-like unlike-review" id="principalSelect-{{$reviews[$i]['id']}}"
                                             value="{{$reviews[$i]['id']}}" data-source="{{$reviews[$i]['source_id']}}">
                                          <i class="love-e-icon"></i>
                                          </button>
                                       </div>
                                       <p class="likes-on-review"
                                          id="likes_of_review_{{$reviews[$i]['id']}}">{{$reviews[$i]['total_likes_of_a_review']}}
                                          people liked this
                                       </p>
                                       {{--others review but not liked--}}
                                       @elseif(Session::has('customer_id') && $reviews[$i]['liked'] == 0 && Session::get('customer_id') != $reviews[$i]['customer_id'])
                                       <div class="like-content">
                                          <button class="btn-like like-review" id="principalSelect-{{$reviews[$i]['id']}}"
                                             value="{{$reviews[$i]['id']}}" data-source="{{$reviews[$i]['source_id']}}">
                                          <i class="love-e-icon"></i>
                                          </button>
                                       </div>
                                       <p class="likes-on-review"
                                          id="likes_of_review_{{$reviews[$i]['id']}}">{{$reviews[$i]['total_likes_of_a_review']}}
                                          people liked this
                                       </p>
                                       {{--Own review cannot be liked--}}
                                       @elseif(Session::has('customer_id') && $reviews[$i]['liked'] == 0 && Session::get('customer_id') == $reviews[$i]['customer_id'])
                                       {{--
                                       <div class="like-content">--}}
                                          {{--<button class="btn-like">--}}
                                          {{--<i class="love-e-icon"></i>--}}
                                          {{--</button>--}}
                                          {{--
                                       </div>
                                       --}}
                                       <p class="likes-on-review"
                                          id="likes_of_review_{{$reviews[$i]['id']}}">{{$reviews[$i]['total_likes_of_a_review']}}
                                          people liked this
                                       </p>
                                       {{--logged out--}}
                                       @elseif(!Session::has('customer_id'))
                                       <div class="like-content">
                                          <button class="btn-like">
                                          <i class="love-e-icon"></i>
                                          </button>
                                       </div>
                                       <p class="likes-on-review"
                                          id="likes_of_review_{{$reviews[$i]['id']}}">{{$reviews[$i]['total_likes_of_a_review']}}
                                          people liked this
                                       </p>
                                       @endif{{--Like option ends--}}
                                    </div>
                                    <p class="review-liability">
                                       This review is the subjective opinion of a Royalty member and
                                       not of Royalty.
                                          </p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- Partner reply -->
                        @if(isset($reviews[$i]['comments'][0]))
                        <div class="row m-0 pull-right">
                           <div class="">
                              <!-- Partner reply box -->
                              <div class="whitebox comment-box-partner">
                                 <div class="comment-content comment-content-partner">
                                    <p class="comment-name partner-response">
                                       <a href="{{url('/partner-profile/'.$pname.'/'.$reviews[$i]['main_branch_id'])}}"
                                         ><b>
                                       {{$reviews[$i]['partner_name']}}</b>
                                       </a>
                                       <span>responded to this review</span>
                                    </p>
                                    <p class="partner-reply-date">Posted
                                       on: {{date('d-m-y', strtotime($reviews[$i]['comments'][0]['posted_on'])) }}
                                    </p>
                                    <p class="partner-reply">{{$reviews[$i]['comments'][0]['comment']}}</p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif
                     </div>
                     @endfor
                     @else
                     @if(Session::has('customer_id'))
                     <div class="no-info ash">
                        @if($userInfo['customer_username'] == Session::get('customer_username'))
                        <h4>You haven't posted any review yet.</h4>
                        @else
                        <h4>{{$userInfo['customer_first_name'].' '.$userInfo['customer_last_name']}}
                           hasn't posted any review yet.
                        </h4>
                        @endif
                     </div>
                     @else
                     <div class="no-info ash">
                        <h4>{{$userInfo['customer_first_name'].' '.$userInfo['customer_last_name']}}
                           hasn't posted any review yet.
                        </h4>
                     </div>
                     @endif
                     @endif
                  </div>
                  {{--FOLLOWING--}}
                  <div class="tab-pane fade" id="following">
                     <div class="ash">
                        @if((isset($following_list['partner']) && count($following_list['partner']) > 0) ||
                        (isset($following_list['customer']) && count($following_list['customer']) > 0) )
                        <div class="following-head">
                           <h4>Privilege Partners {{'('.count($following_list['partner']).')'}}</h4>
                           <div class="bottom-bar-partner"></div>
                        </div>
                        <div class="row m-0">
                           @foreach($following_list['partner'] as $following)
                           <div class="col-md-3 col-sm-6 col-xs-12">
                              <div class="user_following">
                                 <?php $pname = str_replace("'", "", $following['partner_name']); ?>
                                 <a href="{{url('partner-profile/'.$pname.'/'.$following['main_branch_id'])}}"
                                    target="_blank">
                                    <img src="{{ $following['partner_profile_image'] }}"
                                       class="img-circle img-left img-40 lazyload"
                                        Royalty partner">
                                    <p class="dots text-top-right">{{$following['partner_name']}}</p>
                                    <p class="following-p-category"> {{--Partner Category--}}
                                       @if($following['partner_category'] == '3')
                                       Food & Drinks
                                       @elseif($following['partner_category'] == '1')
                                       Beauty & Spa
                                       @elseif($following['partner_category'] == '2')
                                       Entertainment
                                       @elseif($following['partner_category'] == '4')
                                       Getaways
                                       @elseif($following['partner_category'] == '5')
                                       Health & Fitnes
                                       @else
                                       Lifestyle
                                       @endif
                                    </p>
                                 </a>
                              </div>
                           </div>
                           @endforeach
                        </div>
                        <div class="following-head">
                           <h4>Royals {{'('.count($following_list['customer']).')'}}</h4>
                           <div class="bottom-bar-royal"></div>
                        </div>
                        <div class="row m-0">
                           @foreach($following_list['customer'] as $following)
                           <div class="col-md-3 col-sm-6 col-xs-12">
                              <div class="user_following">
                                 <a href="{{url('user-profile/'.$following['customer_username'])}}"
                                    target="_blank">
                                    <img src="{{ asset($following['customer_profile_image']) }}"
                                       class="img-circle img-left img-40 lazyload" alt="Royalty image">
                                    <p class="text-top-right dots">{{$following['customer_first_name'].' '.$following['customer_last_name']}}</p>
                                    <p class="following-u-category">
                                       @if($following['customer_type'] == 2)Royalty Member
                                       @else Member
                                       @endif
                                    </p>
                                 </a>
                              </div>
                           </div>
                           @endforeach
                        </div>
                        @else
                        @if(Session::has('customer_id') && $userInfo['customer_id'] == Session::get('customer_id'))
                        <div class="no-info">
                           <h4>Get a list of your followed partner stores and Royalty members</h4>
                        </div>
                        @else
                        <div class="no-info">
                           <h4>{{$userInfo['customer_first_name'].' '.$userInfo['customer_last_name']}}
                              is not following anyone.
                           </h4>
                        </div>
                        @endif
                        @endif
                     </div>
                  </div>
                  {{--FOLLOWERS--}}
                  <div class="tab-pane fade" id="followers">
                     <div class="ash">
                        @if(isset($follower_list) && count($follower_list['follower']) > 0)
                        <div class="row m-0">
                           @foreach($follower_list['follower'] as $follower)
                           <div class="col-md-3 col-sm-6 col-xs-12">
                              <div class="user_following">
                                 <a href="{{url('user-profile/'.$follower['customer_username'])}}"
                                    target="_blank">
                                    <img src="{{asset($follower['customer_profile_image'] != '' ?
                                       $follower['customer_profile_image'] : 'images/user.png' )}}"
                                       class="img-circle img-left img-40 lazyload" Royalty user">
                                    <p class="text-top-right dots">{{$follower['customer_first_name'].' '.$follower['customer_last_name']}}</p>
                                    <p class="follower-u-category">
                                       @if($follower['customer_type']== 2)Royalty Member
                                       @else Member
                                       @endif
                                 </a>
                              </div>
                           </div>
                           @endforeach
                        </div>
                        @else
                        @if(Session::has('customer_id') && $userInfo['customer_id'] == Session::get('customer_id'))
                        <div class="no-info">
                           <h4>No one is currently following you.</h4>
                        </div>
                        @else
                        <div class="no-info">
                           <h4>{{$userInfo['customer_first_name'].' '.$userInfo['customer_last_name']}}
                              doesn't
                              have any follower.
                           </h4>
                        </div>
                        @endif
                        @endif
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@include('footer')
<script src="{{asset('js/user-profile-view.js')}}"></script>
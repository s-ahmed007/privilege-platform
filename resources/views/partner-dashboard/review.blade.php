<?php use App\Http\Controllers\functionController; ?>
@include('partner-dashboard.header')
<style>
    .review-container button {
        margin-left: unset;
    }

    .comment-avatar center {
        text-align: center;
    }

    ul li{list-style-type: none}
    .fontnum_of_likes,
    .fontnum_of_reviews {
        color: #007bff;
        font-style: normal;
    }

    .partner-reply-date,
    .review-post-date {
        font-size: 0.7em;
        font-style: italic;
    }

    .partner-reply-partner-image img{
        border-radius: 50%;
        width: 50%;
        display: block;
        margin: 0 auto;
    }

    .reviewer-name {
        -ms-word-break: break-all;
        -ms-word-wrap: break-all;
        -webkit-word-break: break-word;
        -webkit-word-wrap: break-word;
        word-break: break-word;
        word-wrap: break-word;
        -webkit-hyphens: auto;
        -moz-hyphens: auto;
        hyphens: auto;
    }

    .reviewer-star-rating-div {
        color: #ffc107;
        display: flex;
        margin-right: 5px;
    }

    .reviewer-star-rating-div i {
        margin: 0 1px;
    }

    .review-star {
        display: inline-block;
    }

    .partner-reply,
    .review-description {
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .social-buttons {
        float: right;
        font-size: 1.5em;
    }
    .likes-on-review {
        padding-top: 3px;
        cursor: pointer;
        font-weight: 700;
    }
    .like-button {
        margin-bottom: 10px;
        display: inherit;
    }

    .like-content {
        margin-right: 5px;
    }
    .liker {
        margin: 5px 0;
    }

    .liker_img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .liker_name {
        display: inline-block;
        font-weight: 700;
    }

    .review-liability {
        border-top: 1px solid #f5f5f5;
        font-style: italic;
        color: gray;
        margin-bottom: 10px;
        margin-top: 20px;
    }

    .comment-box-partner {
        border-left: 1px solid #f5f5f5;
        margin-top: 10px;
    }

    .comment-content-partner {
        margin-left: 20px;
        margin-bottom: 10px;
    }

    .partner-reply-btn {
        float: right;
        margin-right: unset;
    }

    .img-40 {
        height: 40px;
        width: 40px;
    }

    .fa-facebook-square,
    .fa-twitter-square{
        color: #007bff;
    }
    .modal-header {
        background-color: #007bff;
        color: #fff;
    }

    .modal-title {
        background-color: #007bff;
        color: #fff;
    }
    .reply_edit_btn{
        float: right;
        cursor: pointer;
        padding: 3px;
        color: #fff;
        background-color: #007bff;
        border-radius: 5px;
        padding: 2px 2px 0px 4px;
    }
        
    .reply_delete_btn{
        float: right;
        padding: 1px 4px 0px 5px;
        border-radius: 5px;
        color: #fff;
        background-color: red;
        cursor: pointer;}

        .reply_delete_btn:hover{
            color: white;
        }
    .like-content .btn-like {
        text-align: center;
        border-radius: 50%;
        background: #dc143c;
        box-shadow: 0 10px 20px -8px #dc143c;
        padding: 1px 4px;
        font-size: 1em;
        cursor: pointer;
        border: none;
        outline: 0;
        color: #fff;
        text-decoration: none;
        transition: 0.3s ease;
    }

    .btn-like {
        float: left;
        margin-right: 5px;
    }

    .likes-on-review {
        padding-top: 3px;
        cursor: pointer;
        font-weight: 700;
    }
    .animate-like {
        animation-name: likeAnimation;
        animation-iteration-count: 1;
        animation-fill-mode: forwards;
        animation-duration: 0.65s;
    }
    .love-e-icon:before {
        content: "\f004";
        font-family: "Font Awesome 5 Pro", Bangla167, sans-serif;
        font-style: normal;
    }
    .love-f-icon:before {
        content: "\f004";
        font-family: "Font Awesome 5 Pro", Bangla167, sans-serif;
        font-weight: 900;
        font-style: normal;
    }
    .reply-moderation{
        background: red;
    color: white;
    font-weight: bold;
    padding: 0 5px;
    }
</style>
<div class="container-fluid">
<div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
        <h3 class="d-inline-block">{{__('partner/common.all_reviews')}}</h3>
        @if($all_tab)
            <a class="btn btn-success" href="{{url('partner/branch/review')}}">{{__('partner/common.all_reviews')}}</a>
        @endif
                <h5 class="d-inline-block float-right">{{__('partner/reviews.reviews_after_successful_transactions')}}</h5>
        </div>
        <div class="col-md-6">
        @if(session('reply_success'))
                <div class="alert alert-success">
                    {{ session('reply_success') }}
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12 white-box">
            @if(count($reviews) > 0)
                @for($i=0; $i < count($reviews); $i++)
                    <!-- User review -->
                    <div class="row">
                        <div class="col-md-2 col-sm-2 col-xs-3">
                            <div class="comment-avatar center">
                                <img src="{{ asset($reviews[$i]['customerInfo']['customer_profile_image'])}}"
                                     class="img-circle img-40 primary-border-1 lazyload" alt="Royalty user-pic">
                                <p class="comment-name reviewer-name mt">{{ $reviews[$i]['customerInfo']['customer_full_name'] }}</p>
                                <p>
                                    <i class="fa fa-comment user-total-reviews">
                                        <span>{{ functionController::reviewNumber($reviews[$i]['customer_id']) }}</span>
                                    </i>
                                    <i class="fa fa-thumbs-up likes_of_user_{{$reviews[$i]['customer_id']}}">
                                        <span>{{ functionController::likeNumber($reviews[$i]['customer_id']) }}</span>
                                    </i>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-10 col-sm-10 col-xs-9">
                            <div class="comment-box">
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
                                    $pretext = 'Review about';
                                    $partner_name = " " . str_replace("'", "\'", $reviews[$i]['partnerInfo']['partner_name']);
                                    $posttext = 'on royaltybd.com';
                                    $review_body = $body;
                                    $review_head = $heading;
                                    $enc_review_id = (new functionController)->socialShareEncryption('encrypt', $reviews[$i]['id']);
                                    $review_url = url('/review/' . $enc_review_id);
                                    ?>
                                    <div class="social-buttons">
                                        <span onclick="window.open('https://twitter.com/intent/tweet?text=' +
                                                encodeURIComponent('<?php echo $pretext . $partner_name . $newline . $newline .
                                            substr($review_head,0, 30).'...' . $newline . substr($review_body,0, 130).'...' .
                                            $newline . $newline . $review_url;?>')); return false;">
                                             <a href="#"><i class="fab fa-twitter-square"></i></a>
                                        </span>
                                        <span>
                                            <?php $review_url = 'https://www.facebook.com/sharer.php?href=https%3A%2F%2F'.url('/').'%2Freview-share%2F' . $enc_review_id; ?>
                                            <a href="<?php echo $review_url;?>" target="_blank"><i class="fab fa-facebook-square"></i></a>
                                        </span>
                                        @if(Session::get('customer_id') == $reviews[$i]['customer_id'])
                                            <p class="review-delete-warning">
                                                <a class="btn btn-danger btn-xs" href="{{url('/reviewDelete/'.$reviews[$i]['id'])}}"
                                                   onclick="return confirm('Are you sure you want to delete this review?')">
                                                    <i class="delete-icon"></i>
                                                </a>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="comment-content">
                                    <div class="review-star">
                                        @if($reviews[$i]['rating'] == 1)
                                            <div class="reviewer-star-rating-div">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                            </div>
                                        @elseif($reviews[$i]['rating'] == 2)
                                            <div class="reviewer-star-rating-div">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                            </div>
                                        @elseif($reviews[$i]['rating'] == 3)
                                            <div class="reviewer-star-rating-div">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                            </div>
                                        @elseif($reviews[$i]['rating'] == 4)
                                            <div class="reviewer-star-rating-div">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star-o"></i>
                                            </div>
                                        @else
                                            <div class="reviewer-star-rating-div">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <?php
                                    $posted_on = date("Y-M-d H:i:s", strtotime($reviews[$i]['posted_on']));
                                    $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                    ?>
                                    <span class="review-post-date">
                                    {{$created->diffForHumans()}}
                                    </span>

                                    <p class="review-head bold">{{$review_head}}</p>
                                    <p class="review-description">{{$review_body}}</p>
                                    <div class="like-button">
                                    {{--onclick event for liker list--}}
                                    <?php if(count($reviews[$i]['likes']) > 0){
                                        $onclick = 'onclick="getReviewLikerList('.$reviews[$i]['id'].')"';
                                    }else{
                                        $onclick = '';
                                    }
                                    ?>
                                    {{--Like option--}}
                                    @if(Session::has('customer_id') && $reviews[$i]['previous_like'] == 1)
                                        <!-- if liked and want to unlike -->
                                        <div class="like-content" title="Like">
                                            <button class="btn-like unlike-review" id="principalSelect-{{$reviews[$i]['id']}}"
                                                    value="{{$reviews[$i]['id']}}" data-source="{{$reviews[$i]['previous_like_id']}}">
                                                <i class="love-f-icon"></i>
                                            </button>
                                        </div>
                                        <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$reviews[$i]['id']}}">
                                            {{ count($reviews[$i]['likes']) }}
                                            {{ count($reviews[$i]['likes']) > 1 ? ' likes' : ' like'}}
                                        </p>
                                            <!-- if wants to like -->
                                     @elseif(Session::has('customer_id') && $reviews[$i]['previous_like'] == 0 && Session::get('customer_id') != $reviews[$i]['customer_id'])
                                        <div class="like-content">
                                            <button class="btn-like like-review" id="principalSelect-{{$reviews[$i]['id']}}"
                                                    value="{{$reviews[$i]['id']}}" data-source="{{$reviews[$i]['previous_like_id']}}">
                                                <i class="love-e-icon"></i>
                                            </button>
                                        </div>
                                        <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$reviews[$i]['id']}}">
                                            {{ count($reviews[$i]['likes']) }}
                                            {{ count($reviews[$i]['likes']) > 1 ? ' likes' : ' like'}}
                                        </p>
                                            <!-- if own review and cant like -->
                                      @elseif(Session::has('customer_id') && Session::get('customer_id') == $reviews[$i]['customer_id'])
                                        <!-- {{--
                              <div class="like-content" title="You can not like your own review">--}}
                                            {{--<button class="btn-like" data-source="{{$reviews[$i]['source_id']}}">--}}
                                            {{--<i class="love-e-icon"></i>--}}
                                            {{--</button>--}}
                                            {{--
                                         </div>
                                         --}} -->
                                            <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$reviews[$i]['id']}}">
                                                {{ count($reviews[$i]['likes']) }}
                                                {{ count($reviews[$i]['likes']) > 1 ? ' likes' : ' like'}}
                                            </p>
                                            <!-- if partner wants to like -->
                                        @elseif(Session::has('branch_id') && $reviews[$i]['previous_like'] == 0)
                                            <div class="like-content">
                                                <button class="btn-like like-review" id="principalSelect-{{$reviews[$i]['id']}}"
                                                        value="{{$reviews[$i]['id']}}" data-source="{{$reviews[$i]['previous_like_id']}}">
                                                    <i class="fa fa-heart-o"></i>
                                                </button>
                                            </div>
                                            <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$reviews[$i]['id']}}">
                                                {{ count($reviews[$i]['likes']) }}
                                                {{ count($reviews[$i]['likes']) > 1 ? ' likes' : ' like'}}
                                            </p>
                                        @elseif(Session::has('branch_id') && $reviews[$i]['previous_like'] == 1)
                                            <div class="like-content" title="Like">
                                                <button class="btn-like unlike-review" id="principalSelect-{{$reviews[$i]['id']}}"
                                                        value="{{$reviews[$i]['id']}}" data-source="{{$reviews[$i]['previous_like_id']}}">
{{--                                                    <i class="love-f-icon"></i>--}}
                                                    <i class="fa fa-heart"></i>
                                                </button>
                                            </div>
                                            <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$reviews[$i]['id']}}">
                                                {{ count($reviews[$i]['likes']) }}
                                                {{ count($reviews[$i]['likes']) > 1 ? ' likes' : ' like'}}
                                            </p>
                                        @else
                                        <!-- when no one is logged in and cannot like  -->
                                            <div class="like-content">
                                                <button class="btn-like" data-toggle="modal" data-target="#nonClickableLike">
                                                    <i class="love-e-icon"></i>
                                                </button>
                                            </div>
                                            <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$reviews[$i]['id']}}">
                                                {{ count($reviews[$i]['likes']) }}
                                                {{ count($reviews[$i]['likes']) > 1 ? ' likes' : ' like'}}
                                            </p>
                                        @endif{{--Like option ends--}}
                                    </div>
                                    <p class="review-liability">{{__('partner/reviews.review_not_of_rbd')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Partner reply in written -->
                    @if(isset($reviews[$i]['comments'][0]))
                        <div class="row">
                            
                                @if($reviews[$i]['comments'][0]['moderation_status'] == 1)
                                <div class="col-md-10 col-md-offset-2 col-sm-11 col-sm-offset-1 col-xs-11 col-xs-offset-1">
                                <div class="whitebox comment-box-partner review_reply_{{$reviews[$i]["comments"][0]['id']}}">
                                    <div class="comment-content comment-content-partner">
                                        <span class="reply_edit_btn" onclick="editReviewReply('{{$reviews[$i]["comments"][0]["id"]}}')">
                                            <i class="fa fa-edit"></i></span><br><br>
                                        <a href="{{url('partner/branch/deleteReviewReply/'.$reviews[$i]["comments"][0]['id'])}}"
                                           class="reply_delete_btn" onclick="return confirm('Are you sure?')">
                                            <i class="fa fa-trash"></i></a>
                                        <p class="comment-name partner-response"><b>{{$reviews[$i]['partnerInfo']['partner_name']}}</b>
                                            <span>responded to this review</span>
                                        </p>
                                        <p class="partner-reply-date">
                                            {{date('d-m-y', strtotime($reviews[$i]['comments'][0]['posted_on'])) }}
                                        </p>
                                        <p class="partner-reply">{{$reviews[$i]['comments'][0]['comment']}}</p>
                                    </div>
                                </div>
                                </div>
                                <!-- Partner reply edit box -->
                                <div class="whitebox partner-color reply_box_{{$reviews[$i]['comments'][0]['id']}}" style="display: none;">
                                    <form action="{{url('partner/branch/editReviewReply/'.$reviews[$i]['comments'][0]['id'])}}"
                                          method="post" style="padding: 15px 0;">
                                        {{csrf_field()}}
                                        <div class="form-group">
                                            <span style="float: right; cursor: pointer;"
                                               onclick="cancelReviewReply('{{$reviews[$i]["comments"][0]["id"]}}')">Cancel</span>
                                        <textarea name="reply" id="review{{$i}}" cols="78" rows="4" required
                                                  placeholder="Your reply goes here..."
                                                  class="form-control" onkeyup="replyChars({{$i}});"
                                                  maxlength="500">{{$reviews[$i]['comments'][0]['comment']}}</textarea>
                                        </div>
                                        <p align="right" style="font-size: small; margin-top: -10px">
                                            <span id="charNum{{$i}}">0/500</span>
                                        </p>
                                        <button type="submit" class="btn btn-primary partner-reply-btn">Edit</button>
                                    </form>
                                </div>
                                @else
                                    <span class=" m-0 pull-right reply-moderation">Your reply is under moderation.</span>
                                @endif
                            <!-- </div> -->
                        </div>
                        <br>
                    @endif
                    <!-- when partner will reply and wil see empty box -->
                    @if(empty($reviews[$i]['comments'][0]['comment']))
                        <div class="row">
                            <div class="col-md-10 col-md-offset-2 col-sm-11 col-sm-offset-1 col-xs-11 col-xs-offset-1">
                                <!-- Partner reply box -->
                                <div class="whitebox partner-color">
                                    <form action="{{url('partner/branch/replyReview/'.$reviews[$i]['id'])}}" method="post" style="padding: 15px 0;">
                                        {{csrf_field()}}
                                        <div class="form-group">
                                        <textarea name="reply" id="review{{$i}}" cols="78" rows="4" placeholder="Your reply goes here..."
                                       required class="form-control" maxlength="500" onkeyup="replyChars({{$i}});"></textarea>
                                        </div>
                                        <p align="right" style="font-size: small; margin-top: -10px">
                                            <span id="charNum{{$i}}">0/500</span>
                                        </p>
                                        <input type="hidden" name="customerID" value="{{$reviews[$i]['customer_id']}}">
                                        <input type="hidden" name="review_id" value="{{  $reviews[$i]['id']}}">
                                        <button type="submit" class="btn btn-primary partner-reply-btn">Reply</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                      
                    @endif
                @endfor
                {{$reviews->links()}}
            @else
                <p>No review</p>
            @endif
        </div>
    </div>
</div>

<!-- liker modal -->
<div id="likerModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header"> <h4 class="modal-title">
                    Likes
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times" style="color: #fff;"></i>
                </button>
               
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="likerList"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partner-dashboard.footer')
<script src="{{asset('js/partner_dashboard/review.js')}}"></script>

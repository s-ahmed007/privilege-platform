@include('header')
<style>
    .card {
        font-size: 1em;
        overflow: hidden;
        padding: 0;
        border: none;
        border-radius: .28571429rem;
        box-shadow: 0 1px 3px 0 #d4d4d5, 0 0 0 1px #d4d4d5;
    }
    .card-block {
        font-size: 1em;
        position: relative;
        margin: 0;
        padding: 1em;
        border: none;
        border-top: 1px solid rgba(34, 36, 38, .1);
        box-shadow: none;
    }
    .card-img-top {
        display: block;
        width: 100%;
        height: auto;
    }
    .card-title {
        line-height: 1.2857em;
    }
    .card-text {
        clear: both;
        margin-top: .5em;
        color: rgba(0, 0, 0, .68);
    }
    .card-footer {
        font-size: 1em;
        position: static;
        top: 0;
        left: 0;
        max-width: 100%;
        padding: .75em 1em;
        border-top: 1px solid rgba(0, 0, 0, .05) !important;
        background: #fff;
    }
    .card-inverse{
        border: 1px solid rgba(0, 0, 0, .05);
    }
    .profile-inline ~ .card-title {
        display: inline-block;
        margin-left: 4px;
        vertical-align: top;
    }
</style>
<section id="hero">
    <div class="container">
        <div class="section-title-hero" data-aos="fade-up">
            <h2>Search Result</h2>
            <p>Search partners and areas</p>
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-sm-12 col-xs-12">
                {{--when exact partners found--}}
                @if(!empty($partner_list_1))
                    <p class="bold">Did you mean?</p>
                    <div class="row" id="offers">
                    @foreach($partner_list_1 as $exclusiveOffers)
                        <?php $pname = str_replace("'", "", $exclusiveOffers['partner_name']); ?>
                        <div class="col-sm-6 col-md-4 col-lg-4 mt-4">
                            @if(count($exclusiveOffers['branches']) == 1)
                                <a href="{{ url('partner-profile/'. $pname .'/'.$exclusiveOffers['branches'][0]['id'])}}">
                            @else
                                <div onclick="showLocationModal( '{{$exclusiveOffers['partner_account_id']}}' )" style="cursor: pointer">
                            @endif
                                <div class="card card-inverse card-info" style="padding: unset">
                                    
                                    @if($exclusiveOffers['partner_cover_photo'] != null)
                                        <img class="card-img-top" src="{{ $exclusiveOffers['partner_cover_photo'] }}" alt="royalty-partner-cover">
                                    @else
                                        <img class="card-img-top" src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/offers/nobanner.png" alt="royalty-partner-cover">
                                    @endif
                                    <div class="card-block">
                                        <h4 class="card-title card-partner-name">{{$exclusiveOffers['partner_name']}}</h4>
                                        <div class="card-text">
                                            <p>
                                                {{$exclusiveOffers['locations']}} - <?php $ratings = [1,2,3,4,5]; ?>
                                    @if($exclusiveOffers['average_rating'] == 0)
                                        <span class="partner-box-info-rating">new</span>
                                    @elseif(in_array($exclusiveOffers['average_rating'], $ratings))
                                        <i class="bx bxs-star yellow"></i>
                                        <span class="partner-box-info-rating">{{round($exclusiveOffers['average_rating']).'.0'}}</span>
                                    @else
                                        <i class="bx bxs-star yellow"></i>
                                        <span class="partner-box-info-rating">{{round($exclusiveOffers['average_rating'], 1)}}</span>
                                    @endif
                                    </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <label class="label-tag-small">OFFER</label>
                                        <small class="bold black"> {{$exclusiveOffers['offer_heading']}}</small>
                                    </div>
                                </div>
                            @if(count($exclusiveOffers['branches']) == 1)
                                </a>
                            @else
                                </div>
                            @endif
                        </div>
                    @endforeach
                    </div>
                    {{--when similar named partners found--}}
                @elseif(isset($partner_final_list) && count($partner_final_list) > 0)
                    <p class="bold">Did you mean?</p>
                    <div class="row" id="offers">
                    @foreach($partner_final_list as $exclusiveOffers)
                        <?php $pname = str_replace("'", "", $exclusiveOffers['partner_name']); ?>
                        <div class="col-sm-6 col-md-4 col-lg-4 mt-4">
                            @if(count($exclusiveOffers['branches']) == 1)
                                <a href="{{ url('partner-profile/'. $pname .'/'.$exclusiveOffers['branches'][0]['id'])}}">
                            @else
                                <div onclick="showLocationModal( '{{$exclusiveOffers['partner_account_id']}}' )" style="cursor: pointer">
                            @endif
                                <div class="card card-inverse card-info" style="padding: unset">
                                    @if($exclusiveOffers['partner_cover_photo'] != null)
                                        <img class="card-img-top" src="{{ $exclusiveOffers['partner_cover_photo'] }}" alt="royalty-partner-cover">
                                    @else
                                        <img class="card-img-top" src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/offers/nobanner.png" alt="royalty-partner-cover">
                                    @endif
                                    <div class="card-block">
                                        <h4 class="card-title card-partner-name">{{$exclusiveOffers['partner_name']}}</h4>
                                        <div class="card-text">
                                            <p>
                                                {{$exclusiveOffers['locations']}} - <?php $ratings = [1,2,3,4,5]; ?>
                                            @if($exclusiveOffers['average_rating'] == 0)
                                                <span class="partner-box-info-rating">new</span>
                                            @elseif(in_array($exclusiveOffers['average_rating'], $ratings))
                                                <i class="bx bxs-star yellow"></i>
                                                <span class="partner-box-info-rating">{{round($exclusiveOffers['average_rating']).'.0'}}</span>
                                            @else
                                                <i class="bx bxs-star yellow"></i>
                                                <span class="partner-box-info-rating">{{round($exclusiveOffers['average_rating'], 1)}}</span>
                                            @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <label class="label-tag-small">OFFER</label>
                                        <small class="bold black"> {{$exclusiveOffers['offer_heading']}}</small>
                                    </div>
                                </div>
                            @if(count($exclusiveOffers['branches']) == 1)
                                </a>
                            @else
                                </div>
                            @endif
                        </div>
                    @endforeach
                    </div>
                @else
                    <div class="center">
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/search.png"
                             class="lazyload" alt="Royalty search">
                        <p>SORRY! RESULT NOT FOUND.</p>
                        <ul>
                            <li>• Search using a different word.</li>
                            <li>• Check your spelling.</li>
                            <li>• Browse our
                                <a href="{{ url('offers/all') }}" target="_blank"><b> partners.</b></a>
                            </li>
                            <li>Go through our
                                <a href="{{ url('/faq') }}" target="_blank"><b>FAQ's</b> page.</a>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
            <div class="col-md-3 col-sm-12 col-xs-12">
                <div class="search-trending-container">
                    <p class="bold">Trending Offers</p>
                    <hr>
                    @if(isset($profileImages))
                        @foreach($profileImages as $info)
                            <div class="partner-details">
                                <img src="{{ $info['partner_profile_image'] }}" alt="Royalty Profile Image"
                                     class="img-circle img-40 primary-border lazyload">
                                <p class="partner-search-name">
                                    <?php $pname = str_replace("'", "", $info['partner_name']); ?>
                                    <a href="{{url('partner-profile/'.$pname.'/'.$info['main_branch_id'])}}">
                                        {{$info['partner_name']}}</a>
                                    @foreach($categories as $category)
                                        @if($category->id == $info['partner_category'])
                                            <a href="{{ url('offers/'.$category->type) }}" target="_blank">
                                                {{$category->name}}</a>
                                        @endif
                                    @endforeach
                                </p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@include('footer')
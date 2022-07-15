@include('partner-dashboard.header')

    <div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
            <h3 class="d-inline-block">{{__('partner/common.dashboard')}}</h3>
            <h5 class="d-inline-block float-right">{{__('partner/dashboard.here_you_will_find_customer_stats')}} </h5>
        </div>
    </div>
        <div class="row">
            <div class="col-lg-4 col-sm-6 col-xs-12">
                <a href="{{url('partner/branch/transaction_statistics')}}">
                    <div class="white-box analytics-info">
                        <h3 class="box-title">{{__('partner/dashboard.transactions')}}</h3>
                        <ul class="list-inline two-part">
                            <li style="border-bottom: unset">
                                <div id="sparklinedash"></div>
                            </li>
                            <li class="text-right"><i class="ti-arrow-up text-purple"></i>
                                <span >Total: {{$data['total_transaction']}}</span><br>
                                <span >Current Month: {{$data['running_month_transaction']}}</span>
                            </li>
                        </ul>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-sm-6 col-xs-12">
                <a href="{{url('partner/branch/profile_visit')}}">
                    <div class="white-box analytics-info">
                        <h3 class="box-title">{{__('partner/dashboard.profile_views')}}</h3>
                        <ul class="list-inline two-part">
                            <li style="border-bottom: unset">
                                <div id="sparklinedash2"></div>
                            </li>
                            <li class="text-right"><i class="ti-arrow-up text-purple"></i>
                                <span>Total: {{$data['total_profile_visit']}}</span><br>
                                <span>Current Month: {{$data['running_month_profile_visit']}}</span>
                            </li>
                        </ul>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-sm-6 col-xs-12">
                <a href="{{url('partner/branch/review')}}">
                    <div class="white-box analytics-info">
                        <h3 class="box-title">{{__('partner/dashboard.reviews')}}
                            <span>(<i class="fa fa-star"></i>{{round($data['rating'], 1)}})</span>
                        </h3>
                        <ul class="list-inline two-part">
                            <li style="border-bottom: unset"></li>
                            <li class="text-right">
                                <span >Total: {{$data['total_review']}}</span><br>
                                <span >Current Month: {{$data['running_month_review']}}</span>
                            </li>
                        </ul>
                    </div>
                </a>
            </div>
        </div>
        <div class="row">
{{--            <div class="col-md-12 col-lg-8 col-sm-12">--}}
{{--                <div class="white-box">--}}
{{--                    <h3 class="box-title">Recent Comments</h3>--}}
{{--                    <div class="comment-center p-t-10">--}}
{{--                        <div class="comment-body">--}}
{{--                            <div class="user-img"> <img src="../plugins/images/users/pawandeep.jpg" alt="user" class="img-circle">--}}
{{--                            </div>--}}
{{--                            <div class="mail-contnet">--}}
{{--                                <h5>Pavan kumar</h5><span class="time">10:20 AM   20  may 2016</span>--}}
{{--                                <br/><span class="mail-desc">Donec ac condimentum massa. Etiam pellentesque pretium lacus. Phasellus ultricies dictum suscipit. Aenean commodo dui pellentesque molestie feugiat. Aenean commodo dui pellentesque molestie feugiat</span> <a href="javacript:void(0)" class="btn btn btn-rounded btn-default btn-outline m-r-5"><i class="ti-check text-success m-r-5"></i>Approve</a><a href="javacript:void(0)" class="btn-rounded btn btn-default btn-outline"><i class="ti-close text-danger m-r-5"></i> Reject</a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="comment-body">--}}
{{--                            <div class="user-img"> <img src="../plugins/images/users/sonu.jpg" alt="user" class="img-circle">--}}
{{--                            </div>--}}
{{--                            <div class="mail-contnet">--}}
{{--                                <h5>Sonu Nigam</h5><span class="time">10:20 AM   20  may 2016</span>--}}
{{--                                <br/><span class="mail-desc">Donec ac condimentum massa. Etiam pellentesque pretium lacus. Phasellus ultricies dictum suscipit. Aenean commodo dui pellentesque molestie feugiat. Aenean commodo dui pellentesque molestie feugiat</span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="comment-body b-none">--}}
{{--                            <div class="user-img"> <img src="../plugins/images/users/arijit.jpg" alt="user" class="img-circle">--}}
{{--                            </div>--}}
{{--                            <div class="mail-contnet">--}}
{{--                                <h5>Arijit singh</h5><span class="time">10:20 AM   20  may 2016</span>--}}
{{--                                <br/><span class="mail-desc">Donec ac condimentum massa. Etiam pellentesque pretium lacus. Phasellus ultricies dictum suscipit. Aenean commodo dui pellentesque molestie feugiat. Aenean commodo dui pellentesque molestie feugiat</span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="panel">
                    <div class="sk-chat-widgets">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                {{__('partner/dashboard.top_customers')}}
                                <a href="{{url('partner/branch/all_top_customers')}}" class="pull-right">{{__('partner/dashboard.see_all')}}</a>
                            </div>
                            <div class="panel-body">
                                <ul class="chatonline">
                                    <li style="float: right;">{{__('partner/dashboard.scan')}}</li>
                                    @foreach($top_transactors as $user)
                                        <li>
                                            <a style="cursor: default;">
                                                <span class="customer-scan-count">{{$user->transaction_count}}</span>
                                                <img src="{{$user->customer_profile_image}}" alt="user-img" class="img-circle">
                                            <span class="customer-name">{{$user->customer_full_name}}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="panel">
                    <div class="sk-chat-widgets">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                {{__('partner/dashboard.top_leaderboard')}}
                                <a href="{{url('partner/branch/leaderboard')}}" class="pull-right">{{__('partner/dashboard.see_all')}}</a>
                            </div>
                            <div class="panel-body">
                                <ul class="chatonline">
                                    <li style="float: right;">{{__('partner/dashboard.point')}}</li>
                                    @foreach($leaderBoard as $key => $value)
                                        <li>
                                            <a style="cursor: default;">
                                                <span class="customer-scan-count">
                                                    {{ $value['point'] }}
                                                </span>
                                                <?php
//                                                if($value['prev_index'] != null){
//                                                    if($value['prev_index'] == $key){
//                                                        echo '<i class="fa fa-minus"></i>';
//                                                    }elseif($value['prev_index'] > $key){
//                                                        echo '<i class="fa fa-arrow-up"></i>';
//                                                    }elseif($value['prev_index'] < $key){
//                                                        echo '<i class="fa fa-arrow-down"></i>';
//                                                    }
//                                                }else{
//                                                    echo '<i class="fa fa-minus"></i>';
//                                                }
                                                ?>
                                                <img src="{{$value['profile_image']}}" alt="partner-img" class="img-circle">
                                                <span class="customer-name">{{ $value['partner_name'] }}, {{ $value['area'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="panel">
                    <div class="sk-chat-widgets">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                {{__('partner/dashboard.recent_reviews')}}
                                <a href="{{url('partner/branch/review')}}" class="pull-right">{{__('partner/dashboard.see_all')}}</a>
                            </div>
                            <div class="panel-body">
                                <ul class="chatonline">
                                    @foreach($reviews as $review)
                                        <li>
                                            <img src="{{$review->customerInfo->customer_profile_image}}" alt="user-img" class="img-circle">
                                            <span class="">{{$review->customerInfo->customer_full_name}}</span>
                                            <div class="review-star" style="color:#ffc107;">
                                                @if($review->rating == 1)
                                                    <div class="reviewer-star-rating-div">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                    </div>
                                                @elseif($review->rating == 2)
                                                    <div class="reviewer-star-rating-div">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                    </div>
                                                @elseif($review->rating == 3)
                                                    <div class="reviewer-star-rating-div">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                    </div>
                                                @elseif($review->rating == 4)
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
                                            $posted_on = date("Y-M-d H:i:s", strtotime($review->posted_on));
                                            $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                            ?>
                                            <span class="review-post-date pull-right">
                                            {{$created->diffForHumans()}}
                                            </span>
                                            @if ($review->heading != null && $review->heading != 'n/a')
                                            <p><b>Review Heading: {{$review->heading}}</b></p>
                                            @endif
                                            @if ($review->body != null && $review->body != 'n/a')
                                            <p>Review Body: {{$review->body}}</p>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4><b>{{__('partner/dashboard.transaction_stats_peak_hour')}}</b></h4>
                <div class="analytics-info">
                    <span class="">{{__('partner/dashboard.most_transaction_time')}}: {{$data['peak_hour']}}</span>
                </div><br>
                <form class="form-inline" action="">
                    <div class="form-group">
                        <label for="peak_hour_from">From</label>
                        <input type="date" id="peak_hour_from" class="form-control">
                        {{--                    <input type="date" id="peak_hour_from" class="form-control" value="{{date('Y-m-d')}}">--}}
                    </div>
                    <div class="form-group">
                        <label for="peak_hour_to">To</label>
                        <input type="date" id="peak_hour_to" class="form-control">
                        {{--                    <input type="date" id="peak_hour_to" class="form-control" value="{{date('Y-m-d')}}">--}}
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" onclick="sortPeakHour()">Sort</button>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-warning" onclick="resetPeakHourAnalytics()">Reset</button>
                    </div>
                </form>
                <br>
                <div id="peakHourChart"></div>
            </div>
        </div>

    </div>

@include('partner-dashboard.footer')

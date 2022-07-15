@include('transactionRequest.header')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale = 1.0, user-scalable = no">
<link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet">
<div class="container">
    <span class="user_not_found"></span>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <input type="text" class="form-control" id="customer_id"
                       placeholder="Enter Customer Card Number Here (এখানে গ্রাহকের কার্ড নম্বর লিখুন)" maxlength="16">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <button class="btn btn-success form-control" onclick="checkCustomer()">CONFIRM</button>
            </div>
        </div>
    </div>
    <br>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
               aria-selected="true">Requests</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
               aria-selected="false">My Offers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="reward-tab" data-toggle="tab" href="#reward" role="tab" aria-controls="reward"
               aria-selected="false">My Rewards</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact"
               aria-selected="false">Profile</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="howto-tab" data-toggle="tab" href="#howto" role="tab" aria-controls="howto"
               aria-selected="false">How It Works</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
                <div class="col-md-12">
                    <p>When a customer scans QR on your desk and selects offer, you will see the request below.</p>
                    <p style="font-size: 0.9em"> যখন কোনও গ্রাহক/কাস্টমার আপনার ডেস্কে কিউআর(QR) স্ক্যান করে অফার নির্বাচন করবেন, আপনি নীচে অনুরোধটি
                        দেখতে পাবেন।
                    </p>
                    @if(session('accepted'))
                        <div class="msg_accept">
                            <span>{{session('accepted')}}</span>
                        </div>
                    @elseif(session('rejected'))
                        <div class="msg_reject">
                            <span>{{session('rejected')}}</span>
                        </div>
                    @endif
                    <div class="all_pending_requests">
                        @if(count($notifications) > 0)
                            <ul class="list-group">
                                @foreach($notifications as $notification)
                                    <li class="list-group-item">
                                        <?php
                                        $posted_on = date("Y-M-d H:i:s", strtotime($notification['posted_on']));
                                        $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                        $prev_time = \Carbon\Carbon::now()->subMinutes(10);
                                        ?>
                                        <span class="relative_time">{{$created->diffForHumans()}}</span><br>
                                        <img src="{{$notification->image}}" alt="Profile" class="pro_pic">
                                        <span class="request-text">
                                 {{$notification->customer_name}} has requested
                                 ({{$notification->request->offer->offer_description}}).
                                 </span>
                                        <div class="request_{{$notification->source_id}}">
                                            @if($notification->request->status == 0)
                                                {{-- @if($created < $prev_time)
                                                <span class="expired">Expired</span>
                                                @else --}}
                                                <button class="btn btn-success action_btn notification_{{$notification->id}}"
                                                        onclick="updateStatus('{{$notification->id}}', '{{$notification->source_id}}', '1', '{{$created}}')"
                                                >Accept
                                                </button>
                                                <button class="btn btn-danger action_btn notification_{{$notification->id}}"
                                                        onclick="updateStatus('{{$notification->id}}', '{{$notification->source_id}}', '2', '{{$created}}')"
                                                >Reject
                                                </button>
                                                {{-- @endif --}}
                                            @elseif($notification->request->status == \App\Http\Controllers\Enum\TransactionRequestStatus::ACCEPTED)
                                                <span class="accepted">Accepted</span>
                                            @else
                                                <span class="rejected">Rejected</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <br><br>
                            <p>No request has been made yet.</p>
                            <p style="font-size: 0.9em">এখনও কোন অনুরোধ করা হয়নি।</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            @if(count($sorted_offers) > 0)
                @foreach($sorted_offers as $offer)
                    <div class="column">
                        <div class="row m-z-a">
                            <div class="partner-offer-box-l">
                                <h4>{{$offer->offer_description}}</h4>
                                <?php
                                if ($offer->actual_price != 0) {
                                    $deducted_price = $offer->actual_price - $offer->price;
                                    $percentage = floor(($deducted_price * 100) / $offer->actual_price);
                                } else {
                                    $percentage = 0;
                                }
                                ?>
                                <div class="partner-offer-timings">
                                    <p>Valid till -
                                        <span>
                        {{date("F d, Y", strtotime($offer->date_duration[0]['to']))}}
                        </span>
                                    </p>
                                    <p>Valid for - <span>{{$offer->valid_for}}</span></p>
                                    <p>Valid on -
                                        <span>
                        <?php
                                            $weekdays = $offer->weekdays[0];
                                            ?>
                                            @if($weekdays['sat'] == '1' && $weekdays['sun'] == '1' && $weekdays['mon'] == '1' && $weekdays['tue'] == '1' &&
                                            $weekdays['wed'] == '1' && $weekdays['thu'] == '1' && $weekdays['fri'] == '1')
                                                All days
                                            @else
                                                @if($weekdays['sat'] == '1')
                                                    Sat
                                                @endif
                                                @if($weekdays['sun'] == '1')
                                                    Sun
                                                @endif
                                                @if($weekdays['mon'] == '1')
                                                    Mon
                                                @endif
                                                @if($weekdays['tue'] == '1')
                                                    Tue
                                                @endif
                                                @if($weekdays['wed'] == '1')
                                                    Wed
                                                @endif
                                                @if($weekdays['thu'] == '1')
                                                    Thu
                                                @endif
                                                @if($weekdays['fri'] == '1')
                                                    Fri
                                                @endif
                                            @endif
                        </span>
                                    </p>
                                    @if($offer->time_duration != null)
                                        <p>Timing - <span>
                        <?php $i = 0; ?>
                                                @foreach($offer->time_duration as $duration)
                                                    {{date('h:i a', strtotime($duration['from'])).' - '.date('h:i a', strtotime($duration['to']))}}
                                                    <?php
                                                    echo $i != count($offer->time_duration) - 1 ? ',' : '';
                                                    $i++; ?>
                                                @endforeach
                        </span>
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    @php
                                        $date = date("d-m-Y");
                                        //check expiry
                                        $offer_date = $offer->date_duration[0];
                                        if (
                                        new DateTime($offer_date["from"]) <= new DateTime($date) && new DateTime($offer_date["to"]) >= new DateTime($date)
                                        && $offer->active == 1
                                        ) {
                                        echo '<span class="badge badge-success">Available</span>';
                                        } else {
                                        echo '<span class="badge badge-danger">Expired</span>';
                                        }
                                    @endphp
                                    <span>Used: {{$offer->offer_use_count}} times</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p style="padding: 10px 20px;">Sorry, no offers available at the moment, please check back later for current offers.</p>
            @endif
        </div>
        <div class="tab-pane fade" id="reward" role="tabpanel" aria-labelledby="reward-tab">
            <br>
            PAYMENT DUE: {{$payment_info['due']}} BDT <br>
            PAYMENT PAID: {{$payment_info['paid']}} BDT<br>
            LAST PAID: {{$payment_info['last_paid']}}<br>
            @if(count($sorted_rewards) > 0)
                @foreach($sorted_rewards as $offer)
                    <div class="column">
                        <div class="row m-z-a">
                            <div class="partner-offer-box-l">
                                <h4>{{$offer->offer_description}}</h4>
                                <?php
                                if ($offer->actual_price != 0) {
                                    $deducted_price = $offer->actual_price - $offer->price;
                                    $percentage = floor(($deducted_price * 100) / $offer->actual_price);
                                } else {
                                    $percentage = 0;
                                }
                                ?>
                                <div class="partner-offer-timings">
                                    <p>Valid till -
                                        <span>
                                        {{date("F d, Y", strtotime($offer->date_duration[0]['to']))}}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    @php
                                        $date = date("d-m-Y");
                                        //check expiry
                                        $offer_date = $offer->date_duration[0];
                                        if (
                                        new DateTime($offer_date["from"]) <= new DateTime($date) && new DateTime($offer_date["to"]) >= new DateTime($date)
                                        && $offer->active == 1
                                        ) {
                                        echo '<span class="badge badge-success">Available</span>';
                                        } else {
                                        echo '<span class="badge badge-danger">Expired</span>';
                                        }
                                    @endphp
                                    <span>Used: {{$offer->offer_use_count}} times, Cost Price: {{$offer->offer_use_count * $offer->actual_price}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p style="padding: 10px 20px;">Sorry, no rewards available at the moment, please check back later for current offers.</p>
            @endif
        </div>
        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            <div class="col-md-12 column">
                <div class="container">
                    <h3><u>Your details</u></h3>
                    <p>{{$user->branchScanner->branch->info->partner_name}}</p>
                    <p>{{$user->branchScanner->branch->partner_area}}</p>
                    <p>{{$user->branchScanner->full_name}}</p>
                    <p>{{$user->phone}}</p>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="howto" role="tabpanel" aria-labelledby="howto-tab">
            <br>
            <h3><u>How to give OFFERS to Royalty Members</u></h3>
            <p style="text-align: center;">HOW TO GIVE DISCOUNTS</p>
            <div class="row mtb-10">
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="how-it-works-img">
                        <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/how-it-works-merchant/HIW-MERCHANT1.1.png"/>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="how-it-works-img">
                        <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/how-it-works-merchant/HIW-MERCHANT1.2.png"/>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="how-it-works-img">
                        <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/how-it-works-merchant/HIW-MERCHANT1.3.png"/>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="how-it-works-img">
                        <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/how-it-works-merchant/HIW-MERCHANT1.4.png"/>
                    </div>
                </div>
            </div>
            <p style="text-align: center;">HOW TO GIVE DEALS</p>
            <div class="row mtb-10" style="margin-bottom: 30px">
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="how-it-works-img">
                        <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/how-it-works-merchant/HIW-MERCHANT2.1.png"/>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="how-it-works-img">
                        <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/how-it-works-merchant/HIW-MERCHANT2.2.png"/>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="how-it-works-img">
                        <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/how-it-works-merchant/HIW-MERCHANT2.3.png"/>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="how-it-works-img">
                        <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/how-it-works-merchant/HIW-MERCHANT2.4.png"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- offers modal -->
<div class="modal fade" id="offersModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Continue Transaction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="user_info">
                    <div class="row">
                        <div class="col-md-2">
                            <img src="" class="pro_pic user_img" alt="User Image">
                        </div>
                        <div class="col-md-10">
                            <span class="customer_name">customer name</span><br>
                            <small>ID: <span class="customer_id">customer id</span></small>
                        </div>
                    </div>
                    <br>
                    <ul class="list-group w-100" id="offersList"></ul>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
@include('transactionRequest.footer')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/merchant.js')}}"></script>
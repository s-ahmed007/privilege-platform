<?php
if(!session('adm_pass_change_stat') || session('adm_pass_change_stat') < \App\Http\Controllers\Enum\AdminRole::adm_pass_change_stat ){
$logout = url('adminLogout');
?>
<script>window.location.href = '{{$logout}}';</script>
<?php
}

//update sidebar counts
$sidebar_notification_counts = (new \App\Http\Controllers\adminController2())->getSidebarNotificationCount();
$admin_notification_count = $sidebar_notification_counts['activity_notification'];
$scanner_request_count = $sidebar_notification_counts['scanner_request_count'];
$user_royalty_reward_redeem_count = $sidebar_notification_counts['user_royalty_reward_redeem_count'];
$user_partner_reward_redeem_count = $sidebar_notification_counts['user_partner_reward_redeem_count'];
$transaction_request_count = $sidebar_notification_counts['transaction_request_count'];
$partner_post_request_count = $sidebar_notification_counts['partner_post_request_count'];
$pending_reviews = $sidebar_notification_counts['pending_reviews'];
$pending_replies = $sidebar_notification_counts['pending_replies'];

//To Disable Cache Load if Browser Back Button Pressed
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo.png">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Royalty | Admin</title>
    <!-- Bootstrap -->
    <link href="{{ asset('admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->

    <link href="{{asset('css/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9e60b11f48.js" crossorigin="anonymous"></script>
    <!-- NProgress -->
    <link href="{{ asset('admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{ asset('admin/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="{{ asset('admin/build/css/custom.min.css') }}" rel="stylesheet">
    <!-- Custom  Style -->
    <link href="{{ asset('admin/build/css/admin-panel.css') }}" rel="stylesheet">
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
    <style>
        .page_loader {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: #969696;
            z-index: 9999;
            opacity: 0.5;
        }
        .page_loader img {
            position: fixed;
            top: 50%;
            left: 50%;
        }
        .form-group .main_branch {
            display: none;
        }

        .form-group .main_branch + .btn-group > label span {
            width: 20px;
        }

        .form-group .main_branch + .btn-group > label span:first-child {
            display: none;
        }

        .form-group .main_branch + .btn-group > label span:last-child {
            display: inline-block;
        }

        .form-group .main_branch:checked + .btn-group > label span:first-child {
            display: inline-block;
        }

        .form-group .main_branch:checked + .btn-group > label span:last-child {
            display: none;
        }

        .pagination {
            float: right;
        }
        .notify_num {
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 7px;
            background: #fa3e3e;
            position: absolute;
            margin: 0;
            padding: 0 5px 1px 4px;
            text-align: center;
            -webkit-font-smoothing: subpixel-antialiased;
            line-height: 1.3;
            font-size: 10px;
            display: inline-block;
            top: 10px;
            right: 6px;
            color: #fff;
        }
    </style>
</head>
<body class="nav-md">
<div class="page_loader" style="display: none;" id="page_loader">
    <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif" alt="Royalty Loading GIF" class="lazyload" title="Royalty loading icon">
</div>
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="{{ url('dashboard') }}" class="site_title"><span>Home</span></a>
                </div>
                <div class="clearfix"></div>
                <div class="profile clearfix">
                    <div class="profile_pic">
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo.png" alt="Royalty Icon"
                             class="img-circle profile_img">
                    </div>
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2>{{session('admin_username')}}</h2>
                    </div>
                </div>
                <br/>
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <h3>Menu</h3>
                        <ul class="nav side-menu">
                            @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin ||
                                session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                <li><a href="{{ url('dashboard') }}"><i class='bx bxs-dashboard'></i>Dashboard</a></li>
                                <li>
                                    <a><i class='bx bx-stats' ></i>Analytics&nbsp;<span class="arrow-icon"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ url('admin/analytics') }}">Platform Analytics</a></li>
                                        <li><a href="{{ url('admin/membership_analytics') }}">Membership Analytics</a></li>
                                        <li><a href="{{ url('admin/transaction_analytics') }}">Transaction Analytics</a></li>
                                        <li><a href="{{ url('admin/scan_analytics') }}">Partner Scan Analytics</a></li>
                                    <!-- <li><a href="{{ url('transactionList/active') }}">Active/Inactive Partner Scans</a></li> -->
                                        <li><a href="{{url('admin/analytics/search')}}">Search Analytics</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a><i class='bx bx-award' ></i>Leader Board&nbsp;<span class="arrow-icon"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{url('admin/customer/scan_leaderboard')}}">Customer Leader Board</a></li>
                                        <li><a href="{{url('admin/customer/credit_leaderboard')}}">Customer Credit Leader Board</a></li>
                                        <li><a href="{{ url('admin/refer/leader-board') }}">Customer Refer Leader Board</a></li>
                                        <li><a href="{{url('admin/partner/scan_leaderboard')}}">Partner Leader Board</a></li>
                                    </ul>
                                </li>
                            @endif
                            @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin ||
                                     session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                <li>
                                    <a><i class='bx bx-group'></i>Customer Hub&nbsp;<span class="arrow-icon"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ url('customers/card_users') }}">All Members</a></li>
                                        {{--                              <li><a href="{{ url('card-delivery/free_trial') }}">Free Trial Members</a></li>--}}
{{--                                        <li><a href="{{ url('customers/active') }}">Active Members</a></li>--}}
{{--                                        <li><a href="{{ url('customers/inactive') }}">Inactive Members</a></li>--}}
{{--                                        <li><a href="{{ url('tempBuyCard') }}">Temporary Membership Purchase</a></li>--}}
{{--                                        <li><a href="{{ url('card-delivery/all') }}">Card Delivery</a></li>--}}
{{--                                        <li><a href="{{ url('purchase/history/all') }}">Purchase History</a></li>--}}
                                        {{--                              <li><a href="{{ url('admin/user_leaderboard') }}">Customer Leaderboard</a></li>--}}
                                        <li><a href="{{ url('admin/manual_registration') }}">Manual Registration</a></li>
                                    </ul>
                                </li>
                            @endif
                            @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin ||
                                    session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                <li>
                                    <a><i class='bx bx-receipt' ></i>Transactions&nbsp;<span class="arrow-icon"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ url('partners-all-transactions/active') }}">All Transactions</a></li>
                                        <li><a href="{{ url('manual-transaction') }}">Manual Transaction</a></li>
                                         <li><a href="{{ url('admin/transaction_requests') }}" style="display: inline-block;">Transaction Request</a>
                                            @if($transaction_request_count > 0)
                                                <span style="color: #fff;display: unset;padding: 2px 6px;background-color: red;
                                       border-radius: 45%;">{{$transaction_request_count}}</span>
                                            @endif
                                        </li>
                                    </ul>
                                </li>
                            @endif
                        <!-- <li><a href="{{ url('allCOD') }}"><i class="money-icon"></i>Cash On Delivery</a></li> -->
                            <li>
                                <a><i class='bx bxs-group' ></i>Partner Hub&nbsp;<span class="arrow-icon"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ url('form_upload') }}">Add Partner</a></li>
                                    <li><a href="{{ url('add-division-area') }}">Add Division+Area</a></li>
                                    <li><a href="{{ url('admin/add-branch') }}">Add Branches</a></li>
                                    <li><a href="{{ url('admin/offers/add') }}">Add Offers</a></li>
                                    <li><a href="{{ url('admin/trendingBrands') }}">Add Trending Offers+New Partners/Occasional Partners</a></li>
                                    <li><a href="{{ url('admin/featuredPartners') }}">Add Featured Partners</a></li>
                                    <li><a href="{{ url('admin/branch_facilities') }}">Branch Facilities</a></li>

                                    {{-- <li><a href="{{ url('addHotspot') }}">Add Hotspot+Partner</a></li>
                                    <li><a href="{{ url('coupon') }}">Add Coupon</a></li>
                                    <li><a href="{{ url('addPromo') }}">Add Online Promo Code</a></li> --}}
                                </ul>
                            </li>
                            <li>
                                <a><i class='bx bxs-user-detail'></i>Partner Details&nbsp;<span class="arrow-icon"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ url('allPartners') }}">All Partner Branches</a></li>
                                    <li><a href="{{ url('changePartnerStatus') }}">Change Main Partner Status</a></li>
                                <!-- {{--
                              <li><a href="{{ url('allHotspots') }}">All Hotspots</a></li>--}} {{--
                              <li><a href="{{ url('hotspotPartners') }}">Partners in Hotspot</a></li>--}} {{--
                              <li><a href="{{ url('allCoupons') }}">All Coupons</a></li>--}} {{--
                              <li><a href="{{ url('allPromo') }}">All Promo Codes</a></li>--}} -->
                                <!-- <li><a href="{{ url('under-moderation/customer') }}">Under Moderation (customer)</a></li>--}} -->
                                    {{-- <li><a href="{{ url('rbd-coupon-payment') }}">Coupon Payment</a></li> --}}
                                </ul>
                            </li>
                            @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin ||
                                    session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                <li>
                                    <a><i class='bx bx-info-circle' ></i>Partner/ Scanner info&nbsp;<span class="arrow-icon"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ url('partner-branches') }}">Add/Edit Scanners</a></li>
                                        <li><a href="{{ url('all-scanners') }}">All Scanners</a></li>
                                        <li><a href="{{ url('/scanner-request') }}" style="display: inline-block;">Scanner Reward Requests</a>
                                            @if($scanner_request_count > 0)
                                                <span style="color: #fff;display: unset;padding: 2px 6px;background-color: red;
                                       border-radius: 45%;">{{$scanner_request_count}}</span>
                                            @endif
                                        </li>
                                        <li><a href="{{ url('branch-user-scanner-prizes') }}">Add Scanner Reward</a></li>
                                        {{--                              <li><a href="{{ url('scanner-leader-board') }}">Partner Outlet Leaderboard</a></li>--}}
                                        <li><a href="{{ url('branch-user-leaderboard-prizes') }}">Leaderboard Prizes</a></li>
{{--                                        <li><a href="{{ url('branch-owner') }}">All Owners</a></li>--}}
                                    </ul>
                                </li>
                            @endif
{{--                            @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin ||--}}
{{--                                    session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)--}}
{{--                                <li>--}}
{{--                                    <a><i class='bx bx-user-voice' ></i>Seller Hub&nbsp;<span class="arrow-icon"></span></a>--}}
{{--                                    <ul class="nav child_menu">--}}
{{--                                        <li><a href="{{ url('card-seller') }}">All Sellers</a></li>--}}
{{--                                        <li><a href="{{ url('/card-promo') }}">Promo Codes</a></li>--}}
{{--                                    </ul>--}}
{{--                                </li>--}}
{{--                            @endif--}}
                            @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin ||
                                    session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                <li>
                                    <a><i class='bx bx-money'></i>Prices&nbsp;<span class="arrow-icon"></span></a>
                                    <ul class="nav child_menu">
{{--                                        <li><a href="{{ url('admin/membership_prices') }}">Membership Prices</a></li>--}}
                                        <li><a href="{{ url('admin/other_prices') }}">Other Prices</a></li>
                                    </ul>
                                </li>
                            @endif
                            @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin ||
                                    session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                <li>
                                    <a><i class='bx bx-gift' ></i>Rewards&nbsp;<span class="arrow-icon"></span></a>
                                    <ul class="nav child_menu">
                                        <li>
                                            <a href="{{url("admin/reward/create?branch_id=".\App\Http\Controllers\Enum\AdminScannerType::royalty_branch_id)}}">
                                                Add Royalty Reward</a>
                                        </li>
                                        <li><a href="{{ url('admin/rewards/add') }}">Add Partner Reward</a></li>
                                        <li><a href="{{url("admin/reward/".\App\Http\Controllers\Enum\AdminScannerType::royalty_branch_id)}}">All
                                                Rewards</a>
                                        </li>
                                        <li><a href="{{url("admin/redeemed_reward/royalty/all")}}" style="display: inline-block">Royalty Reward Request
                                                @if($user_royalty_reward_redeem_count > 0)
                                                    <span style="color: #fff;display: unset;padding: 2px 6px;background-color: red;
                                       border-radius: 45%;">{{$user_royalty_reward_redeem_count}}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li><a href="{{url("admin/redeemed_reward/partner/all")}}" style="display: inline-block">Partner Reward Request
                                                @if($user_partner_reward_redeem_count > 0)
                                                    <span style="color: #fff;display: unset;padding: 2px 6px;background-color: red;
                                       border-radius: 45%;">{{$user_partner_reward_redeem_count}}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li><a href="{{url('admin/rewards/payment')}}">Partner Reward Payment</a></li>
                                        <li><a href="{{url('admin/royalty/rewards/costing')}}">Royalty Reward Costing</a></li>
                                    </ul>
                                </li>

{{--                                <li>--}}
{{--                                    <a><i class='bx bxs-discount' ></i>Deals&nbsp;<span class="arrow-icon"></span></a>--}}
{{--                                    <ul class="nav child_menu">--}}
{{--                                        <li><a href="{{ url('admin/deal/add') }}">Add Deal</a></li>--}}
{{--                                        <li><a href="{{ url('admin/deals/all') }}">All Deals</a></li>--}}
{{--                                        <li><a href="{{ url('admin/deals/purchased/all') }}">Purchased Deals</a></li>--}}
{{--                                        <li><a href="{{ url('admin/deal_refund_requests/all') }}">Refund Request</a></li>--}}
{{--                                        <li><a href="{{ url('admin/deals_payment') }}">Deal Payment</a></li>--}}
{{--                                    </ul>--}}
{{--                                </li>--}}
                            @endif
                            @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin ||
                                    session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                <li>
                                    <a><i class='bx bx-message-alt-detail' ></i>SMS&nbsp;<span class="arrow-icon"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ url('sms-all-customers') }}">All Members</a></li>
                                        <li><a href="{{ url('sms-customer') }}">Existing Members</a></li>
                                        <li><a href="{{ url('sms-guest') }}">One Time</a></li>
                                        <li><a href="{{ url('sms-all-partners') }}">All Partners</a></li>
                                        <li><a href="{{ url('sms_scanners') }}">Existing Scanners</a></li>
                                    </ul>
                                </li>
                            @endif
                            @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin ||
                                     session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                <li>
                                    <a><i class='bx bx-news' ></i>Newsfeed&nbsp;<span class="arrow-icon"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ url('admin/post') }}" style="display: inline-block">All posts
                                                @if($partner_post_request_count > 0)
                                                    <span style="color: #fff;display: unset;padding: 2px 6px;background-color: red;
                                       border-radius: 45%;">{{$partner_post_request_count}}</span>
                                                @endif
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin ||
                                    session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                <li>
                                    <a><i class='bx bx-bell' ></i>Push Notifications&nbsp;<span class="arrow-icon"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ url('send-push-notification/customer') }}">To Members</a></li>
                                        <li><a href="{{ url('send-push-notification/scanner') }}">To Scanners</a></li>
                                        <li><a href="{{ url('admin/scheduled-notification') }}">Scheduled Notifications</a></li>
                                    </ul>
                                </li>
                            @endif
                            <li>
                                <a><i class='bx bx-star' ></i>Reviews&nbsp;<span class="arrow-icon"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ url('admin/allCustomerReviews') }}">All Reviews</a></li>
                                    <li><a href="{{ url('admin/pending_reviews') }}">Pending Reviews
                                            @if($pending_reviews > 0)
                                                <span style="color: #fff;display: unset;padding: 2px 6px;background-color: red;
                                       border-radius: 45%;">{{$pending_reviews}}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li><a href="{{ url('admin/pending_review_replies') }}">Pending Replies
                                            @if($pending_replies > 0)
                                                <span style="color: #fff;display: unset;padding: 2px 6px;background-color: red;
                                       border-radius: 45%;">{{$pending_replies}}</span>
                                            @endif
                                        </a></li>
                                </ul>
                            </li>
                            <li><a href="{{ url('admin/blog-post') }}"><i class='bx bxl-blogger' ></i>Blog</a></li>
                            <li>
                                <a><i class='bx bx-list-ul' ></i> Categories&nbsp;<span class="arrow-icon"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ url('admin/main_cat') }}">Main category</a></li>
                                    <li><a href="{{ url('admin/sub_cat_1') }}">Sub cat 1</a></li>
                                    <li><a href="{{ url('admin/sub_cat_2') }}">Sub cat 2</a></li>
                                    <li><a href="{{ url('admin/category_relation') }}">Category Relation</a></li>
                                    <li><a href="{{ url('admin/part_cat_relation') }}">Partner Category Relation</a></li>
                                </ul>
                            </li>
                            <li>
                                <a><i class='bx bx-network-chart' ></i>Others&nbsp;<span class="arrow-icon"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ url('admin/partner-request') }}">New Partner Request</a></li>
                                    <li><a href="{{ url('admin/partner-offer-request') }}">Partner Offer Request</a></li>
                                    <li><a href="{{ url('/openings') }}">Job Openings</a></li>
                                    {{--<li><a href="{{ url('addNewsletter') }}">Newsletter</a></li>--}}
                                    <li><a href="{{ url('admin/allWishes') }}">All Wishes</a></li>
                                    <li><a href="{{ url('allNews') }}">Press</a></li>
                                    <li><a href="{{ url('/all-contacts') }}">Contacts</a></li>
                                    <li><a href="{{ url('admin/influencer-requests') }}">Influencer Requests</a></li>
                                    <li><a href="{{ url('admin/sent_message_history/all') }}">Sent Message History</a></li>
                                    <li><a href="{{ url('admin/generate/csv') }}">Generate CSV</a></li>
                                    <li><a href="{{ url('admin/donation') }}">Donation</a></li>
                                    <li><a href="{{ url('admin/dynamic_links') }}">Dynamic Links</a></li>
                                <!-- {{--<li><a href="{{ url('birthdays') }}">Birthdays</a></li>--}} -->
                                <!-- <li><a href="{{ url('admin/b2b2c-clients') }}">B2B2C Clients</a></li> -->
                                <!-- {{--<li><a href="{{ url('refer-bonus') }}">Refer Bonus</a></li>--}} -->
                                </ul>
                            </li>

                        </ul>
                    </div>
                </div>
                <!-- /sidebar menu -->
            </div>
        </div>
        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav>
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="bar-icon"></i></a>
                    </div>
                    <ul class="nav navbar-nav navbar-right">
                        {{--                  @if(Session()->has('leaderboard_alert'))--}}
                        {{--                     @if(Session()->get('leaderboard_alert') == 0)--}}
                        {{--                  <br><span class="alert alert-info">Please update the leaderboard</span>--}}
                        {{--                     @endif--}}
                        {{--                  @endif--}}
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                                    Super Admin&nbsp;<span class="arrow-icon"></span>
                                @elseif(session('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                    Support Admin&nbsp;<span class="arrow-icon"></span>
                                @elseif(session('admin') == \App\Http\Controllers\Enum\AdminRole::internAdmin)
                                    Intern&nbsp;<span class="arrow-icon"></span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                @if(session('admin') == 'superadmin')
                                    <li><a href="{{ url('admin/settings') }}"><i class="glyphicon glyphicon-cog pull-right"></i> Settings</a>
                                    </li>
                                @endif
                                <li><a href="{{ url('adminLogout') }}"><i class="logout-icon pull-right"></i> Log Out</a></li>
                            </ul>
                        </li>
                        <li>
                            @if($admin_notification_count > 0)
                                <a href="{{url('admin/all_notifications')}}">
                                    &nbsp;<i class="bell-icon new_admin_notification" style="color:red;"></i>
                                    <span id="admin_notification_number" class="notify_num">{{$admin_notification_count}}</span>
                                </a>
                            @else
                                <a href="{{url('admin/all_notifications')}}">
                                    &nbsp;<i class="bell-icon new_admin_notification"></i>
                                    <span id="admin_notification_number"></span>
                                </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- /top navigation -->
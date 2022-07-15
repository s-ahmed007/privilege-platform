<?php
   use App\BranchUser;
   //days remaining of this partner
   $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.branch.info')->first();
   $curDate = date("Y-m-d");
   $cur_date = new DateTime($curDate);
   $expiry_date = new DateTime($user->branchScanner->branch->info->expiry_date);
   $interval = date_diff($cur_date, $expiry_date);
   $daysRemaining = $interval->format('%R%a');
   //create session of partner name
   session(['partner_name' => $user->branchScanner->branch->info->partner_name]);
?>
{{--logout if already deactive or expired--}}
@if($daysRemaining <= 0)
<script>
   var url = "{{ url('/partner_logout') }}";

   $('<form action="' + url + '" method="POST">' +
      '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
      '</form>').appendTo($(document.body)).submit();
</script>
@elseif($user->active == 0 || $user->branchScanner->branch->active == 0 || $user->branchScanner->branch->info->account->active == 0)
<script>
   var url = "{{ url('/partner_logout') }}";

   $('<form action="' + url + '" method="POST">' +
      '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
      '</form>').appendTo($(document.body)).submit();
</script>
@endif
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <meta name="author" content="">
      <link rel="icon" type="image/png" sizes="16x16" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo-merchant.png">
      <title>Royalty Merchant | royaltybd.com</title>
      <!-- Bootstrap Core CSS -->
      <link href="{{asset('partner-dashboard/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
      <!-- Menu CSS -->
      <link href="{{asset('partner-dashboard/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css')}}" rel="stylesheet">
      <!-- toast CSS -->
      <link href="{{asset('partner-dashboard/plugins/bower_components/toast-master/css/jquery.toast.css')}}" rel="stylesheet">
      <!-- morris CSS -->
      <link href="{{asset('partner-dashboard/plugins/bower_components/morrisjs/morris.css')}}" rel="stylesheet">
      <!-- chartist CSS -->
      <link href="{{asset('partner-dashboard/plugins/bower_components/chartist-js/dist/chartist.min.css')}}" rel="stylesheet">
      <link href="{{asset('partner-dashboard/plugins/bower_components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css')}}" rel="stylesheet">
      <!-- animation CSS -->
      <link href="{{asset('partner-dashboard/css/animate.css')}}" rel="stylesheet">
      <!-- Custom CSS -->
      <link href="{{asset('partner-dashboard/css/style.css')}}" rel="stylesheet">
      <script src="https://kit.fontawesome.com/9e60b11f48.js" crossorigin="anonymous"></script>
      <!-- color CSS -->
      <link href="{{asset('partner-dashboard/css/colors/default.css')}}" id="theme" rel="stylesheet">
      <link href="{{asset('css/merchant.css')}}" rel="stylesheet">
      <link href="{{asset('css/partner-navbar.css')}}" rel="stylesheet">
      <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
      <style>
         .notify_num{
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
         .notify-img img{
         width: 45px;
         height: 45px;
         /* margin: 0 auto 8px; */
         display: block;
         }
         .unseen_notification {
         background-color: #e7e7e7 !important;
         }
      </style>
   </head>
   <body class="fix-header">
      <div class="preloader">
         <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
         </svg>
      </div>
      <div id="wrapper">
      <nav class="navbar navbar-default navbar-static-top m-b-0">
         <div class="navbar-header">
            <div class="top-left-part">
               <a class="logo" href="{{url('partner/branch/dashboard')}}">
                  <img src="{{session('partner_pro_img')}}" alt="Profile Image" width="50" height="50" style="display: inline-block;">
                  <h5 style="display: inline-block; font-family: Muli, Bangla1015, sans-serif; overflow: hidden;
                        white-space: nowrap; text-overflow: ellipsis; width: 150px;" class="dots">{{session('partner_name')}}<br>
                     <!-- @if(session('branch_user_role') == \App\Http\Controllers\Enum\BranchUserRole::branchOwner)
                     <small>{{__('partner/common.owner')}}</small>
                     @else
                     <small>{{__('partner/common.employee')}}</small>
                     @endif -->
                  </h5>
               </a>
            </div>
            <ul class="nav navbar-top-links navbar-right pull-right">
            <li>
                  <a class="dropdown-toggle bell" data-toggle="dropdown" role="button" aria-haspopup="true"
                     aria-expanded="false">{{__('partner/common.language')}}
                  </a>
                  <ul class="dropdown-menu">
                     <li><a href="{{url('partner/branch/setLocale/en')}}">English</a></li>
                     <li><a href="{{url('partner/branch/setLocale/bn')}}">বাংলা</a></li>
                  </ul>
               </li>
                <li>
                    <a>
                    <i class="fa fa-phone"></i>&nbsp; 01312620202
                </a>
            </li>
               <li>
                  @if(count($allNotifications) != 0)
                     <a class="dropdown-toggle bell" data-toggle="dropdown" role="button" aria-haspopup="true"
                        aria-expanded="false"><i class="fa fa-bell"></i>
                     @if($allNotifications->unseen_count != 0)
                     <span class="notify_num merchant_request_count">{{$allNotifications->unseen_count}}</span>
                     @endif
                     </a>
                  <ul class="dropdown-menu notify-drop">
                     <div class="notify-drop-title">
                        <div class="row">
                           <div class="col-md-12 col-sm-12 col-xs-12">
                              <p>{{__('partner/notification.your_notifications')}}</p>
                           </div>
                        </div>
                     </div>
                     <div class="drop-content" id="partner_notifications">
                        <?php
                           if (count($allNotifications)>0){
                               $notifications = (new \App\Http\Controllers\TransactionRequest\v2\functionController())
                                   ->getNotificationView($allNotifications);
                               echo $notifications;
                           }else{
                               echo "<li class=\"no_notif\">".__('partner/notification.no_notification')."</li>";
                           }
                           ?>
                     </div>
                     <div class="notify-drop-footer text-center">
                        <a href="{{url('partner/branch/notification/all')}}">{{__('partner/notification.view_all_notifications')}}
                        </a>
                     </div>
                     {{--when merchant doesn't have any notification ends--}}
                  </ul>
                  @else
                  <a class="dropdown-toggle bell" data-toggle="dropdown" role="button" aria-haspopup="true"
                     aria-expanded="false"><i class="fa fa-bell"></i>
                  <span class="merchant_request_count"></span>
                  </a>
                  <!-- {{--                    <a href="#" class="dropdown-toggle bell" data-toggle="dropdown" role="button" aria-haspopup="true"--}}
                     {{--                       aria-expanded="false"><i class="fa fa-bell"></i>--}}
                     {{--                        <span class="notify_num merchant_request_count"></span>--}}
                     {{--                    </a>--}} -->
                  <ul class="dropdown-menu notify-drop">
                     <div class="notify-drop-title">
                        <div class="row">
                           <div class="col-md-12 col-sm-12 col-xs-12">
                              <p>Your Notifications</p>
                           </div>
                        </div>
                     </div>
                     <div class="drop-content" id="partner_notifications">
                        <li class="no_notif">{{__('partner/notification.no_notification')}}</li>
                     </div>
                     {{--when merchant doesn't have any notification ends--}}
                  </ul>
                  @endif
               </li>
               <li>
                  <a class="nav-link" href="{{url('partner/branch/point_prizes')}}">
                  <button class="btn btn-primary" style="background-color:#FFC82C; color: black !important; font-weight: bold;border: 1px solid #ffc82c;">
                  {{__('partner/common.point_balance')}} {{$point}}
                  </button>
                  </a>
               </li>
               <li>
                  <a class="nav-toggler open-close waves-effect waves-light hidden-md hidden-lg" href="javascript:void(0)"><i class="fa fa-bars"></i></a>
               </li>
               <li>
                  <a class="profile-pic" style="cursor: pointer;" onclick="merchantLogout()">{{__('partner/common.logout')}}</a>
               </li>
            </ul>
         </div>
      </nav>
      <div class="navbar-default sidebar" role="navigation" style="background:unset">
         <div class="sidebar-nav slimscrollsidebar">
            <div class="sidebar-head">
               <h3><span class="fa-fw open-close"><i class="ti-close ti-menu"></i></span> <span class="hide-menu">Navigation</span></h3>
            </div>
            <ul class="nav" id="side-menu">
               <li>
                  <a href="{{url('partner/branch/requests')}}" class="waves-effect">
                     <i class="fa fa-credit-card fa-fw" aria-hidden="true"></i>{{__('partner/sidebar.checkout_request')}}</a>
               </li>
               @if(session('branch_user_role') == \App\Http\Controllers\Enum\BranchUserRole::branchOwner)
               <li>
                  <a href="{{url('partner/branch/dashboard')}}" class="waves-effect">
                  <i class="fa fa-columns fa-fw"></i>{{__('partner/common.dashboard')}}</a>
               </li>
               @endif
               <li>
                  <a href="{{url('partner/branch/how_it_works')}}" class="waves-effect">
                  <i class="fas fa-question fa-fw"></i>{{__('partner/common.how_to_use')}}</a>
               </li>
               <li>
                  <a href="{{url('partner/branch/transactions')}}" class="waves-effect">
                  <i class="fa fa-money fa-fw"></i>{{__('partner/common.all_transactions')}}</a>
               </li>
               @if(session('branch_user_role') == \App\Http\Controllers\Enum\BranchUserRole::branchOwner)
                  <li>
                     <a href="{{url('partner/branch/review')}}" class="waves-effect">
                        <i class="fa fa-edit fa-fw" aria-hidden="true"></i>{{__('partner/common.all_reviews')}}</a>
                  </li>
               @endif
               <!-- {{--                    @if(session('branch_user_role') == \App\Http\Controllers\Enum\BranchUserRole::branchOwner)--}}
                  {{--                    <li>--}}
                  {{--                        <a href="{{url('partner/branch/peak_hour')}}" class="waves-effect">--}}
                  {{--                            <i class="fa fa-columns fa-fw" aria-hidden="true"></i>Transaction Peak Hour</a>--}}
                  {{--                    </li>--}}
                  {{--                    @endif--}} -->
               <li>
                  <a href="{{url('partner/branch/leaderboard')}}" class="waves-effect">
                  <i class="fa fa-users fa-fw" aria-hidden="true"></i>{{__('partner/sidebar.leaderboard')}}</a>
               </li>
               <li>
                  <a href="{{url('partner/branch/profile')}}" class="waves-effect">
                  <i class="fa fa-user fa-fw" aria-hidden="true"></i>{{__('partner/sidebar.profile')}}</a>
               </li>
               <li>
                  <a href="{{url('partner/branch/offers')}}" class="waves-effect">
                  <i class="fa fa-tags fa-fw" aria-hidden="true"></i>{{__('partner/common.my_offers')}}</a>
               </li>
{{--               <li>--}}
{{--                  <a href="{{url('partner/branch/deals')}}" class="waves-effect">--}}
{{--                  <i class="fas fa-percentage fa-fw" aria-hidden="true"></i>{{__('partner/common.my_deals')}}</a>--}}
{{--               </li>--}}
               <li>
                  <a href="{{url('partner/branch/rewards')}}" class="waves-effect">
                  <i class="fa fa-gift fa-fw" aria-hidden="true"></i>{{__('partner/common.customer_rewards')}}</a>
               </li>
               @if(session('branch_user_role') == \App\Http\Controllers\Enum\BranchUserRole::branchOwner)
               <li>
                  <a href="{{url('partner/branch/post')}}" class="waves-effect">
                  <i class="fa fa-rss fa-fw" aria-hidden="true"></i>{{__('partner/sidebar.manage_newsfeed_post')}}</a>
               </li>
               <li>
                  <a href="{{url('partner/branch/offer_request')}}" class="waves-effect">
                  <i class="fa fa-reply fa-fw" aria-hidden="true"></i>{{__('partner/common.request_new_offer')}}</a>
               </li>
               @endif
            </ul>
         </div>
      </div>
      <div id="page-wrapper">
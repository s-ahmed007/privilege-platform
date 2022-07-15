@if(!session()->has('partner_id'))
<script>
   window.location = "{{ url('/') }}";
</script>
@endif
<?php use \App\Http\Controllers\functionController; ?>
@include('header')
<style>
   .page_loader {
   position: fixed;
   width: 100%;
   height: 100%;
   top: 0;
   left: 0;
   background-color: #969696;
   z-index: 999;
   opacity: .5;
   }
   .page_loader img {
   position: fixed;
   top: 50%;
   left: 50%;
   }
</style>
<link href="{{asset('css/animate.min.css')}}" rel="stylesheet">
{{--loading gif--}}
<div class="page_loader" style="display: none;">
   <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif" alt="Royalty Loading GIF" class="lazyload" Royalty gif">
</div>
<div class="container partner-account-container">
   <div class="row">
      <div class="acc-navbar col-md-3 col-sm-12 col-xs-12">
         <div class="partner-acc-navbar-header">
            {{--Partner Profile Picture--}}
            <?php $pname = str_replace("'", "", Session::get('partner_name')); ?>
            <a href="{{ url('partner-profile/'.$pname.'/'.Session::get('current_branch_id')) }}" target="_blank">
            <img src="{{ asset(Session::get('partner_profile_image')) }}" class="img-circle customer-acc-profile-image lazyload" Royalty partner">
            </a>
            {{--Partner Name--}}
            <a href="{{ url('partner-profile/'.$pname.'/'.Session::get('current_branch_id')) }}" target="_blank">
               <p class="partner-acc-navbar-name mtb-10">{{Session::get('partner_name').' ('.$partner_data->partner_area.')'}}</p>
            </a>
            <div class="mtb">
               {{--If 10 days remaining--}} @if(Session::get('yearRemaining')
               <='+0' && Session::get( 'monthRemaining') <='+0' && Session::get( 'daysRemaining') < '+10' && Session::get( 'daysRemaining')> '+0')
               <span class="btn btn-primary">
               Active
               <i class="smile-icon"></i>
               </span> {{--If expired--}} @elseif(Session::get('yearRemaining')
               <='+0' && Session::get( 'monthRemaining') <='+0' && Session::get( 'daysRemaining') <='+0' ) <span class="btn btn-primary">
               <i class="circle-icon redbtn"></i> Expired
               <i class="sad-icon"></i>
               </span>
               @else {{--If active--}}
               <span class="btn btn-primary">
               <i class="circle-icon greenbtn"></i>
               Active
               <i class="smile-icon"></i>
               </span> @endif
            </div>
            @if(count($allBranches->branches) > 1)
            <div style="margin-bottom: 10px;">
               <a href="#" data-toggle="modal" data-target="#branchModal" style="color: white;">
                  <div class="btn btn-primary" style="margin-bottom: 10px;">My Branches</div>
               </a>
            </div>
            @endif
            @if(isset($followers_info))
               <a href="#" data-toggle="modal" data-target="#followersModal">
                  <div>
                     <button class="btn btn-primary">
                     <i class="user-icon"></i>&nbsp;
                     {{'Followers('.(count($followers_info)).')'}}
                     </button>
                  </div>
               </a>
            @endif
         </div>
         <ul class="nav nav-pills nav-stacked" id="customer-acc-nav-pills">
            <li class="active">
               <a href="#Info" data-toggle="pill">
               <i class='bx bx-info-circle' ></i>Business Info
               </a>
            </li>
            <li>
               <a href="#Dashboard" data-toggle="pill">
               <i class='bx bx-stats' ></i>Stats Dashboard
               </a>
            </li>
            <li>
               <a href="#Transaction-History" data-toggle="pill">
               <i class="transaction-icon"></i>Offers Availed
               </a>
            </li>
            <li><a href="#Ratings-Reviews" data-toggle="pill">
               <i class="review-icon"></i>Ratings & Reviews
               </a>
            </li>
            <li>
               <a href="#posts" data-toggle="pill" id="post_in_account">
               <i class='bx bx-news' ></i>News Posts
               </a>
            </li>
         </ul>
      </div>
      <div class="tab-content col-md-9 col-sm-12 col-xs-12">
         <div class="tab-pane active" id="Info">
            <div class="row">
               <header class="banner">
                  <h1>BUSINESS INFORMATION</h1>
               </header>
               <!-- PERSONAL INFORMATION -->
               <div class="col-md-6 col-sm-12 col-xs-12 mtb-10">
                  <div class="pn">
                     <h3 class="partner-account-dashboard-personal-header">PERSONAL INFO</h3>
                     <div class="personal-info-details-container">
                        <table class="table" style="margin-bottom: unset">
                           <tbody>
                              <tr>
                                 <td>
                                    <p class="personal-info-p">
                                       ✉️ {{ $partner_data->partner_email }}
                                    </p>
                                 </td>
                              </tr>
                              <tr>
                                 <td>
                                    <p class="personal-info-p">
                                       📞 {{ $partner_data->partner_mobile }}
                                    </p>
                                 </td>
                              </tr>
                              <tr>
                                 <td>
                                    <p class="personal-info-p">
                                       <?php
                                          $date = date_create(Session::get('partner_expiry_date'));
                                          ?>
                                       📅 Expiry Date: {{date_format($date, "d-m-Y")}}
                                    </p>
                                 </td>
                              </tr>
                              <tr>
                                 <td>
                                    <p class="personal-info-p">
                                       🏬 {{ $partner_data->partner_address }}
                                    </p>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
               <!-- BANNER -->
               <div class="col-md-6 col-sm-12 col-xs-12 mtb-10">
                  <div id="text-carousel" class="carousel slide" data-ride="carousel">
                     <div class="row">
                        <div class="col-xs-12">
                           <div class="carousel-inner">
                              <div class="item active">
                                 <div class="carousel-content">
                                    <div>
                                       <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/partner-account/banner/b1.png"
                                          style="width: 100%" class="lazyload" Royalty partner">
                                    </div>
                                 </div>
                              </div>
                              <div class="item">
                                 <div class="carousel-content">
                                    <div>
                                       <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/partner-account/banner/b2.jpg"
                                          style="width: 100%" class="lazyload" Royalty partner">
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <a class="left carousel-control" href="#text-carousel" data-slide="prev"
                        style="background-image: none">
                     <span class="glyphicon glyphicon-chevron-left"></span>
                     </a>
                     <a class="right carousel-control" href="#text-carousel" data-slide="next"
                        style="background-image: none">
                     <span class="glyphicon glyphicon-chevron-right"></span>
                     </a>
                  </div>
               </div>
            </div>
         </div>
         <div class="tab-pane" id="Dashboard">
            <div class="row">
               <header class="banner">
                  <h1>STATS DASHBOARD</h1>
               </header>
               <div class="col-md-8 col-sm-12 col-xs-12">
                  <div class="mtb">
                     <div>
                        <h3 class="partner-account-cards-title">STATS</h3>
                     </div>
                     <div class="clearfix statsbox">
                        <div class="col-md-3 col-sm-6 col-xs-6">
                           <div class="acc-stat-image-container">
                              <div class="partner-account-star-image-dashboard-container">
                                 <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/card_used.png" Royalty partner account" class="partner-account-star-image-dashboard lazyload">
                              </div>
                              <h4 class="partner-account-dashboard-stats-number2">{{ Session::get('cardUsed') }}</h4>
                              <span class="bold">
                              <?php if (Session::get('cardUsed') > 1) echo 'Cards Used'; else echo 'Card Used';?>
                              </span>
                           </div>
                           <p class="dashboard-col-text center">Total Cards used in store</p>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-6">
                           <div class="acc-stat-image-container">
                              <div class="partner-account-star-image-dashboard-container">
                                 <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/customers_visited.png" Royalty partner account" class="partner-account-star-image-dashboard lazyload">
                              </div>
                              <h4 class="partner-account-dashboard-stats-number3">{{ Session::get('customerNumber') }}</h4>
                              <span class="bold">
                              {{ Session::get('customerNumber') > 1 ? 'Customers' : 'Customer'}}</span>
                           </div>
                           <p class="dashboard-col-text center">Total Customers visited </p>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-6">
                           <div class="acc-stat-image-container">
                              <div class="partner-account-star-image-dashboard-container">
                                 <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/star_giving.png" Royalty partner account" class="partner-account-star-image-dashboard lazyload">
                              </div>
                              <h4 class="partner-account-dashboard-stats-number1">
                                 {{ session()->get('partner_average') != 0 ? round(session()->get('partner_average'),1) : 0.0 }} 
                              </h4>
                              <span class="bold">Rating </span>
                           </div>
                           <p class="dashboard-col-text center">Your Average Rating</p>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-6">
                           <div class="acc-stat-image-container">
                              <div class="partner-account-star-image-dashboard-container">
                                 <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/dashboard_review.png" Royalty partner account" class="partner-account-star-image-dashboard lazyload">
                              </div>
                              <h4 class="partner-account-dashboard-stats-number4"> {{ session()->get('partner_totalReviews') }}</h4>
                              <span class="bold">
                              {{ session()->get('partner_totalReviews') > 1 ? 'Reviews' : 'Review'}}
                              </span>
                           </div>
                           <p class="dashboard-col-text center">Total Customer Reviews</p>
                        </div>
                     </div>
                     <div class="clearfix info_tran_user_review">
                        {{--<div class="col-md-6 col-sm-12 col-xs-12 mtb ">--}}
                           {{--<div class="white-panel pn">--}}
                              {{--<h3 class="partner-account-dashboard-top-transaction-header">--}}
                                 {{--TOP TRANSACTIONS--}}
                              {{--</h3>--}}
                              {{--<div class="partner-account-dashboard-top-transactions-body">--}}
                                 {{--@if(isset($transactionHistory['top_transaction']) && count($transactionHistory['top_transaction']) >0 )--}}
                                    {{--@foreach($transactionHistory['top_transaction'] as $top_tran)--}}
                                    {{--<div class="top-transactions-details-container">--}}
                                       {{--<span class="top-transactions-profile-image">--}}
                                       {{--<img src="{{asset($top_tran['customer_profile_image'])}}" class="lazyload" Royalty user"/>--}}
                                       {{--</span>--}}
                                       {{--<span class="top-transactions-name">--}}
                                         {{--<a--}}
                                             {{--href="{{url('user-profile/'.$top_tran['customer_username'])}}"--}}
                                             {{--target="_blank">--}}
                                          {{--<a>--}}
                                          {{--<p class="dots"--}}
                                             {{--style="width: 103px;">{{$top_tran['customer_first_name'].' '.$top_tran['customer_last_name']}}</p>--}}
                                          {{--</a>--}}
                                       {{--</span>--}}
                                       {{--<span class="top-transactions-amount">--}}
                                       {{--<b>৳{{round($top_tran['amount_spent'])}}</b>--}}
                                       {{--</span>--}}
                                    {{--</div>--}}
                                    {{--@endforeach--}}
                                 {{--@else--}}
                                 {{--<div class="no-info">--}}
                                    {{--<p style="font-weight: 700;">No transactions</p>--}}
                                 {{--</div>--}}
                                 {{--@endif--}}
                              {{--</div>--}}
                           {{--</div>--}}
                        {{--</div>--}}
                        <div class="col-md-6 col-sm-12 col-xs-12 mtb-10">
                           <!-- TOP USER -->
                           <div class="white-panel pn">
                              <h3 class="partner-account-dashboard-top-users-header">TOP ROYALS</h3>
                              <div class="partner-account-dashboard-top-users-body">
                                 @if(isset($topUsers) && count($topUsers) > 0)
                                    @foreach($topUsers as $user)
                                       <div class="top-users-details-container">
                                          <span class="top-users-profile-image">
                                          <img src="{{asset($user->info->customer_profile_image)}}" class="lazyload" Royalty user" />
                                          </span>
                                          <span class="top-users-name">
                                             {{--<a--}}
                                                {{--href="{{url('user-profile/'.$user['info']['customer_username'])}}"--}}
                                                {{--target="_blank">--}}
                                             <a>
                                                {{--<p class="dots" style="width: 103px;">{{$user->info->customer_full_name}} </p>--}}
                                                <p class="dots">{{$user->info->customer_full_name}} </p>
                                             </a>
                                          </span>
                                          <span class="top-users-amount">
{{--                                          <b>{{$user->total_trans}}</b>--}}
                                          </span>
                                       </div>
                                    @endforeach
                                 @else
                                 <div class="no-info">
                                    <p>No Royals</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 mtb-10">
                           <div class="white-panel pn">
                              <h3 class="partner-account-dashboard-top-followers-header">TOP
                                 REVIEWERS
                              </h3>
                              <div class="partner-account-dashboard-top-followers-body">
                                 @if(isset($topReviewers) && count($topReviewers) > 0) @foreach($topReviewers as $reviewer)
                                 <div class="top-followers-details-container">
                                    <span class="top-followers-profile-image">
                                    <img src="{{asset($reviewer['info']['customer_profile_image'])}}" class="lazyload" Royalty user"/>
                                    </span>
                                    <span class="top-followers-name">
                                       <!-- <a
                                          href="{{url('user-profile/'.$reviewer['info']['customer_username'])}}"
                                          target="_blank"> -->
                                          <a>
                                          <p class="dots"
                                             style="width: 103px;">{{$reviewer['info']['customer_first_name'].' '.$reviewer['info']['customer_last_name']}}</p>
                                       </a>
                                    </span>
                                    <span class="top-followers-reviews">
                                    <i class="review-icon"></i>{{$reviewer['total_review']}}
                                    </span>
                                 </div>
                                 @endforeach @else
                                 <div class="no-info">
                                    <p>No Reviewers</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- *********************RIGHT SIDEBAR CONTENT STARTS**************** -->
               <div class="col-md-4 col-sm-12 col-xs-12 recent_trans_followers">
                  <!-- SELECT A BRANCH -->
                  <div class="recent-transactions-container mtb-10">
                  <h3 class="graybox-head">RECENTLY AVAILED OFFERS</h3>
                     <div class="recent-transactions-body">
                        @if(count($transactionHistory['transactions']) == 0 )
                        <div class="no-info">
                           <p style="font-weight: 700;">No transactions</p>
                        </div>
                        @elseif(count($transactionHistory['transactions']) < 5 )
                           @foreach($transactionHistory[ 'transactions'] as $tr)
                              <div class="transactions-line">
                                 <p><i class="transaction-icon"></i></p>
                                 <p class="transaction-history-time">
                                    <?php
                                       $posted_on = date("Y-M-d H:i:s", strtotime($tr['posted_on']));
                                       $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                       ?>
                                    {{$created->diffForHumans()}}
                                 </p>
                                 <p class="transaction-history-details">
                                       {{--<a--}}
                                       {{--href="{{url('user-profile/'.$tr['customer_username'])}}">--}}
                                    {{--{{$tr['customer_first_name'].' '.$tr['customer_last_name']}}--}}
                                    {{--</a>--}}
                                    <b>{{$tr['customer']['customer_full_name']}}</b> availed an offer.
                                 </p>
                              </div>
                           @endforeach
                        @elseif(count($transactionHistory['transactions']) >= 5)
                           @for($i=0; $i < 5; $i++)
                        <div class="transactions-line">
                           <p><i class="transaction-icon"></i></p>
                           <p class="transaction-history-time">
                              <?php
                                 $posted_on = date("Y-M-d H:i:s", strtotime($transactionHistory['transactions'][$i]['posted_on']));
                                 $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                 ?>
                              {{$created->diffForHumans()}}
                           </p>
                           <p class="transaction-history-details">
                                 {{--<a--}}
                                 {{--href="{{url('user-profile/'.$transactionHistory['transactions'][$i]['customer_username'])}}"--}}
                                 {{--target="_blank">--}}
                                 {{--<a>--}}
                              <b>{{$transactionHistory['transactions'][$i]['customer']['customer_full_name']}}</b> availed an offer.
                           </p>
                        </div>
                        @endfor @endif
                     </div>
                  </div>
                  <!-- recent followers -->
                  {{--<div class="mtb">--}}
                     {{--<div class="white-panel pn">--}}
                        {{--<h3 class="partner-account-dashboard-top-users-header">RECENT FOLLOWERS</h3>--}}
                        {{--<div class="partner-account-dashboard-top-users-body">--}}
                           {{--@if(isset($recentFollowers) && count($recentFollowers) > 0)--}}
                              {{--@foreach($recentFollowers as $recentFollower)--}}
                              {{--<div class="top-users-details-container">--}}
                                 {{--<span class="top-users-profile-image">--}}
                                 {{--@if($recentFollower['customer_profile_image'] != '')--}}
                                 {{--<img src="{{asset($recentFollower['customer_profile_image'])}}" class="lazyload" Royalty user"/>--}}
                                 {{--@else--}}
                                 {{--<img src="{{ asset('images/user.png') }}" class="lazyload" Royalty user"/>--}}
                                 {{--@endif--}}
                                 {{--</span>--}}
                                 {{--<span class="top-users-name">--}}
                                 {{--<!-- <a--}}
                                    {{--href="{{url('user-profile/'.$recentFollower['customer_username'])}}"--}}
                                    {{--target="_blank"> -->--}}
                                    {{--<a>--}}
                                 {{--{{$recentFollower['customer_first_name'].' '.$recentFollower['customer_last_name']}}--}}
                                 {{--</a>--}}
                                 {{--</span>--}}
                              {{--</div>--}}
                              {{--@endforeach--}}
                           {{--@else--}}
                           {{--<div class="no-info">--}}
                              {{--<p>No Followers</p>--}}
                           {{--</div>--}}
                           {{--@endif--}}
                        {{--</div>--}}
                     {{--</div>--}}
                  {{--</div>--}}
               </div>
            </div>
         </div>
         @if(isset($transactionHistory))
         <div id="Transaction-History" class="tab-pane">
            <div class="row m-0">
               <header class="banner">
                  <h1>OFFERS AVAILED</h1>
               </header>
               <form class="form-horizontal form-label-left mtb-10">
                  {{csrf_field()}}
                  <div class="form-group center m-0">
                     <div class="col-md-4 sort-head">Sort by Month/Year</div>
                     <div class="col-md-4 sort-year">
                        <select class="form-control" id="sortPartTranHisYear" onchange="SortPartTranHis()">
                           <option value="all">Year</option>
                           <?php
                              for ($i = 2018; $i <= date('Y'); $i++) {
                                  echo "<option value='$i'>$i</option>";
                              }
                              ?>
                        </select>
                     </div>
                     <div class="col-md-4 sort-month">
                        <select class="form-control" id="sortPartTranHisMonth" onchange="SortPartTranHis()">
                           <option value="all">Month</option>
                           <option value="01">January</option>
                           <option value="02">February</option>
                           <option value="03">March</option>
                           <option value="04">April</option>
                           <option value="05">May</option>
                           <option value="06">June</option>
                           <option value="07">July</option>
                           <option value="08">August</option>
                           <option value="09">September</option>
                           <option value="10">October</option>
                           <option value="11">November</option>
                           <option value="12">December</option>
                        </select>
                     </div>
                  </div>
               </form>
               <div class="acc-transaction-container" id="partner_tran_his">
                  @if(count($transactionHistory['transactions']) > 0)
                  <div class="table" style="text-align: center">
                     <div class="row header table_row">
                        <div class="cell">Date & Time</div>
                        <div class="cell">Customer Name</div>
                        <div class="cell">Points</div>
                        <div class="cell">Offers Availed</div>
                     </div>
                     <?php
                        //initialize total coupon used variable
                        $total_coupon_used = 0;
                        ?>
                     @foreach($transactionHistory['transactions'] as $tr)
                     <div class="row table_row">
                        <div class="cell" data-title="Date & Time">
                           <?php
                              $posted_on = date("Y-M-d H:i:s", strtotime($tr->posted_on));
                              $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                              echo date_format($created, "d-m-y &#9202 h:i A");
                           ?>
                        </div>
                        <div class="cell" data-title="Customer Name">
                           <b> {{$tr->customer->customer_full_name}}</b>
                        </div>
                        <div class="cell" data-title="Points">
                           {{$tr->transaction_point}}
                        </div>
                        <div class="cell" data-title="Offer Availed">
                           @if($tr->offer != null)
                              {{$tr->offer->offer_description}}
                           @elseif($tr->bonus != null)
                              {{$tr->bonus->coupon->coupon_details}}
                           @else
                              {{"Discount Availed"}}
                           @endif
                        </div>
                     </div>
                     @endforeach
                     <div class="row table_row">
                        <div class="cell total" data-title="Total">
                        </div>
                        <div class="cell" data-title="Customer Name">
                           <i class="minus-icon"></i>
                        </div>
                        <div class="cell" data-title="Points">
                           <b>{{$transactionHistory['total_point']}}</b>
                        </div>
                        <div class="cell" data-title="Offers Availed">
                           <i class="minus-icon"></i>
                        </div>
                     </div>
                  </div>
                  @else
                  <h4 class="no-info">No transactions have been made yet.</h4>
                  @endif
               </div>
            </div>
         </div>
         @endif {{--Reviews section--}}
         <div id="Ratings-Reviews" class="tab-pane">
            <div class="row">
               <header class="banner">
                  <h1>RATINGS & REVIEWS</h1>
               </header>
               <div class="make_review col-md-12" id="reviews">
                  {{--rating summary--}}
                  <div class="row rating_table">
                     <p class="rating-head">Rating</p>
                     <div class="col-md-5 col-sm-5 col-xs-12 partner-acc-overall-rating">
                        <span class="overall-rating">
                        <span>{{ isset($starAverage['average_rating']) ? round($starAverage['average_rating'],1) : '0.0' }}</span>
                        <span>/ 5</span>
                        </span>
                        <br>
                        <div class="overall-rating-info mtb-10">
                           <div class="overall-rating-star">
                              @if(isset($starAverage['average_rating']))
                                 @if($starAverage['average_rating'] == 1)
                                    <i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                                    <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                                 @elseif($starAverage['average_rating'] == 2)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i>
                                    <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                                 @elseif($starAverage['average_rating'] == 3)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                                    <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                                 @elseif($starAverage['average_rating'] == 4)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                                    <i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i>
                                 @elseif($starAverage['average_rating'] == 5)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                                 @elseif($starAverage['average_rating']>1.0 && $starAverage['average_rating']<=1.5)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i><i class="bx bx-star yellow"></i>
                                    <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                                 @elseif($starAverage['average_rating']>1.5 && $starAverage['average_rating']< 2.0)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i>
                                    <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                                 @elseif($starAverage['average_rating']>2.0 && $starAverage['average_rating']<=2.5)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i>
                                    <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                                 @elseif($starAverage['average_rating']>2.5 && $starAverage['average_rating']< 3.0)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                                    <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                                 @elseif($starAverage['average_rating']>3.0 && $starAverage['average_rating']<=3.5)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                                    <i class="bx bxs-star-half yellow"></i><i class="bx bx-star yellow"></i>
                                 @elseif($starAverage['average_rating']>3.5 && $starAverage['average_rating']< 4.0)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                                    <i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i>
                                 @elseif($starAverage['average_rating']>4.0 && $starAverage['average_rating']<=4.5)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i>
                                 @elseif($starAverage['average_rating']>4.5 && $starAverage['average_rating']<=5.0)
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                                    <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
                                 @else
                                    <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                                    <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
                                 @endif
                              @endif
                           </div>
                              <!-- @if(count($reviews) > 0) 
                              <p>
                                 Based on {{count($reviews) > 1 ? count($reviews).' Reviews' : '1 Review' }} </p>
                              @else 
                              <p>No reviews yet<p>
                              @endif -->
                        </div>
                     </div>
                     <div class="col-md-6 col-sm-6 col-xs-12 star_rating_box center">
                        <div class="ratebox">
                           <div>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bxs-star yellow"></i>
                           </div>
                           <div class="progress">
                              <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"
                                 style="width:{{ round($starAverage['5_star']).'%'}};{{round($starAverage['5_star']) == 0 ? 'background-color: unset' : ''}}">
                                 <!-- {{ round($starAverage['5_star']).'%'}} -->
                              </div>
                           </div>
                           <div>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bx-star yellow"></i>
                           </div>
                           <div class="progress">
                              <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"
                                 style="width:{{ round($starAverage['4_star']).'%'}};{{round($starAverage['4_star']) == 0 ? 'background-color: unset' : ''}}">
                                 <!-- {{ round($starAverage['4_star']).'%'}} -->
                              </div>
                           </div>
                           <div>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bx-star yellow"></i>
                              <i class="bx bx-star yellow"></i>
                           </div>
                           <div class="progress">
                              <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"
                                 style="width:{{ round($starAverage['3_star']).'%'}};{{round($starAverage['3_star']) == 0 ? 'background-color: unset' : ''}}">
                                 <!-- {{ round($starAverage['3_star']).'%'}} -->
                              </div>
                           </div>
                           <div>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bx-star yellow"></i>
                              <i class="bx bx-star yellow"></i>
                              <i class="bx bx-star yellow"></i>
                           </div>
                           <div class="progress">
                              <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"
                                 style="width:{{ round($starAverage['2_star']).'%'}};{{round($starAverage['2_star']) == 0 ? 'background-color: unset' : ''}}">
                                 <!-- {{ round($starAverage['2_star']).'%'}} -->
                              </div>
                           </div>
                           <div>
                              <i class="bx bxs-star yellow"></i>
                              <i class="bx bx-star yellow"></i>
                              <i class="bx bx-star yellow"></i>
                              <i class="bx bx-star yellow"></i>
                              <i class="bx bx-star yellow"></i>
                           </div>
                           <div class="progress">
                              <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"
                                 style="width:{{ round($starAverage['1_star']).'%'}};{{round($starAverage['1_star']) == 0 ? 'background-color: unset' : ''}}">
                                 <!-- {{ round($starAverage['1_star']).'%'}} -->
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-1 col-sm-1 col-xs-hidden"></div>
                  </div>
                  {{--reviews--}}
                  <div class="review-container">
                     <p class="reviews-head" style="padding: 30px;"><u>
                        @if(count($reviews) > 0)
                        {{count($reviews) > 1 ? count($reviews).' Reviews' : '1 Review' }}
                        @else
                        No reviews yet
                        @endif
                        </u>
                     </p>
                     @if(isset($reviews) && count($reviews) > 0)
                     <!--  {{-- variable comes from "profilefromoffer" function in homeController --}} -->
                     <?php $row = count($reviews); ?>
                     @for($i=0; $i
                     < $row; $i++) 
                     <div class="whitebox-inner-box-inner">
                        <div class="row">
                           <div class="col-md-2 col-sm-2 col-xs-3">
                              <!-- User profile picture -->
                              <div class="comment-avatar center">
                                 {{--<a href="{{url('user-profile/'.$reviews[$i]['customer_username'])}}" target="_blank">--}}
                                 <img src="{{ asset($reviews[$i]['customer_profile_image'])}}" class="img-circle img-30 primary-border lazyload" Royalty user">
                                 {{--</a>--}}
                                 <p class="comment-name reviewer-name mt">
                                    {{--  <a> --}}
                                    {{$reviews[$i]['customer_full_name']}}
                                    {{--  </a> --}}
                                 </p>
                                 {{--<a href="{{url('user-profile/'.$reviews[$i]['customer_username'])}}" target="_blank">--}}
                                 <i class="review-icon review-icon">
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
                                       $pretext = 'Reviews On';
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
                                       <i class="bx bxl-facebook-circle"></i>
                                       </a>
                                       </span>
                                    </div>
                                    {{--social media buttons END--}}
                                 </div>
                                 <div class="comment-content">
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
                                    <span class="review-post-date">{{date('d-m-y', strtotime($reviews[$i]['posted_on'])) }}</span>
                                    <h4 class="review-head bold">{{$review_head}}</h4>
                                    <p class="review-description">{{$review_body}} {{-- substr($reviews[$i]['comment'],0,random_int(50, 150))...<a href="#" style="color: #007bff ">Read more</a> --}}
                                    </p>
                                    <div class="like-button">
                                       {{--onclick event for liker list--}}
                                        <?php if($reviews[$i]['total_likes_of_a_review'] > 0){
                                            $onclick = 'onclick="getReviewLikerList('.$reviews[$i]['id'].')"';
                                        }else{
                                            $onclick = '';
                                        }
                                        ?>
                                       {{--Like option--}}
                                       @if(isset($reviews[$i]['liked']) && $reviews[$i]['liked'] == 1)
                                       <div class="like-content" title="Like">
                                          <button class="btn-like unlike-review" id="principalSelect-{{$reviews[$i]['id']}}"
                                             value="{{$reviews[$i]['id']}}" data-source="{{$reviews[$i]['source_id']}}">
                                          <i class="love-f-icon"></i>
                                          </button>
                                       </div>
                                       <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$reviews[$i]['id']}}">
                                          {{$reviews[$i]['total_likes_of_a_review']}}
                                          {{ $reviews[$i]['total_likes_of_a_review'] > 1 ? ' likes' : ' like'}}
                                       </p>
                                       @elseif(isset($reviews[$i]['liked']) && $reviews[$i]['liked'] == 0)
                                       <div class="like-content">
                                          <button class="btn-like like-review" {!! $onclick !!} id="principalSelect-{{$reviews[$i]['id']}}"
                                             value="{{$reviews[$i]['id']}}" data-source="{{$reviews[$i]['source_id']}}">
                                          <i class="love-e-icon"></i>
                                          </button>
                                       </div>
                                       <p class="likes-on-review" id="likes_of_review_{{$reviews[$i]['id']}}">
                                          {{$reviews[$i]['total_likes_of_a_review']}}
                                          {{ $reviews[$i]['total_likes_of_a_review'] > 1 ? ' likes' : ' like'}}
                                       </p>
                                       @endif
                                       <!-- like option ends -->
                                    </div>
                                    <p class="review-liability">
                                       This review is the subjective opinion of a Royalty member and not of Royalty.
                                       </p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- partner reply -->
                        @if(isset($reviews[$i]['comments'][0])){{--check if partner already replied or not--}} {{--reply section of partner--}}
                        <div class="row m-0 pull-right">
                           <div class="">
                              <!-- Partner reply box -->
                              <div class="whitebox comment-box-partner">
                                 <div class="comment-content comment-content-partner">
                                    <p class="comment-name partner-response">
                                    <b>You</b>
                                       <span>responded to this review</span>
                                    </p>
                                    <p class="partner-reply-date">{{date('d-m-y', strtotime($reviews[$i]['comments'][0]['posted_on'])) }}</p>
                                    <p class="partner-reply"> {{$reviews[$i]['comments'][0]['comment']}}
                                    </p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @else {{--if partner did not reply and will reply--}}
                        <div class="row">
                           <div class="col-md-offset-2 col-md-10">
                              <!-- Partner reply box -->
                              <div class="whitebox partner-color">
                                 <form action="{{url('replyReview/'.$reviews[$i]['id'])}}" method="post" onsubmit="return checkReply({{$i}})">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                       <textarea name="reply" id="review{{$i}}" cols="78" rows="4" placeholder="Your reply goes here..." style="float: left;"
                                          required maxlength="500" class="form-control" onkeyup="replyChars({{$i}});"></textarea>
                                    </div>
                                    <p align="right" style="font-size: small; margin-top: -10px">
                                       <span id="charNum{{$i}}">0/500</span>
                                    </p>
                                    <input type="hidden" name="customerID" value="{{$reviews[$i]['customer_id']}}">
                                    <input type="hidden" name="review_id" value="{{  $reviews[$i]['id']}}">
                                    <br>
                                    <button type="submit" class="btn btn-primary rpl_btn" style="margin-top: 10px;float: right;margin-right: unset">
                                    Reply
                                    </button>
                                 </form>
                              </div>
                           </div>
                        </div>
                        @endif
                     </div>
                     @endfor @else
                     <h4 class="no-info">No royalty members have reviewed your profile yet.</h4>
                     @endif
                  </div>
               </div>
            </div>
         </div>
         <div id="posts" class="tab-pane">
            <div class="row">
               <header class="banner">
                  <h1>YOUR POSTS</h1>
               </header>
               <div class="col-md-12 col-sm-12 col-xs-12">
                  @if(isset($allPosts) && count($allPosts) > 0 && !Session::has('single_noti'))
                  <div class="row newsfeed_dashboard" id="newsfeed_dashboard">
                     <ol class="activity-feed">
                        @foreach($allPosts as $post)
                        <?php
                           $posted_on = date("Y-M-d H:i:s", strtotime($post['posted_on']));
                           $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                           ?>
                        <li class="feed-item" data-content="&#xf00c;" data-time="{{$created->diffForHumans()}}" data-color="darkblue" id="post-id-{{$post['id']}}">
                           <section style="background-color: #f5f5f5;">
                              <label for="expand_4">
                                 {{--social media buttons deleted from here--}}
                                 <h4 class="post_header">
                                    <i class="announce-icon"></i>
                                    &nbsp;<b>{{$post['header']}}</b>
                                 </h4>
                                 <p class="post_caption">
                                    <i class='bx bx-news' ></i> &nbsp;{{$post['caption']}}
                                 </p>
                                 <br>
                                 <img src="{{asset($post['image_url'])}}" class="post_image lazyload" alt="Royalty news"/>
                                 <div>
                                    <i class="love-e-icon" style="float: left;margin-right: 3px;color: #d90345"></i>
                                    <p onclick="getNewsFeedLikerList('{{$post['id']}}')" class="likes-on-post"
                                       style="color: #007bff;font-weight: bold">{{$post['total_likes']}}
                                    {{$post['total_likes'] > 1 ? ' likes' : ' like'}}
                                    </p>
                                 </div>
                                 @if($post['post_link'] != null)
                                 <a href="{{$post['post_link']}}" target="_blank" class="btn btn-danger">Read more</a>
                                 @endif
                              </label>
                           </section>
                        </li>
                        @endforeach
                     </ol>
                  </div>
                  @elseif(Session::get('single_noti'))
                  <?php $single_post = Session::get('single_noti');?>
                  <div class="row newsfeed_dashboard" id="newsfeed_dashboard">
                     <ol class="activity-feed">
                        <?php
                           $posted_on = date("Y-M-d H:i:s", strtotime($single_post->posted_on));
                           $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                           ?>
                        <li class="feed-item" data-content="&#xf00c;" data-time="{{$created->diffForHumans()}}" data-color="darkblue" id="post-id-{{$single_post->id}}">
                           <section style="background-color: #f5f5f5;">
                              <label for="expand_4">
                                 social media buttons deleted from here
                                 <h4 class="post_header">
                                    <i class="announce-icon"></i>
                                    &nbsp;<b>{{$single_post->header}}</b>
                                 </h4>
                                 <p class="post_caption">
                                    <i class='bx bx-news' ></i> &nbsp;{{$single_post->caption}}
                                 </p>
                                 <br>
                                 <img src="{{asset($single_post->image_url)}}" class="post_image lazyload" alt="Royalty news"/>
                                 <div>
                                    <i class="love-e-icon" style="float: left;margin-right: 3px;color: #d90345"></i>
                                    <p onclick="getNewsFeedLikerList('{{$single_post->id}}')" class="likes-on-post" style="color: #007bff;font-weight: bold">
                                       {{$single_post->total_likes}} {{$single_post->total_likes > 1 ? ' likes' : ' like'}}</p>
                                 </div>
                                 @if($single_post->post_link != null)
                                 <a href="{{$single_post->post_link}}" target="_blank" class="btn btn-danger">Read more</a>
                                 @endif
                              </label>
                           </section>
                        </li>
                     </ol>
                  </div>
                  <?php session()->forget('single_noti'); ?>
                  @else
                  <h4 class="no-info">
                     You can post all your offers here on our live news feed section to reach out to all
                     members.
                  </h4>
                  @endif
               </div>
            </div>
         </div>
         <div id="statistics" class="tab-pane">
            <div class="row">
               <header class="banner">
                  <h1>STATISTICS</h1>
               </header>
               <div>
                  <div class="row" style="text-align: center">
                     <p>MARKET TRENDS</p>
                     <div class="col-md-12">
                        <p>Sales state</p>
                     </div>
                     <div class="col-md-12">
                        <p>Follower trend</p>
                     </div>
                     <div class="col-md-12">
                        <p>Rating trend</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
{{--Branch Modal--}}
<div id="branchModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">    <h4 class="modal-title">Branches</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
        
         </div>
         <div class="modal-body">
            <div class="partner-branches">
               <ul>
                  @for($i=0; $i < count($allBranches->branches); $i++)
                  <a href="{{ url('branch-account/'.$allBranches->branches[$i]['id']) }}">
                     <li>
                        <span>
                        {{  $allBranches->branches[$i]['partner_address'] }}
                        </span>
                     </li>
                  </a>
                  @endfor
               </ul>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Discount Modals-->
<div id="discountModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header"> <h4 class="modal-title">Transaction Details</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
           
         </div>
         <div class="modal-body bill_confirmation" id="profile_modal" class="profile_modal" style="color: #007bff;">
            <div style="padding: 10px;text-align: center">
               <span class="discount_in_confirm_bill" style="font-size: 40px;"></span>
               <span>Tk</span>
               <p style="margin-top: -4px;font-size: 0.8em;">Discount availed</p>
            </div>
            <div class="row">
               <div class="col-md-6 col-md-offset-3">
                  <table class="table table-borderless">
                     <tbody>
                        <tr>
                           <td>Subtotal</td>
                           <td id="final_bill_in_confirm_bill">Tk</td>
                        </tr>
                        <tr>
                           <td>Discount</td>
                           <td class="discount_in_confirm_bill discount-in-confirm-bill">Tk</td>
                        </tr>
                        <tr>
                           <td>Total</td>
                           <td id="final_amount_in_confirm_bill">Tk</td>
                        </tr>
                     </tbody>
                  </table>
                  <div class="customer-id-details">
                     <span>Customer ID:</span>
                     <span id="cus_id_in_confirm_bill"></span>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <form action="{{url('confirmDiscount')}}" method="POST" style="float: right">
               {{csrf_field()}}
               <input type="hidden" id="submit_customer_id" name="customer_id">
               <input type="hidden" id="submit_total_bill" name="total_bill">
               <input type="hidden" id="submit_discount" name="discount">
               <input type="hidden" id="submit_final_bill" name="final_bill">
               <input type="hidden" id="customer_request" name="requestCode">
               <input type="submit" class="btn btn-default" value="Confirm">
            </form>
         </div>
      </div>
   </div>
</div>
{{-- =========FOLLOWERS Modal============= --}}
<div id="followersModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">     <h4 class="modal-title">Your Followers</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
       
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            @if(isset($followers_info) && count($followers_info) > 0) @foreach($followers_info as $info)
            <div class="row">
               <div class="col-md-8 col-sm-6 col-xs-12 partner-followers-name">
                  <a href="{{url('user-profile/'.$info['customer_username'])}}" target="_blank">
                     <img class="image-left" src="{{asset($info['customer_profile_image'] != '' ?
                        $info['customer_profile_image'] : 'images/user.png' )}}" Royalty user">
                     <p class="text-top-right">
                        {{$info['customer_first_name'].' '.$info['customer_last_name']}}
                     </p>
                     <p class="text-btm-right">
                        <?php $time = strtotime($info['following_since']);
                           $month = date("F", $time);/*get month from date*/
                           $year = date("Y", $time);/*get year from date*/
                           ?>
                        following since {{$month.', '.$year}}
                     </p>
                  </a>
               </div>
               <div class="col-md-4 col-sm-6 col-xs-12 " style="text-align: center">
                  <div class="partner-followers-type">
                     <i class="bx bxs-star yellow"></i>
                     @if($info['customer_type']==1)
                     Gold Member
                     @elseif($info['customer_type']==2)
                        Royalty Premium Member
                     @else
                     Member
                     @endif
                  </div>
               </div>
            </div>
            @endforeach @else
            <div class="no-info">
               <p>No Followers</p>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>
<div id="reviewDoesNotExist1" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">No review</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
          
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal" style="text-align: unset">
            <div class="no-info">
               <p>{{session('review_does_not_exist')}}</p>
            </div>
         </div>
      </div>
   </div>
</div>
@include('footer')
<script>
   function replyChars(i) {
       var no_of_chars = $("#review"+i).val();
       $("#charNum"+i).text(no_of_chars.length+'/500');
   }
</script>
@include('footer-js.partner-account-js') {{--check if partner try to reply with nothing--}}
<script>
   function checkReply(id) {
       var str = document.getElementById('review' + id).value;
       if (!str.replace(/\s/g, '').length) {
           alert('Please write something');
           return false;
       }
       return true;
   }
</script>
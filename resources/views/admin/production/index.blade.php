@include('admin.production.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<style>
   .stats-count-box-1, .stats-count-box-2, .stats-count-box-3, .stats-count-box-4, .stats-count-box-5, .stats-count-box-6, .stats-count-box-7, .stats-count-box-8, .stats-count-box-9{
   padding: 15px;
   border-radius: 5px;
   color: white;
   }
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
</style>
<div class="right_col" role="main">
   <div class="page_loader" style="display: none;">
      <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif" alt="Royalty Loading GIF" class="lazyload" title="Royalty loading icon">
   </div>
   <div class="title_left">
   </div>
   <div class="row tile_count">
{{--      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">--}}
{{--         <div class="stats-count-box-1 guest-label">--}}
{{--            <div class="count guest_count"></div>--}}
{{--            <p class="count_top">Guest Users</p>--}}
{{--         </div>--}}
{{--      </div>--}}
{{--      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">--}}
{{--         <div class="stats-count-box-5 trial-label">--}}
{{--            <div class="count green trial_count"></div>--}}
{{--            <p class="count_top">Trial Members</p>--}}
{{--         </div>--}}
{{--      </div>--}}
{{--      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">--}}
{{--         <div class="stats-count-box-5 premium-label">--}}
{{--            <div class="count green card_holder_count"></div>--}}
{{--            <p class="count_top">Premium Members</p>--}}
{{--         </div>--}}
{{--      </div>--}}
{{--      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">--}}
{{--         <a href="{{url('admin/expired_members/active')}}">--}}
{{--            <div class="stats-count-box-8 expired-label">--}}
{{--               <div class="count expired_user_count"></div>--}}
{{--               <p class="count_top">Expired Members</p>--}}
{{--            </div>--}}
{{--         </a>--}}
{{--      </div>--}}
      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">
         <div class="stats-count-box-4 total-label">
            <div class="count green all_user_count"></div>
            <p class="count_top">Total Members</p>
         </div>
      </div>
      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">
         <a href="{{url('admin/active_member_tran_analytics')}}">
            <div class="stats-count-box-9 active-label">
               <div class="count active_user_count"></div>
               <p class="count_top">Active Members</p>
            </div>
         </a>
      </div>
      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">
         <div class="stats-count-box-8 inactive-label">
            <div class="count inactive_user_count"></div>
            <p class="count_top">Inactive Members</p>
         </div>
      </div>
{{--      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">--}}
{{--         <div class="stats-count-box-8 expiring-label">--}}
{{--            <div class="count expiring_user_count"></div>--}}
{{--            <p class="count_top">Expiring Members</p>--}}
{{--         </div>--}}
{{--      </div>--}}
      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">
         <a href="{{url('partners-all-transactions/active')}}">
            <div class="stats-count-box-9 transactions-label">
               <div class="count transaction_count"></div>
               <p class="count_top">Transactions</p>
            </div>
         </a>
      </div>
      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">
         <div class="stats-count-box-9 reviews-label">
            <div class="count review_count"></div>
            <p class="count_top">Reviews</p>
         </div>
      </div>
      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">
         <div class="stats-count-box-6 partner-label">
            <div class="count all_partner_count"></div>
            <p class="count_top">Partners</p>
         </div>
      </div>
      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">
         <div class="stats-count-box-7 partner-outlet-label">
            <div class="count all_branch_count"></div>
            <p class="count_top">Partner Outlets</p>
         </div>
      </div>
      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">
         <div class="stats-count-box-9 partner-offer-label">
            <div class="count offers_count"></div>
            <p class="count_top">Offers</p>
         </div>
      </div>

      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">
         <div class="stats-count-box-9 verified-email-label">
            <div class="count verified_email"></div>
            <p class="count_top">Verified Email</p>
         </div>
      </div>

      <div class="col-md-2 col-sm-3 col-xs-6 tile_stats_count">
         <div class="stats-count-box-9 completed-profile-label">
            <div class="count completed_profile"></div>
            <p class="count_top">Completed Profile</p>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-6">
         {{--    customer leaderboard--}}
         <h3>Customer Leaderboard</h3>
         <div class="row">
            <!--<div class="col-md-12">
               <form class="form-inline" action="">
                   <div class="form-group">
                       <select class="form-control" id="userLeadYear">
                           <option disabled selected>Year</option>
                           <?php
                  for ($i = 2018; $i <= date('Y'); $i++) {
                      $selected = $year == $i ? 'selected' : '';
                      echo "<option value='$i' $selected>$i</option>";
                  }
                  ?>
                       </select>
                   </div>
                   <div class="form-group">
                       <select class="form-control" id="userLeadMonth">
                           <option disabled selected>Month</option>
                           <option value="01" {{$month == '01' ? 'selected' : ''}}>January</option>
                           <option value="02" {{$month == '02' ? 'selected' : ''}}>February</option>
                           <option value="03" {{$month == '03' ? 'selected' : ''}}>March</option>
                           <option value="04" {{$month == '04' ? 'selected' : ''}}>April</option>
                           <option value="05" {{$month == '05' ? 'selected' : ''}}>May</option>
                           <option value="06" {{$month == '06' ? 'selected' : ''}}>June</option>
                           <option value="07" {{$month == '07' ? 'selected' : ''}}>July</option>
                           <option value="08" {{$month == '08' ? 'selected' : ''}}>August</option>
                           <option value="09" {{$month == '09' ? 'selected' : ''}}>September</option>
                           <option value="10" {{$month == '10' ? 'selected' : ''}}>October</option>
                           <option value="11" {{$month == '11' ? 'selected' : ''}}>November</option>
                           <option value="12" {{$month == '12' ? 'selected' : ''}}>December</option>
                       </select>
                   </div>
                   <div class="form-group">
                       <label></label>
                       <button type="button" class="btn btn-primary form-control" onclick="sortCustomerLeaderBoard()">Sort</button>
                   </div>
               </form>
               </div>
               <br><br><br>-->
            <div class="col-xs-12">
               <div class="table-responsive">
                  <table id="userLeaderBoardList" class="table table-bordered table-hover table-striped projects">
                  </table>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-6">
         {{--    partner leaderboard--}}
         <h3 style="display: inline-block;">Partner Leaderboard</h3>
         <a href="{{url('admin/partner/scan_leaderboard')}}" class="btn btn-primary" style="float: right; margin-right: 10px; display: inline-block">View Details</a>
         <div class="row">
            <!--<div class="col-md-12">
               <form class="form-inline" action="">
                   <div class="form-group">
                       <select class="form-control" id="partLeadYear">
                           <option disabled selected>Year</option>
                           <?php
                  for ($i = 2018; $i <= date('Y'); $i++) {
                      $selected = $year == $i ? 'selected' : '';
                      echo "<option value='$i' $selected>$i</option>";
                  }
                  ?>
                       </select>
                   </div>
                   <div class="form-group">
                       <select class="form-control" id="partLeadMonth">
                           <option disabled selected>Month</option>
                           <option value="01" {{$month == '01' ? 'selected' : ''}}>January</option>
                           <option value="02" {{$month == '02' ? 'selected' : ''}}>February</option>
                           <option value="03" {{$month == '03' ? 'selected' : ''}}>March</option>
                           <option value="04" {{$month == '04' ? 'selected' : ''}}>April</option>
                           <option value="05" {{$month == '05' ? 'selected' : ''}}>May</option>
                           <option value="06" {{$month == '06' ? 'selected' : ''}}>June</option>
                           <option value="07" {{$month == '07' ? 'selected' : ''}}>July</option>
                           <option value="08" {{$month == '08' ? 'selected' : ''}}>August</option>
                           <option value="09" {{$month == '09' ? 'selected' : ''}}>September</option>
                           <option value="10" {{$month == '10' ? 'selected' : ''}}>October</option>
                           <option value="11" {{$month == '11' ? 'selected' : ''}}>November</option>
                           <option value="12" {{$month == '12' ? 'selected' : ''}}>December</option>
                       </select>
                   </div>
                   <div class="form-group">
                       <label></label>
                       <button type="button" class="btn btn-primary form-control" onclick="sortPartnerOutletLeaderBoard()">Sort</button>
                   </div>
               </form>
               </div>
               <br><br><br>-->
            <div class="col-xs-12">
               <div class="table-responsive">
                  <table id="partnerLeaderBoardList" class="table table-bordered table-hover table-striped projects">
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="{{asset('js/admin/analytics/statistics.js')}}"></script>
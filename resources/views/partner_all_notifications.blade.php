@if(!session()->has('partner_id'))
<script>
   window.location = "{{ url('/') }}";
</script>
@endif
<?php use \App\Http\Controllers\functionController; ?>
@include('header')
<div class="container">
   <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
          <p>Your Notifications</p>
            <hr>
          <?php
          $partnerAllNotifications = Session::get('partnerAllNotifications');
          ?>
          @if($partnerAllNotifications['total_notifications'] != 0)
             @if(isset($partnerAllNotifications['today']) && count($partnerAllNotifications['today']) > 0)
                <div class="col-md-6">Today</div><br>
                  <?php
                  $notifications = (new functionController)->partnerAllNotificationView($partnerAllNotifications['today']);
                  echo $notifications;
                  ?>
             @endif
             @if(isset($partnerAllNotifications['yesterday']) && count($partnerAllNotifications['yesterday']) > 0)
                <div class="col-md-6">Yesterday</div><br>
                  <?php
                  $notifications = (new functionController)->partnerAllNotificationView($partnerAllNotifications['yesterday']);
                  echo $notifications;
                  ?>
             @endif
             @if(isset($partnerAllNotifications['this_week']) && count($partnerAllNotifications['this_week']) > 0)
                <div class="col-md-6">This week</div><br>
                  <?php
                  $notifications = (new functionController)->partnerAllNotificationView($partnerAllNotifications['this_week']);
                  echo $notifications;
                  ?>
             @endif
             @if(isset($partnerAllNotifications['earlier']) && count($partnerAllNotifications['earlier']) > 0)
                <div class="col-md-6">Earlier</div><br>
                  <?php
                  $notifications = (new functionController)->partnerAllNotificationView($partnerAllNotifications['earlier']);
                  echo $notifications;
                  ?>
             @endif
          @else
          <!-- if no notification -->
              <p>You don't have any notifications.</p>
          @endif
      </div>
      <div class="col-md-3 col-sm-5 col-xs-12 notif-left">
         <!-- {{--
         <div class="cus_notif_heading">
            --}}
         {{--
         <p class="cus_notif_p">Follow requests</p>
         --}}
         {{--
      </div>
      --}}
         {{--@if($follow_requests != null)--}}
         {{--
         <div class="left-block">
            --}}
         {{--@foreach($follow_requests as $request)--}}
         {{--<div class="row for_norequest explore follow-request-{{$request['customer_id']}}"--}}
         {{--id="follow-request-{{$request['customer_id']}}">--}}
         {{--
         <div class="col-md-6 col-sm-6 request-name">
            --}}
         {{--<a--}}
         {{--href="{{url('user-profile/'.$request['customer_username'])}}">--}}
         {{--<img src="{{asset($request['profile_image'])}}" class="img-circle img-40 primary-border lazyload img-left">--}}
         {{--
         <p class="follow-req-names">{{$request['customer_name']}}</p>
         --}}
         {{--
         <p style="font-size: 0.8em;">--}}
         {{--<i class="bx bxs-star yellow"></i>--}}
         {{--@if($request['customer_type']==1)Gold Member--}}
         {{--@elseif($request['customer_type']==2) Platinum Member--}}
         {{--@else Member--}}
         {{--@endif--}}
         {{--
      </p>
      --}}
         {{--</a>--}}
         {{--
      </div>
      --}}
         {{--
         <div class="col-md-6 col-sm-6 follow-req-btn">--}}
         {{--<button class="btn btn-accept accept-follow-request-{{$request['customer_id']}}"--}}
         {{--id="accept-follow-request" value="{{$request['customer_id']}}">Accept--}}
         {{--</button>--}}
         {{--<button class="btn btn-reject ignore-follow-request-{{$request['customer_id']}}"--}}
         {{--id="ignore-follow-request" value="{{$request['customer_id']}}">Ignore--}}
         {{--</button>--}}
         {{--
      </div>
      --}}
         {{--
      </div>
      --}}
         {{--@endforeach--}}
         {{--
      </div>
      --}}
         {{--@else--}}
         {{--
         <h5 class="no-follow-req">No Request(s)</h5>
         --}} -->
            {{--@endif--}}
            <div class="cus_notif_heading">
               <p class="cus_notif_p">Explore</p>
               <p class="cus_notif_para">
                  <a href="{{ url('offers/all') }}">See all</a>
               </p>
            </div>
            <hr>
            @foreach($partnerInfo as $info)
               <div class="row explore">
                  <div class="col-md-2 col-sm-2 col-xs-2">
                     <img src="{{ $info['partner_profile_image'] }}" alt="image" class="img-circle img-40 primary-border lazyload" Royalty explore">
                  </div>
                  <div class="col-md-10 col-sm-10 col-xs-10 explore-category">
                     <div class="explore-partner-info">
                         <?php $pname = str_replace("'", "", $info['partner_name']); ?>
                        <p>
                           <b>
                              <a
                                 href="{{ url('partner-profile/'. $pname.'/'.$info['main_branch_id']) }}">{{$info['partner_name']}}
                              </a>
                           </b>
                        </p>
                        @foreach($categories as $category)
                           @if($category->id == $info['partner_category'])
                              <a href="{{ url('offers/'.$category->type) }}" target="_blank">{{$category->name}}</a>
                           @endif
                        @endforeach
                     </div>
                  </div>
               </div>
            @endforeach
            <div class="cus_notif_heading notif-top-brands">
               <p class="cus_notif_p">Top Brands</p>
            </div>
            <hr>
            @foreach($topBrands as $info)
               <div class="row explore">
                  <div class="col-md-2 col-sm-2 col-xs-2">
                     <img src="{{ $info['partner_profile_image'] }}" alt="image" class="img-circle img-40 primary-border lazyload" Royalty Profile Image">
                  </div>
                  <div class="col-md-10 col-sm-10 col-xs-10 explore-category">
                     <div class="explore-partner-info">
                         <?php $pname = str_replace("'", "", $info['partner_name']); ?>
                        <p>
                           <b><a href="{{url('partner-profile/'.$pname.'/'.$info['main_branch_id'])}}">{{$info['partner_name']}}</a></b>
                        </p>
                        @foreach($categories as $category)
                           @if($category->id == $info['partner_category'])
                              <a href="{{ url('offers/'.$category->type) }}" target="_blank">{{$category->name}}</a>
                           @endif
                        @endforeach
                     </div>
                  </div>
               </div>
            @endforeach
         </div>
   </div>
</div>
@include('footer')
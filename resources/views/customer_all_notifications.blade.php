@if(!session()->has('customer_id'))
<script>
   window.location = "{{ url('/') }}";
</script>
@endif
<?php use \App\Http\Controllers\functionController; ?>
@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Your notifications</h2>
         <!-- <p>Edit Profile</p> -->
      </div>
   </div>
</section>
<section id="contact" class="contact">
<div class="container">
   <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
      <?php
          $allNotifications = Session::get('customerAllNotifications');
          ?>
          @if($allNotifications['total_notifications'] != 0)
              @if(isset($allNotifications['today']) && count($allNotifications['today']) > 0)
                  <p>Today</p>
                  <?php
                  $notifications = (new functionController)->allNotificationView($allNotifications['today']);
                  echo $notifications;
                  ?>
              @endif
              @if(isset($allNotifications['yesterday']) && count($allNotifications['yesterday']) > 0)
                  <p>Yesterday</p>
                  <?php
                  $notifications = (new functionController)->allNotificationView($allNotifications['yesterday']);
                  echo $notifications;
                  ?>
              @endif
              @if(isset($allNotifications['this_week']) && count($allNotifications['this_week']) > 0)
                  <p>This week</p>
                  <?php
                  $notifications = (new functionController)->allNotificationView($allNotifications['this_week']);
                  echo $notifications;
                  ?>
              @endif
              @if(isset($allNotifications['earlier']) && count($allNotifications['earlier']) > 0)
                  <p>Earlier</p>
                  <?php
                  $notifications = (new functionController)->allNotificationView($allNotifications['earlier']);
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
               {{--<img src="{{asset($request['profile_image'])}}" class="img-circle img-40 primary-border img-left lazyload" alt="Royalty Customer follow">--}}
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
   </div>
</div>
</section>
@include('footer')
{{--===============================================================================================--}}
{{--=====================accept or ignore follow request with AJAX & JavaScript===============--}}
{{--=====================================================================================--}}
<script>
   // accept follow request
   $(document).on('click', '#accept-follow-request', function () {
       var url = "{{ url('/accept-follow-request') }}";
   
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id': this.value},
           success: function (data) {
               $('.accept-follow-request-' + data).hide();
               $('.ignore-follow-request-' + data).hide();
               //get multiple elements by 1 class to change their text at the same time
               var elms = document.querySelectorAll(".follow-request-text-" + data);
               for (var i = 0; i < elms.length; i++) {
                   elms[i].innerHTML = ' started following you';
               }
           }
       });
   });
   
   // ignore follow request
   $(document).on('click', '#ignore-follow-request', function () {
       var url = "{{ url('/ignore-follow-request') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id': this.value},
           success: function (data) {
               // when more than 1 requests exist
               if (data['request_number'] == null) {
                   //get multiple elements by 1 class to change their text at the same time
                   var elms = document.querySelectorAll(".follow-request-" + data['follower']);
                   for (var i = 0; i < elms.length; i++) {
                       elms[i].remove();
                   }
                   // when only 1 request exists
               } else {
                   //get multiple elements by 1 class to change their text at the same time
                   var num = document.querySelectorAll('.for_norequest').length;
                   if (num == 1) {
                       var elms = document.querySelectorAll(".follow-request-" + data['follower']);
                       elms[0].innerHTML = "<h5 style='margin-left: 15px'>No Request(s)</h5>";
                       for (var i = 1; i < elms.length; i++) {
                           elms[i].remove();
                       }
                   } else {
                       var elms = document.querySelectorAll(".follow-request-" + data['follower']);
                       for (var i = 0; i < elms.length; i++) {
                           elms[i].remove();
                       }
                   }
               }
           }
       });
   });
</script>
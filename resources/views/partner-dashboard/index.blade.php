@include('partner-dashboard.header')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale = 1.0, user-scalable = no">
<link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet">
<div class="container-fluid">
   <div class="row bg-title">
      <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
         <h3 class="d-inline-block">{{__('partner/checkout.checkout_customers_accept_requests')}}</h3>
         <h5 class="d-inline-block float-right">{{__('partner/checkout.follow_any_method')}}</h5>
      </div>
   </div>
   <span class="user_not_found"></span>
   <div class="row">
      <div class="col-md-12">
         <div class="white-box" style="margin-bottom: unset">
            <p class="box-title">{{__('partner/checkout.enter_card_number')}}</p>
            <div class="row">
               <div class="col-md-8">
                  <div class="form-group" style="margin-bottom:unset;">
                     <input type="text" class="form-control" id="customer_id" maxlength="16"
                            placeholder="{{__('partner/checkout.enter_customer_card_number_here')}}">
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group" style="margin-bottom:unset;">
                     <button class="btn btn-primary form-control" id="checkout_user" onclick="checkCustomer()">CONFIRM</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <p class="m-dashboard-or">
<span>{{__('partner/common.or')}}
</span>
   </p>
   <div class="row">
      <div class="col-md-12">
         <div class="white-box">
            <p class="box-title">{{__('partner/checkout.accept_customer_request')}}</p>
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
            @if(count($tran_notifications) > 0)
            <!-- <p style="font-weight: bolder;">{{__('partner/checkout.all_requests')}}</p> -->
            <ul class="list-group">
               @foreach($tran_notifications as $notification)
               <li class="list-group-item">
                  <div class="row">
                     <div class="col-md-9">
                        <?php
                           $posted_on = date("Y-M-d H:i:s", strtotime($notification['posted_on']));
                           $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                           $prev_time = \Carbon\Carbon::now()->subMinutes(10);
                           ?>
                        <span class="relative_time">{{$created->diffForHumans()}}</span><br>
                        <img src="{{$notification->image}}" alt="Profile" class="pro_pic">
                        <span class="request-text">
                        <b>{{$notification->customer_name}}</b>
                           @if($notification->request->redeem_id)
                              has requested a reward
                              ({{$notification->request->offer->offer_description}}),
                              Quantity: {{$notification->request->redeem->quantity}}
                           @else
                              has requested a transaction
                              ({{$notification->request->offer->offer_description}}).
                           @endif

                        </span>
                     </div>
                     <div class="col-md-3">
                        <div class="request_{{$notification->source_id}}">
                           @if($notification->request->status == 0)
                           {{-- @if($created < $prev_time)
                           <span class="expired">Expired</span>
                           @else --}}
                           <button class="btn btn-success action_btn notification_{{$notification->id}}"
                              onclick="updateStatus('{{$notification->id}}', '{{$notification->source_id}}', '1', '{{$created}}')"
                              >Accept</button>
                           <button class="btn btn-danger action_btn notification_{{$notification->id}}"
                              onclick="updateStatus('{{$notification->id}}', '{{$notification->source_id}}', '2', '{{$created}}')"
                              >Reject</button>
                           {{-- @endif --}}
                           @elseif($notification->request->status == \App\Http\Controllers\Enum\TransactionRequestStatus::ACCEPTED)
                           <span class="accepted">Accepted</span>
                           @else
                           <span class="rejected">Rejected</span>
                           @endif
                        </div>
                     </div>
                  </div>
               </li>
               @endforeach
            </ul>
            @else
            <p>{{__('partner/checkout.no_request_yet')}}</p>
            @endif
         </div>
         </div>
      </div>
   </div>
</div>
<!-- offers moda -->
<div class="modal fade" id="offersModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="modal-title" id="exampleModalLabel">Continue Transaction</h5>
         </div>
         <div class="modal-body">
            <div id="user_info">
               <div class="row">
                  <div class="col-md-1">
                     <img src="" class="pro_pic user_img" alt="customer profile image" style="display:inline">
                  </div>
                  <div class="col-md-11">
                     <div style="padding-left:5px">
                        <span class="customer_name">customer name</span><br>
                        <span>Membership Number: <span class="customer_id">customer id</span></span>
                     </div>
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
@include('partner-dashboard.footer')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/merchantv2.js')}}"></script>
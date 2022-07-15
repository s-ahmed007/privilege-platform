@include('admin.production.header')
<link rel="stylesheet" href="{{asset('css/merchant.css')}}">
<style>
   .pro_pic {width: 50px;height: 50px;border-radius: 50%}
   li:not(:last-child){border-bottom: unset}
   /* .request-text{display: inline-grid;} */
</style>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         <h3>{{$status." "}}Transaction Requests</h3>
      </div>
      <div class="title_right">
         @if(session('accepted'))
         <div class="msg_accept">
            <span>{{session('accepted')}}</span>
         </div>
         @elseif(session('rejected'))
         <div class="msg_reject">
            <span>{{session('rejected')}}</span>
         </div>
         @endif
      </div>
   </div>
   <div class="clearfix"></div>
   <div>
      <a class="btn btn-guest" href="{{url('admin/transaction_requests')}}">Pending</a>
      <a class="btn btn-all" href="{{url('admin/transaction_requests/accepted')}}">Accepted</a>
      <a class="btn btn-expired" href="{{url('admin/transaction_requests/declined')}}">Rejected</a>
   </div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if(count($notifications) > 0)
               <table class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th style="text-align: unset">Info</th>
                        <!-- @if($status == "Pending")
                           @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                              <th>Action</th>
                           @endif
                        @endif -->
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($notifications as $notification)
                     <?php
                        $posted_on = date("Y-M-d H:i:s", strtotime($notification->posted_on));
                        $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                        $prev_time = \Carbon\Carbon::now()->subMinutes(10);
                        ?>
                     <tr>
                        <td style="text-align: unset">
                           <span class="relative_time">{{$created->diffForHumans()}}</span><br>
                           <img src="{{$notification->customerInfo->customer_profile_image}}" alt="Profile" class="pro_pic">
                           <span class="request-text">
                           {{$notification->customerInfo->customer_full_name}}
                               @if($notification->transactionRequest->redeem_id)
                                   has requested a reward
                               @else
                                   has requested a transaction
                               @endif
                           ({{$notification->transactionRequest->offer->offer_description}}) at
                           {{$notification->branchUser->branch->info->partner_name .', '.
                           $notification->branchUser->branch->partner_address}}
                           </span>
                           <br>
                            @if($notification->transactionRequest->status == 1)
                                @if($notification->transactionRequest->transaction->branch_user_id !=
                                    \App\Http\Controllers\Enum\AdminScannerType::accept_tran_req)
                                    {{"Transacted by: ".$notification->branchUser->full_name }}
                                   @if($notification->transactionRequest->transaction->platform == \App\Http\Controllers\Enum\PlatformType::web)
                                        <br>From Merchant Dashboard
                                   @elseif($notification->transactionRequest->transaction->platform == \App\Http\Controllers\Enum\PlatformType::android)
                                        <br>From Merchant App
                                   @endif
                                @elseif($notification->transactionRequest->transaction->branch_user_id ==
                                    \App\Http\Controllers\Enum\AdminScannerType::accept_tran_req)
                                    Transacted by: Royalty Admin
                                @endif
                            @elseif($notification->transactionRequest->status == 2)
                                @if($notification->transactionRequest->updated_by !=
                                    \App\Http\Controllers\Enum\AdminScannerType::accept_tran_req)
                                    @if($notification->transactionRequest->branchScanner)
                                        {{"Rejected by: ".$notification->transactionRequest->branchScanner->full_name }}
                                    @endif
                                @elseif($notification->transactionRequest->updated_by ==
                                    \App\Http\Controllers\Enum\AdminScannerType::accept_tran_req)
                                    Rejected by: Royalty Admin
                                @endif
                            @endif
                            <br>
                           <p>{{date("F d, Y # h:i A", strtotime($notification->posted_on))}}</p>
                           @if($notification->customerInfo->customerHistory==null)
                              <span class="guest-label">Guest Member</span>
                           @elseif($notification->customerInfo->customerHistory->type==\App\Http\Controllers\Enum\CustomerType::card_holder)
                              <span class="premium-label">Premium Member</span>
                           @elseif ($notification->customerInfo->customerHistory->type==\App\Http\Controllers\Enum\CustomerType::trial_user)
                              <span class="trial-label">Trial</span>
                           @endif
                            @if($notification->transactionRequest->redeem_id)
                                <span class="guest-label">Reward</span>
                            @endif

                            @if($status == "Pending")
                           @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                      
                              <button class="btn btn-success action_btn notification_{{$notification->id}}"
                                 onclick="updateStatus('{{$notification->id}}', '{{$notification->source_id}}', '1',
                                         '{{$notification->branch_user_id}}')">Accept
                              </button>
                              <button class="btn btn-danger action_btn notification_{{$notification->id}}"
                                 onclick="updateStatus('{{$notification->id}}', '{{$notification->source_id}}', '2',
                                         '{{$notification->branch_user_id}}')">Reject
                              </button>
                            
                           @endif
                        @endif
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
                  {{$notifications->links()}}
               @else
                  @if($status == "Pending")
                     <p>No request has been made yet.</p>
                  @elseif($status == "Accepted")
                     <p>No accepted request has been found.</p>
                  @else
                     <p>No rejected request has been found.</p>
                  @endif
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/merchant.js')}}"></script>
<script>
   // update request status
   function updateStatus(notificationID, sourceID, status, branch_user_id) {
      var conf_text = status == 1 ? 'Are you sure you want to accept? ' : 'Are you sure you want to reject?';
       if (confirm(conf_text)) {
           var url =
               base_url +
               "/" +
               "admin/update_transaction_request/" +
               notificationID +
               "/" +
               sourceID +
               "/" +
               branch_user_id +
               "/" +
               status;
           window.location = url;
       } else {
           return false;
       }
   }
</script>
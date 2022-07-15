<?php use App\Http\Controllers\Enum\ValidFor; ?>
@include('partner-dashboard.header')
<div class="container-fluid">
   <div class="row bg-title">
      <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
         <h3 class="d-inline-block">{{__('partner/common.my_deals')}}</h3>
         <h5 class="d-inline-block float-right">{{__('partner/deals.deals_that_can_be_availed')}}</h5>
      </div>
   </div>
   @if($branch_vouchers)
   <div class="row">
      <div class="col-lg-4 col-sm-6 col-xs-12">
         <div class="white-box">
            <p style="color: red;font-weight:bold;">{{__('partner/deals.deals_payment_due')}}: {{intval($payment_details->credit)}} BDT </p>
         </div>
      </div>
      <div class="col-lg-4 col-sm-6 col-xs-12">
         <div class="white-box">
            <p style="color: green;font-weight:bold;">{{__('partner/deals.deals_payment_paid')}}: {{intval($payment_details->credit_used)}} BDT </p>
         </div>
      </div>
      <div class="col-lg-4 col-sm-6 col-xs-12">
         <div class="white-box">
            @if(count($payment_details->paidHistory) > 0)
               <?php $last_paid = $payment_details->paidHistory->sortByDesc('id')->first(); ?>
               <p style="color: #007bff;font-weight:bold;">{{__('partner/deals.deals_last_paid')}}: {{date('F d, Y', strtotime($last_paid->created_at))}} </p>
            @else
               <p style="color: #007bff;font-weight:bold;">{{__('partner/deals.deals_last_paid')}}: __ / __ / __
            @endif
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-12">
      <div style="float: right;">
      <a href="{{url('partner/branch/deals/payments')}}"> {{__('partner/deals.see_all_deal_payment_history')}} </a>
      </div>
      </div>
   </div>

   @foreach($branch_vouchers as $voucher)
   <div class="row">
      <div class="col-md-12">
         <div class="column">
            <div class="row m-z-a">
               <div class="col-md-10">
                  <div class="partner-offer-box-l">
                     <h4>{{$voucher['heading']}}</h4>
                     <div class="partner-offer-timings">
                        <p>Valid till -
                           <span>{{date('F d, Y', strtotime($voucher['date_duration']['to']))}}</span>
                        </p>
                        @if($voucher['weekdays'])
                        <p>Valid on -
                           <span>
                           <?php $weekdays = $voucher['weekdays']; ?>
                           @if($weekdays['sat'] == '1' && $weekdays['sun'] == '1' && $weekdays['mon'] == '1' && $weekdays['tue'] == '1' &&
                           $weekdays['wed'] == '1' && $weekdays['thu'] == '1' && $weekdays['fri'] == '1')
                           All days
                           @else
                              @if($weekdays['sun'] == '1') Sun @endif
                              @if($weekdays['mon'] == '1') Mon @endif
                              @if($weekdays['tue'] == '1') Tue @endif
                              @if($weekdays['wed'] == '1') Wed @endif
                              @if($weekdays['thu'] == '1') Thu @endif
                              @if($weekdays['fri'] == '1') Fri @endif
                              @if($weekdays['sat'] == '1') Sat @endif
                           @endif
                           </span>
                        </p>
                        @endif
                        <p>Valid for -
                           <span>
                           {{$voucher['valid_for'] == ValidFor::ALL_MEMBERS ? 'All Members':'Premium Members'}}
                           </span>
                        </p>
                        @if($voucher['time_duration'])
                        <p>Timing -
                           <span>
                           <?php $i=0; ?>
                           @foreach($voucher['time_duration'] as $duration)
                           {{date('h:i A', strtotime($duration['from'])).' - '.date('h:i A', strtotime($duration['to']))}}
                           <?php
                              echo $i != count($voucher['time_duration'])-1 ? ',' : '';
                              $i++; ?>
                           @endforeach
                           </span>
                        </p>
                        @else
                        <br>
                        @endif
                        <p>Original price - 
                           <span>{{intval($voucher['actual_price']).' Tk'}}</span>
                        </p>
                        <p>Royalty commission - 
                           <span>
                           @if($voucher['commission_type'] == 1)
                              {{$voucher['commission'].' TK'}}
                           @else
                              {{$voucher['selling_price'] * $voucher['commission'] / 100}} TK
                           @endif
                           </span>
                        </p>
                        <p>Selling price - 
                           <span>{{intval($voucher['selling_price']).' Tk'}}</span>
                        </p>
                        <br>
                        <p>Available - 
                           <span>{{$voucher['counter_limit'] - $voucher['purchased']}}
                           </span>
                        </p>
                        <p>Purchased - 
                           <span>{{$voucher['purchased']}}</span>
                        </p>
                        <p>Redeemed - 
                           <span>{{$voucher['redeemed']}}</span>
                        </p>
                     </div>
                     <div>
                        <span class="offer-used-partner"></span>
                     </div>
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="pp-offer-btn mtb-10">
                     <button class="btn btn-primary offerDetails" data-offer-id="{{$voucher['id']}}" data-offer-tab="details">Details</button>
                     <button class="btn btn-primary offerDetails" data-offer-id="{{$voucher['id']}}" data-offer-tab="tnc">T&C</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="modal fade" id="offerDetails_{{$voucher['id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">   <h4 class="modal-title">{{ session('partner_name') }}</h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i>
               </button>
            
            </div>
            <div class="modal-body">
               <div role="tabpanel">
                  <!-- Nav tabs -->
                  <ul class="nav nav-tabs" role="tablist">
                     <li role="presentation" class="active"><a href="#detailsTab{{$voucher['id']}}" aria-controls="detailsTab{{$voucher['id']}}" role="tab" data-toggle="tab">Details</a>
                     </li>
                     <li role="presentation"><a href="#tncTab{{$voucher['id']}}" aria-controls="tncTab{{$voucher['id']}}" role="tab" data-toggle="tab">T&C</a>
                     </li>
                  </ul>
                  <!-- Tab panes -->
                  <div class="tab-content offer-tab-content">
                     <div role="tabpanel" class="tab-pane active" id="detailsTab{{$voucher['id']}}">
                        {!! html_entity_decode($voucher['description']) !!}
                     </div>
                     <div role="tabpanel" class="tab-pane" id="tncTab{{$voucher['id']}}">
                        {!! html_entity_decode($voucher['tnc']) !!}
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   @endforeach
   @else
      <p>You have no deal yet. To add any deal please contact here +8801312620202</p>
   @endif
</div>
@include('partner-dashboard.footer')
<script type="text/javascript">
   // script to show individual offer details modal & open specific tab
   $(document).ready(function() {
     $(".offerDetails").click(function() {
       var offer_id = $(this).data("offer-id");
       var offer_tab = $(this).data("offer-tab");
       if (offer_tab === "details") {
         $('a[href^="#tncTab' + offer_id + '"]')
           .parent()
           .removeClass("active");
         $('a[href^="#detailsTab' + offer_id + '"]')
           .parent()
           .addClass("active");
         $("#tncTab" + offer_id).removeClass("active");
         $("#detailsTab" + offer_id).addClass("active");
       } else if (offer_tab === "tnc") {
         $('a[href^="#detailsTab' + offer_id + '"]')
           .parent()
           .removeClass("active");
         $('a[href^="#tncTab' + offer_id + '"]')
           .parent()
           .addClass("active");
         $("#detailsTab" + offer_id).removeClass("active");
         $("#tncTab" + offer_id).addClass("active");
       }
       $("#offerDetails_" + offer_id).modal("show");
     });
   });
</script>
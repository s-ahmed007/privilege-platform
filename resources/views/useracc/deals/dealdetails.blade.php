@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <!-- <h2>Find your profile details, usages, rewards all together here</h2> -->
         <p>Deal Details</p>
      </div>
   </div>
</section>
<section class="counts">
<div class="container">
   <div class="row">
      <div class="col-md-3">
         <div class="sidebar mb-3 shadow">
            @include('useracc.sidebar')
         </div>
      </div>
      <div class="col-md-9">
            <div class="row m-0">
               <div class="col-md-8">
                  <div class="whitebox">
                  <h2>{{$voucher_details->voucher->heading}}</h2>
                  <p>Order Value: ৳{{intval($voucher_details->voucher->selling_price)}}</p>
                  <p>Expiry: {{date('F d, Y', strtotime($voucher_details->expiry_date))}}</p>
                  <hr>
                  <h4>How to use?</h4>
                  <p>1. Using your phone
                     <br>
                     Static text on how to use via phone will go here
                  </p>
                  <p>2. Printing a deal
                     Static text on how to use by printing the deal will go here
                  </p>
                  <hr>
                  <h4>About the merchant:
                  </h4>
                  <p>{{$voucher_details->voucher->branch->info->partner_name.', '.$voucher_details->voucher->branch->partner_area}}</p>
                  <hr>
                  <h4>About the Deal:
                  </h4>
                  <p>{{$voucher_details->voucher->heading}}</p>
                  <p>{!! html_entity_decode($voucher_details->voucher->tnc) !!}</p>
                  <hr>
                  <p>Deals related general T&C can be found <a href="{{ url('terms&conditions') }}">here.</a></p>
                  @if($voucher_details->available)
                     @if($voucher_details->refund)
                        @if($voucher_details->refund->refund_status == \App\Http\Controllers\Enum\DealRefundStatus::ACCEPTED)
                           <p style="color: green">Refund request accepted. Please check our T&Cs <a href="{{ url('terms&conditions') }}" target="_blank">here</a> to know more about when you can get the credit back.</p>
                        @elseif($voucher_details->refund->refund_status == \App\Http\Controllers\Enum\DealRefundStatus::REJECTED)
                           <p style="color: red"> Your refund request has been rejected </p>
                        @else
                           <p style="color:#12a03e">Refund request has been sent.<p>
                        @endif
                     @else
                     <p>You can cancel the purchased deal and ask for a refund below. Please check our T&Cs regarding refund <a href="{{ url('terms&conditions') }}" target="_blank">here</a>
                     </p>
                        <a class="dealRefund" data-deal-purchase-id="{{$voucher_details->id}}">Cancel & Refund</a>
                     @endif
                  @endif
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="whitebox">
                     <h4>Need help with this order?</h4>
                     <p>
                        Call the customer support<br>
                        <a href="tel:+8809638620202">
                        +880-963-862-0202
                        </a> (10am-6pm)   
                     </p>
                  </div>
               </div>
            </div>
      </div>
   </div>
</div>
</div>
</div>
<section>

<!-- deal refund modal -->
<div class="modal fade" id="dealRefundModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
     <div class="modal-content">
        <div class="modal-header">  <h4 class="modal-title">Refund Request!</h4>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="cross-icon"></i>
           </button>
         
        </div>
        <div class="modal-body">
            <div class="row">
               <form class="" action="{{url('users/voucher_refund_request')}}" method="POST">
                  {{csrf_field()}}
                  <div class="col-md-12">
                     <label class="control-label">Write your reason:<span style="color:red;font-size: 1.5em">*</span></label>
                     <span style="color: red;">
                        @if ($errors->getBag('default')->first('comment'))
                            {{ $errors->getBag('default')->first('comment') }}
                        @endif
                     </span>
                     <textarea name="comment" class="form-control" id="comments" cols="3" rows="6" placeholder="Enter your message…" required></textarea><br>
                  </div>
                  <div class="col-md-12" style="text-align: center;">
                     <input type="hidden" name="purchase_id" id="purchase_id">
                     <button type="submit" class="btn btn-success">Submit</button>
                  </div>
               </form>
            </div>
        </div>
     </div>
  </div>
</div>

@if(session('ref_request_saved'))
<div class="modal" id="dealRefundSuccessModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
     <div class="modal-content">
        <div class="modal-header">        <h4 class="modal-title">Success!</h4>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="cross-icon"></i>
           </button>
   
        </div>
        <div class="modal-body">
            <p>{{session('ref_request_saved')}}</p>
        </div>
     </div>
  </div>
</div>
@endif

@include('footer')

<script type="text/javascript">
   $(document).ready(function () {
     $(".dealRefund").click(function () {
       $("#purchase_id").val($(this).data("deal-purchase-id"));
       $("#dealRefundModal").modal("show");
     });
   });
</script>
@if(session('ref_request_saved'))
   <script type="text/javascript">
      $(document).ready(function () {
         $("#dealRefundSuccessModal").modal("show");
      });
   </script>
@endif
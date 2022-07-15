@include('partner-dashboard.header')
<div class="container-fluid">
   <div class="row bg-title">
      <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
         <h3 class="d-inline-block">{{__('partner/deals.deals_payment_history')}}</h3>
      </div>
   </div>
   @if($payment_details)
      <div class="row">
         <div class="col-md-12">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Amount</th>
                  <th scope="col">Date</th>
                </tr>
              </thead>
              <tbody>
               @foreach($payment_details->paidHistory as $key => $payment)
                <tr>
                  <th scope="row">{{$key+1}}</th>
                  <td>{{$payment->credit}} BDT</td>
                  <td>{{date('F d, Y', strtotime($payment->created_at))}}</td>
                </tr>
               @endforeach
              </tbody>
            </table>
         </div>
      </div>
   @else
      <p>You have no deal yet. To add any deal please contact here +8801312620202</p>
   @endif
</div>
@include('partner-dashboard.footer')
<?php use App\Http\Controllers\Enum\ReviewType; ?>
@include('header')
<link href="{{asset('emoji/css/emoji.css')}}" rel="stylesheet">
<style>
   td {
      width:25%;
   }
   .no-data-found{
      padding: 10px;
   }
</style>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <!-- <h2>Find your profile details, usages, rewards all together here</h2> -->
         <p>Deals purchased</p>
      </div>
   </div>
</section>
<section class="counts">
   <div class="container">
      <div class="row">
         <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="sidebar mb-3 shadow">
               @include('useracc.sidebar')
            </div>
         </div>
         <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
            <div class="tabbable-panel">
               <div class="tabbable-line">
                  <ul class="nav nav-tabs">
                     <li class="active">
                        <a href="#tab_default_1" data-toggle="tab">
                           ALL </a>
                     </li>
                     <li>
                        <a href="#tab_default_2" data-toggle="tab">
                           AVAILABLE </a>
                     </li>
                     <li>
                        <a href="#tab_default_3" data-toggle="tab">
                           REDEEMED </a>
                     </li>
                     <li>
                        <a href="#tab_default_4" data-toggle="tab">
                           EXPIRED </a>
                     </li>
                  </ul>
                  <div class="tab-content">
                     <div class="tab-pane active" id="tab_default_1">
                        <div class="table-responsive whitebox">
                           @if(count($purchased_vouchers['all']) > 0 )
                              @foreach($purchased_vouchers['all'] as $value)
                                 <table class="table">
                                    <tbody>
                                    <tr>
                                       <td>
                                          Purchased On: {{date('F d, Y', strtotime($value->created_at))}}<br>
                                          @if($value->redeemed == 0)
                                             Expiry: {{date('F d, Y', strtotime($value->expiry_date))}}
                                          @endif
                                       </td>
                                       <td>
                                          {{$value->heading}}<br>
                                          @if($value->redeemed == 1)
                                             <span class="btn" style="color:#fff; background-color: lightgrey;cursor: default; padding: 0px 5px;">Redeemed</span>
                                          @endif
                                       </td>
                                       <td> {{$value->partner_name}}<br> {{$value->partner_area}}</td>
                                       <td class="center">
                                          <a href="{{url('users/'.session('customer_username').'/deal_details/'.$value->id)}}"><button class="btn btn-primary">
                                                View details </button></a><br><br>
                                          @if($value->redeemed == 1)
                                             @if($value->review_id == null)
                                                <?php $review_submit_url = url('createReview/'.$value->partner_account_id.'/'.$value->id); ?>
                                                <button class="btn btn-green"
                                                        onclick="createReview('{{ $review_submit_url }}',
                                                                '{{ReviewType::DEAL}}')">Review</button>
                                             @else
                                                <span class="btn" style="color: #fff;background-color: #ffc107;">
                                             Reviewed</span>
                                             @endif
                                          @endif
                                       </td>
                                    </tr>
                                    </tbody>
                                 </table>
                              @endforeach
                           @else
                              <h4 class="no-data-found">No deals purchased.</h4>
                           @endif
                        </div>
                     </div>
                     <div class="tab-pane" id="tab_default_2">
                        <div class="table-responsive whitebox">
                           @if(count($purchased_vouchers['available']) > 0)
                              @foreach($purchased_vouchers['available'] as $value)
                                 <table class="table">
                                    <tbody>
                                    <tr>
                                       <td>Purchased On: {{date('F d, Y', strtotime($value->created_at))}}<br>
                                          Expiry: {{date('F d, Y', strtotime($value->expiry_date))}}
                                       </td>
                                       <td>{{$value->heading}}</td>
                                       <td> {{$value->partner_name}}<br> {{$value->partner_area}}</td>
                                       <td style="text-align:center">
                                          <a href="{{url('users/'.session('customer_username').'/deal_details/'.$value->id)}}"><button class="btn btn-primary">
                                                View details </button>
                                          </a>
                                       </td>
                                    </tr>
                                    </tbody>
                                 </table>
                              @endforeach
                           @else
                              <h4 class="no-data-found">No available deals.</h4>
                           @endif
                        </div>
                     </div>
                     <div class="tab-pane" id="tab_default_3">
                        <div class="table-responsive whitebox">
                           @if(count($purchased_vouchers['redeemed']) > 0)
                              @foreach($purchased_vouchers['redeemed'] as $value)
                                 <table class="table">
                                    <tbody>
                                    <tr>
                                       <td> Purchased On: {{date('F d, Y', strtotime($value->created_at))}}</td>
                                       <td>{{$value->heading}}</td>
                                       <td>       {{$value->partner_name}}<br> {{$value->partner_area}}</td>
                                       <td style="text-align:center">  <a href="{{url('users/'.session('customer_username').'/deal_details/'.$value->id)}}"><button class="btn btn-primary">
                                                View details </button></a><br><br>
                                          @if($value->review_id == null)
                                             <?php $review_submit_url = url('createReview/'.$value->partner_account_id.'/'.$value->id); ?>
                                             <button class="btn btn-green" onclick="createReview('{{ $review_submit_url }}', '{{ReviewType::DEAL}}')">Review</button>
                                          @else
                                             <span class="btn" style="color:#fff; background-color: #13CE66;cursor: default; padding: 0px 5px;">Reviewed</span>
                                          @endif
                                       </td>
                                    </tr>
                                    </tbody>
                                 </table>
                              @endforeach
                           @else
                              <h4 class="no-data-found">No redeemed deals.</h4>
                           @endif
                        </div>
                     </div>
                     <div class="tab-pane" id="tab_default_4">
                        <div class="table-responsive whitebox">
                           @if(count($purchased_vouchers['expired']) > 0)
                              @foreach($purchased_vouchers['expired'] as $value)
                                 <table class="table">
                                    <tbody>
                                    <tr>
                                       <td>       Purchased On: {{date('F d, Y', strtotime($value->created_at))}}<br>
                                          Expired On: {{date('F d, Y', strtotime($value->expiry_date))}}
                                       </td>
                                       <td>          {{$value->heading}}</td>
                                       <td> {{$value->partner_name}}<br> {{$value->partner_area}}</td>
                                       <td style="text-align:center">         <a href="{{url('users/'.session('customer_username').'/deal_details/'.$value->id)}}"><button class="btn btn-primary">
                                                View details </button></a>
                                       </td>
                                    </tr>
                                    </tbody>
                                 </table>
                              @endforeach
                           @else
                              <h4 class="no-data-found">No expired deals.</h4>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
@include('useracc.commonDivs')
@include('footer')
@include('footer-js.user-account-js')
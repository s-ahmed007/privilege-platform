@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         <h3>Card Delivery Information</h3>
         <a class="btn btn-all" href="{{url('card-delivery/all')}}">All</a>
         <a class="btn btn-guest" href="{{url('card-delivery/ordered')}}">Ordered</a>
         <a class="btn btn-premium" href="{{url('card-delivery/delivered')}}">Delivered</a>
      </div>
      <div class="title_right">
         <div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">
            <form action="{{ url('deliveryCustomerById') }}" method="post">
               {{csrf_field()}}
               <div class="form-group">
                  <label for="customerSearchKey">Search Confirmed Member</label><br>
                  <input type="text" class="form-control" name="customerSearchKey" id="customerSearchKey"
                     placeholder="Customer with name, E-mail or phone" style="width: 100%;">
               </div>
            </form>
         </div>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($card_delivery_list)
               <table class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>S/N</th>
                        <th>Customer Information</th>
                        <th>Transaction Information</th>
                        <th>Delivery Details</th>
                        <th>Delivery Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($card_delivery_list as $customerInfo)
                     <tr <?php if ($customerInfo->delivery_type == 1)
                        echo "style='background-color: #ffedc9; background-image: linear-gradient(to top right, #ffd3c9, #c9f7ff, #cdc9ff);'";
                        ?>>
                        <th>{{ $customerInfo->serial }}</th>
                        <td>Card No: <b style="color: #007bff;">{{ $customerInfo->cid }}</b> <br>
                           Name: {{ $customerInfo->customer_full_name }}
                           <br> Mobile: {{ $customerInfo->customer_contact_number }} <br>
                           Email: {{ $customerInfo->customer_email }}
                        </td>
                        <td>
                           @if ($customerInfo->customer_type==2)
                           <p class="card-type-premium">Premium Member</p>
                           @endif
                           @if($customerInfo->delivery_type == 6 || $customerInfo->delivery_type == 7)
                           <span style="color: red; line-height: 0;">(Card Lost)</span>
                           @endif
                           @if($customerInfo->delivery_type == 3 || $customerInfo->delivery_type == 4 || $customerInfo->delivery_type == 6 || $customerInfo->delivery_type == 7)
                           @if($customerInfo->order_date != null)
                           <p>Order date: <b>{{ date("M d, Y h:i A", strtotime($customerInfo->order_date)) }}</b></p>
                           <p>Approve date: <b>{{ date("F d, Y", strtotime($customerInfo->tran_date))}}</b></p>
                           @else
                           <p>Order date: <b>Not Available</b></p>
                           <p>Approve date: <b>{{ date("F d, Y", strtotime($customerInfo->tran_date))}}</b></p>
                           @endif
                           @else
                           <p>Order date: <b>{{ date("M d, Y h:i A", strtotime($customerInfo->tran_date))}}</b></p>
                           @endif
                           <p>Transaction ID: <b>{{ $customerInfo->tran_id }}</b></p>
                           <p>Via: <b>
                              @if($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::web ||
                              $customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::rbd_admin)
                              Website
                              @elseif($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::android)
                              Android
                              @elseif($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::ios)
                              IOS
                              @elseif($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::sales_app)
                              Sales App
                              @else
                              N/A
                              @endif
                              </b>
                           </p>
                        </td>
                        <td>
                           @if($customerInfo->delivery_type==2)
                           <span>Office Pickup</span>
                           @else
                           <select class="form-control" id="delivery_type_{{$customerInfo->cid}}"
                              onfocus="previous_delivery_type({{ $customerInfo->cid }})"
                              onchange="update_delivery_type({{ $customerInfo->cid }})">
                              <option <?php if ($customerInfo->delivery_type == '') echo 'selected disabled';?>>
                                 -----
                              </option>
                              <option value="1" <?php if ($customerInfo->delivery_type == 1) echo 'selected';?>>
                                 Online Pay
                              </option>
                              <option value="4" <?php if ($customerInfo->delivery_type == 4) echo 'selected';?>>
                                 COD
                              </option>
                              <option value="5" <?php if ($customerInfo->delivery_type == 5) echo 'selected';?>>
                                 Customization
                              </option>
                              <option value="6" <?php if ($customerInfo->delivery_type == 6) echo 'selected';?>>
                                 COD (Lost-card)
                              </option>
                              <option value="7" <?php if ($customerInfo->delivery_type == 7) echo 'selected';?>>
                                 Customization (Lost-card)
                              </option>
                              <option value="3" <?php if ($customerInfo->delivery_type == 3) echo 'selected';?>>
                                 Pre-Order COD
                              </option>
                              <option value="9" <?php if ($customerInfo->delivery_type == 9) echo 'selected';?>>
                                 Spot Delivery
                              </option>
                              <option value="10" <?php if ($customerInfo->delivery_type == 10) echo 'selected';?>>
                                 Influencer
                              </option>
                              <option value="11" <?php if ($customerInfo->delivery_type == 11) echo 'selected';?>>
                                 Trial
                              </option>
                              <option value="12" <?php if ($customerInfo->delivery_type == 12) echo 'selected';?>>
                                 Renew
                              </option>
                           <option value="13" <?php if ($customerInfo->delivery_type == \App\Http\Controllers\Enum\DeliveryType::made_by_admin) echo 'selected';?>>
                               Admin
                           </option>
                           </select>
                           @endif
                           <p class="middle">
                              <span style="text-decoration: underline;">PAID AMOUNT</span><br>
                              <span class="btn-all" style="padding: 1px;">৳<span id="card_amount_{{$customerInfo->cid}}">
                              {{ intval($customerInfo->total_payable) }}</span></span><br>
                              <b>
                           <p class="middle">Duration : {{ $customerInfo->month }}
                                   {{ $customerInfo->month > 1 ? ' Months':'Month'}}</p></b>
                           <span><b>{{isset($customerInfo->promo_id) ? 'Promo used : '.$customerInfo->promo_code : '' }}</b></span>
                           </p>
                           <!-- This portion for updating incorrect COD Price : Starts -->
                           {{--@if($customerInfo->delivery_type == 2 || $customerInfo->delivery_type == 3 || $customerInfo->delivery_type == 4 || $customerInfo->delivery_type == 6 || $customerInfo->delivery_type == 7)
                           <div style="white-space: nowrap; font-size: 12px">
                              <form action="">
                                 <span>
                                 <input type="text" name="actual_price" maxlength="4" placeholder="Actual Price(৳)" id="actual_price_{{$customerInfo->cid}}" style="width: 60%; background-color: lightgoldenrodyellow" required>
                                 <button type="button" class="btn btn-delete" onclick="update_price({{ $customerInfo->cid }})">Update</button>
                                 </span>
                              </form>
                           </div>
                           @endif--}}
                           <!-- This portion for updating incorrect COD Price : Ends -->
                        </td>
                        <td>
                           <select class="form-control" id="status_block_{{$customerInfo->cid}}"
                              style="font-weight: bold"
                              onchange="change_delivery_status({{ $customerInfo->cid }})">
                              <option value="1"
                                 <?php if ($customerInfo->delivery_status == 1) echo 'selected';?> onchange="change_delivery_status(1, {{ $customerInfo->cid }})">
                                 Ordered
                              </option>
                              <option value="2"
                                 <?php if ($customerInfo->delivery_status == 2) echo 'selected';?> onchange="change_delivery_status(2, {{ $customerInfo->cid }})">
                                 Given to rapido
                              </option>
                              <option value="3"
                                 <?php if ($customerInfo->delivery_status == 3) echo 'selected';?> onchange="change_delivery_status(3, {{ $customerInfo->cid }})">
                                 Delivered
                              </option>
                           </select>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
                  <tfoot>
                     <tr>
                     </tr>
                  </tfoot>
               </table>
               {{$card_delivery_list->links()}}
               @else
               <div style="font-size: 1.4em; color: red;">
                  {{ 'No customers found.' }}
               </div>
               @endif
            </div>
            <input type="hidden" id="previous_delivery_type" name="previous_delivery_type" value="0"/>
         </div>
      </div>
   </div>
</div>
<script>
   //JAVASCRIPT to change delivery status
   function change_delivery_status(customer_id) {
       var current_status = document.getElementById("status_block_" + customer_id).value;
       var url = "{{ url('/change_delivery_status') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {
               '_token': '<?php echo csrf_token(); ?>',
               'current_status': current_status,
               'customer_id': customer_id
           },
           success: function (data) {
               console.log(data);
           }
       });
   }

   //JAVASCRIPT to update delivery type
   function update_delivery_type(customer_id) {
       if (confirm("Are you sure to change delivery type?")) {
           var delivery_type = document.getElementById("delivery_type_" + customer_id).value;
           var url = "{{ url('/update_delivery_type') }}";
           $.ajax({
               type: "POST",
               url: url,
               data: {
                   '_token': '<?php echo csrf_token(); ?>',
                   'delivery_type': delivery_type,
                   'customer_id': customer_id
               },
               success: function (data) {
                   console.log(data);
               }
           });
       } else {
           var previous_delivery_type = document.getElementById('previous_delivery_type');
           var previous = parseInt(previous_delivery_type.value);
           $("#delivery_type_" + customer_id).val(previous);
           return false;
       }
   }

   //JAVASCRIPT to get previous delivery type
   function previous_delivery_type(customer_id) {
       var delivery_type = document.getElementById("delivery_type_" + customer_id).value;
       var previous_delivery_type = document.getElementById('previous_delivery_type');
       previous_delivery_type.value = delivery_type;
   }

   //JAVASCRIPT to update actual price
   function update_price(customer_id) {
       var updated_price = document.getElementById("actual_price_" + customer_id).value;
       if(updated_price.toString().length < 3 || updated_price.toString().length > 4 || isNaN(updated_price) == true){
           alert('Incorrect Price');
           return false;
       }
       if (confirm("Updated Amount is: BDT " + updated_price)) {
           var url = "{{ url('/update_actual_price') }}";
           $.ajax({
               type: "POST",
               url: url,
               data: {
                   '_token': '<?php echo csrf_token(); ?>',
                   'updated_price': updated_price,
                   'customer_id': customer_id
               },
               success: function (data) {
                   console.log(data);
                   if (data == 1) {
                       $("#card_amount_" + customer_id).empty();
                       $("#card_amount_" + customer_id).html(updated_price);
                   } else {
                       alert('Error Occurred! Try Again!');
                   }
               }
           });
       } else {
           return false;
       }
   }
</script>
@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
   $(function () {
       $("#customerSearchKey").autocomplete({
           source: '{{url('/customerByKey')}}',
           autoFocus: true,
           delay: 500
       });
   });
</script>
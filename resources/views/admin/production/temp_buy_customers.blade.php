@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         @if (session('status'))
         <div class="alert alert-success">
            {{ session('status') }}
         </div>
         @elseif (session('delete customer'))
         <div class="alert alert-danger">
            {{ session('delete customer') }}
         </div>
         @elseif(session('try_again'))
         <div class="alert alert-warning">
            {{ session('try_again') }}
         </div>
         @elseif(session('cod_exists'))
         <div class="alert alert-warning">
            {{ session('cod_exists') }}
         </div>
         @endif
         <h3>Customers attempt to purchase membership</h3>
      </div>
      <div class="title_right">
         <div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">
            <form action="{{ url('temp-customer') }}" method="post">
               {{csrf_field()}}
               <div class="form-group">
                  <label for="customerSearchKey">Search Temporary Customer</label><br>
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
               @if($profileInfo)
               <table class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>S/N</th>
                        <th style="width: 10%">Image</th>
                        <th style="width: 20%">Customer ID</th>
                        <th style="width: 20%">Customer Info</th>
                        <th>Membership</th>
                        <th>Attempt Date</th>
                        @if(session('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                        <th>Action</th>
                        @endif
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($profileInfo as $customerInfo)
                     <tr>
                        <th>{{ $customerInfo->serial }}</th>
                        <td><img src="{{asset($customerInfo->customer_profile_image)}}" width="100%"
                           style="border-radius: 50%"></td>
                        <td>{{ $customerInfo->customer_id }}<br>
                           @if($customerInfo->platform != null)
                              Via:
                              @if($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::web)
                                 <span class="website-label">Website</span>
                              @elseif($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::android)
                                 <span class="android-label">Android</span>
                              @elseif($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::ios)
                                 <span class="ios-label">IOS</span>
                              @endif
                           @endif
                        </td>
                        <td>
                           <b>{{ $customerInfo->customer_full_name }}</b>
                           <br>{{ $customerInfo->customer_contact_number }}
                           <br>{{ $customerInfo->customer_email }}
                           <br>{{ $customerInfo->tran_id }}
                        </td>
                        <td><b>{{ $customerInfo->month }}{{$customerInfo->month > 1 ? ' months' : ' month'}}</b></td>
{{--                        <td>--}}
{{--                           @if($customerInfo->delivery_type==1) --}}
{{--                              <p class="temp-pay">Online Pay</p>--}}
{{--                           @elseif ($customerInfo->delivery_type==3) --}}
{{--                              <p class="temp-pay">Pre-Order COD</p>--}}
{{--                           @elseif ($customerInfo->delivery_type==4) --}}
{{--                              <p class="temp-pay">COD</p>--}}
{{--                           @elseif ($customerInfo->delivery_type==5) --}}
{{--                              <p class="temp-pay">Customization</p>--}}
{{--                           @elseif ($customerInfo->delivery_type==6) --}}
{{--                              <p class="temp-pay">COD (Lost-card)</p>--}}
{{--                           @elseif ($customerInfo->delivery_type==7) --}}
{{--                              <p class="temp-pay">Customization (Lost-card)</p>--}}
{{--                           @elseif ($customerInfo->delivery_type==11)--}}
{{--                              <p class="temp-pay">Trial</p>--}}
{{--                           @else <b>Delivery : others</b>--}}
{{--                           @endif--}}
{{--                           <p class="temp-pay">Month: {{$customerInfo->month}}</p>--}}
{{--                        </td>--}}
                        <td>
                           {{$customerInfo->order_date != null ? date("F d, Y &#9202; h:i A", strtotime($customerInfo->order_date)) : 'N/A'}}
                        </td>
                        @if(\Illuminate\Support\Facades\Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                        <td>
                           <a href="{{url('/delete-temp-customer/'.$customerInfo->customer_id)}}" class="btn btn-danger"
                              onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                        @endif
                     </tr>
                     @endforeach
                  </tbody>
                  <tfoot>
                     <tr>
                     </tr>
                  </tfoot>
               </table>
               {{ $profileInfo->links() }}
               @else
               <div style="font-size: 1.4em; color: red;">
                  {{ 'No customers found.' }}
               </div>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
   $(function () {
       $("#customerSearchKey").autocomplete({
           source: '{{url('/customerByTemp')}}',
           autoFocus: true,
           delay: 500
       });
   });
</script>
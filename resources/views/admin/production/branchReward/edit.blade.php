@include('admin.production.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>
<script>tinymce.init({selector: '#textarea1', plugins: "lists, advlist"});</script>
<script>tinymce.init({selector: '#textarea2', plugins: "lists, advlist"});</script>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         @if ($errors->any())
         <div class="alert alert-danger">
            <ul>
               @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
               @endforeach
            </ul>
         </div>
         @endif
         <h3>Edit Reward</h3>
      </div>
      <div class="title_right">
         <h5 style="color: red; float:right;">Fields with * are required</h5>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="row">
      <div class="col-md-12">
         <div class="x_panel">
            <div class="x_content">
               <br/>
               <form class="form-horizontal form-label-left" method="post" action="{{ url('/admin/reward/'.$offer_details->id) }}"
                  enctype="multipart/form-data">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="_method" value="PUT"/>
                  <input type="hidden" name="prev_url" value="{{url()->previous()}}"/>
                  @if($partner_info)
                  <input type="hidden" name="branch_id" value="{{$partner_info->id}}">
                  @else
                  <input type="hidden" name="branch_id" value="{{\App\Http\Controllers\Enum\AdminScannerType::royalty_branch_id}}">
                  @endif
                  <div class="row">
                     <div class="col-md-9">
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-6 col-xs-12">Date Duration:
                           @if(isset($partner_info))
                           <?php $exp_date = date("d-m-Y", strtotime($partner_info->info->expiry_date)); ?>
                           (exp date: {{$exp_date}})<span style="color:red;font-size: 1.5em">*</span>
                           @endif
                           </label>
                           <div class="col-md-4 col-sm-6 col-xs-12">
                              @if(isset($offer_details->date_duration))
                              <?php
                                 $point_details = $offer_details->date_duration;
                                 $from_date = date("Y-m-d", strtotime($point_details[0]['from']));
                                 ?>
                              <input
                                 type="date" class="form-control" name="date_from2" value="{{$from_date}}" required>
                              @else
                              <input
                                 type="date" class="form-control" name="date_from2" required>
                              @endif
                           </div>
                           <div class="col-md-4 col-sm-6 col-xs-12">
                              @if(isset($offer_details->date_duration))
                              <?php
                                 $point_details = $offer_details->date_duration;
                                 $to_date = date("Y-m-d", strtotime($point_details[0]['to']));
                                 ?>
                              <input
                                 type="date" class="form-control" name="date_to2" value="{{$to_date}}" required>
                              @else
                              <input
                                 type="date" class="form-control" name="date_to2" required>
                              @endif
                           </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-6 col-xs-12">Required Fields:<span style="color:red;font-size: 1.5em">*</span></label>
                           <div class="col-md-8 col-sm-6 col-xs-12" style="margin: 10px 0px -10px 0px">
                              @if($phone)
                              <label><input type="checkbox" name="phone" checked> Phone</label>
                              @else
                              <label><input type="checkbox" name="phone"> Phone</label>
                              @endif
                              @if($email)
                              <label><input type="checkbox" name="email" checked> Email</label>
                              @else
                              <label><input type="checkbox" name="email"> Email</label>
                              @endif
                              @if($del_add)
                              <label><input type="checkbox" name="del_add" checked> Delivery Address</label>
                              @else
                              <label><input type="checkbox" name="del_add"> Delivery Address</label>
                              @endif
                              @if($others)
                              <label><input type="checkbox" name="others" onclick="otherReqField(this.checked)"
                                 checked> Others</label>
                              <input type="text" class="form-control" name="others_value" placeholder="Others"
                                 value="{{$others}}"><br>
                              @else
                              <label><input type="checkbox" name="others" onclick="otherReqField(this.checked)"> Others</label>
                              <input type="text" class="form-control" name="others_value" placeholder="Others"
                                 style="display: none;"><br>
                              @endif
                           </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-6 col-xs-12">Selling Credits:<span
                              style="color:red;font-size: 1.5em">*</span></label>
                           <div class="col-md-8 col-sm-6 col-xs-12">
                              @if(isset($offer_details->point))
                              <input type="text" class="form-control" name="selling_points"
                                 value="{{$offer_details->selling_point}}" required readonly>
                              @else
                              <input type="text" class="form-control" name="selling_points" value="" required
                                 readonly>
                              @endif
                           </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-6 col-xs-12">Selling Price:<span
                              style="color:red;font-size: 1.5em">*</span></label>
                           <div class="col-md-8 col-sm-6 col-xs-12">
                              @if(isset($offer_details->actual_price))
                              <input type="text" class="form-control" name="actual_price"
                                 value="{{$offer_details->actual_price}}" required>
                              @else
                              <input type="text" class="form-control" name="actual_price" value="" required>
                              @endif
                           </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-6 col-xs-12">Reward Heading:<span
                              style="color:red;font-size: 1.5em">*</span></label>
                           <div class="col-md-8 col-sm-6 col-xs-12">
                              @if(isset($offer_details->offer_description))
                              <input type="text" class="form-control" name="reward_description"
                                 value="{{$offer_details->offer_description}}" required>
                              @else
                              <input type="text" class="form-control" name="reward_description" value="" required>
                              @endif
                           </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-6 col-xs-12">Available Rewards:</label>
                           <div class="col-md-8 col-sm-6 col-xs-12">
                              @if(!$offer_details->counter_limit)
                              <span style="font-weight: bold; color: red;">Unlimited</span>
                              @endif
                              <input type="number" class="form-control" min="1" name="counter_limit" required
                                 placeholder="Counter limit" value="{{$offer_details->counter_limit}}" readonly>
                           </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-6 col-xs-12">Redeem Limit:</label>
                           <div class="col-md-8 col-sm-6 col-xs-12">
                              <input type="number" class="form-control" min="1" name="scan_limit" placeholder="Redeem limit"
                              value="{{$offer_details->scan_limit}}">
                           </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-6 col-xs-12">Offer Description:<span
                              style="color:red;font-size: 1.5em">*</span></label>
                           <div class="col-md-8 col-sm-6 col-xs-12">
                              @if(isset($offer_details->tnc))
                              <textarea id="textarea1"
                                 name="reward_full_description">{{$offer_details->offer_full_description}}</textarea>
                              @else
                              <textarea id="textarea1" name="reward_full_description">Terms</textarea>
                              @endif
                           </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-6 col-xs-12">Terms & Conditions:<span
                              style="color:red;font-size: 1.5em">*</span></label>
                           <div class="col-md-8 col-sm-6 col-xs-12">
                              @if(isset($offer_details->tnc))
                              <textarea id="textarea2" name="tnc">{{$offer_details->tnc}}</textarea>
                              @else
                              <textarea id="textarea2" name="tnc">Terms</textarea>
                              @endif
                           </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-3 col-xs-12">Image:</label>
                           <div class="col-md-8 col-sm-8 col-xs-12">
                              <input type="file" class="form-control" style="height: unset;" name="offerImage"/>
                           </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-4 col-sm-6 col-xs-12">Priority:<span style="color:red;font-size: 1.5em">*</span></label>
                           <div class="col-md-8 col-sm-6 col-xs-12">
                              <input type="text" name="priority" class="form-control"
                                     value="{{$offer_details->priority}}" required>
                           </div>
                       </div>
                      </div>
                     </div>
                     <div class="col-md-3"></div>
                  </div>
                  <div class="ln_solid"></div>
                  <div class="form-group">
                     <div class="col-xs-12">
                           <button type="submit" class="btn btn-activate pull-right">Submit</button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script type="text/javascript">
   $(document).ready(function () {
       var time_duration_count = $("#time_duration_count2").val();
       var i = time_duration_count - 1;
       //if there is no previously added extra discounts
       if (isNaN(i)) {
           var i = 0;
       }
       $('#add2').click(function () {
           i++;
           $('#time_durations2').append(
               '<div id="row2' + i + '" class="col-md-12">'
               + '<div class="col-md-4 col-sm-4 col-xs-12">'
               + '<input type="time" class="form-control" name="time_duration_from2[]" value="12:00">'
               + '</div>'
               + '<div class="col-md-4 col-sm-4 col-xs-12">'
               + '<input type="time" class="form-control" name="time_duration_to2[]" value="12:00">'
               + '</div>'
               + '<div class="col-md-2 col-sm-2 col-xs-12">'
               + '<button name="remove2" id="' + i + '" class="btn btn-danger btn_remove2">Remove</button>'
               + '</div>'
               + '</div>'
           );
           //Following script to remove newly add discount blocks
           $(document).on('click', '.btn_remove2', function () {
               var button_id = $(this).attr("id");
               $('#row2' + button_id + '').remove();
           });
       });
       //Following script to remove previous discount blocks
       $(document).on('click', '.btn_remove2', function () {
           var button_id = $(this).attr("id");
           $('#row2' + button_id + '').remove();
       });
   });
   
   function otherReqField(value) {
       if(value){
           $('input[name$="others_value"]').css('display', 'block').prop('required', true);
       }else{
           $('input[name$="others_value"]').css('display', 'none').prop('required', false);
       }
   }
</script>
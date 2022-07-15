@include('header')
<style>
   * {transition: unset !important;}
</style>
<?php
   if (Session::get('user_profile_image_name') != '') {
       Session::forget('user_profile_image_name');
   }
   
   if (Session::get('user_profile_image') != '') {
       Session::forget('user_profile_image');
   }
   ?>
<script src="{{asset('js/imageCrop/jquery.js')}}"></script>
<script src="{{asset('js/imageCrop/croppie.js')}}"></script>
<link href="{{asset('admin/vendors/croppie/croppie.css')}}" rel="stylesheet">
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Edit Profile</h2>
         <p>Update your personal information</p>
      </div>
   </div>
</section>
<section id="contact" class="contact">
   <div class="container">
      @if (isset($profileInfo))
      <div class="row">
         <div class="page-title">
            <div style="text-align: center;">
               @if (session('status'))
               <br>
               <div class="alert alert-success">
                  {{ session('status') }}
               </div>
               @elseif (session('invalid_dob'))
               <br>
               <div class="alert alert-danger">
                  {{ session('invalid_dob') }}
               </div>
               @endif
            </div>
         </div>
         <div class="col-lg-4">
            <!-- <div class="info"> -->
            <div>
               <form action="{{ url('updateUserProPic/'. $profileInfo->customer_id) }}" class="form-horizontal"
                     method="post" onsubmit="return imageCheck();">
                  {{ csrf_field() }}
                  <div class="img-section">
                     <span class="error_image"></span><span class="correct_image"></span>
                     <div style="cursor: pointer">
                        <img id="upload-cropped-image" src="{{asset($profileInfo->customer_profile_image)}}"
                           class="imgCircle" alt="Profile picture" onclick="editImage()" width="100%"
                             style="height: 150px;width: 150px;border: 1px solid #007bff;">
                     </div>
                     <div class="upload-btn-wrapper">
                        <div class="image-upload">
                           <button class="btn btn-primary">Upload Profile Picture</button>
                           <input type="file" id="upload" name="customerProfileImage" accept="image/*"
                              data-target="#cropModal" data-toggle="modal" style="cursor: pointer">
                              <p>Add a nice photo of yourself for your profile.</p>
                        </div>
                     </div>
                  </div>
                  {{--modal to crop image--}}
                  <div id="cropModal" class="modal fade" role="dialog" style="top: 5%;">
                     <div class="modal-dialog" style="z-index:99999;text-align: center">
                        <div class="modal-content">
                           <div class="modal-header"><h4 class="modal-title">Upload Profile Picture</h4>
                              <button type="button" class="close" data-dismiss="modal">
                              <i class="cross-icon"></i>
                              </button>
                           </div>
                           <div class="modal-body">
                              <div id="upload-demo"></div>
                              <button class="btn btn-primary upload-result" data-dismiss="modal"><span>Upload</span>
                              </button>
                           </div>
                        </div>
                     </div>
                  </div>
                  {{--modal to crop image ends--}}
                  <p class="center">
                     <button type="submit" class="btn btn-primary" style="display: none;" id="submit_photo_btn">
                     Submit</button>
                  </p>
               </form>
            </div>
         </div>
         <div class="col-lg-8 mt-5 mt-lg-0" data-aos="fade-left" data-aos-delay="200">
            <div class="form-vertical input-form-box">
               <div class="col-sm-offset-2 col-sm-8">
                  @if ($errors->getBag('default')->first('customer_email'))
                  <span class="red"> {{ $errors->getBag('default')->first('customer_email') }}</span>
                  @elseif(session('email_updated'))
                  <span class="green">{{session('email_updated')}}</span>
                  @endif
                  <div class="form-row">
                     <div class="col-md-9 col-sm-9 col-xs-12 form-group">
                        <input type="text" name="customer_email" class="form-control" id="email"
                           disabled value="{{ $profileInfo->customer_email }}">
                     </div>
                     <div class="col-md-3 col-sm-3 col-xs-12 button-col form-group">
                        <button id="" class="btn btn-primary"
                           onclick="email_edit_active()" style="margin: unset;">Edit E-mail Address
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <div class="form-vertical input-form-box">
               <div class="col-sm-offset-2 col-sm-8">
                  @if ($errors->getBag('default')->first('customer_username'))
                   <span class="red"> {{ $errors->getBag('default')->first('customer_username') }}</span>
                  @elseif(session('username_updated'))
                  <span class="green">{{session('username_updated')}}</span>
                  @endif
                  <div class="form-row">
                     <div class="col-md-9 col-sm-9 col-xs-12 form-group" style="padding-bottom: unset;">
                        <form method="post" action="{{url('updateUserUsername')}}">
                           <div class="col-md-9 col-sm-9 col-xs-12 form-group p-lr-unset"
                                style="padding-bottom: unset;">
                              {{csrf_field()}}
                              <input type="text" name="customer_username" class="form-control" id="username"
                                 disabled value="{{ $profileInfo->customer_username }}" onkeyup="usernameSpace()"
                                 onmouseup="usernameSpace()" minlength="1" maxlength="15">
                           </div>
                           <div class="col-md-3 col-sm-3 col-xs-12 button-col form-group" id="username_edit_done"
                                style="display: none;padding-bottom: unset; margin-bottom: unset;">
                              <button type="submit" class="btn btn-primary">Submit</button>
                           </div>
                        </form>
                     </div>
                     <div id="username_edit_active" class="col-md-3 col-sm-3 col-xs-12 button-col form-group">
                        <button class="btn btn-primary" onclick="username_edit_active()" style="margin: unset;">
                            Edit Username</button>
                     </div>
                  </div>
               </div>
            </div>
            <div class="form-vertical input-form-box">
               <?php $contact = substr($profileInfo->customer_contact_number, -10);?>
               <div class="col-sm-offset-2 col-sm-8">
                  <span class="correct_phone"></span>
                  <span class="error_phone" id="error_phone"></span>
                  <div class="form-row">
                     <div class="col-md-9 col-sm-9 col-xs-12 form-group">
                        <form>
                           <div class="col-md-3 col-sm-6 col-xs-6 p-lr-unset">
                              <input class="form-control" value="+880" id="country_code" disabled>
                           </div>
                           <div class="col-md-6 col-sm-6 col-xs-6 p-lr-unset">
                              <input class="form-control" value="{{ $contact }}" id="phone_number" disabled>
                           </div>
                        </form>
                     </div>
                     <div class="col-md-3 col-sm-3 col-xs-12 button-col form-group">
                        <button id="phone_edit_active" class="btn btn-primary" onclick="updatePhone()"
                                style="margin: unset;">Edit Phone Number</button>
                        <!-- <button id="phone_edit_active" class="btn btn-primary" onclick="smsLogin()" style="margin: unset;">Edit</button> -->
                     </div>
                  </div>
               </div>
            </div>
{{--            <div class="form-vertical input-form-box" id="fake_pin_section">--}}
{{--               <div class="col-sm-offset-2 col-sm-8">--}}
{{--                  @if (session('pin_updated'))--}}
{{--                  <div style="color:green;">--}}
{{--                     <ul>--}}
{{--                        <li>{{ session('pin_updated') }}</li>--}}
{{--                     </ul>--}}
{{--                  </div>--}}
{{--                  @endif--}}
{{--                  <div class="form-row">--}}
{{--                     <div class="col-md-9 col-sm-9 col-xs-12 form-group">--}}
{{--                        <input type="password" value="1234" class="form-control" disabled>--}}
{{--                     </div>--}}
{{--                     <div class="col-md-3 col-sm-3 col-xs-12 button-col form-group">--}}
{{--                        <button class="btn btn-primary" id="pin_edit_active" onclick="pin_edit_active()"--}}
{{--                                style="margin: unset;">--}}
{{--                        Edit PIN--}}
{{--                        </button>--}}
{{--                     </div>--}}
{{--                  </div>--}}
{{--               </div>--}}
{{--            </div>--}}
            <div class="form-vertical input-form-box" id="pin_section" style="display: none;">
               <div class="col-sm-offset-2 col-sm-8">
                  <form method="post" action="{{url('updateUserPin')}}" onsubmit="return checkPin()">
                     {{csrf_field()}}
                     <div class="row">
                        <div class="col-md-9 col-sm-9 col-xs-12 form-group">
                           @if (session('wrong_cur_credential'))
                           <div style="color: #cc0404;">
                              <ul>
                                 <li>{{ session('wrong_cur_credential') }}</li>
                              </ul>
                           </div>
                           @endif
                           <label for="password">Current PIN</label>
                           <input type="password" id="current_pin" name="current_pin" class="form-control"
                              maxlength="4" required>
                           <span toggle="#current_pin"
                              class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                        </div>
                        <div class="col-md-3"></div>
                     </div>
                     <label for="pin">New PIN</label>
                     <span class="set_pin_error red"></span>
                     @if (session('error_new_pin'))
                     <span class="set_pin_error red">{{session('error_new_pin')}}</span>
                     @endif
                     <div class="row">
                        <div class="col-md-9 col-sm-9 col-xs-12 form-group">
                           <input type="password" id="new_pin" class="form-control" name="new_pin" maxlength="4">
                           <span toggle="#new_pin"
                              class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12 edit-pin-btn">
                           <button type="submit" class="btn btn-primary">
                           Submit
                           </button>
                        </div>
                     </div>
                  </form>
                  <p style="cursor:pointer; color: #0c85d0;" onclick="resetPin({{$contact}})">Forgot current PIN?</p>
               </div>
            </div>
         </div>
      </div>
      @endif
   </div>
</section>
{{--modal to show your card approval is pending--}}
<div id="editEmailRequestModal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">Sorry!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <div class="no-info">
               <p>To change your E-mail please contact our customer
                  support.
               </p>
            </div>
         </div>
      </div>
   </div>
</div>
<div id="phone_change_modal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">Update your phone number</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="user_deactive_text">
            <div class="row m-0">
               <div class="col-md-12">
                  <p class="update_phone_heading"></p>
                  <p class="update_phone_status"></p>
                  <!-- <br> -->
               </div>
               <div id="update_phone">
                  <div class="form-vertical input-form-box">
                     <div class="col-md-12">
                        <span class="error_edit_phone"></span>
                     </div>
                      <div class="col-md-6 col-md-offset-3">
                         <br>
                         <div class="col-md-3 col-sm-3 col-xs-6 p-lr-unset">
                            <input class="form-control" value="+880" id="country_code" disabled
                                   style="border-radius: 4px 0 0 4px;">
                         </div>
                         <div class="col-md-9 col-sm-9 col-xs-6 p-lr-unset">
                            <input class="form-control" id="edit_phone_number" placeholder="1XXXXXXXXX" minlength="10"
                                   maxlength="10" style="border-radius: 0 4px 4px 0;">
                         </div>
                     </div>

                     <div class="col-md-12" style="text-align: center;">
                        <br>
                       <button class="btn btn-primary verify_button" onclick="return sendOtp()">Next</button>
                       <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif"
                            alt="Royalty Loading GIF" class="loading-gif" style="display: none; position: relative;"
                            title="Royalty loading icon">
                     </div>
                  </div>
               </div>
               <div id="verify_code" style="display: none; margin: 0 auto">
                  <p></p>
                  <div class="form-vertical input-form-box">
                     <div class="col-md-12 col-sm-12 col-xs-6 edit-phone-num">
                        <input class="form-control" placeholder="Enter verification code"
                           name="phone_verifying_code" id="phone_verifying_code" minlength="6" maxlength="6">
                     </div>
                     <div class="col-md-12">
                         <br>
                        <p class="middle">
                           <button class="btn btn-primary verify_button" onclick="return verifyOTP()">Verify</button>
                           <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif"
                                alt="Royalty Loading GIF" class="loading-gif"
                                style="display: none; position: relative;" title="Royalty loading icon">
                        </p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   function insertAccKitStat(phone) {
       var url = "{{ url('/setStatNumber') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'phone': phone},
           success: function (response) {
               localStorage.setItem('otp_stat_phone_id_edit', response['id']);
           }
       });
   }
   function updateStatNumber(id, status, phone=null) {
       var url = "{{ url('/updateStatNumber') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'id': id, 'status': status, 'phone': phone},
           success: function (data) {
               //acc_kit_stat updated
           }
       });
   }
   function updatePhone() {
       $(".update_phone_heading").text('Please note, if you change your phone number you have to verify it again.');
       $("#phone_change_modal").modal('show');
   }
   function sendOtp() {
       var phone = $("#edit_phone_number").val().replace(/\s/g, '');
       if (phone.length == 10 && /^\d+$/.test(phone)) {
          $(".error_edit_phone").empty();
          $(".loading-gif").css('display', 'block');
          var phone = $("#edit_phone_number").val();
          var url = "{{ url('/checkPhoneNumber') }}";
          $.ajax({
              type: "POST",
              url: url,
              data: {'_token': '<?php echo csrf_token(); ?>', 'phone': phone},
              success: function (data) {
                  if(data['customer'] === 'invalid'){
                      //insert into stats table
                      // insertAccKitStat(phone);
                      //proceed to verify code
                      $("#update_phone").css('display', 'none');
                      $(".update_phone_heading").text(data['message']);
                      $("#verify_code").css('display', 'block');
                      $(".phone_verify_msg").text(data['message']);
                      $("#verification_code_modal").modal('show');
                  }else{
                      $(".update_phone_heading").text('This phone number already exists.').css('color', 'red');
                  }
                  $(".loading-gif").css('display', 'none');
              }
          });
       }else{
         $(".error_edit_phone").text('Please enter a valid phone number').css('color', 'red');
         return false;
       }
   }
   function verifyOTP() {
       $(".loading-gif").css('display', 'inline-block');
       var phone = $("#edit_phone_number").val();
       var code = $("#phone_verifying_code").val();
       var verification_type = '{{\App\Http\Controllers\Enum\VerificationType::phone_verification}}';
   
       var url = "{{ url('/check_code_phone') }}";
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'code': code, 'phone': phone, 'type': verification_type},
           success: function (response) {
               var stat_id = localStorage.getItem('otp_stat_phone_id_edit');
               var update_phone_status = $(".update_phone_status");
               if(response.status){
                   // updateStatNumber(stat_id, 1, '+880'+phone);
                   var update_phone_url = "{{ url('/updateUserPhone') }}";
                   $.ajax({
                       type: "POST",
                       url: update_phone_url,
                       data: {'_token': '<?php echo csrf_token(); ?>', 'updatePhone': '+880'+phone},
                       success: function (update_response) {
                           //Update successful
                           if (update_response > 0) {
                               update_phone_status.text('Phone successfully updated')
                                   .css({'color': 'green', 'text-align': 'center'});
                           } else {//error occurred
                               update_phone_status.text('Phone successfully updated')
                                   .css({'color': 'green', 'text-align': 'center'});
                           }
                           window.setTimeout(function(){
                               // Move to a new location or you can do something else
                               window.location.href = "{{url()->current()}}";
                           }, 2000);
                       }
                   });
               }else{
                   update_phone_status.text(response.message).css({'color': 'red', 'text-align': 'center'});
                   // updateStatNumber(stat_id, 2);
               }
               $(".loading-gif").css('display', 'none');
           }
       });
   }
</script>
<!-- JAVASCRIPT For Edit E-mail, username, phone and pin Starts-->
<script>
   function username_edit_active(){
       $("#username_edit_active").hide();
       $("#username_edit_done").show();
       $('#username').prop("disabled", false);
   }
   
   function pin_edit_active(){
       $("#fake_pin_section").hide();
       $("#pin_section").show();
   }
   
   function email_edit_active() {
       $(".customerEmailEditModalCross").css('display', 'block');
       $("#email_edit_title").html('Update Your E-mail');
       $("#email_edit_text").html('Please note, if you change your E-mail address you have to verify it again in' +
           ' order to avail offers.');
       $("#email_edit_or_verify").val('edit');
       $("#email_verify_modal").modal('toggle');
   }
   
   function dob_edit_active() {
       $("#dob_edit_title").html('Update Your DOB');
       $("#dob_edit_text").html('You can only change your birthday once');
       $("#dob_update_modal").modal('toggle');
   }
</script>
<!-- JAVASCRIPT For Edit E-mail, phone and pin Ends-->
<script>
   function default_img_url() {
       return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAYAAAB5fY51AAAIq0lEQVR4Xu3UAQkAAAwCwdm' +
           '/9HI83BLIOdw5AgQIRAQWySkmAQIEzmB5AgIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+' +
           'UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw' +
           '/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgMADGTkBLe/7cXcAAAAASUVORK5CYII='
   }
   
   if (screen.width < 378) {
       $uploadCrop = $('#upload-demo').croppie({
           enableExif: true,
           viewport: {
               width: 200,
               height: 200,
               type: 'rectangle'
           },
           boundary: {
               width: 200,
               height: 200
           }
       });
   } else {
       $uploadCrop = $('#upload-demo').croppie({
           enableExif: true,
           viewport: {
               width: 300,
               height: 300,
               type: 'rectangle'
           },
           boundary: {
               width: 300,
               height: 300
           }
       });
   }
   
   $('#upload').on('change', function () {
       //initiate array of extension
       var fileTypes = ['jpg', 'png', 'jpeg'];
       var fullPath = document.getElementById('upload').value;
       if (fullPath) {
           var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
           var filename = fullPath.substring(startIndex);
           if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
               filename = filename.substring(1);
               //get extension of filename
               var ext = filename.split('.').pop().toLowerCase();
               //check if extension is allowed or not
               if ($.inArray(ext, fileTypes) != -1) {
                   //extension is allowed ; put image in the crop area to crop
                   var reader = new FileReader();
                   reader.onload = function (e) {
                       $uploadCrop.croppie('bind', {
                           url: e.target.result
                       }).then(function () {
                           console.log('jQuery bind complete');
                       });
                   }
                   reader.readAsDataURL(this.files[0]);
               } else {
                   $('#upload').val('');
                   alert('Please select an image file');
               }
           }
       }
   });
   
   $('.upload-result').on('click', function (ev) {
       $uploadCrop.croppie('result', {
           type: 'canvas',
           size: 'viewport'
       }).then(function (resp) {
           var url = "{{ url('/editUserImageSelf') }}";
           $.ajax({
               url: url,
               type: "POST",
               data: {
                   "_token": "{{ csrf_token() }}",
                   "image": resp
               },
               success: function (data) {
                   html = '<img src="' + resp + '" />';
                   // $("#upload-cropped-image").html(html);
                   $("#upload-cropped-image").attr("src", resp);
                   $("#submit_photo_btn").css('display', 'inline-block');
               }
           });
       });
   });
   
   /*This function is added for Image Reupload Facility: Start*/
   function editImage() {
       location.reload(true);
       editImage2();
   }
   
   function editImage() {
       $("#upload").click();
   }
   
   /*This function is added for Image Reupload Facility: End*/
</script>
<script>
   function imageCheck() {
       var default_img = document.getElementById("upload-cropped-image").getAttribute("src");
       if (default_img == 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png' || default_img == default_img_url()) {
           $('.error_image').html('Please select an image');
           return false;
       } else {
           $('.error_image').empty();
           return true;
       }
   }
</script>
<script>
   {{--$(document).ready(function () {--}}
   {{--    // initialize Account Kit with CSRF protection--}}
   {{--    AccountKit_OnInteractive = function () {--}}
   {{--        AccountKit.init(--}}
   {{--            {--}}
   {{--                appId: "149014475722955",--}}
   {{--                state: "b08d0b28daea6b8736011930f9dfac9f",--}}
   {{--                version: "v1.0",--}}
   {{--                fbAppEventsEnabled: true--}}
   {{--            }--}}
   {{--        );--}}
   {{--    };--}}
   {{--});--}}
   
   {{--// login callback--}}
   {{--function loginCallback(response) {--}}
   {{--    if (response.status === "PARTIALLY_AUTHENTICATED") {--}}
   {{--        var code = response.code;--}}
   {{--        var csrf = response.state;--}}
   {{--        //To get number from FB--}}
   {{--        var get_number_url = "{{ url('/getPhoneFromFB') }}";--}}
   {{--        $.ajax({--}}
   {{--            type: "POST",--}}
   {{--            url: get_number_url,--}}
   {{--            data: {'_token': '<?php echo csrf_token(); ?>', code: code, csrf: csrf},--}}
   {{--            success: function (data) {--}}
   {{--                console.log(data);--}}
   {{--                var country_code = data.prefix;--}}
   {{--                var phone_number = data.number;--}}
   {{--                if (isNaN(phone_number)) {--}}
   {{--                    alert('Something went wrong, please try again!');--}}
   {{--                    return false;--}}
   {{--                }--}}
   {{--                if (country_code != '880') {--}}
   {{--                    alert('Sorry! we accept only Bangladeshi number');--}}
   {{--                    return false;--}}
   {{--                }--}}
   {{--                $('#verified_phone').attr('value', phone_number);--}}
   {{--                var phone_full_number = '+880' + $('#verified_phone').val();--}}
   {{--                var phone_10_digit = phone_number;--}}
   {{--                // alert(data);--}}
   {{--                //check if this number already exists in database or not--}}
   {{--                var check_exists_url = "{{ url('/verifyPrePhone') }}";--}}
   {{--                $.ajax({--}}
   {{--                    type: "POST",--}}
   {{--                    url: check_exists_url,--}}
   {{--                    data: {'_token': '<?php echo csrf_token(); ?>', 'phone': phone_full_number},--}}
   {{--                    success: function (data) {--}}
   {{--                        //phone already exists--}}
   {{--                        if (data > 0) {--}}
   {{--                            var old_number = $('#old_number').val();--}}
   {{--                            $('#phone_number').val(old_number);--}}
   {{--                            $(".error_phone").html('Phone number already exists');--}}
   {{--                            $(".correct_phone").empty();--}}
   {{--                        } else {//phone verified--}}
   {{--                            $('#phone_number').prop('disabled', false);--}}
   {{--                            $('#phone_number').val(phone_10_digit);--}}
   {{--                            $('#phone_number').prop('disabled', true);--}}
   {{--                            $('#verified_phone').attr('data-verify-status', 1);--}}
   
   {{--                            var update_phone_url = "{{ url('/updateUserPhone') }}";--}}
   {{--                            $.ajax({--}}
   {{--                                type: "POST",--}}
   {{--                                url: update_phone_url,--}}
   {{--                                data: {'_token': '<?php echo csrf_token(); ?>', 'updatePhone': phone_full_number},--}}
   {{--                                success: function (update_response) {--}}
   {{--                                    //Update successful--}}
   {{--                                    if (update_response > 0) {--}}
   {{--                                        $(".correct_phone").html('Phone successfully updated');--}}
   {{--                                        $(".error_phone").empty();--}}
   {{--                                    } else {//error occurred--}}
   {{--                                        $(".error_phone").html('Please try again');--}}
   {{--                                        $(".correct_phone").empty();--}}
   {{--                                    }--}}
   {{--                                }--}}
   {{--                            });--}}
   {{--                        }--}}
   {{--                    }--}}
   {{--                });--}}
   {{--            }--}}
   {{--        });--}}
   
   {{--        // Send code to server to exchange for access token--}}
   {{--    } else if (response.status === "NOT_AUTHENTICATED") {--}}
   {{--        alert('You did not check your phone');--}}
   {{--        // handle authentication failure--}}
   {{--    } else if (response.status === "BAD_PARAMS") {--}}
   {{--        alert('Something went wrong');--}}
   {{--        // handle bad parameters--}}
   {{--    }--}}
   {{--}--}}
   
   {{--// phone form submission handler--}}
   {{--function smsLogin() {--}}
   {{--    var countryCode = document.getElementById("country_code").value;--}}
   {{--    var phoneNumber = document.getElementById("phone_number").value;--}}
   
   {{--    AccountKit.login(--}}
   {{--        'PHONE',--}}
   {{--        {countryCode: countryCode, phoneNumber: phoneNumber}, // will use default values if not specified--}}
   {{--        loginCallback--}}
   {{--    );--}}
   {{--}--}}
   
   //Check white space in username
   function usernameSpace() {
       var str = document.getElementById("username").value;
       str = str.replace(/\s+/g, '');
       $('#username').val(str);
   }
   
   //Check white space in pin
   function checkPin() {
       var pin = document.getElementById("new_pin").value;
       var current_pin = document.getElementById("current_pin").value;
       pin = pin.replace(/\s+/g, '');
       $('#new_pin').val(pin);
   
       if(isNaN(pin)){
           $(".set_pin_error").text('Only number is allowed');
           return false;
       }else if(pin.length > 4 || pin.length < 4){
           $(".set_pin_error").text('Please insert a 4 DIGIT PIN');
           return false;
       }else if(pin==null || pin==""){
           $(".set_pin_error").text('Please insert your PIN');
           return false;
       }else{
           $(".set_pin_error").text('');
       }
   }
   $("#new_pin").keyup(function () {
       checkPin();
   })
   
</script>
<script>
   function resetPin(phone){
       localStorage.setItem('reset_phone', phone);
       var base_url = window.location.origin;
       window.location = base_url+'/reset_pin';
   }
</script>
@include('footer')
<script>
   function updateDOB() {
       var year = $('#birth_year').val();
       var month = $('#birth_month').val();
       var day = $('#birth_day').val();
       if(isValidDate(year, month, day)){
   //do nothing
       }else{
           return false;
       }
       var url = "{{ url('/update-dob/') }}";
   
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'year': year, 'month': month, 'day': day},
           success: function (data) {
               console.log(data);
               if(data === 1){
                   $('#hide-dob').hide();
               }
           }
       });
   }
</script>
<script>
   @if(session('wrong_cur_credential') || session('error_new_pin'))
       $("#fake_pin_section").hide();
       $("#pin_section").show();
   @endif
</script>
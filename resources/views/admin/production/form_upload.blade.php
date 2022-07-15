@include('admin.production.header')
{{--<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>--}}
{{--<script>tinymce.init({selector: '#textarea1', plugins: "lists, advlist"});</script>--}}
{{--<script>tinymce.init({selector: '#textarea2', plugins: "lists, advlist"});</script>--}}
{{--<script>tinymce.init({selector: '#textarea3', plugins: "lists, advlist"});</script>--}}
<div class="right_col" role="main">

<div class="page-title">
        <div class="title_left">
            <h3>Add New Partner</h3>
        </div>
        @if(session('try_again'))
         <div class="title_right alert alert-warning"
            style="text-align: center;"> {{ session('try_again') }} 
         </div>
         @endif
    </div>
<div class="clearfix"></div>
   <div class="col-md-12 col-xs-12">
      <div class="x_panel">
         <div class="x_content">
            <div class="panel panel-default">
               <div class="panel-body">
                  <form class="form-horizontal form-label-left" method="post" action="{{ url('addPartner') }}"
                     enctype="multipart/form-data">
                     <div class="row">
                        <div class="col-md-12">
                           <span style="color: #E74430;">
                           @if ($errors->getBag('default')->first('category'))
                           {{ $errors->getBag('default')->first('category') }}
                           @endif
                           </span>
                           <label class="control-label">Select Category</label>
                           <select class="form-control" name="category" id="category_list">
                              <option selected disabled>-----</option>
                              @foreach($all_categories as $category)
                              <option value="{{$category->id}}">{{$category->name}}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-12">
                           <label class="control-label">Select Subcategory</label>
                           <div id="partner_type">
                              <p style="color: #E74430">Please select category first</p>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label class="control-label">Name</label>
                              <span style="color: #E74430;">
                              @if ($errors->getBag('default')->first('name'))
                              {{ $errors->getBag('default')->first('name') }}
                              @endif
                              </span>
                              <input type="text" class="form-control" placeholder="Enter partner name here" name="name" value="{{old('name')}}">
                           </div>
                        </div>
                        <!-- Col -->
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label class="control-label">Representing type:</label>
                              <span style="color: #E74430;">
                              @if ($errors->getBag('default')->first('type'))
                              {{ $errors->getBag('default')->first('type') }}
                              @endif
                              </span>
                              <input type="text" class="form-control" placeholder="(45 chars)" name="type" value="{{old('type')}}">
                           </div>
                        </div>
                        <!-- Col -->
                     </div>
                     <!-- Row -->
                     <div class="row">
{{--                        <div class="col-sm-4">--}}
{{--                           <div class="form-group">--}}
{{--                              <label class="control-label">Username</label>--}}
{{--                              <span style="color: #E74430;">--}}
{{--                              @if ($errors->getBag('default')->first('username'))--}}
{{--                              {{ $errors->getBag('default')->first('username') }}--}}
{{--                              @endif--}}
{{--                              </span>--}}
{{--                              <input type="text" class="form-control" placeholder="Username" name="username"--}}
{{--                                 value="{{old('username')}}">--}}
{{--                           </div>--}}
{{--                        </div>--}}
{{--                        <!-- Col -->--}}
{{--                        <div class="col-sm-4">--}}
{{--                           <div class="form-group">--}}
{{--                              <label class="control-label">Password</label>--}}
{{--                              <span style="color: #E74430;">--}}
{{--                              @if ($errors->getBag('default')->first('password'))--}}
{{--                              {{ $errors->getBag('default')->first('password') }}--}}
{{--                              @endif--}}
{{--                              </span>--}}
{{--                              <input type="text" class="form-control"--}}
{{--                                 placeholder="(0-9, A-Z, a-z),Min 8 characters"--}}
{{--                                 name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"--}}
{{--                                 title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">--}}
{{--                           </div>--}}
{{--                        </div>--}}
{{--                        <!-- Col -->--}}
{{--                        <div class="col-sm-4">--}}
{{--                           <div class="form-group">--}}
{{--                              <label class="control-label">Admin Code</label>--}}
{{--                              <span style="color: #E74430;" class="error_admin_code">--}}
{{--                              @if ($errors->getBag('default')->first('admin_code'))--}}
{{--                              {{ $errors->getBag('default')->first('admin_code') }}--}}
{{--                              @endif--}}
{{--                              </span>--}}
{{--                              <input type="text" class="form-control" placeholder="Code (min 5 character)"--}}
{{--                                 id="admin_code" name="admin_code">--}}
{{--                           </div>--}}
{{--                        </div>--}}
                        <!-- Col -->
                     </div>
                     <!-- Row -->
                     <div class="row">
                        <div class="col-sm-12">
                           <div class="form-group">
                              <label class="control-label">About</label>
                              <span style="color: #E74430;">
                              @if ($errors->getBag('default')->first('about'))
                              {{ $errors->getBag('default')->first('about') }}
                              @endif
                              </span>
                              <input type="text" class="form-control" placeholder="Enter information about the partner" name="about">
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Facebook</label>
                              <span style="color: #E74430;">
                              @if ($errors->getBag('default')->first('facebook'))
                              {{ $errors->getBag('default')->first('facebook') }}
                              @endif
                              </span>
                              <input type="text" class="form-control" placeholder="facebook.com (Optional)"
                                 name="facebook">
                           </div>
                        </div>
                        <!-- Col -->
                        <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Website</label>
                              <span style="color: #E74430;">
                              @if ($errors->getBag('default')->first('website'))
                              {{ $errors->getBag('default')->first('website') }}
                              @endif
                              </span>
                              <input type="text" class="form-control" placeholder="www.example.com (Optional)"
                                 name="website">
                           </div>
                        </div>
                        <!-- Col -->
                        <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Instagram</label>
                              <span style="color: #E74430;">
                              @if ($errors->getBag('default')->first('instagram'))
                              {{ $errors->getBag('default')->first('instagram') }}
                              @endif
                              </span>
                              <input type="text" class="form-control" placeholder="www.instagram.com (Optional)"
                                 name="instagram">
                           </div>
                        </div>
                        <!-- Col -->
                     </div>
                     <!-- Row -->
                     <div class="row">
                        <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Select Profile Image(300*300px)</label>
                              <input id="file-1c" class="file" name="profile" type="file" data-min-file-count="1"
                                 required>
                           </div>
                        </div>
                        <!-- Col -->
                        <!-- <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Select Banner</label>
                              <input id="file-2c" class="file" name="thumb_pic" type="file"
                                 data-min-file-count="1">
                           </div>
                        </div> -->
                        <!-- Col -->
                        <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Select Gallery Images</label>
                              <span style="color: #E74430;">
                                 @if ($errors->getBag('default')->first('gallery[]'))
                                 {{ $errors->getBag('default')->first('gallery[]') }}
                                 @endif
                                 </span>
                              <input id="file-3c" class="file" name="gallery[]" type="file"
                                 onchange="gallery_uploads();" multiple data-min-file-count="1" required>
                              <div id="gallery_image_names" style="color: #924210;"></div>
                           </div>
                        </div>

                        <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Select Menu Images</label>
                              <span style="color: #E74430;">
                                 @if ($errors->getBag('default')->first('menu[]'))
                                 {{ $errors->getBag('default')->first('menu[]') }}
                                 @endif
                                 </span>
                              <input id="file-4c" class="file" name="menu[]" type="file"
                                 onchange="menu_uploads();" multiple data-min-file-count="1">
                              <div id="menu_image_names" style="color: #924210;"></div>
                           </div>
                        </div>

                        <!-- Col -->
                     </div>
                     <!-- Row -->
                     <div class="row">
                        <!-- <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Owner Name</label>
                              <input type="text" class="form-control" placeholder="Owner Name (Optional)"
                                 name="owner">
                           </div>
                        </div> -->
                        <!-- Col -->
                        <!-- <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Owner Contact</label>
                              <span style="color: #E74430;" class="error_ownerContact"></span>
                              <input type="text" class="form-control" placeholder="Owner Contact (Optional)"
                                 id="ownerContact" name="ownerContact" maxlength="14">
                           </div>
                        </div> -->
                        <!-- Col -->
                        <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Contract Expiry</label>
                              <span style="color: #E74430;">
                              @if ($errors->getBag('default')->first('contract_expiry_date'))
                              {{ $errors->getBag('default')->first('contract_expiry_date') }}
                              @endif
                              </span>
                              <input type="date" class="form-control" name="contract_expiry_date" value="{{old('contract_expiry_date')}}" required>
                           </div>
                        </div>
                        <!-- Col -->
                     </div>
                     <!-- Row -->
                     <input type="hidden" name="cat_rel_ids" id="cat_rel_ids">
                        <div class="row">
                           <div class="col-sm-12">
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <div class="form-group">
                                 <div class="pull-right">
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                    <button type="submit" class="btn btn-activate pull-right">Submit</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="{{ asset('js/add_partner.js') }}"></script>
<script>
   $('#file-fr').fileinput({
       language: 'fr',
       uploadUrl: '#',
       allowedFileExtensions: ['jpg', 'png', 'gif']
   });
   $('#file-es').fileinput({
       language: 'es',
       uploadUrl: '#',
       allowedFileExtensions: ['jpg', 'png', 'gif']
   });
   $("#file-0").fileinput({
       'allowedFileExtensions': ['jpg', 'png', 'gif']
   });
   $("#file-1").fileinput({
       uploadUrl: '#', // you must set a valid URL here else you will get an error
       allowedFileExtensions: ['jpg', 'png', 'gif'],
       overwriteInitial: false,
       maxFileSize: 1000,
       maxFilesNum: 10,
       //allowedFileTypes: ['image', 'video', 'flash'],
       slugCallback: function (filename) {
           return filename.replace('(', '_').replace(']', '_');
       }
   });
   
   
   $(document).ready(function () {
       $("#test-upload").fileinput({
           'showPreview': false,
           'allowedFileExtensions': ['jpg', 'png', 'gif'],
           'elErrorContainer': '#errorBlock'
       });
       $("#kv-explorer").fileinput({
           'theme': 'explorer',
           'uploadUrl': '#',
           overwriteInitial: false,
           initialPreviewAsData: true,
           initialPreview: [
               "http://lorempixel.com/1920/1080/nature/1",
               "http://lorempixel.com/1920/1080/nature/2",
               "http://lorempixel.com/1920/1080/nature/3"
           ],
           initialPreviewConfig: [
               {caption: "nature-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1},
               {caption: "nature-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2},
               {caption: "nature-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3}
           ]
       });
   });
</script>
{{--====================================================--}}
{{--javascript to limit Menu & Gallery Images--}}
{{--========================================================--}}
<script>
   $('input#file-3c').change(function () {
       var files = $(this)[0].files;
       if (files.length > 20) {
           $('#gallery_image_names').html('');
           alert("Limit Exceeds : you can select max 20 Gallery images!");
           $('input#file-3c').val('');
       } else {
   
       }
   });
   $('input#file-4c').change(function () {
       var files = $(this)[0].files;
       if (files.length > 20) {
           $('#menu_image_names').html('');
           alert("Limit Exceeds : you can select max 20 Menu images!");
           $('input#file-4c').val('');
       } else {
   
       }
   });
</script>
{{--<script type="text/javascript">--}}
   {{--$(document).ready(function () {--}}
       {{--var i = 0;--}}
       {{--$('#add').click(function () {--}}
           {{--i++;--}}
           {{--$('#special_discounts').append(--}}
               {{--'<div id="row' + i + '"><br><br>'--}}
               {{--+ '<div class="col-md-4 col-sm-4 col-xs-12">'--}}
               {{--+ '<input type="text" class="form-control" placeholder="Discount Title" name="special_discount_title[]" value="">'--}}
               {{--+ '</div>'--}}
               {{--+ '<div class="col-md-3 col-sm-3 col-xs-12">'--}}
               {{--+ '<input type="text" class="form-control" placeholder="Gold Discount (digit)" name="special_discount_gold[]" value="">'--}}
               {{--+ '</div>'--}}
               {{--+ '<div class="col-md-3 col-sm-3 col-xs-12">'--}}
               {{--+ '<input type="text" class="form-control" placeholder="Platinum Discount (digit)" name="special_discount_platinum[]" value="">'--}}
               {{--+ '</div>'--}}
               {{--+ '<div class="col-md-2 col-sm-2 col-xs-12">'--}}
               {{--+ '<button name="remove" id="' + i + '" class="btn btn-danger btn_remove">Remove</button>'--}}
               {{--+ '</div>'--}}
               {{--+ '</div>'--}}
           {{--);--}}
   
           {{--$(document).on('click', '.btn_remove', function () {--}}
               {{--var button_id = $(this).attr("id");--}}
               {{--$('#row' + button_id + '').remove();--}}
           {{--});--}}
       {{--});--}}
   {{--});--}}
   {{--
</script>--}}
<script>
   function menu_uploads() {
       var menu_images = document.getElementById('file-4c');
       var menu_img = [];
       for (i = 0; i < menu_images.files.length; i++) {
           menu_img[i] = ' <i class="img-icon" style="color: #007bff"></i> ' + menu_images.files.item(i).name;
       }
   
       if (menu_images.files.length > 1) {
           document.getElementById("menu_image_names").innerHTML = menu_img;
       } else {
           document.getElementById("menu_image_names").innerHTML = '';
       }
   }
</script>
<script>
   function gallery_uploads() {
       var gallery_images = document.getElementById('file-3c');
       var gallery_img = [];
       for (i = 0; i < gallery_images.files.length; i++) {
           gallery_img[i] = ' <i class="img-icon" style="color: #007bff"></i> ' + gallery_images.files.item(i).name;
       }
   
       if (gallery_images.files.length > 1) {
           document.getElementById("gallery_image_names").innerHTML = gallery_img;
       } else {
           document.getElementById("gallery_image_names").innerHTML = '';
       }
   }
</script>
@include('admin.production.header')
{{--Crop image--}}
<script src="{{asset('js/imageCrop/jquery.js')}}"></script>
<script src="{{asset('js/imageCrop/croppie.js')}}"></script>
<link href="{{asset('admin/vendors/croppie/croppie.css')}}" rel="stylesheet">

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @endif
            <h3>Edit Customer</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        @if (isset($profileInfo))
            <form action="{{ url('customerEditDone/'. $profileInfo->customer_id) }}" class="form-horizontal">
                <div class="img-section">
                    <div style="cursor: pointer">
                        <img id="upload-cropped-image" src="{{ $profileInfo->customer_profile_image != '' ? $profileInfo->customer_profile_image
                        :'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/user.png' }}"
                             class="imgCircle" alt="Profile picture" onclick="editImage()" width="100%">
                    </div>
                    <div class="upload-btn-wrapper">
                        <div class="image-upload">
                            <button class="custom_upload_btn">Edit Profile Picture</button>
                            <input type="file" id="upload" name="customerProfileImage" accept="image/*"
                                   data-target="#cropModal"
                                   data-toggle="modal" style="cursor: pointer">
                        </div>
                    </div>
                </div>
                {{--modal to crop image--}}
                <div id="cropModal" class="modal fade " role="dialog" style="top: 5%;">
                    <div class="modal-dialog" style="z-index:99999;text-align: center">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title pull-left">Upload Image</h4>
                                <button type="button" class="close" data-dismiss="modal">
                                    <i class="cross-icon"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="upload-demo"></div>
                                <button class="btn btn-primary upload-result" data-dismiss="modal">
                                    <span>Upload Image</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{--modal to crop image ends--}}
                <div class="form-group">
                    <label class="control-label col-sm-2" for="first_name">Customer Id:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('customer_id'))
                            {{ $errors->getBag('default')->first('customer_id') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="text" name="customer_id" class="form-control" id="customer_id"
                               value="{{ $profileInfo->customer_id }}" pattern="[0-9]{16}" maxlength="16"
                               minlength="16">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="first_name">Name:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('customer_full_name'))
                            {{ $errors->getBag('default')->first('customer_full_name') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="text" name="customer_full_name" class="form-control" id="first_name"
                               value="{{ $profileInfo->customer_full_name }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="customer_email">Email:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('customer_email'))
                            {{ $errors->getBag('default')->first('customer_email') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="text" name="customer_email" class="form-control" id="email"
                               value="{{ $profileInfo->customer_email }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="name">Mobile:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('mobile'))
                            {{ $errors->getBag('default')->first('mobile') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="text" name="mobile" class="form-control" id="mobile"
                               value="{{ $profileInfo->customer_contact_number }}" maxlength="14" minlength="14">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="name">Address:</label>
                    <span style="color: red;">
                    @if ($errors->getBag('default')->first('address'))
                            {{ $errors->getBag('default')->first('address') }}
                        @endif
                </span>
                    <div class="col-sm-10">
                        <input type="text" name="address" class="form-control" id="address"
                               value="{{ $profileInfo->customer_address }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="dob">DOB <br>(d-m-y):</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('dob'))
                            {{ $errors->getBag('default')->first('dob') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        @if($profileInfo->customer_dob)
                            <input type="text" name="dob" class="form-control" id="dob"
                                   value="{{ date("d-m-Y", strtotime($profileInfo->customer_dob)) }}">
                        @else
                            <input type="text" name="dob" class="form-control" id="dob" placeholder="d-m-y">
                        @endif
                    </div>
                </div>
                @if(session('admin')==\App\Http\Controllers\Enum\AdminRole::superadmin)
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="expiry_date">Expiry Date <br>(d-m-y):</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('expiry_date'))
                                {{ $errors->getBag('default')->first('expiry_date') }}
                            @endif
                        </span>
                        <div class="col-sm-10">
                            <input type="text" name="expiry_date" class="form-control" id="expiry_date"
                                   value="{{ date("d-m-Y", strtotime($profileInfo->expiry_date)) }}" placeholder="d-m-y">
                        </div>
                    </div>
                @else
                    <div class="form-group" style="display: none;">
                        <label class="control-label col-sm-2" for="expiry_date">Expiry Date <br>(d-m-y):</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('expiry_date'))
                                {{ $errors->getBag('default')->first('expiry_date') }}
                            @endif
                        </span>
                        <div class="col-sm-10">
                            <input type="text" name="expiry_date" class="form-control" id="expiry_date"
                                   value="{{ date("d-m-Y", strtotime($profileInfo->expiry_date)) }}" placeholder="d-m-y">
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label class="control-label col-sm-2" for="refer_code">Refer Code:</label>
                    <span style="color: red;">
                            @if ($errors->getBag('default')->first('refer_code'))
                            {{ $errors->getBag('default')->first('refer_code') }}
                        @endif
                        </span>
                    @if($profileInfo->reference_used > 0)
                        <div class="col-sm-10">
                            <input type="text" name="refer_code" class="form-control" id="expiry_date"
                                   value="{{ $profileInfo->referral_number }}" placeholder="Refer Code" readonly>
                        </div>
                    @else
                        <div class="col-sm-10">
                            <input type="text" name="refer_code" class="form-control" id="expiry_date"
                                   value="{{ $profileInfo->referral_number }}" placeholder="Refer Code">
                        </div>
                    @endif

                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="gender">Gender:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('customer_gender'))
                            {{ $errors->getBag('default')->first('customer_gender') }}
                        @endif
                    </span>
                    <label style="margin: 5px 10px 10px 10px"> Male
                        <input type="radio"
                               <?php if ($profileInfo->customer_gender == 'male') echo 'checked';?> value="male"
                               name="customer_gender">
                        <span class="checkmark"></span>
                    </label>
                    <label> Female
                        <input type="radio"
                               <?php if ($profileInfo->customer_gender == 'female') echo 'checked';?>  value="female"
                               name="customer_gender">
                        <span class="checkmark"></span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="email_verify">Email verify:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('email_verify'))
                            {{ $errors->getBag('default')->first('email_verify') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <select name="email_verify" id="email_verify">
                            <option value="1" {{$profileInfo->email_verified == 1 ? 'selected':''}}>Verified</option>
                            <option value="0" {{$profileInfo->email_verified == 0 ? 'selected':''}}>Not verified</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="prev_url" value="{{url()->previous()}}"/>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-activate pull-right">Submit</button>
                    </div>
                </div>
            </form>

        @endif
    </div>
</div>

<script>
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
            var url = "{{ url('/editUserImage') }}";
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "customerProfileImage": resp
                },
                success: function (data) {//alert(data);
                    html = '<img src="' + resp + '" />';
                    // $("#upload-cropped-image").html(html);
                    $("#upload-cropped-image").attr("src", resp);
                    console.log(data);
                }
            });
        });
    });

    /*This function is added for Image Reupload Facility: Start*/
    function editImage() {
        //alert("hiiiiiii");
        location.reload(true);
        editImage2();
    }

    function editImage() {
        $("#upload").click();
    }

    /*This function is added for Image Reupload Facility: End*/
</script>
@include('admin.production.footer')
@include('admin.production.header')
{{--Crop image--}}
<script src="{{asset('js/imageCrop/jquery.js')}}"></script>
<script src="{{asset('js/imageCrop/croppie.js')}}"></script>
<link href="{{asset('admin/vendors/croppie/croppie.css')}}" rel="stylesheet">

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Edit COD Customer Information</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        @if (isset($profileInfo))
            <form action="{{ url('CODEditDone/'. $profileInfo['customer_id']) }}" class="form-horizontal">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="col-sm-2" for="first_name">Customer Id:</label>
                            <span style="color: red;">
                  @if ($errors->getBag('default')->first('customer_id'))
                                    {{ $errors->getBag('default')->first('customer_id') }}
                                @endif
              </span>
                            <div class="col-sm-10">
                                <input type="text" name="customer_id" class="form-control" id="customer_id"
                                       value="{{ $profileInfo['customer_id'] }}" pattern="[0-9]{16}" maxlength="16"
                                       minlength="16">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="col-sm-2" for="gender">Card Type:</label>
                            <span style="color: red;">
                    @if ($errors->getBag('default')->first('customer_type'))
                                    {{ $errors->getBag('default')->first('customer_type') }}
                                @endif
                </span>
                            <div class="col-sm-10">
                                <select class="form-control" name="customer_type">
                                    @if($profileInfo['customer_type'] == 1)
                                        <option selected value="{{$profileInfo['customer_type']}}">Gold</option>
                                        <option value="2">Royalty Premium Membership</option>
                                    @else
                                        <option value="1">Gold</option>
                                        <option selected value="{{$profileInfo['customer_type']}}">Royalty Premium Membership</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="col-sm-2" for="name">Shipping Address:</label>
                            <span style="color: red;">
                    @if ($errors->getBag('default')->first('shipping_address'))
                                    {{ $errors->getBag('default')->first('shipping_address') }}
                                @endif
                </span>
                            <div class="col-sm-10">
                                <input type="text" name="shipping_address" class="form-control" id="shipping_address"
                                       value="{{ $profileInfo['shipping_address'] }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            {{--<label style="margin: -5px 5px 10px -2px" class="control-label col-sm-2"--}}
                            {{--for="gender">Action:</label>--}}
                            <span>
                   <input style="margin: 5px 5px 5px 5px" type="checkbox" name="is_approve"
                   <?php if ($profileInfo['moderator_status'] == '1') echo 'checked'; ?>>
                   <b style="color: green">Approve the customer</b>
               </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <button type="submit" class="btn success-d" style="float: right">Submit</button>
                        </div>
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
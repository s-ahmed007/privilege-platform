@include('admin.production.header')
{{--Crop image--}}
<script src="{{asset('js/imageCrop/jquery.js')}}"></script>
<script src="{{asset('js/imageCrop/croppie.js')}}"></script>
<link href="{{asset('admin/vendors/croppie/croppie.css')}}" rel="stylesheet">

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-warning">
                    {{ session('status') }}
                </div>
            @endif
            <h3>Create Sub Category 1</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        <form action="{{ url('admin/sub_cat_1/') }}" class="form-horizontal" method="post">
            <!-- <div class="img-section">
                <div style="cursor: pointer">
                    <img id="upload-cropped-image" src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/user.png"
                         class="imgCircle" alt="Profile picture" onclick="editImage()" width="100%">
                </div>
                <div class="upload-btn-wrapper">
                    <div class="image-upload">
                        <button class="custom_upload_btn">Edit Category Icon</button>
                        <input type="file" id="upload" name="customerProfileImage" accept="image/*"
                           data-target="#cropModal" data-toggle="modal" style="cursor: pointer">
                    </div>
                </div>
            </div> -->
            {{--modal to crop image--}}
            <div id="cropModal" class="modal fade " role="dialog" style="top: 5%;">
                <div class="modal-dialog" style="z-index:99999;text-align: center">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
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
                <label class="control-label col-sm-2" for="first_name">Category Name:</label>
                <span style="color: red;">
                    @if ($errors->getBag('default')->first('category_name'))
                        {{ $errors->getBag('default')->first('category_name') }}
                    @endif
                </span>
                <div class="col-sm-10">
                    <input type="text" name="category_name" class="form-control" required>
                </div>
            </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-activate pull-right">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $uploadCrop = $('#upload-demo').croppie({
        enableExif: true,
        viewport: {
            width: 100,
            height: 100,
            type: 'rectangle'
        },
        boundary: {
            width: 100,
            height: 100
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
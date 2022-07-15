@include('admin.production.header')
{{--Crop image--}}
<script src="{{asset('js/imageCrop/jquery.js')}}"></script>
<script src="{{asset('js/imageCrop/croppie.js')}}"></script>
<link href="{{asset('admin/vendors/croppie/croppie.css')}}" rel="stylesheet">

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <h3>Edit Main Category</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        @if (isset($category))
            <form action="{{ url('admin/main_cat/'. $category->id) }}" class="form-horizontal" method="post"
                  enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT"/>
                <div class="img-section">
                    <div style="cursor: pointer">
                        <img id="upload-cropped-image" src="{{ $category->icon != '' ? $category->icon
                        :'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/accounts/user-account/user.png' }}"
                             class="imgCircle" alt="Profile picture" onclick="editImage()" width="100%">
                    </div>
                    <div class="upload-btn-wrapper">
                        <div class="image-upload">
                            <button class="custom_upload_btn">Edit Category Icon</button>
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
                    <label class="control-label col-sm-2">Category Name:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('category_name'))
                            {{ $errors->getBag('default')->first('category_name') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="text" name="category_name" class="form-control" value="{{ $category->name }}" placeholder="Food & Drinks" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">Category Type:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('category_type'))
                            {{ $errors->getBag('default')->first('category_type') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="text" name="category_type" class="form-control" value="{{ $category->type }}" placeholder="food_and_drinks" required>
                    </div>
                </div>
<!--                 <div class="form-group">
                    <label class="control-label col-sm-2">Category Caption:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('category_caption'))
                            {{ $errors->getBag('default')->first('category_caption') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="text" name="category_caption" class="form-control" value="{{ $category->caption }}" placeholder="Text that shows in offers page banner" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">Banner Image:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('category_banner'))
                            {{ $errors->getBag('default')->first('category_banner') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="file" name="category_banner">
                    </div>
                </div> -->
                <div class="form-group">
                    <label class="control-label col-sm-2">Priority:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('priority'))
                            {{ $errors->getBag('default')->first('priority') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="number" name="priority" class="form-control" value="{{ $category->priority }}"
                        required>
                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
            width: 512,
            height: 512,
            type: 'rectangle'
        },
        boundary: {
            width: 512,
            height: 512
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
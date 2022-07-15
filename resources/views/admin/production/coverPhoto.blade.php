@include('admin.production.header')
{{--Crop image--}}
<script src="{{asset('js/imageCrop/jquery.js')}}"></script>
<script src="{{asset('js/imageCrop/croppie.js')}}"></script>
<link href="{{asset('admin/vendors/croppie/croppie.css')}}" rel="stylesheet">

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Cover Photo</h3>
        </div>
        @if (Session::has('updated'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
        @elseif(session('try_again'))
            <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        <form action="{{url('update_partner_cover_photo/'.$cover_photo->partner_account_id)}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="img-section">
                <div style="cursor: pointer">
                    <img id="upload-cropped-image" src="{{ $cover_photo->partner_cover_photo != null ? $cover_photo->partner_cover_photo
                        :'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/icon/Royalty_logo.png' }}"
                         class="" alt="Cover Photo" onclick="editImage()">
                </div>
                <div class="upload-btn-wrapper">
                    <div class="image-upload">
                        <button class="custom_upload_btn">Edit Cover Photo</button>
                        <input type="file" id="upload" name="partnerCoverPhoto" accept="image/*"
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
            <button class="btn btn-activate pull-right">Update Cover Photo</button>
        </form>
    </div>
</div>

<script>
    $uploadCrop = $('#upload-demo').croppie({
        enableExif: true,
        viewport: {
            width: 400,
            height: 200,
            type: 'rectangle'
        },
        boundary: {
            width: 400,
            height: 200
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
                success: function (data) {
                    $("#upload-cropped-image").attr("src", resp);
                }
            });
        });
    });

    /*This function is added for Image Reupload Facility: Start*/
    function editImage() {
        $("#upload").click();
    }
    /*This function is added for Image Reupload Facility: End*/
</script>

@include('admin.production.footer')
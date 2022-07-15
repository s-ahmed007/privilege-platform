@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')

<div class="right_col" role="main">
    <div class="heading">
        <h3>Gallery Image Management</h3>
    </div>
    <div class="bar-long"></div>
    <div class="image-add">
        @if ($errors->getBag('default')->first('gallery'))
            <div class="image-req">
                <span class="image-req-text">Image field is required</span>
            </div>
        @endif
        <form action="{{url('addGalleryImages')}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="file" id="file-3c" name="gallery[]" onchange="gallery_uploads();" multiple class="hidden">
            <label for="file-3c" class="upload-btn-ash" style="cursor:pointer;">Upload file</label>
            <div id="gallery_image_names" style="color: #924210;"></div>
            <br>
            <button class="btn btn-activate pull-right">Submit</button>
        </form>
        @if (Session::has('updated'))
            <span class="alert alert-success">{{ Session::get('updated') }}</span>
        @elseif(session('try_again'))
            <span class="alert alert-warning"> {{ session('try_again') }} </span>
        @endif
    </div>
    <hr>
    <div class="panel-body">
        <div class="table-responsive transactions-table">
            @if(isset($galleryImages))
                <div class="row">
                    @foreach($galleryImages as $galleryImage)
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="image-box row">
                                <img src="{{asset($galleryImage['partner_gallery_image'])}}" width="100%" height="200px"
                                     alt="Partner Gallery Image">
                                <br>
                                <textarea id="add-gallery-caption-{{$galleryImage['id']}}" rows="3" style="width: 100%"
                                          placeholder="Add image caption">{{$galleryImage['image_caption']}}</textarea>
                                <button class="btn btn-activate col-md-12 col-sm-12 col-xs-12"  style="margin: 10px 0 10px 0;" id="save-gallery-caption"
                                        value="{{$galleryImage['id']}}">Update Caption
                                </button>
                                <button class="btn btn btn-danger col-md-12 col-sm-12 col-xs-12" style="margin: 10px 0 10px 0;">
                                    <a href="{{url('delete-gallery-image/'.$galleryImage['id'])}}"
                                       onclick="return confirm('Are you sure?')" style="color: white">
                                        Delete Image
                                    </a>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <h2>No Gallery Image.</h2>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div id="captionUpdated" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
                <h3 id="caption-updated"></h3>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="img_count" value="{{ count($galleryImages) }}">
@include('partner-admin.production.footer')

<script>
    function gallery_uploads() {
        var gallery_images = document.getElementById('file-3c');
        var gallery_img = [];
        for (i = 0; i < gallery_images.files.length; i++) {
            gallery_img[i] = ' <i class="image-icon" style="color: darkgreen"></i> ' + gallery_images.files.item(i).name;
        }
        if (gallery_images.files.length > 1) {
            var files_quantity_text = '<span style="color: black; font-weight: bold">' + gallery_images.files.length + ' Files Selected: </span>';
        } else {
            var files_quantity_text = '<span style="color: black; font-weight: bold">' + gallery_images.files.length + ' File Selected: </span>';
        }
        document.getElementById("gallery_image_names").innerHTML = files_quantity_text + gallery_img;
    }
</script>
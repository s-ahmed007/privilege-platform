@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')

<div class="right_col" role="main">
    <div class="heading">
        <h3>Menu Image Management</h3>
    </div>
    <div class="bar-long"></div>
    <div class="image-add">
        @if ($errors->getBag('default')->first('menu'))
            <div class="image-req">
                <span class="image-req-text">Image field is required</span>
            </div>
        @endif
        <form action="{{url('addMenuImages')}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="file" id="file-4c" name="menu[]" onchange="menu_uploads();" multiple class="hidden">
            <label for="file-4c" class="upload-btn-ash" style="cursor: pointer">Upload file</label>
            <div id="menu_image_names" style="color: #924210;"></div>
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
            @if(isset($menuImages))
                <div class="row">
                    @foreach($menuImages as $menuImage)
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="image-box">
                                <img src="{{asset($menuImage['partner_menu_image'])}}" width="100%" height="200px"
                                     alt="Image">
                                <br>
                                <p>
                                    <a href="{{url('delete-menu-image/'.$menuImage['id'])}}"
                                       onclick="return confirm('Are you sure?')" class="btn btn-primary"
                                       style="float: right;">Delete</a>
                                </p>
                                <h5>Menu Image</h5>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <h2>No Menu Image.</h2>
            @endif
        </div>
    </div>
</div>
<input type="hidden" id="img_count" value="{{ count($menuImages) }}">
@include('partner-admin.production.footer')

<script>
    function menu_uploads() {
        var menu_images = document.getElementById('file-4c');
        var menu_img = [];
        for (i = 0; i < menu_images.files.length; i++) {
            menu_img[i] = ' <i class="img-icon" style="color: darkgreen"></i> ' + menu_images.files.item(i).name;
        }
        if (menu_images.files.length > 1) {
            var files_quantity_text = '<span style="color: black; font-weight: bold">' + menu_images.files.length + ' Files Selected: </span>';
        } else {
            var files_quantity_text = '<span style="color: black; font-weight: bold">' + menu_images.files.length + ' File Selected: </span>';
        }
        document.getElementById("menu_image_names").innerHTML = files_quantity_text + menu_img;
    }
</script>
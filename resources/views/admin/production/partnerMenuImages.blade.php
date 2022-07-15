@include('admin.production.header')
<style>
    .upload-btn-ash {
        padding: 5px;
        border: 1px solid #007bff;
        background-color: lightgrey;
        border-radius: 5px;
        font-weight: bold;
        color: #007bff;
        font-family: 'muli', sans serif
    }
    .heading h3 {
        text-align: center;
        font-weight: bold
    }
    .image-add {
        text-align: center
    }

    .image-req {
        display: block;
        margin: 25px
    }
    .image-req-text {
        color: #fff;
        padding: 10px;
        background-color: red;
        border-radius: 5px
    }
    .image-box {
        border: 1px solid #007bff;
        padding: 5px;
        border-radius: 5px;
        margin: 5px;
        font-weight: bold
    }
    .image-box img {
        box-shadow: 0 0 5px 0 rgba(0, 0, 0, .2);
        border-radius: 5px 5px 0 0
    }
    .pinned, .not_pinned{position: absolute; padding: 5px; background: #fff; color: #007bff;    height: 30px;
    width: 30px;
    text-align: center;}
</style>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <div class="heading">
                <h3>{{$partner_name->partner_name}} => Menu Image Management</h3>
            </div>
        </div>
        @if (Session::has('updated'))
            <span style="float: right; padding: 10px 60px" class="alert alert-success">{{ Session::get('updated') }}</span>
        @elseif(session('try_again'))
            <span style="float: right; padding: 10px 60px" class="alert alert-warning">{{ session('try_again') }}</span>
        @elseif(session('pinned_img_changed'))
            <span style="float: right; padding: 10px 60px" class="alert alert-success">{{ session('pinned_img_changed') }}</span>
        @endif
    </div>
    <div class="clearfix"></div>
    <hr>
    <div class="panel-body">
        <div class="image-add">
            @if ($errors->getBag('default')->first('menu'))
                <div class="image-req">
                    <span class="image-req-text">Image field is required</span>
                </div>
            @endif
            <form action="{{url('admin/addMenuImages/'.$partner_id)}}" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <input type="file" id="file-3c" name="menu[]" onchange="menu_uploads();" multiple class="hidden">
                <label for="file-3c" class="upload-btn-ash">Choose file</label>
                <div id="menu_image_names" style="color: #924210;"></div>
                <br>
                <button class="btn btn-activate pull-right">Submit</button>
            </form>
        </div>
        <hr>
        <div class="table-responsive transactions-table">
            @if(isset($menuImages))
                <div class="row">
                    @foreach($menuImages as $menuImage)
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="image-box row">
                                @if($menuImage['pinned'] == 1)
                                    <a href="{{url('admin/pin_menu_image/'.$menuImage['partner_account_id'].
                                        '/'.$menuImage['id'])}}" class="pinned">
                                        <i class="fas fa-bookmark"></i>
                                    </a>
                                @else
                                    <a href="{{url('admin/pin_menu_image/'.$menuImage['partner_account_id'].
                                        '/'.$menuImage['id'])}}" class="not_pinned">
                                        <i class="far fa-bookmark"></i>
                                    </a>
                                @endif
                                <img src="{{asset($menuImage['partner_menu_image'])}}" width="100%" height="200px"
                                    alt="Partner Menu Image">
                                <br>
                                <textarea id="add-menu-caption-{{$menuImage['id']}}" rows="3" style="width: 100%"
                                    placeholder="Add image caption">{{$menuImage['image_caption']}}</textarea>
                                <button class="btn btn-activate col-md-12 col-sm-12 col-xs-12"  style="margin: 10px 0 10px 0;" id="save-menu-caption"
                                        value="{{$menuImage['id']}}">Update Caption
                                </button>
{{--                                @if(count($menuImages) >1)--}}
                                <a href="{{url('admin/delete-partner-menu-image/'.$menuImage['id'])}}"
                                onclick="return confirm('Are you sure?')" style="color: white">
                                    <button class="btn btn btn-danger col-md-12 col-sm-12 col-xs-12" style="margin: 10px 0 10px 0;">
                                        Delete Image
                                    </button>
                                </a>
{{--                                @else--}}
{{--                                    <button class="btn btn btn-primary col-md-12 col-sm-12 col-xs-12"--}}
{{--                                        data-toggle="modal" data-target="#canNotDeleteModal" style="margin: 10px 0 10px 0;">--}}
{{--                                        Delete Image--}}
{{--                                    </button>--}}
{{--                                @endif--}}
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
<!-- Can not delete menu image Modal -->
<div id="canNotDeleteModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
                <h3 id="caption-updated">At least one image should be here.</h3>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="img_count" value="{{ count($menuImages) }}">
@include('admin.production.footer')

<script>
    function menu_uploads() {
        var menu_images = document.getElementById('file-3c');
        var menu_img = [];
        for (i = 0; i < menu_images.files.length; i++) {
            menu_img[i] = ' <i class="image-icon" style="color: darkgreen"></i> ' + menu_images.files.item(i).name;
        }
        if (menu_images.files.length > 1) {
            var files_quantity_text = '<span style="color: black; font-weight: bold">' + menu_images.files.length + ' Files Selected: </span>';
        } else {
            var files_quantity_text = '<span style="color: black; font-weight: bold">' + menu_images.files.length + ' File Selected: </span>';
        }
        document.getElementById("menu_image_names").innerHTML = files_quantity_text + menu_img;
    }

    {{--====================================================--}}
    {{--javascript to save menu caption--}}
    {{--========================================================--}}
    $(document).on('click', '#save-menu-caption', function () {
        var image_id = this.value;
        var caption = $('#add-menu-caption-' + image_id).val();
        var url = "{{ url('/admin/addPartnerMenuCaption') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'id' : image_id, 'caption': caption},
            success: function (data) {
                if(data[0] == 'updated'){
                    $("#captionUpdated").modal('toggle');
                    $("#caption-updated").html('Caption updated successfully');
                    $("#caption-updated").css({'color': 'green'});
                }else{
                    $("#captionUpdated").modal('toggle');
                    $("#caption-updated").html('Something went wrong');
                    $("#caption-updated").css({'color': 'red'});
                }
            }
        });
    });
</script>
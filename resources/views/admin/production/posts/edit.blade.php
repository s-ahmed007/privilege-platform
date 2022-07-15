@include('admin.production.header')
<link href="{{ asset('emoji/css/emoji.css') }}" rel="stylesheet">
<style>
    .emoji-picker-icon {
        cursor: pointer;
        position: absolute;
        right: 20px;
        top: 5%;
        font-size: 20px;
        opacity: 0.7;
        z-index: 100;
        transition: none;
        color: black;
    }
    .emoji-wysiwyg-editor{min-height: 34px !important; border-radius: 5px}
</style>
<div class="page_loader" style="display: none">
    <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif" alt="Royalty Loading GIF" class="lazyload">
</div>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('error'))
                <div class="alert alert-warning">
                    {{ session('error') }}
                </div>
            @endif
            <h3>Edit Posts</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" onsubmit="return showPageLoader()"
                          action="{{ url('/admin/post/'.$allPosts->id) }}" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT"/>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Header:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                  <span style="color: red;">
                                    @if ($errors->getBag('default')->first('postHeader'))
                                       {{ $errors->getBag('default')->first('postHeader') }}
                                    @endif
                                  </span>
                                <input type="text" class="form-control" placeholder="Header" name="postHeader"
                                    required="required" value="{{$allPosts->header}}" data-emojiable="true"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Caption:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                @if ($errors->getBag('default')->first('postCaption'))
                                    {{ $errors->getBag('default')->first('postCaption') }}
                                @endif
                                <input type="text" class="form-control" placeholder="Caption" name="postCaption"
                                    value="{{$allPosts->caption}}" data-emojiable="true"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">External Link:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" style="height: unset;" placeholder="External link(Optional)"
                                       value="{{$allPosts->post_link}}" name="postLink"/>
                            </div>
                        </div>
                        <div class="form-group">
                            @if($allPosts->media_type == \App\Http\Controllers\Enum\MediaType::IMAGE)
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Image: (Max file size 10MB)</label>
                            @else
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Video: (Max file size 10MB)</label>
                            @endif
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="file" class="form-control" style="height: unset;" name="postImage"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php
                                if($allPosts->scheduled_at != null){
                                    $date = date_create($allPosts->scheduled_at);
                                    $date = "(".date_format($date,  'm/d/y h:i A').")";
                                }else{
                                    $date = "";
                                }
                            ?>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Schedule: {{$date}}</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="datetime-local" class="form-control" name="postSchedule" />
                                <p style="color:red">HH:MM AA, Always set minute to 00</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-activate pull-right">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="{{asset('emoji/js/config.js')}}"></script>
<script src="{{asset('emoji/js/util.js')}}"></script>
<script src="{{asset('emoji/js/jquery.emojiarea.js')}}"></script>
<script src="{{asset('emoji/js/emoji-picker.js')}}"></script>
<script>
    function showPageLoader(){
        $('.page_loader').css('display', 'block');
        return true;
    }

    $(function() {
        // Initializes and creates emoji set from sprite sheet
        window.emojiPicker = new EmojiPicker({
            emojiable_selector: '[data-emojiable=true]',
            assetsPath: '{{asset('emoji/img/')}}',
            popupButtonClasses: 'smile-icon'
        });
        // Finds all elements with `emojiable_selector` and converts them to rich emoji input fields
        // You may want to delay this step if you have dynamically created input fields that appear later in the loading process
        // It can be called as many times as necessary; previously converted input fields will not be converted again
        window.emojiPicker.discover();
    });
</script>

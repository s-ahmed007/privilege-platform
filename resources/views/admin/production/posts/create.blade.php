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
            <h3>Create A New Post</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/admin/post') }}"
                          onsubmit="return showPageLoader()" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Header:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                  <span style="color: red;">
                                    @if ($errors->getBag('default')->first('postHeader'))
                                     {{ $errors->getBag('default')->first('postHeader') }}
                                    @endif
                                  </span>
                                <input type="text" class="form-control" placeholder="Post Header" name="postHeader"
                                       data-emojiable="true" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Caption:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                  <span style="color: red;">
                                    @if ($errors->getBag('default')->first('postCaption'))
                                     {{ $errors->getBag('default')->first('postCaption') }}
                                    @endif
                                  </span>
                                <input type="text" class="form-control" placeholder="Post Caption" name="postCaption"
                                       data-emojiable="true"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">External Link:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" style="height: unset;" placeholder="External link(Optional)" name="postLink"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Image/Video: (Max file size 10MB)</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('postImage'))
                                        {{ $errors->getBag('default')->first('postImage') }}
                                    @endif
                                  </span>
                                <input type="file" class="form-control" style="height: unset;" name="postImage" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Schedule (optional):</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="datetime-local" class="form-control" name="postSchedule" placeholder="2019-01-20 20:00:00 (Optional)"/>
                                <p style="color:red">Set minute to 00</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-activate pull-right">Submit</button>
                            </div>
                        </div>
                    </form>
                    <button data-toggle="modal" data-target="#postGuideline" class="btn btn-primary pull-right">Guideline</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="postGuideline" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
                <h4 class="modal-title">Guideline to create post</h4>
            </div>
            <div class="modal-body">
                Picture size should be 316px by 476px fitting our frame (too large or small size may change the quality of the image)<br>
                <b>There two types of comment section</b><br>
                <ul>
                    <li><b>Header</b> – Partner will add the topic or main theme of the post. (30 words limit)</li>
                    <li><b>Caption</b> – Partner will give small detailed summary of the offer or include the main reason of their posting. (30-50 words limit)</li>
                </ul>
                <b>Rules and Regulation of news feed posting</b><br>
                <ul>
                    <li>Partners are allowed to post advertisements or offers that will promote their promotion along with our discounts.</li>
                    <li>Partners can advertise their stores but cannot show offers that does not include Royalty. This is mainly because our discount and partner’s individual discount may cause confusion within the customers.</li>
                    <li>Only related posts are to be uploaded from partner stores.</li>
                    <li>External posts from partners store via third party cannot be uploaded.</li>
                    <li>Please be aware that all these post will go through moderation before posting and it is essential for all the partners to read these guidelines before posting on the news feed.</li>
                    <li>If any of the rules or regulation are violated Royalty has the right to delete or suspend the partners news feed.</li>
                    <li>If your post is denied by our moderator, an E-mail will be sent to the partners account to explain the issues regarding the post.</li>
                    <li>If partner faces any difficulty in uploading or posting, partner can directly call us at <b>+880-963-862-0202</b> or E-mail us at <b>support@royaltybd.com</b>.</li>
                </ul>
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
@include('b2b2c.layout.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>

<script>tinymce.init({selector: '#summernote', plugins: "lists, advlist"});</script>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/client/all-post') }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Header:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                  <span style="color: red;">
                                    @if ($errors->getBag('default')->first('postHeader'))
                                     {{ $errors->getBag('default')->first('postHeader') }}
                                    @endif
                                  </span>
                                <input type="text" class="form-control" placeholder="Post Header" name="postHeader"/>
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
                                       required="required"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">External Link:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" style="height: unset;" placeholder="External link(Optional)" name="postLink"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Image:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="file" class="form-control" style="height: unset;" name="postImage" required="required"/>
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
            <div class="modal-header">    <h4 class="modal-title">Guideline to create post</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
            
            </div>
            <div class="modal-body">
                Picture size should be 316px by 476px fitting our frame (too large or small size may change the quality of the image)<br>
                <b>There two types of comment section</b><br>
                <ul>
                    <li><b>Header</b> – Client will add the topic or main theme of the post. (30 words limit)</li>
                    <li><b>Caption</b> – Client will give small detailed summary of the offer or include the main reason of their posting. (30-50 words limit)</li>
                </ul>
                <b>Rules and Regulation of news feed posting</b><br>
                <ul>
                    <li>Clients are allowed to post advertisements or offers that will promote their promotion along with our discounts.</li>
                    <li>Clients can advertise their stores but cannot show offers that does not include Royalty. This is mainly because our discount and client’s individual discount may cause confusion within the customers.</li>
                    <li>Only related posts are to be uploaded from client stores.</li>
                    <li>External posts from clients store via third party cannot be uploaded.</li>
                    <li>Please be aware that all these post will go through moderation before posting and it is essential for all the clients to read these guidelines before posting on the news feed.</li>
                    <li>If any of the rules or regulation are violated Royalty has the right to delete or suspend the clients news feed.</li>
                    <li>If your post is denied by our moderator, an E-mail will be sent to the clients account to explain the issues regarding the post.</li>
                    <li>If client faces any difficulty in uploading or posting, client can directly call us at <b>+880-963-862-0202</b> or E-mail us at <b>support@royaltybd.com</b>.</li>
                </ul>
            </div>
        </div>

    </div>
</div>

@include('b2b2c.layout.footer')
<script>
    $('#file-fr').fileinput({
        language: 'fr',
        uploadUrl: '#',
        allowedFileExtensions: ['jpg', 'png', 'gif']
    });
    $('#file-es').fileinput({
        language: 'es',
        uploadUrl: '#',
        allowedFileExtensions: ['jpg', 'png', 'gif']
    });
    $("#file-0").fileinput({
        'allowedFileExtensions': ['jpg', 'png', 'gif']
    });
    $("#file-1").fileinput({
        uploadUrl: '#', // you must set a valid URL here else you will get an error
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        overwriteInitial: false,
        maxFileSize: 1000,
        maxFilesNum: 10,
        //allowedFileTypes: ['image', 'video', 'flash'],
        slugCallback: function (filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });


    $(document).ready(function () {
        $("#test-upload").fileinput({
            'showPreview': false,
            'allowedFileExtensions': ['jpg', 'png', 'gif'],
            'elErrorContainer': '#errorBlock'
        });
        $("#kv-explorer").fileinput({
            'theme': 'explorer',
            'uploadUrl': '#',
            overwriteInitial: false,
            initialPreviewAsData: true,
            initialPreview: [
                "http://lorempixel.com/1920/1080/nature/1",
                "http://lorempixel.com/1920/1080/nature/2",
                "http://lorempixel.com/1920/1080/nature/3"
            ],
            initialPreviewConfig: [
                {caption: "nature-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1},
                {caption: "nature-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2},
                {caption: "nature-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3}
            ]
        });
    });
</script>
@include('partner-dashboard.header')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-4">
            <h3>{{__('partner/post.post_create')}}</h3>
        </div>
        <div class="col-lg-7 col-md-8 col-sm-8 col-xs-8">
        <button data-toggle="modal" data-target="#postGuideline" class="btn btn-primary pull-right">Guideline</button>
</div>
    </div>
    <div class="title_right">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <form class="form-horizontal form-label-left" method="post" action="{{ url('/partner/branch/post') }}"
                  enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{__('partner/post.header')}}:</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                         <span style="color: red;">
                            @if ($errors->getBag('default')->first('postHeader'))
                                {{ $errors->getBag('default')->first('postHeader') }}
                            @endif
                         </span>
                         <input type="text" class="form-control" placeholder="{{__('partner/post.post_header')}}"
                                name="postHeader" required/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{__('partner/post.caption')}}:</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('postCaption'))
                                {{ $errors->getBag('default')->first('postCaption') }}
                            @endif
                        </span>
                        <input type="text" class="form-control" placeholder="{{__('partner/post.post_caption')}}"
                               name="postCaption" required/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{__('partner/post.post_image')}}:</label>
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
                    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
           
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
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
                Picture size should be 316px by 476px fitting our frame (too large or small size may change the quality of the image)<br><br>
                <b>There two types of comment section</b><br>
                
                    <p><b>Header</b> – Partner will add the topic or main theme of the post. (30 words limit)</p>
                    <p><b>Caption</b> – Partner will give small detailed summary of the offer or include the main reason of their posting. (30-50 words limit)</p>
     <br>
                <b>Rules and Regulation of news feed posting</b><br>
                <ul>
                    <p>-Partners are allowed to post advertisements or offers that will promote their promotion along with our discounts.</p>
                    <p>-Partners can advertise their stores but cannot show offers that does not include Royalty. This is mainly because our discount and partner’s individual discount may cause confusion within the customers.</p>
                    <p>-Only related posts are to be uploaded from partner stores.</p>
                    <p>-External posts from partners store via third party cannot be uploaded.</p>
                    <p>-Please be aware that all these post will go through moderation before posting and it is essential for all the partners to read these guidelines before posting on the news feed.</p>
                    <p>-If any of the rules or regulation are violated Royalty has the right to delete or suspend the partners news feed.</p>
                    <p>-If your post is denied by our moderator, an E-mail will be sent to the partners account to explain the issues regarding the post.</p><br>
                    <p>If partner faces any difficulty in uploading or posting, partner can directly call us at <b>+880-963-862-0202</b> or E-mail us at <b>support@royaltybd.com</b>.</p>
                </ul>
            </div>
        </div>

    </div>
</div>


@include('partner-dashboard.footer')

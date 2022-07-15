@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')
<!-- page content -->
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Add post</h3>
        </div>
        @if (Session::has('updated'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
        @elseif(session('try_again'))
            <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
    </div>

    <div class="clearfix"></div>
    <div class="panel-body">
        <form class="form-horizontal form-label-left" method="post" action="{{ url('addPost') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Header:</label>
                <div class="col-md-8 col-sm-4 col-xs-12">
                      <span style="color: red;">
                        @if ($errors->getBag('default')->first('discount_for_gold'))
                              {{ $errors->getBag('default')->first('discount_for_gold') }}
                          @endif
                      </span>
                    <input type="text" class="form-control"name="postHeader" value="{{old('discount_for_gold')}}">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Caption:</label>
                <div class="col-md-8 col-sm-4 col-xs-12">
                      <span style="color: red;">
                        @if ($errors->getBag('default')->first('discount_for_gold'))
                              {{ $errors->getBag('default')->first('discount_for_gold') }}
                          @endif
                      </span>
                    <input type="text" class="form-control"name="postCaption" value="{{old('discount_for_gold')}}">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Image:</label>
                <div class="col-md-8 col-sm-4 col-xs-12">
                    <span style="color: red;">
                      @if ($errors->getBag('default')->first('discount_details_for_gold'))
                            {{ $errors->getBag('default')->first('discount_details_for_gold') }}
                        @endif
                    </span>
                    <input type="file"  name="postImage" value="{{old('discount_details_for_gold')}}">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                    <button type="submit" class="btn btn-activate pull-right">Add</button>
                </div>
            </div>
        </form>
        <button data-toggle="modal" data-target="#postGuideline" class="btn btn-success">Guideline</button>

        <div class="ln_solid"></div>
        <div class="page-title">
            <div class="title_left">
                <h3>All posts</h3>
            </div>
        </div>
        <div class="table-responsive transactions-table">
            @if(isset($allPosts))
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Header</th>
                        <th>Caption</th>
                        <th>Posted on</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($allPosts as $allPost)
                        <tr>
                            <td><img src="{{asset($allPost['image_url'])}}" width="100%" height="200px" alt="post-image"></td>
                            <td>{{$allPost['header']}}</td>
                            <td>{{$allPost['caption']}}</td>
                            <td>{{$allPost['posted_on']}}</td>
                            @if($allPost['moderate_status'] == 0)
                                <td><i class="close-icon" style="font-size: 2em; color: red;"></i></td>
                            @else
                                <td><i class="check-icon" style="font-size: 2em; color: green;"></i></td>
                            @endif
                            <td><a href="{{url('edit-post/'.$allPost['id'])}}">Edit</a></td>
                            <td><a href="{{url('delete-post/'.$allPost['id'])}}" onclick="return confirm('Are you sure?')">Delete</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <h2>No menu image.</h2>
            @endif
        </div>

    </div>
</div>
<!-- Modal -->
<div id="postGuideline" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">  <h4 class="modal-title">Guideline to create post</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
              
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
                    <li>If any of the rules or regulation are violated royalty bd has the right to delete or suspend the partners news feed.</li>
                    <li>If your post is denied by our moderator, an E-mail will be sent to the partners account to explain the issues regarding the post.</li>
                    <li>If partner faces any difficulty in uploading or posting, partner can directly call us at <b>+880-963-862-0202</b> or E-mail us at <b>support@royaltybd.com</b>.</li>
                </ul>
            </div>
        </div>

    </div>
</div>
@include('partner-admin.production.footer')
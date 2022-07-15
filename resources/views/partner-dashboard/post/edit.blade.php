@include('partner-dashboard.header')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
            <h3>{{__('partner/post.post_edit')}}</h3>
        </div>
    </div>
    <div class="title_right">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    </div>
    <!-- /.row -->
    <!-- .row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <form class="form-horizontal form-label-left" method="post"
                  action="{{ url('/partner/branch/post/'.$post->id) }}" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT"/>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{__('partner/post.header')}}:</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                          <span style="color: red;">
                            @if ($errors->getBag('default')->first('postHeader'))
                                  {{ $errors->getBag('default')->first('postHeader') }}
                              @endif
                          </span>
                        <input type="text" class="form-control" placeholder="{{__('partner/post.post_header')}}" name="postHeader"
                               required="required" value="{{$post->header}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{__('partner/post.caption')}}:</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        @if ($errors->getBag('default')->first('postCaption'))
                            {{ $errors->getBag('default')->first('postCaption') }}
                        @endif
                        <input type="text" class="form-control" placeholder="{{__('partner/post.post_caption')}}" name="postCaption"
                               value="{{$post->caption}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{__('partner/post.post_image')}}:</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="file" class="form-control" style="height: unset;" name="postImage"/>
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
    <!-- /.row -->
</div>
<!-- /.container-fluid -->


@include('partner-dashboard.footer')

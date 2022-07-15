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
            <h3>Edit Post</h3>
        </div>
        @if (Session::has('updated'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
        @elseif(session('try_again'))
            <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
    </div>

    <div class="clearfix"></div>
    <div class="panel-body">
        @if (isset($post))
            <form class="form-horizontal form-label-left" method="post" action="{{ url('editPost/'.$post['id']) }}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Heading:</label>
                    <span style="color: red;">
                    @if ($errors->getBag('default')->first('header'))
                        {{ $errors->getBag('default')->first('header') }}
                    @endif
                    </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" class="form-control" name="header" value="{{$post->postHeader->header}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Caption:</label>
                    <span style="color: red;">
                    @if ($errors->getBag('default')->first('caption'))
                        {{ $errors->getBag('default')->first('caption') }}
                    @endif
                    </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" class="form-control" name="caption" value="{{$post['caption']}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Previous image:</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <img src="{{asset($post['image_url'])}}" alt="Post Image" width="30%" height="100px">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">New image:</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="file" name="postImage">
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                        <button type="submit" class="btn btn-activate pull-right">Submit</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@include('partner-admin.production.footer')

@include('partner-dashboard.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-4 col-xs-12">
            <h3>{{__('partner/post.post_crud')}}</h3>
            <a type="button" class="btn btn-create" href="{{ url('/partner/branch/post/create') }}"
               style="margin-left: unset;">+ {{__('partner/post.post_create')}}</a>
            <a href="{{url('partner/branch/post')}}" class="btn btn-default">{{__('partner/post.all')}}</a>
            <a href="{{url('partner/branch/pending_post')}}" class="btn btn-warning">{{__('partner/post.pending')}}</a>
            <a href="{{url('partner/branch/approved_post')}}" class="btn btn-success">{{__('partner/post.approved')}}</a>
        </div>
        <div class="col-md-7 col-lg-7">
            @if(Session::has('error'))
                <div class="alert alert-danger">
                    {{ Session::get('error') }}
                </div>
            @elseif(Session::has('status'))
                <div class="alert alert-success">
                    {{ Session::get('status') }}
                </div>
            @endif
        </div>
    </div>
    <!-- /.row -->
    <!-- .row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            @if($posts)
                <table id="postsList" class="table table-bordered table-hover table-striped projects">
                    <thead>
                    <tr>
                        <th>Posted On</th>
                        <th>Image</th>
                        <th>Header</th>
                        <th>Caption</th>
                        <th>Activity</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($posts as $post)
                        <tr class="post_row" data-post-id='{{ $post->id }}'>
                            <?php $posted_on = date("F d, Y h:i A", strtotime($post->posted_on)); ?>
                            <td>{{ $posted_on }}
                                @if($post->scheduled_at > date("Y-m-d H:i:s") && $post->moderate_status == 0)
                                    <b style="color: #c13e3e;">{{'Scheduled at '. date("h:i A F d, Y", strtotime($post->scheduled_at))}}</b>
                                @endif
                            </td>
                            <td><img src="{{asset($post['image_url'])}}" width="100%" alt="post-image"></td>
                            <td>{{ $post->header }}</td>
                            <td>{{ $post->caption }}</td>
                            <td>Shared: {{$post->share_post_count}}<br>
                            Loved: {{$post->like_count}}
                            </td>
                            <td>
                                <a href="{{url('partner/branch/post/'.$post->id.'/edit')}}" class="btn btn-primary editBtn">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <br><br>
                                <button class="btn btn-danger deleteBtn" data-post-id='{{ $post->id }}'>
                                    <i class="fa fa-trash"></i>
                                </button>
                                <br><br>
                                @if($post->moderate_status == 1)
                                    <button class="label-success">Approved</button>
                                @else
                                    <button class="label-danger">Pending</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div style="font-size: 1.4em; color: red;">
                    {{ 'No post found.' }}
                </div>
            @endif
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->

@include('partner-dashboard.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure?")) {
            //fetch the post id
            var postId = $(this).attr('data-post-id');
            var url = "{{ url('/partner/branch/post/') }}";
            url += '/' + postId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $(document).ready(function () {
        $('#postsList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>
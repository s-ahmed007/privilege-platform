@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('delete partner'))
                <div class="alert alert-danger">
                    {{ session('delete partner') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @endif
            <h3>All Posts</h3>
        </div>
        <div class="title_left">
            <div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">
                <form action="{{ url('searchPartnerPost') }}" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="partnerSearchKey">Partner:</label><br>
                        <input type="text" class="form-control" name="partnerName" id="partnerSearchKey"
                               placeholder="Partner with name or E-mail" style="width: 100%;border-radius: 25px">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                @if($posts)
                        <table class="table table-striped projects">
                            <thead>
                            <tr>
                                <th style="width: 10%">Partner name</th>
                                <th style="width: 20%">Image</th>
                                <th>Header</th>
                                <th>Caption</th>
                                <th>Posted on</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($posts as $post)
                                <tr>
                                    <td>{{ $post['partnerInfo']['partner_name'] }}</td>
                                    <td><img src="{{ asset($post['image_url']) }}" width="200px"></td>
                                    <td>{{ $post['postHeader']['header'] }}</td>
                                    <td>{{ $post['caption'] }}</td>
                                    <td>{{ $post['posted_on'] }}</td>
                                    <td><i class="check-icon" style="font-size: 2em; color: green;"
                                           id="statusSign_{{$post['id']}}"></i></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'Post not found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(function () {
        $("#partnerSearchKey").autocomplete({
            source: '{{url('/partnerByKey')}}',
            autoFocus: true,
            delay: 500
        });
    });
</script>
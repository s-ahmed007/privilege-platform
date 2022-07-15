@include('admin.production.header')

<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Partner's posts</h3>
            </div>

            <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                    <form action="{{ url('PostByPartnerName') }}" method="post">
                        <div class="input-group">
                            <select name="partnerName" class="form-control">
                                <option>Select partner</option>
                                @foreach($partnerName as $name)
                                    <option value="{{$name['partner_name']}}">{{$name['partner_name']}}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <span class="input-group-btn">
                                      <button class="btn btn-default" type="submit">Go!</button>
                                    </span>
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
                    @if($allPosts)
                        <!-- start project list -->
                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th style="width: 10%">Partner name</th>
                                    <th style="width: 20%">Image</th>
                                    <th>Header</th>
                                    <th>Caption</th>
                                    <th>Posted on</th>
                                    <th>Status</th>
                                    <th>Approval</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($allPosts as $post)
                                    <tr>
                                        <td>{{ $post['partnerInfo']['partner_name']}}</td>
                                        <td><img src="{{ asset($post['image_url']) }}" width="200px"></td>
                                        <td>{{ $post['header'] }}</td>
                                        <td>{{ $post['caption'] }}</td>
                                        <td>{{ $post['posted_on'] }}</td>
                                        @if($post['moderate_status'] == 0)
                                            <td><i class="fa fa-close" style="font-size: 2em; color: red;"
                                                   id="statusSign_{{$post['id']}}"></i></td>
                                            <td><input type="checkbox" name="approved" id="postApproval"
                                                       value="1_{{$post['id']}}"></td>
                                        @else
                                            <td><i class="fa fa-check" style="font-size: 2em; color: green;"
                                                   id="statusSign_{{$post['id']}}"></i></td>
                                            <td><input type="checkbox" name="notApproved" id="postApproval"
                                                       value="0_{{$post['id']}}" checked></td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <!-- end project list -->
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
</div>

@include('admin.production.footer')
{{-- ========================================================================================================
  ========================customer approval with JavaScript & Ajax====================
============================================================================================================= --}}
<script>
    $(document).on('click', '#postApproval', function () {
        var url = "{{ url('/postApproval') }}";

        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'id': this.value},
            success: function (data) {
                console.log(data);
                // alert(data);
                if (data[0] === '1') {
                    $('#statusSign_' + data[1]).removeClass('fa-close');
                    $('#statusSign_' + data[1]).addClass('fa-check');
                    document.getElementById('statusSign_' + data[1]).style.color = 'green';
                } else {
                    $('#statusSign_' + data[1]).removeClass('fa-check');
                    $('#statusSign_' + data[1]).addClass('fa-close');
                    document.getElementById('statusSign_' + data[1]).style.color = 'red';
                }
            }
        });
    });
</script>
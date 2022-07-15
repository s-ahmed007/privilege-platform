@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('updated'))
                <div class="alert alert-success">
                    {{ session('updated') }}
                </div>
            @elseif (session('ip_deleted'))
                <div class="alert alert-danger">
                    {{ session('ip_deleted') }}
                </div>
            @elseif (session('created'))
                <div class="alert alert-success">
                    {{ session('created') }}
                </div>
            @endif
            <h3>Ip Address of {{$branch_info->info->partner_name . ' ('.$branch_info->partner_area.')'}}</h3>
            <a type="button" class="btn btn-create" href="{{ url('/create-branch-ip-address/'.$branch_id) }}" style="margin-left: unset;">+ Create A New Ip Address</a>
        </div>

    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <table id="userList" class="table table-striped projects">
                        <thead>
                        <tr>
                            <th>Ip Address</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($ipAddress as $key => $value)
                            <tr>

                                <td>{{ $value->ip_address }}</td>
                                <td align="center">
                                    <select id="ip_address_edit_{{$value->id}}" onchange="ip_address_edit('{{$value->id}}')">
                                        <option disabled selected>--Options--</option>
                                        <option value="1">Edit</option>
                                        <option value="2">Delete</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
{{-- ============================================================================================================
  ========================customer approval with JavaScript & Ajax====================
============================================================================================================= --}}
<script>
    function userApproval(status, userId, prompt_text) {
        if (prompt_text === 'Are you sure you want to activate this user?') {
            if (confirm(prompt_text)) {
                userApprovalAjax(status, userId);
            }
        } else {
            if (confirm(prompt_text)) {
                userApprovalAjax(status, userId);
            }
        }
    }

    function userApprovalAjax(status, userId) {
        var url = "{{ url('branchUserApproval') }}";

        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'userId': userId, 'status': status},
            success: function (data) {
                console.log(data);
                // alert(data);
                if (data === '1') {
                    $('#statusSign_' + userId).removeClass('cross-icon');
                    $('#statusSign_' + userId).addClass('check-icon');
                    document.getElementById('statusSign_' + userId).style.color = 'green';
                    $('#statusSign_' + userId).attr("onclick", "userApproval(2," + userId + ", 'Are you sure you want to deactivate this customer?')");
                } else {
                    $('#statusSign_' + userId).removeClass('check-icon');
                    $('#statusSign_' + userId).addClass('cross-icon');
                    document.getElementById('statusSign_' + userId).style.color = 'red';
                    $('#statusSign_' + userId).attr("onclick", "userApproval(1," + userId + ", 'Are you sure you want to activate this customer?')");
                }
            }
        });
    }
</script>
<script>
    function ip_address_edit(ip_id) {
        var option_type = document.getElementById("ip_address_edit_" + ip_id).value;
        //return false;
        if (option_type == 1) {
            var url = "{{url('/edit-branch-ip-address')}}" + '/' + ip_id;
            window.location = url;
        } else if (option_type == 2) {
            if(confirm('Are you sure?')){
                var url = "{{url('/delete-branch-ip-address')}}" + '/' + ip_id;
                window.location = url;
            }
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#userList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>

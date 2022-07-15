@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        color: #337ab7 !important;background-color: #eee !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        color: #337ab7 !important; background-color: #ffffff !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current{
        color: #ffffff !important; background-color: #337ab7 !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
        color: #ffffff !important; background-color: #337ab7 !important}
</style>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('updated'))
                <div class="alert alert-success">
                    {{ session('updated') }}
                </div>
            @elseif (session('user_deleted'))
                <div class="alert alert-danger">
                    {{ session('user_deleted') }}
                </div>
            @elseif (session('created'))
                <div class="alert alert-success">
                    {{ session('created') }}
                </div>
            @endif
            <h3>All Scanners</h3>
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
                            <th>Name</th>
                            <th>Role</th>
                            <th>Partner Name</th>
                            <th>Point</th>
                            <th>Phone Number</th>
                            <th>PIN</th>
                            @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                                <th>Status</th>
                            @endif
{{--                            <th>Action</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $key => $value)
                            <tr>
                                <td>{{ $value->full_name}}</td>
                                <td>{{ $value->branchUser->role > 100 ? "Owner":"Scanner"}}</td>
                                <td>{{ $value->branch->info->partner_name }} {{ $value->branch->partner_area }}</td>
                                <td>{{ $value->scannerReward->point }}</td>
                                <td>{{ $value->branchUser['phone'] }}</td>
                                <td>{{ $value->branchUser['pin_code'] }}</td>
                                @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                                    <div id="user_approval">
                                        @if($value->branchUser->active == 0)
                                            <td style="cursor:pointer;text-align:center;">
                                                <i class="cross-icon-admin" style="font-size: 2em;" id="statusSign_{{$value->branch_user_id}}"
                                                   onclick="userApproval('1', '{{$value->branch_user_id}}', 'Are you sure you want to activate this user?')"></i>
                                            </td>
                                        @else
                                            <td style="cursor: pointer;text-align: center;">
                                                <i class="check-icon" style="font-size: 2em; color: green;" id="statusSign_{{$value->branch_user_id}}"
                                                   onclick="userApproval('2', '{{$value->branch_user_id}}', 'Are you sure you want to deactivate this user?')"></i>
                                            </td>
                                        @endif
                                    </div>
                                @endif
{{--                                <td align="center">--}}
{{--                                    <select id="user_edit_{{$value->branch_user_id}}" onchange="user_edit('{{$value->branch_user_id}}')"--}}
{{--                                            class="selectChangeOff">--}}
{{--                                        <option disabled selected>--Options--</option>--}}
{{--                                        <option value="1">Edit</option>--}}
{{--                                        @if(count($value->transactions) == 0)--}}
{{--                                            <option value="2">Delete</option>--}}
{{--                                        @endif--}}
{{--                                    </select>--}}
{{--                                </td>--}}
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
                if (data === '1') {
                    $('#statusSign_' + userId).removeClass('cross-icon-admin');
                    $('#statusSign_' + userId).addClass('check-icon');
                    document.getElementById('statusSign_' + userId).style.color = 'green';
                    $('#statusSign_' + userId).attr("onclick", "userApproval(2," + userId + ", 'Are you sure you want to deactivate this user?')");
                } else {
                    $('#statusSign_' + userId).removeClass('check-icon');
                    $('#statusSign_' + userId).addClass('cross-icon-admin');
                    $('#statusSign_' + userId).attr("onclick", "userApproval(1," + userId + ", 'Are you sure you want to activate this user?')");
                }
            }
        });
    }
</script>
<script>
    function user_edit(user_id) {
        var option_type = document.getElementById("user_edit_" + user_id).value;
        //return false;
        if (option_type == 1) {
            var url = "{{url('/edit-branch-scanner')}}" + '/' + user_id+"/"+"fromallscanners";
            window.location = url;
        } else if (option_type == 2) {
            if(confirm('Are you sure?')){
                var url = "{{url('/delete-branch-scanner')}}" + '/' + user_id;
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
    //to keep select option unselected from prev page
    $(document).ready(function () {
        $(".selectChangeOff").each(function () {
            $(this).val($(this).find('option[selected]').val());
        });
    })
</script>

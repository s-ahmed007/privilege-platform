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
            @elseif (session('deleted'))
                <div class="alert alert-danger">
                    {{ session('deleted') }}
                </div>
            @elseif (session('created'))
                <div class="alert alert-success">
                    {{ session('created') }}
                </div>
            @endif
            <h3>Leader Board Prizes</h3>
            <a type="button" class="btn btn-create" href="{{ url('/create-leaderboard-prize') }}" style="margin-left: unset;">+ Create A New Prize</a>
        </div>

    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <table id="prizeList" class="table table-striped projects">
                        <thead>
                        <tr>
                            <th>Prize</th>
                            <th>Month</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($prizes as $key => $value)
                            <tr>
                                <td>{{ $value->prize_text }}</td>
                                <td>{{ $value->month_name }}</td>
                                <td align="center">
                                    <select id="scan_prize_edit_{{$value->id}}" onchange="scan_prize_edit('{{$value->id}}')"
                                        class="selectChangeOff">
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
<script>
    function scan_prize_edit(prize_id) {
        var option_type = document.getElementById("scan_prize_edit_" + prize_id).value;
        //return false;
        if (option_type == 1) {
            var url = "{{url('/edit-leaderboard-prize')}}" + '/' + prize_id;
            window.location = url;
        } else if (option_type == 2) {
            if(confirm('Are you sure?')){
                var url = "{{url('/delete-leaderboard-prize')}}" + '/' + prize_id;
                window.location = url;
            }
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#prizeList').DataTable({
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

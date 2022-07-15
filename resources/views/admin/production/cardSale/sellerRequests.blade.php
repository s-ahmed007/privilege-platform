@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

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
            <h3>All payments of seller</h3>
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
                            <th>Phone</th>
                            {{--<th>Requested Amount</th>--}}
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Payment Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($requests as $key => $value)
                            <tr>
                                <td>{{ $value->account->info->first_name.' '.$value->account->info->last_name }}</td>
                                <td>{{ $value->account->phone }}</td>
                                {{--<td>BDT {{ $value['amount'] }}</td>--}}
                                <td>{{ $value->credit }} </td>
                                <td>{{ $value->debit }} </td>
                                <td>{{ date("F d, Y h:i A", strtotime($value->posted_on)) }}</td>
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
    function sellerRequest(status, id, prompt_text) {
        if (prompt_text === 'Are you sure you want to accept user request?') {
            if (confirm(prompt_text)) {
                requestAcceptAjax(status, id);
            }
        } else {
            if (confirm(prompt_text)) {
                requestAcceptAjax(status, id);
            }
        }
    }

    function requestAcceptAjax(status, id) {
        var url = "{{ url('sellerRequestAccept') }}";

        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'id': id, 'status': status},
            success: function (data) {
                console.log(data);
                // alert(data);
                if (data === '1') {
                    $('#statusSign_' + id).removeClass('cross-icon-admin');
                    $('#statusSign_' + id).addClass('check-icon');
                    document.getElementById('statusSign_' + id).style.color = 'green';
                    $('#statusSign_' + id).attr("onclick", "sellerRequest(2," + id + ", 'Are you sure you want to deny user request?')");
                } else if(data === '2'){
                    $('#statusSign_' + id).removeClass('check-icon');
                    $('#statusSign_' + id).addClass('cross-icon-admin');
                    $('#statusSign_' + id).attr("onclick", "sellerRequest(1," + id + ", 'Are you sure you want to accept user request?')");
                }else{
                    alert('Something went wrong. Please try again')
                }
            }
        });
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

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
            <h3>Branch Scanner Requests</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
            <table id="userList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                        <tr>
                            <th>Scanner Name</th>
                            <th>Scanner Number</th>
                            <th>Branch Name</th>
                            <!-- {{--<th>Requested Amount</th>--}} -->
                            <th>Requested Reward</th>
                            <th>Special Request</th>
                            <th>Request Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                  <tbody>
                  @foreach ($prizeHistory as $key => $value)
                        <tr>
                            <td>{{ $value->branchScanner->full_name }}</td>
                            <td>{{ $value->branchScanner->branchUser->phone }}</td>
                            <td>{{ $value->branchScanner->branch->info->partner_name }}<br>{{ $value->branchScanner->branch->partner_area }}</td>
                            {{--<td>BDT {{ $value['amount'] }}</td>--}}
                            <td>{{ $value->text }}<br><b>{{ 'Points used :'.$value->point }}</b></td>
                            <td style="text-align: center;">{!! $value->request_comment != null ? $value->request_comment : '<i class="fas fa-minus"></i>' !!}</td>
                            <td>{{ date("F d, Y h:i A", strtotime($value->posted_on)) }}</td>
                            @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                                <div id="user_approval">
                                    <td style="cursor:pointer;text-align: center;">
                                    @if($value->status == 0)
                                        <button class="btn btn-success pull-right" id="statusSign_{{ $value->id }}"
                                                onclick="scannerRequest('1', '{{ $value->id }}',
                                                        'Are you sure you want to accept the user request?')">Pay</button>
                                    @else
                                        <button class="btn btn-activate pull-right" id="statusSign_{{ $value->id }}"
                                                onclick="scannerRequest('2', '{{ $value->id }}',
                                                        'Are you sure you want to deny the user request?')">Paid</button>
                                    @endif
                                    </td>
                                </div>
                            @elseif(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                <div id="user_approval">
                                    <td style="text-align: center;">
                                    @if($value->status == 0)
                                        <button class="btn btn-activate pull-right" id="statusSign_{{ $value->id }}">Pay</button>
                                    @else
                                        <button class="btn btn-activate pull-right" id="statusSign_{{ $value->id }}">Paid</button>
                                    @endif
                                    </td>
                                </div>
                            @endif
                        </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                     <tr>
                     </tr>
                  </tfoot>
               </table>
            </div>
            <!--end of .table-responsive-->
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
    function scannerRequest(status, id, prompt_text) {
        if (confirm(prompt_text)) {
            startPageLoader();
            requestAcceptAjax(status, id);
        }
    }

    function requestAcceptAjax(status, id) {
        var url = "{{ url('scannerRequestAccept') }}";

        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'id': id, 'status': status},
            success: function (data) {
                if (data === '1') {
                    $('#statusSign_' + id).removeClass('btn-success').addClass('btn-activate').text('Paid')
                        .attr("onclick", "scannerRequest(2," + id + ", 'Are you sure you want to deny user request?')");
                } else if(data === '2'){
                    $('#statusSign_' + id).removeClass('btn-activate').addClass('btn-success').text('Pay')
                        .attr("onclick", "scannerRequest(1," + id + ", 'Are you sure you want to accept user request?')");
                }else{
                    alert('Something went wrong. Please try again')
                }
                stopPageLoader();
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

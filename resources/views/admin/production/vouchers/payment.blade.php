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
            @elseif (session('try_again'))
                <div class="alert alert-danger">
                    {{ session('try_again') }}
                </div> 
            @endif
            <h3>Deal Payment</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    <table id="branchList" class="table table-bordered table-hover table-striped projects">
                        <thead>
                        <tr>
                            <th>Branch Details</th>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Pay</th> 
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($branches as $key => $value)
                            <tr>
                                <td>{{ $value->branch->info->partner_name.', '.$value->branch->partner_area }}
                                </td>
                                <td>Current: {{ $value->credit.' tk' }}<br>
                                    Paid: {{$value->credit_used.' tk'}}
                                </td>
                                <td>Current: {{ $value->debit.' tk' }}<br>
                                    Paid: {{$value->debit_used.' tk'}}
                                </td>
                                <td>
                                    <input type="button" class="btn btn-activate pull-right" value="Pay" onclick="pay_branch_voucher('{{$value->branch_id}}')">
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
    function pay_branch_voucher(branch_id) {
        if (confirm('Are you sure to pay?')) {
            var url = "{{ url('/admin/pay_branch_voucher') }}" + "/" + branch_id;

           $('<form action="' + url + '" method="POST">' +
           '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
           '<input type="hidden" name="_method" value="POST"/>' +
           '</form>').appendTo($(document.body)).submit();
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#branchList').DataTable({
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
</script>s
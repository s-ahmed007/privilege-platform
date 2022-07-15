@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        color: #337ab7 !important;background-color: #eee !important
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #337ab7 !important;background-color: #ffffff !important
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        color: #ffffff !important;background-color: #337ab7 !important
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        color: #ffffff !important;background-color: #337ab7 !important
    }
</style>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>All Donations</h3>
        </div>
        <h4>Total Donation Received - BDT {{$donations->sum('amount')}}</h4>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($donations)
                        <table id="donationList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Donor Info</th>
                                <th>Amount</th>
                                <th>Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($donations as $key => $donation)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $donation->name }}<br>
                                        {{ $donation->phone }}<br>
                                        {{ $donation->email }}
                                    </td>
                                    <td>{{ intval($donation->amount).' tk' }}</td>
                                    <td>{{date("M d, Y h:i A", strtotime($donation->tran_date))}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No donation found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#donationList').DataTable({
            "paging": true,
            "order": [],
        });
    });
</script>
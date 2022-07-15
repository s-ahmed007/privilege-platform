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
            @if (session('status'))
                <div><span class="success-d">{{ session('status') }}</span></div>
            @endif
            <h3>All Transactions ({{$tab_title}})</h3>
            <a class="btn btn-all" href="{{url('partners-all-transactions/active')}}">Active Partners</a>
            {{--         <a class="btn btn-gold" href="{{url('partners-all-transactions/inactive')}}">Inactive</a>--}}
            <a class="btn btn-expired" href="{{url('partners-all-transactions/expired')}}">Expired Partners</a>
            <a class="btn btn-premium" href="{{url('partners-all-transactions/all')}}">All Partners</a>
            <a class="btn btn-expired" href="{{url('partners-all-transactions/deleted')}}">Deleted Transactions</a>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($allTransactions)
                        <table id="transactionList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Time<i class="transaction-icon custom"></i></th>
                                <th>Partner</th>
                                <th>Customer</th>
                                <th>Credits</th>
                                <th>Offers Availed</th>
                                <th>Scan Type</th>
                                @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin && $tab_title != 'Deleted Transactions')
                                    <th>Actions</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($allTransactions as $transaction)
                                <tr>
                                    <?php
                                    $posted_on = date("Y-M-d H:i:s", strtotime($transaction['posted_on']));
                                    $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                    ?>
                                    <td>{{$transaction['id']}}</td>
                                    <td>{!! date_format($created, "d-m-y &#9202; h:i A") !!}</td>
                                    <td><span style="font-weight: bold;">{{ $transaction['partner_name'].' - '.$transaction['partner_area'] }}
                                        </span><br>
                                        {{substr($transaction['partner_address'], 0,30).'...'}}
                                    </td>
                                    <td><span style="font-weight: bold;">{{ $transaction['customer_name'] }}</span><br>
                                        {{ $transaction['customer_id'] }}<br>
                                        {{ $transaction['customer_phone'] }}<br>
                                        @if($transaction['delivery_type'])
                                            @if($transaction['delivery_type'] == 11 &&
                                                $transaction['ssl_tran_date'] > date("2019-10-17"))
                                                <span class="trial-label">Trial</span>
                                            @else
                                                <span class="premium-label">Premium Member</span>
                                            @endif
                                        @else
                                            <span class="premium-label">Guest Member</span>
                                        @endif
                                        @if($transaction['customer_expired'])
                                            <span class="manual-label">Expired</span>
                                        @endif
                                    </td>
                                    <td style="font-weight: bold; color: darkgreen ">{{ $transaction['point'] }}</td>
                                    @if($transaction['offer_details'] == null)
                                        <td>{{"Discount Availed"}}</td>
                                    @else
                                        <td>{{ $transaction['offer_details'] }}</td>
                                    @endif
                                    @if($transaction['branch_user_id'] == \App\Http\Controllers\Enum\AdminScannerType::manual_transaction)
                                        <td><span class="manual-label">Manual Transaction</span></td>
                                    @elseif($transaction['transaction_request_id']!=null)
                                        <td><span class="qr-label">QR Scan</span> <br>
                                            @if($transaction['branch_user_id'] == \App\Http\Controllers\Enum\AdminScannerType::accept_tran_req)
                                                <br><span class="admin-label">Admin</span>
                                            @endif
                                            @if($transaction['platform'] == \App\Http\Controllers\Enum\PlatformType::android)
                                                <br><span class="android-label">Android</span>
                                            @elseif($transaction['platform'] == \App\Http\Controllers\Enum\PlatformType::ios)
                                                <span class="ios-label">iOS</span>
                                            @else
                                                <span class="na-label">N/A</span>
                                            @endif
                                        </td>
                                     @else
                                        <td><span class="card-label">Card Scan</span></td>
                                    @endif
                                    @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin && $tab_title != 'Deleted Transactions')
                                        <td>
                                            <button class="btn btn-delete deleteTransactionBtn" data-transaction-id="{{$transaction['id']}}">
                                                <i class="fa fa-trash-alt"></i> Delete
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{--                  {{$allTransactions->links()}}--}}
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No Branch found.' }}
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
        $('#transactionList').DataTable({
            "order": [],
        });
    });

    $('.deleteTransactionBtn').on('click', function (event) {
        if (confirm("Are you sure?")) {
            //fetch the partner branch id
            var tranId = $(this).attr('data-transaction-id');
            var url = "{{ url('/delete-transaction') }}";
            url += '/' + tranId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });
</script>
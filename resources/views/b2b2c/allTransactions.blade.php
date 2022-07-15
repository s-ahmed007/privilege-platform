@include('b2b2c.layout.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('delete customer'))
                <div class="alert alert-danger">
                    {{ session('delete customer') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @elseif(session('codPaymentClear'))
                <div class="alert alert-success">
                    {{ session('codPaymentClear') }}
                </div>
            @elseif(session('cardActivated'))
                <div class="alert alert-success">
                    {{ session('cardActivated') }}
                </div>
            @else

            @endif
            <h3>All Transactions</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                @if($allTransactions)
                    <!-- start project list -->
                        <table id="transactionList" class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>Time
                                    <i class="transaction-icon custom"></i>
                                </th>
                                <th>Partner</th>
                                <th>Customer</th>
                                <th>Transaction Amount</th>
                                <th>Discount Availed</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($allTransactions as $transaction)
                                <tr>
                                    <?php
                                    $posted_on = date("Y-M-d H:i:s", strtotime($transaction->posted_on));
                                    $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                    ?>
                                    <td>{{ date_format($created, "d-m-y &#9202; h:i A") }}</td>
                                    <td><span style="font-weight: bold;">{{ $transaction->branch->info->partner_name
                                        .' - '.$transaction->branch->partner_area }}</span><br>
                                        {{substr($transaction->branch->partner_address, 0,30).'...'}}
                                    </td>
                                    <td><span style="font-weight: bold;">{{ $transaction->customer->customer_full_name }}
                                            </span><br>
                                        {{ $transaction->customer->customer_id }}<br>
                                        {{ $transaction->customer->customer_contact_number }}

                                    </td>
                                    <td style="font-weight: bold; color: darkgreen ">{{ $transaction->amount_spent }}</td>
                                    <td>{{ $transaction->discount_amount }}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#transactionList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>

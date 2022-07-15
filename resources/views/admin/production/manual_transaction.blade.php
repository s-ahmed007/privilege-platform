@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.12.4.js"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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
        <h3>Manual Transaction</h3>
        <div class="title_left">
            @if(session('error'))
                <div class="title_right alert alert-warning" style="text-align: center;">
                    {{ session('error') }}
                </div>
            @elseif(session('success'))
                <div class="title_right alert alert-success" style="text-align: center;">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            <div class="title_left">
                <div class="clearfix"></div>
                <div class="panel-body">
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('manual-transaction') }}">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-4">
                                <label class="control-label" for="partner_name">Select Partner:</label>
                                <span style="color: #E74430;" class="error_admin_code">
                                    @if ($errors->getBag('default')->first('partner_name'))
                                        {{ $errors->getBag('default')->first('partner_name') }}
                                    @endif
                                </span>
                                <select class="form-control" name="partner_name" onchange="getBranches(this.value);">
                                    <option selected disabled>-----</option>
                                    @foreach($allPartners as $partner)
                                        <option value="{{$partner->partner_account_id}}">{{$partner->partner_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label" for="partner_branch">Select Branch:</label>
                                <span style="color: #E74430;" class="error_admin_code">
                                    @if ($errors->getBag('default')->first('partner_branch'))
                                        {{ $errors->getBag('default')->first('partner_branch') }}
                                    @endif
                                </span>
                                <select class="form-control" name="partner_branch" id="partner_branch" onchange="getOffers(this.value);"></select>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label" for="branch_offer">Select Offer:</label>
                                <span style="color: #E74430;" class="error_admin_code">
                                    @if ($errors->getBag('default')->first('branch_offer'))
                                        {{ $errors->getBag('default')->first('branch_offer') }}
                                    @endif
                                </span>
                                <select class="form-control" name="branch_offer" id="branch_offer"></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label" for="customer_id">Customer ID:</label>
                                <span style="color: #E74430;" class="error_admin_code">
                        @if ($errors->getBag('default')->first('customer_id'))
                                        {{ $errors->getBag('default')->first('customer_id') }}
                                    @endif
                        </span>
                                <input type="text" name="customer_id" id="customer_id" class="form-control" maxlength="16" minlength="16">
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="date">Date:</label>
                                <span style="color: #E74430;" class="error_admin_code">
                        @if ($errors->getBag('default')->first('date'))
                                        {{ $errors->getBag('default')->first('date') }}
                                    @endif
                        </span>
                                <input type="text" name="date" id="date" placeholder="Ex: 20-06-2019" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="time">Time:</label>
                                <span style="color: #E74430;" class="error_admin_code">
                        @if ($errors->getBag('default')->first('time'))
                                        {{ $errors->getBag('default')->first('time') }}
                                    @endif
                        </span>
                                <input type="time" name="time" id="time" placeholder="Ex: 20-06-2019" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="weekday">Weekday:</label>
                                <span style="color: #E74430;" class="error_admin_code">
                        @if ($errors->getBag('default')->first('weekday'))
                                        {{ $errors->getBag('default')->first('weekday') }}
                                    @endif
                        </span>
                                <select name="weekday" id="weekday" class="form-control">
                                    <option selected disabled>-----</option>
                                    <?php
                                    for ($i = 0; $i < 7; $i++) {
                                        echo "<option value='" . jddayofweek($i, 2) . "'>" . jddayofweek($i, 1) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-activate pull-right">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table id="transactionList" class="table table-bordered table-hover table-striped projects">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Time<i class="transaction-icon custom"></i></th>
                                        <th>Partner</th>
                                        <th>Customer</th>
                                        <th>Points</th>
                                        <th>Offers Availed</th>
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
                                            <td>{{ date_format($created, "d-m-y &#9202; h:i A") }}<br>
                                            </td>
                                            <td><span style="font-weight: bold;">{{ $transaction['partner_name']
                                    .' - '.$transaction['partner_area'] }}</span><br>
                                                {{substr($transaction['partner_address'], 0,30).'...'}}
                                            </td>
                                            <td><span style="font-weight: bold;">{{ $transaction['customer_name'] }}
                                    </span><br>
                                                {{ $transaction['customer_id'] }}<br>
                                                {{ $transaction['customer_phone'] }}<br>
                                                @if($transaction['delivery_type']==null)
                                                <span class="guest-label">Guest Member</span>
                                                @elseif($transaction['delivery_type']!=\App\Http\Controllers\Enum\DeliveryType::virtual_card)
                                                <span class="premium-label">Premium Member</span>
                                                @elseif ($transaction['delivery_type']==\App\Http\Controllers\Enum\DeliveryType::virtual_card)
                                                    <span class="trial-label">Trial</span>
                                                @endif
                                            </td>
                                            <td style="font-weight: bold; color: darkgreen ">{{ $transaction['point'] }}</td>
                                            @if($transaction['offer_details'] == null)
                                                <td>{{"Discount Availed"}}</td>
                                            @else
                                                <td>{{ $transaction['offer_details'] }}</td>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
    function getBranches(partner_id) {
        var url = "{{ url('/get-branch') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'partner_id': partner_id},
            success: function (data) {
                if (data.length !== 0) {
                    var output = '<option selected disabled>-----</option>';
                    var i;
                    for (i = 0; i < data.length; i++) {
                        output += " <option value='" + data[i]['id'] + "'>" + data[i]['area'] + " -> " + data[i]['address'] + "</option>";
                    }
                    $("#partner_branch").hide().html(output).fadeIn('slow');
                }
            }
        });
    }

    function getOffers(branch_id) {
        var url = "{{ url('/get-offer') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'branch_id': branch_id},
            success: function (data) {
                if (data.length !== 0) {
                    var output = '<option selected disabled>-----</option>';
                    var i;
                    for (i = 0; i < data.length; i++) {
                        output += " <option value='" + data[i]['id'] + "'>" + data[i]['offer'] + "</option>";
                    }
                    $("#branch_offer").hide().html(output).fadeIn('slow');
                }
            }
        });
    }

    $(document).ready(function () {
        $('#transactionList').DataTable({
            "order": []
        });
    });

</script>
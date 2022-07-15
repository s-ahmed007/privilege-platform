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
            @elseif (session('over_limit'))
                <div class="alert alert-danger">
                    {{ session('over_limit') }}
                </div>
            @elseif (session('created'))
                <div class="alert alert-success">
                    {{ session('created') }}
                </div>
            @endif
            <h3>All Membership Seller</h3>
            <a type="button" class="btn btn-create" href="{{ url('/create-card-seller/') }}" style="margin-left: unset;">+ Create New Seller</a>
            <a type="button" class="btn btn-create" href="{{ url('/seller-requests/') }}" style="margin-left: unset;">Seller  Payments</a>
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
                            <th>Seller Details</th>
                            <th>Membership Sold</th>
                            <th>Balance</th>
                            <th>Commission (Online Only)</th>
                            <th>Due to Royalty</th>
                            <th>Company Earned</th>
                            <th>Action</th>
                            <th>Pay</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($allSellers as $key => $value)
                            <?php
                            $commission_history = $value->info->commissionHistory;
                            $online_commission = $commission_history->where('type', \App\Http\Controllers\Enum\SellerCommissionType::ONLINE_PAY)
                                ->where('paid', 0)->sum('commission');
                            $seller_balance = $value->balance->debit - $online_commission;
                            ?>
                            <tr>
                                <td>{{ $value->info->first_name.' '.$value->info->last_name }}<br>
                                    {{ $value->phone }}
                                    @if($value->info->promo)
                                        <br>
                                        @foreach($value->info->promo as $promo)
                                            <span class="label label-info">{{$promo->code}}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>Premium: <b>{{ $value->sold_card }}</b><br></td>
                                <td>
                                    @if($seller_balance > 0)
                                        <h4 class="text-success text-center">{{$seller_balance}}</h4>
                                    @elseif($seller_balance < 0)
                                        <h4 class="text-danger text-center">{{$seller_balance}}</h4>
                                    @else
                                        <h4 class="text-center">{{$seller_balance}}</h4>
                                    @endif
                                </td>
                                <td>
                                    @if($online_commission > 0)
                                        <h4 class="text-success text-center">{{$online_commission}}</h4>
                                    @else
                                        <h4 class="text-center">{{$online_commission}}</h4>
                                    @endif
                                </td>
                                <td>
                                    @if($value->balance->debit > 0)
                                        <h4 class="text-danger text-center">{{$value->balance->debit}}</h4>
                                    @else
                                        <h4 class="text-center">{{$value->balance->debit}}</h4>
                                    @endif
                                </td>
                                <td><h4 class="text-success text-center">{{$commission_history->sum('ssl.amount')}}</h4></td>
                                <td>
                                    <select id="user_action_{{$value->id}}" onchange="user_action('{{$value->id}}')"
                                            class="selectChangeOff">
                                        <option disabled selected>--Options--</option>
                                        <option value="1">Edit</option>
                                        {{--<option value="2">Delete</option>--}}
                                        {{--<option value="3">Assign Card</option>--}}
                                        <option value="4">Sales History</option>
                                    </select>
                                    @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                                        <div id="user_approval" style="text-align: center">
                                            @if($value->active == 0)
                                                <i class="cross-icon-admin" style="font-size: 2em; cursor:pointer;"
                                                   id="statusSign_{{$value->info->seller_account_id}}"
                                                   onclick="userApproval('1', '{{$value->info->seller_account_id}}',
                                                           'Are you sure you want to activate this user?')"></i>
                                            @else
                                                <i class="check-icon" style="font-size: 2em; color: green; cursor:pointer;"
                                                   id="statusSign_{{$value->info->seller_account_id}}"
                                                   onclick="userApproval('2', '{{$value->info->seller_account_id}}',
                                                           'Are you sure you want to deactivate this user?')"></i>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <input type="button" class="btn btn-activate pull-right" value="Pay" onclick="pay_seller('{{$value->id}}')">
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
        var url = "{{ url('seller-approval') }}";

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
    function user_action(user_id) {
        var option_type = document.getElementById("user_action_" + user_id).value;

        if (option_type == 1) {
            var url = "{{url('/edit-seller')}}" + '/' + user_id;
            window.location = url;
        } else if (option_type == 2) {
            if(confirm('Are you sure?')){
                var url = "{{url('/delete-seller')}}" + '/' + user_id;
                window.location = url;
            }
        } else if (option_type == 3) {
            var url = "{{ url('/assigned-card') }}" + '/' + user_id;
            window.location = url;
        } else if (option_type == 4) {
            var url = "{{ url('/admin/seller_sales_history') }}" + '/' + user_id;
            window.location = url;
        }
    }
</script>
<script>
    function pay_seller(seller_id) {
        if (confirm('Are you sure to pay?')) {
            var url = "{{ url('/pay-seller') }}" + '/' + seller_id;
            window.location = url;
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
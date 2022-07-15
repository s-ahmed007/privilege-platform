@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <h3>Partner Reward Payment</h3>
        </div>
        <div class="title_right">
            <div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">
                <form action="{{ url('admin/rewards/getSinglePartnerForPayment') }}" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="partnerSearchKey">Search Partner</label><br>
                        <input type="text" class="form-control" name="partner" id="partnerSearchKey"
                               placeholder="Partner with name" style="width: 100%;border-radius: 25px">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <table class="table table-bordered table-hover table-striped projects">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Partner</th>
                            <th>Reward Sales</th>
                            <th>Due</th>
                            <th>Paid</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($branches as $key => $branch)
                            <tr>
                                <td>{{ ($branches->currentpage()-1) * $branches->perpage() + $key + 1 }}</td>
                                <td><b>{{ $branch->info->partner_name.', '.$branch->partner_area }}</b></td>
                                <td>
                                    @foreach($branch->rewards as $key => $reward)
                                        {{$key+1}}.
                                        <b>{{ $reward->offer_description }}</b><br>
                                        Cost Price: {{ $reward->actual_price }}<br>
                                        Availed: {{$reward->offer_use_count}}<br>
                                        Total Cost: {{$reward->actual_price * $reward->offer_use_count}}<br><br>
                                    @endforeach
                                </td>
                                <?php
                                    $sales = (new \App\Http\Controllers\Reward\functionController())->branchPayments($branch->id);
                                ?>
                                <td>{{ $sales['due'] }} </td>
                                <td>Last paid:
                                    @if($sales['last_paid_amount'] == 'N/A' && $sales['last_paid'] == 'N/A')
                                        N/A<br>
                                    @else
                                        {{ $sales['last_paid_amount'].' tk' }} on {{ $sales['last_paid'] }}<br>
                                    @endif
                                    Total paid: {{$sales['paid']}}
                                </td>
                                <td>
                                    @if($sales['due'] > 0)
                                        <button class="btn btn-activate pull-right" onclick="payPartnerForReward('{{$branch->id}}')">Pay</button>
                                    @else
                                        <button class="btn btn-activate pull-right" disabled>Pay</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$branches->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- reward payment Modal-->
<div id="rewardPaymentModal" class="modal fade" role="dialog" style="top: 10%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="cross-icon"></i>
                </button>
                <h4 class="modal-title">Payment</h4>
            </div>
            <div class="modal-body" id="profile_modal" class="profile_modal">
                <form class="form-control form-horizontal" id="reward_payment_form" method="post"
                style="display: contents;">
                    {{csrf_field()}}
                    <label for="">Amount</label>
                    <input type="number" name="amount_to_pay" required>
                    <button type="submit" class="btn btn-activate pull-right">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(function () {
        $("#partnerSearchKey").autocomplete({
            source: '{{url('/admin/rewards/partnerWithBranch')}}',
            autoFocus: true,
            delay: 500
        });
    });

    function payPartnerForReward(branch_id) {
        $('#reward_payment_form').attr('action', "{{url('admin/rewards/clear_payment')}}/"+branch_id);
        $("#rewardPaymentModal").modal('toggle');
    }
</script>

@include('admin.production.header')
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
            <h3>Royalty Reward Costing</h3>
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
                            <th>Reward</th>
                            <th>Available</th>
                            <th>Cost</th>
                            <th>Redeemed</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($rewards as $key => $reward)
                            <tr>
                                <td>{{ ($rewards->currentpage()-1) * $rewards->perpage() + $key + 1 }}</td>
                                <td><b>{{ $reward->offer_description }}</b><br></td>
                                <td>
                                    @if($reward->counter_limit)
                                        {{$reward->counter_limit - $reward->rewardRedeems->sum('quantity')}}
                                    @else
                                        Unlimited
                                    @endif
                                </td>
                                <td>{{ $reward->actual_price }}</td>
                                <td>{{ $reward->rewardRedeems->sum('quantity') }} </td>
                                <td>{{ $reward->actual_price * $reward->rewardRedeems->sum('quantity') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$rewards->links()}}
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
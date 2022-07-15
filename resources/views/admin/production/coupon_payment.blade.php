@include('admin.production.header')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('delete branch'))
                <div class="alert alert-danger">
                    {{ session('delete branch') }}
                </div>
            @elseif (session('delete partner'))
                <div class="alert alert-danger">
                    {{ session('delete partner') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @elseif(session('main_branch_deactivate_msg'))
                <div class="alert alert-danger">
                    {{ session('main_branch_deactivate_msg') }}
                </div>
            @endif
            <h3>Payment for Coupon</h3>
        </div>
        <div class="title_left">
            {{--<div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">--}}
            {{--<form action="{{ url('searchPartner') }}" method="post">--}}
            {{--{{csrf_field()}}--}}
            {{--<div class="form-group">--}}
            {{--<label for="partnerSearchKey">Partner:</label><br>--}}
            {{--<input type="text" class="form-control" name="partnerName" id="partnerSearchKey"--}}
            {{--placeholder="Partner with E-mail" style="width: 100%;border-radius: 25px">--}}
            {{--</div>--}}
            {{--</form>--}}
            {{--</div>--}}
            {{--</div>--}}
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12">
                <div class="">
                    <div class="">
                        <table class="table table-striped projects" id="couponPayment">
                            <thead>
                            <tr>
                                <th>Partner</th>
                                <th>Total Amount</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Last Paid</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($paymentInfo))
                                @foreach ($paymentInfo as $key => $value)
                                    <?php
                                    if($value->couponPayment['updated_at'] != null){
                                        $posted_on = date("Y-M-d H:i:s", strtotime($value->couponPayment['updated_at']));
                                        $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                    }
                                    ?>
                                    <tr class="partner-branch-{{$value->id}}">
                                        <td><span style="font-weight: bold;">{{ $value->info->partner_name }}
                                                - {{ $value->partner_area }}</span><br>
                                            {{substr($value->partner_address, 0,50).'...'}}
                                        </td>
                                        <td class="total-amount-{{$value->id}}">{{ $value->couponPayment['total_amount'] }}</td>
                                        <td class="total-paid-{{$value->id}}">{{ $value->couponPayment['paid_amount'] }}</td>
                                        <td class="total-due-{{$value->id}}">{{ $value->couponPayment['total_amount'] - $value->couponPayment['paid_amount'] }}</td>
                                        <td class="last-paid-{{$value->id}}">
                                            {{ $value->couponPayment['updated_at'] != null ? date_format($created, "d-m-y h:i A") : '' }}
                                        </td>
                                        <td>
                                            @if($value->couponPayment['total_amount'] != $value->couponPayment['paid_amount'] )
                                                <button class="btn btn-primary pay-coupon-{{$value->id}}"
                                                    onclick='payCouponModal("{{$value->id}}", "{{$value->info->partner_name}}", "{{$value->partner_area}}",
                                                        "{{$value->partner_address}}", "{{$value->couponPayment['total_amount']}}", "{{$value->couponPayment['paid_amount']}}")'>
                                                    Pay
                                                </button>
                                            @else
                                                <button class="btn btn-activate pull-right" disabled>Pay</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--modal to show branch list of a partner--}}
    <div id="coupon-payment-modal" class="modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="cross-icon"></i>
                    </button>
                    <h4 class="modal-title coupon-payment-modal-title"></h4>
                    <h6 class="modal-title coupon-payment-modal-title2"></h6>
                </div>
                <div class="modal-body">
                    <div class="partner-branches">
                        {{--<form action="{{ url('pay-partner-for-coupon') }}" method="post">--}}
                        <div class="form-group">
                            <label for="amount">Amount:</label>
                            <input type="number" class="form-control" id="payCouponAmount">
                            <input type="hidden" class="form-control" id="totalDue">
                            <input type="hidden" class="form-control" id="paidAmount">
                            <input type="hidden" class="form-control" id="payCouponBranch">
                        </div>
                        <button class="btn btn-success" onclick="payCoupon(document.getElementById('totalDue').value, document.getElementById('paidAmount').value,
                        document.getElementById('payCouponAmount').value, document.getElementById('payCouponBranch').value)">
                            Submit
                        </button>
                        {{--</form>--}}
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif" alt="Royalty Loading GIF"
                             style="display: none;" class="loading-gif">
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--branch list modal ends--}}
</div>

@include('admin.production.footer')
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#couponPayment').DataTable();
    });
</script>
{{-- ============================================================================================================
                     ========================coupon payment====================
============================================================================================================= --}}
<script>
    function payCouponModal(branchId, name, area, address, totalDue, paidAmount) {
        $(".coupon-payment-modal-title").text("Pay " + " " + name + ", " + area);//set modal title
        $(".coupon-payment-modal-title2").text(address);//set modal title
        $("#payCouponAmount").val('');//set amount empty in every modal opening
        $("#totalDue").val(totalDue);//set branch id in hidden field
        $("#paidAmount").val(paidAmount);//set branch id in hidden field
        $("#payCouponBranch").val(branchId);//set branch id in hidden field
        $('#coupon-payment-modal').modal('toggle');//show modal
    }

    function payCoupon(totalDue, prevPaid, paid, branchId) {
        if (paid < 0 || paid === '') {
            alert('Please enter valid amount');
            return false;
        }

        $(".loading-gif").css('display', 'inline-block');
        var url = "{{ url('/pay-partner-for-coupon') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'branch_id': branchId,
                'totalDue': totalDue,
                'prevPaid': prevPaid,
                'paid': paid
            },
            success: function (data) {
                console.log(data);
                if (data['status']) {
                    $(".total-amount-" + data['branch_id']).html(data['due']);
                    $(".total-paid-" + data['branch_id']).html(data['paid']);
                    $(".total-due-" + data['branch_id']).html(data['total_due']);
                    $(".last-paid-" + data['branch_id']).html(data['last_paid']);
                    $(".partner-branch-" + data['branch_id']).css('background-color', 'darkseagreen');
                    $(".loading-gif").css('display', 'none');
                    $('#coupon-payment-modal').modal('toggle');//show modal
                    $(".pay-coupon-" + data['branch_id']).prop("disabled", true);
                } else {
                    alert('what are you doing man!!!');
                }
            }
        });
    }
</script>
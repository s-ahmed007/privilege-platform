@include('admin.production.header')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Payment for Influencer</h3>
        </div>
        <div class="title_left">
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12">
                <div class="">
                    <div class="">
                        <table class="table table-striped projects" id="couponPayment">
                            <thead>
                            <tr>
                                <th>Influencer</th>
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
                                    if($value['updated_at'] != null){
                                        $posted_on = date("Y-M-d H:i:s", strtotime($value['updated_at']));
                                        $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                    }
                                    ?>
                                    <tr class="influencer-{{$value['influencer_id']}}">
                                        <td><span style="font-weight: bold;">{{ $value['influencer_id']}}<br>{{ $value['influencer_name'] }}</span>
                                        </td>
                                        <td class="total-amount-{{$value['influencer_id']}}">{{ $value['total_amount'] }}</td>
                                        <td class="total-paid-{{$value['influencer_id']}}">{{ $value['paid_amount'] }}</td>
                                        <td class="total-due-{{$value['influencer_id']}}">{{ $value['total_amount'] - $value['paid_amount'] }}</td>
                                        <td class="last-paid-{{$value['influencer_id']}}">
                                            {{ $value['updated_at'] != null ? date_format($created, "d-m-y h:i A") : '' }}
                                        </td>
                                        <td>
                                            @if($value['total_amount'] != $value['paid_amount'] )
                                                <button class="btn btn-primary pay-influencer-{{$value['influencer_id']}}"
                                                    onclick='payInfluencerModal("{{$value['influencer_id']}}", "{{$value['influencer_name']}}",
                                                        "{{$value['total_amount']}}", "{{$value['paid_amount']}}")'>
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

    {{--modal to show payment option of influencer--}}
    <div id="influencer-payment-modal" class="modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="cross-icon"></i>
                    </button>
                    <h4 class="modal-title influencer-payment-modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="partner-branches">
                        {{--<form action="{{ url('pay-partner-for-coupon') }}" method="post">--}}
                        <div class="form-group">
                            <label for="amount">Amount:</label>
                            <input type="number" class="form-control" id="payInfluencerAmount">
                            <input type="hidden" class="form-control" id="totalDue">
                            <input type="hidden" class="form-control" id="paidAmount">
                            <input type="hidden" class="form-control" id="payInfluencer">
                        </div>
                        <button class="btn btn-success" onclick="payInfluencer(document.getElementById('totalDue').value,
                            document.getElementById('paidAmount').value, document.getElementById('payInfluencerAmount').value,
                            document.getElementById('payInfluencer').value)">Submit
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
        $('#couponPayment').DataTable({
            "order": []
        });
    });
</script>
{{-- ============================================================================================================
                     ========================coupon payment====================
============================================================================================================= --}}
<script>
    function payInfluencerModal(influencerId, name, totalDue, paidAmount) {
        $(".influencer-payment-modal-title").text("Pay " + " " + name);//set modal title
        $("#payInfluencerAmount").val('');//set amount empty in every modal opening
        $("#totalDue").val(totalDue);//set branch id in hidden field
        $("#paidAmount").val(paidAmount);//set branch id in hidden field
        $("#payInfluencer").val(influencerId);//set branch id in hidden field
        $('#influencer-payment-modal').modal('toggle');//show modal
    }

    function payInfluencer(totalDue, prevPaid, paid, influencerId) {
        if (paid < 0 || paid === '') {
            alert('Please enter valid amount');
            return false;
        }

        $(".loading-gif").css('display', 'inline-block');
        var url = "{{ url('/pay-influencer') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'influencer_id': influencerId,
                'totalDue': totalDue,
                'prevPaid': prevPaid,
                'paid': paid
            },
            success: function (data) {
                if (data['status']) {
                    $(".total-amount-" + data['influencer_id']).html(data['due']);
                    $(".total-paid-" + data['influencer_id']).html(data['paid']);
                    $(".total-due-" + data['influencer_id']).html(data['total_due']);
                    $(".last-paid-" + data['influencer_id']).html(data['last_paid']);
                    $(".influencer-" + data['influencer_id']).css('background-color', 'darkseagreen');
                    $(".loading-gif").css('display', 'none');
                    $('#influencer-payment-modal').modal('toggle');//show modal
                    $(".pay-influencer-" + data['influencer_id']).prop("disabled", true);
                } else {
                    alert('what are you doing man!!!');
                }
            }
        });
    }
</script>
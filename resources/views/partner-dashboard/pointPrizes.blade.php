@include('partner-dashboard.header')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
            <h3>{{__('partner/point_prize.redeem_points')}}</h3>
            <p>{{__('partner/point_prize.redeem_points_for_reward')}}</p>
        </div>
    </div>
    <!-- /.row -->
    <!-- .row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
        <div style="text-align: center;">
        <p>{{__('partner/point_prize.available_reward_points')}}<span class="scanner_point badge badge-success">
        <b>{{$scanner->point}}</b></span>
    </p>
                <a href="{{url('partner/branch/scanner_prize_history')}}" class="btn btn-success text-white">{{__('partner/point_prize.point_balance_history')}}</a>
            </div>
            <ul style="list-style-type: none;">
                @foreach($prizes as $prize)
                    <li>
                        <!-- <a href="#" data-toggle="modal" data-target="#prizeRequestModal"> -->
                        <a href="#" onclick="sendRequestCheck('{{$prize}}')">
                            <div class="price-card-box row">
                                <div class="price-card-box-line col-md-10 col-sm-10 col-xs-10">
                                    <div class="price-card-box-type">
                                        <b>{{$prize->text}}</b>
                                    </div>
                                    <div class="price-card-box-price">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-2">
                                    <div class="btn btn-success" style="float:right;">
                                        {{$prize->point}} Points >
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <!-- /.row -->

<div id="prizeRequestModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
                <i class="fa fa-times" style="color: #000 !important;"></i>
            </button>
                <h4 class="modal-title">Redeem Reward</h4>
            </div>
            <div class="modal-body">
                <p class="prize_text" style="text-align: center; font-weight: bold;"></p>
                <ul style="list-style-type: none;padding-left: unset;">
                    <li style="border-bottom: unset">
                        <div class="price-card-box row">
                            <div class="price-card-box-line col-md-10 col-sm-10 col-xs-10">
                                <div class="price-card-box-type">
                                    <p style="margin: unset;">Available Reward Points </p>
                                </div>
                                <div class="price-card-box-price">
                                    <span class="available_point" style="float: right;"></span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="price-card-box row" style="border-bottom: 1px solid #e4e6f2;">
                            <div class="price-card-box-line col-md-10 col-sm-10 col-xs-10">
                                <div class="price-card-box-type">
                                    <p class="prize_text" style="margin: unset;"></p>
                                </div>
                                <div class="price-card-box-price">
                                    <span class="prize_point" style="float: right;"></span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="price-card-box row">
                            <div class="price-card-box-line col-md-10 col-sm-10 col-xs-10">
                                <div class="price-card-box-type">
                                    <p class="remaining_point_text" style="margin: unset;"></p>
                                </div>
                                <div class="price-card-box-price">
                                    <span class="remaining_point" style="float: right;"></span>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <form style="display: table;" class="form-control prize_request_form" method="post" onsubmit="return confirm('Are you sure?');">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="">Special Request(Optional):</label>
                        <input type="text" class="form-control" id=""placeholder="Type any request here. Ex: Send recharge or Bkash to 01xxxxxxxxx" name="comment">
                    </div>
                    <input type="hidden" name="prize" id="prize_id">
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary reward_redeem_btn" style="padding: 12px 30px; font-size: 20px">
                            Send Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


</div>
<!-- /.container-fluid -->


@include('partner-dashboard.footer')
<script type="text/javascript">
    function sendRequestCheck(prize) {
        var total_point = $(".scanner_point").text();
        prize = JSON.parse(prize);
        // console.log(prize.point);
        $(".prize_text").text(prize.text);
        $(".available_point").text(total_point);
        $(".prize_point").text('- '+prize.point);

        if(total_point < prize.point){
            $(".remaining_point_text").text("Additional Points Required").css('color', 'red');
            $(".remaining_point").text(prize.point - total_point).css('color', 'red');
            $(".reward_redeem_btn").prop("disabled", true);
        }else{
            $(".remaining_point_text").text("Remaining Points").css('color', 'green');
            $(".remaining_point").text(total_point - prize.point).css('color', 'green');
            $("#prize_id").val(prize.id);
            var url = '{{url("partner/branch/request_scan_prize/")}}';
            $(".prize_request_form").prop('action', url);
        }
        $("#prizeRequestModal").modal('show');
    }
</script>
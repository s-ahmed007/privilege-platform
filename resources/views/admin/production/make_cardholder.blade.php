@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>{{$upgrade_mem == true ? 'Upgrade':'Renew'}} Membership</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        @if (isset($profileInfo))
            <form action="{{ url('admin/upgradeMembershipDone/'. $profileInfo->customer_id) }}" class="form-horizontal"
                method="post">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="customer_id">Customer Name:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('customer_name'))
                            {{ $errors->getBag('default')->first('customer_name') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="text" name="customer_name" class="form-control" id="customer_name" readonly
                           value="{{ $profileInfo->customer_full_name }}" >
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="customer_id">Customer Id:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('customer_id'))
                            {{ $errors->getBag('default')->first('customer_id') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="text" name="customer_id" class="form-control" id="customer_id" readonly
                           value="{{ $profileInfo->customer_id }}" pattern="[0-9]{16}" maxlength="16" minlength="16">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="mem_plan">Membership Plan:</label>
                    <span style="color: red;">
                    @if ($errors->getBag('default')->first('mem_plan'))
                            {{ $errors->getBag('default')->first('mem_plan') }}
                    @endif
                    </span>
                    <div class="col-sm-10">
                        <select name="mem_plan" id="mem_plan" class="form-control" onchange="calculatePrice()">
                            <option selected disabled>Membership Plan</option>
                            @foreach($mem_plans as $plan)
                                <option value="{{$plan->month.'-'.$plan->price}}">
                                    {{$plan->month}}{{$plan->month > 1 ? ' Months':' Month'}} (৳{{$plan->price}})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group" style="display: none;" id="discount_box">
                    <label class="control-label col-sm-2" for="discount">Discount:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('discount'))
                            {{ $errors->getBag('default')->first('discount') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="discount" id="discount" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="final_price">Total Price:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('price'))
                            {{ $errors->getBag('default')->first('price') }}
                        @endif
                    </span>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="price" id="final_price" placeholder="Final Price" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="promo_code">Promo Code:</label>
                    <span style="color: red;">
                        @if ($errors->getBag('default')->first('promo_code'))
                            {{ $errors->getBag('default')->first('promo_code') }}
                        @endif
                        <span class="error_card_promo"></span>
                    </span>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="promo_code" id="promo_code" placeholder="Promo Code (Optional)">
                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" value="{{$mem_type}}" id="mem_type" name="mem_change">
                <input type="hidden" value="{{$delivery_type}}" name="delivery_type">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-activate pull-right" id="submit_btn">{{$upgrade_mem == true ? 'Upgrade':'Renew'}} Membership</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

@include('admin.production.footer')

<script>
    function calculatePrice() {
        billSummery();
    }

    function billSummery(){
        var plan = $("#mem_plan").val();
        plan = plan.split('-');
        $("#final_price").val(plan[1]);
        var promo = $('#promo_code').val();
        if(promo != ''){
            checkPromo();
        }
    }

    $('#promo_code').keyup(delay(function (e) {
        checkPromo();
    }, 1000));

    function checkPromo() {
        var promo = $('#promo_code').val();
        var plan = $('select[name=mem_plan]').val();
        var mem_type = $("#mem_type").val();
        if(plan === null){
            alert('Please choose your plan');
            return false;
        } else{
            var card_month_price = plan.split('-');
            card_price = Number(card_month_price[1]);

            if (promo !== '') {
                var url = "{{ url('/checkCardPromoCode') }}";
                return $.ajax({
                    type: "POST",
                    url: url,
                    data: {'_token': '<?php echo csrf_token(); ?>',
                        'card_promo': promo,
                        'card_price': card_price,
                        'month': Number(card_month_price[0]),
                        'mem_type': mem_type},
                    statusCode: {
                        200: function(data) {
                            $("#discount_box").css('display', 'block');
                            $("#discount").val('-'+data.promo_price);
                            $("#final_price").val(data.price);
                            $(".error_card_promo").empty();
                            $("#submit_btn").prop('disabled', false);
                        },
                        201: function(data) {
                            $(".error_card_promo").html(data.result);
                            $("#submit_btn").prop('disabled', true);
                            return false;
                        }
                    }
                });
            }else{
                var price = Number(plan.split('-')[1]);
                $("#final_price").val(price);
                $(".error_card_promo").empty();
                $("#submit_btn").prop('disabled', false);
            }
        }
    }

    //call function after specific time
    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }
</script>
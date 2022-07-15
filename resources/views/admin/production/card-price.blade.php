@include('admin.production.header')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Extra Charges</h3>
        </div>
        <div class="title_right">
            @if (Session::has('price_added'))
                <div class="alert alert-success" style="text-align: center">{{ session('price_added') }}</div>
            @elseif (Session::has('price_add_error'))
                <div class="alert alert-warning" style="text-align: center">{{ session('price_add_error') }}</div>
            @elseif (Session::has('updated'))
                <div class="alert alert-success" style="text-align: center">{{ session('updated') }}</div>
            @endif
        </div>
    </div>
<?php
    $delivery_charge = $card_prices->where('type', 'delivery_charge')->first()->price;
    $refer_bonus = $card_prices->where('type', 'refer_bonus')->first()->price;
    $per_card_scan = $card_prices->where('type', 'per_card_scan')->first()->price;
    $per_card_sell = $card_prices->where('type', 'per_card_sell')->first()->price;
    $min_card_sell_redeem = $card_prices->where('type', 'min_card_sell_redeem')->first()->price;
    $rating = $card_prices->where('type', 'rating')->first()->price;
    $review = $card_prices->where('type', 'review')->first()->price;
    $daily_point_limit = $card_prices->where('type', 'daily_point_limit')->first()->price;
?>
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <form class="form-horizontal form-label-left" method="post" action="{{ url('change_other_prices') }}">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('delivery_charge'))
                                        {{ $errors->getBag('default')->first('delivery_charge') }}
                                    @else
                                        <br>
                                    @endif
                                </span>
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Delivery Charge:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" name="delivery_charge" value="{{$delivery_charge}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('refer_bonus'))
                                        {{ $errors->getBag('default')->first('refer_bonus') }}
                                    @else
                                        <br>
                                    @endif
                                </span>
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Refer Credit:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" name="refer_bonus" value="{{$refer_bonus}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('per_card_scan'))
                                        {{ $errors->getBag('default')->first('per_card_scan') }}
                                    @else
                                        <br>
                                    @endif
                                </span>
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Scanner Per Scan Point:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" name="per_card_scan" value="{{$per_card_scan}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('per_card_sell'))
                                        {{ $errors->getBag('default')->first('per_card_sell') }}
                                    @else
                                        <br>
                                    @endif
                                </span>
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Per Membership Sell:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" name="per_card_sell" value="{{$per_card_sell}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('min_card_sell_redeem'))
                                        {{ $errors->getBag('default')->first('min_card_sell_redeem') }}
                                    @else
                                        <br>
                                    @endif
                                </span>
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Min Membership Sell Redeem:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" name="min_card_sell_redeem" value="{{$min_card_sell_redeem}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('rating'))
                                        {{ $errors->getBag('default')->first('rating') }}
                                    @else
                                        <br>
                                    @endif
                                </span>
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Rating:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" name="rating" value="{{$rating}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('review'))
                                        {{ $errors->getBag('default')->first('review') }}
                                    @else
                                        <br>
                                    @endif
                                </span>
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Review:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" name="review" value="{{$review}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('daily_point_limit'))
                                        {{ $errors->getBag('default')->first('daily_point_limit') }}
                                    @else
                                        <br>
                                    @endif
                                </span>
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Daily Point Limit:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" name="daily_point_limit" value="{{$daily_point_limit}}">
                                </div>
                            </div>
                        </div>
{{--                        <div class="col-sm-4">--}}
{{--                            <div class="form-group">--}}
{{--                                <span style="color: red;">--}}
{{--                                    @if ($errors->getBag('default')->first('customization_charge'))--}}
{{--                                        {{ $errors->getBag('default')->first('customization_charge') }}--}}
{{--                                    @else--}}
{{--                                        <br>--}}
{{--                                    @endif--}}
{{--                                </span>--}}
{{--                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Customization Charge</label>--}}
{{--                                <div class="col-md-6 col-sm-6 col-xs-12">--}}
{{--                                    <input type="text" class="form-control" name="customization_charge" value="{{$card_prices[4]->price}}">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-4">--}}
{{--                            <div class="form-group">--}}
{{--                                <span style="color: red;">--}}
{{--                                    @if ($errors->getBag('default')->first('lost_card_charge'))--}}
{{--                                        {{ $errors->getBag('default')->first('lost_card_charge') }}--}}
{{--                                    @else--}}
{{--                                        <br>--}}
{{--                                    @endif--}}
{{--                                </span>--}}
{{--                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Lost Card Charge:</label>--}}
{{--                                <div class="col-md-6 col-sm-6 col-xs-12">--}}
{{--                                    <input type="text" class="form-control" name="lost_card_charge" value="{{$card_prices[5]->price}}">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="ln_solid"></div>
                    <div class="form-group">
                            <button type="submit" class="btn btn-activate pull-right">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')
<!-- javascript for deleting price item -->
{{--<script>--}}
   {{--    $('.deleteBtn').on('click', function (event) {--}}
   {{--        if (confirm("Are you sure?")) {--}}
   {{--            //fetch the item id--}}
   {{--            var itemId = $(this).attr('data-price-id');--}}
   {{--            var url = "{{ url('/delete-price-item') }}";--}}
   {{--            url += '/' + itemId;--}}
   {{--            window.location.href = url;--}}
   {{--        }--}}
   {{--    });--}}
   {{--
</script>--}}
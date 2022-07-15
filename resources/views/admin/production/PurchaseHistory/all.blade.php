@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Purchase History {{$tab_title != '' ? '('.$tab_title.')': ''}}</h3>
            <a class="btn btn-all" href="{{url('purchase/history/all')}}">All</a>
            <a class="btn btn-guest" href="{{url('purchase/history/new')}}">New</a>
            <a class="btn btn-premium" href="{{url('purchase/history/upgraded')}}">Upgraded</a>
            <a class="btn btn-premium" href="{{url('purchase/history/renewed')}}">Renewed</a>
            <a class="btn btn-expired" href="{{url('purchase/history/expired')}}">Expired</a>
        </div>
        <div class="title_right">
            <div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">
                <form action="{{ url('purchase/history/customerByKey') }}" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="customerSearchKey">Search Confirmed Members</label><br>
                        <input type="text" class="form-control" name="customerSearchKey" id="customerSearchKey"
                               placeholder="Customer with name, E-mail or phone" style="width: 100%;">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($data)
                        <table class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Customer Information</th>
                                <th>Transaction Information</th>
                                <th>Purchase Type</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $key => $item)
                                <tr>
                                    <td>{{ ($data->currentpage()-1) * $data->perpage() + $key + 1 }}</td>
                                    <td>
                                        <b>Card No: </b> {{$item->customerInfo->customer_id}} <br>
                                        <b>Name: </b> {{$item->customerInfo->customer_full_name}} <br>
                                        <b>Mobile: </b> {{$item->customerInfo->customer_contact_number}} <br>
                                        <b>Email: </b> {{$item->customerInfo->customer_email}} <br>
                                    </td>
                                    <td>
                                        <?php
                                        $posted_on = date("Y-M-d H:i:s", strtotime($item->sslInfo->tran_date));
                                        $tran_date = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                        ?>
                                        <b>Order Date: </b> {{ date_format($tran_date, "d-m-y &#9202; h:i A") }} <br>
                                        <b>Transaction ID: </b> {{ $item->sslInfo->tran_id}} <br>
                                        @if($item->sslInfo->platform == \App\Http\Controllers\Enum\PlatformType::web ||
                                            $item->sslInfo->platform == \App\Http\Controllers\Enum\PlatformType::rbd_admin)
                                            <b>Via:</b>  Website<br>
                                        @elseif($item->sslInfo->platform == \App\Http\Controllers\Enum\PlatformType::android)
                                            <b>Via:</b> Android<br>

                                        @elseif($item->sslInfo->platform == \App\Http\Controllers\Enum\PlatformType::ios)
                                            <b>Via:</b> iOS<br>

                                        @elseif($item->sslInfo->platform == \App\Http\Controllers\Enum\PlatformType::sales_app)
                                            <b>Via:</b> Sales App<br>
                                        @else
                                            <b>Via:</b>N/A<br>
                                        @endif

                                        @if($item->sslInfo->cardDelivery == null)
                                            <b>Payment Type: </b> N/A <br>
                                        @elseif($item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::home_delivery
                                                || $item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::renew
                                                 || $item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::lost_card_without_customization
                                                 || $item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::lost_card_with_customization
                                                 || $item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::card_customization)
                                            <b>Payment Type: </b> Online Pay<br>
                                        @elseif($item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::spot_delivery)
                                            <b>Payment Type: </b> Spot Purchase<br>
                                        @elseif($item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::made_by_admin)
                                            <b>Payment Type: </b> Admin<br>
                                        @elseif($item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::cod
                                                ||$item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::office_pickup
                                                ||$item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::influencer_delivery)
                                            <b>Payment Type: </b> Cash on Delivery<br>
                                        @endif
                                        @if($item->sslInfo->promoUsage)
                                                <b>Paid Amount: </b> {{$item->sslInfo->amount}} (<b>Promo Applied: </b>{{$item->sslInfo->promoUsage->promoCode->code}}) <br>
                                        @else
                                                <b>Paid Amount: </b> {{$item->sslInfo->amount}} <br>
                                        @endif

                                    </td>
                                    <td>
                                        <b>Duration: </b> {{$item->sslInfo->month}} month(s)<br>
                                        @if($item->sellerInfo)
                                            <b>Seller Name: </b> {{$item->sellerInfo->first_name}} {{$item->sellerInfo->last_name}} <br>
                                        @endif
                                        @if($item->sslInfo->cardDelivery == null)
                                            N/A
                                        @elseif($item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::home_delivery
                                                                                        && $item->customerInfo->isUpgrade())
                                            <span class="upgrade-label">Upgrade</span><br>
                                        @elseif($item->sslInfo->cardDelivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::renew )
                                            <span class="renew-label">Renew</span><br>
                                        @endif
                                        @if((new \App\Http\Controllers\functionController2())->daysRemaining(date('Y-m-d', strtotime
                                        ($item->sslInfo->tran_date . ' + '. $item->sslInfo->month.' months')))<=0)
                                            <span class="manual-label">Purchase Expired</span><br>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                            </tr>
                            </tfoot>
                        </table>
                        {{$data->links()}}
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No customers found.' }}
                        </div>
                    @endif
                </div>
                <input type="hidden" id="previous_delivery_type" name="previous_delivery_type" value="0"/>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(function () {
        $("#customerSearchKey").autocomplete({
            source: '{{url('/customerByKey')}}',
            autoFocus: true,
            delay: 500
        });
    });
</script>
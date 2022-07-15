@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Free trial users</h3>
        </div>
        <div class="title_right">
            <div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">
                <form action="{{ url('freeTrialUserById') }}" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="customerSearchKey">Search Members</label><br>
                        <input type="text" class="form-control" name="customerSearchKey" id="customerSearchKey"
                           placeholder="Customer with name, E-mail or phone" style="width: 100%;">
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
                    @if($card_delivery_list)
                        <table id="freeUsersList" class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Customer Information</th>
                                <th>Transaction Information</th>
                                <th>Delivery Details</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($card_delivery_list as $customerInfo)
                                <tr>
                                    <th>{{ $customerInfo->serial }}</th>
                                    <td>Card No: <b style="color: #007bff;">{{ $customerInfo->cid }}</b> <br>
                                        Name: {{ $customerInfo->customer_full_name }}
                                        <br> Mobile: {{ $customerInfo->customer_contact_number }} <br>
                                        Email: {{ $customerInfo->customer_email }}
                                    </td>
                                    <td>
                                        @if ($customerInfo->customer_type==2)
                                            <p class="card-type-premium">Premium Member</p>
                                        @endif
                                        <p>Free trial activated: <b>{{ date("F d, Y", strtotime($customerInfo->tran_date))}}</b></p>
                                        <p>Via: <b>
                                            @if($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::web)
                                                Website
                                            @elseif($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::android)
                                                Android
                                            @elseif($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::ios)
                                                IOS
                                            @elseif($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::sales_app)
                                                Sales App
                                            @else
                                                N/A
                                            @endif
                                        </b></p>
                                    </td>
                                    <td>
                                        @if($customerInfo->delivery_type==2)
                                            <span>Office Pickup</span>
                                        @else
                                            <select class="form-control" id="delivery_type_{{$customerInfo->cid}}"
                                                    onfocus="previous_delivery_type({{ $customerInfo->cid }})"
                                                    onchange="update_delivery_type({{ $customerInfo->cid }})">
                                                <option <?php if ($customerInfo->delivery_type == '') echo 'selected disabled';?>>
                                                    -----
                                                </option>
                                                <option value="1" <?php if ($customerInfo->delivery_type == 1) echo 'selected';?>>
                                                    Online Pay
                                                </option>
                                                <option value="4" <?php if ($customerInfo->delivery_type == 4) echo 'selected';?>>
                                                    COD
                                                </option>
                                                <option value="5" <?php if ($customerInfo->delivery_type == 5) echo 'selected';?>>
                                                    Customization
                                                </option>
                                                <option value="6" <?php if ($customerInfo->delivery_type == 6) echo 'selected';?>>
                                                    COD (Lost-card)
                                                </option>
                                                <option value="7" <?php if ($customerInfo->delivery_type == 7) echo 'selected';?>>
                                                    Customization (Lost-card)
                                                </option>
                                                <option value="3" <?php if ($customerInfo->delivery_type == 3) echo 'selected';?>>
                                                    Pre-Order COD
                                                </option>
                                                <option value="9" <?php if ($customerInfo->delivery_type == 9) echo 'selected';?>>
                                                    Spot Delivery
                                                </option>
                                                <option value="10" <?php if ($customerInfo->delivery_type == 10) echo 'selected';?>>
                                                    Influencer
                                                </option>
                                                <option value="11" <?php if ($customerInfo->delivery_type == 11) echo 'selected';?>>
                                                    Trial
                                                </option>
                                                <option value="12" <?php if ($customerInfo->delivery_type == 12) echo 'selected';?>>
                                                    Renew
                                                </option>
                                            </select>
                                        @endif
                                        <p align="middle" style="margin-top: 10px;">
                                            <b>Duration : {{ $customerInfo->month == 1 ? $customerInfo->month.' month' : $customerInfo->month.' months'}}</b>
                                        </p>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$card_delivery_list->links()}}
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
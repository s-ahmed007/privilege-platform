@include('b2b2c.layout.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('delete customer'))
                <div class="alert alert-danger">
                    {{ session('delete customer') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @elseif(session('codPaymentClear'))
                <div class="alert alert-success">
                    {{ session('codPaymentClear') }}
                </div>
            @elseif(session('cardActivated'))
                <div class="alert alert-success">
                    {{ session('cardActivated') }}
                </div>
            @else

            @endif
            <h3>Card Delivery Information</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                @if($card_delivery_list)
                    <!-- start project list -->
                        <table id="deliveryList" class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>Customer Information</th>
                                <th>Order Information</th>
                                <th>Shipping Address</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($card_delivery_list as $customerInfo)
                                <tr <?php if ($customerInfo->delivery_type == 5 || $customerInfo->delivery_type == 7)
                                    echo "style='background-color: #ffedc9; background-image: linear-gradient(to top right, #ffd3c9, #c9f7ff, #cdc9ff);'";
                                    ?>>
                                    <td>Card No: <b style="color: #007bff;">{{ $customerInfo->customer_id }}</b> <br>
                                        Name: {{ $customerInfo->customer_full_name }}
                                        <br> Mobile: {{ $customerInfo->customer_contact_number }} <br>
                                        E-mail: {{ $customerInfo->customer_email }}
                                    </td>
                                    <td>
                                    <p>Order date: <b>{{ date("d-m-Y", strtotime($customerInfo->member_since)) }}</b></p>
                                     <div class="col-md-7" style="padding: 0px">
                                        <select class="form-control" id="status_block_{{$customerInfo->customer_id}}"
                                                style="font-weight: bold"
                                                onchange="change_delivery_status({{ $customerInfo->customer_id }})">
                                            <option value="1"
                                                    <?php if ($customerInfo->delivery_status == 1) echo 'selected';?> onchange="change_delivery_status(1, {{ $customerInfo->customer_id }})">
                                                Not Delivered
                                            </option>
                                            <option value="2"
                                                    <?php if ($customerInfo->delivery_status == 2) echo 'selected';?> onchange="change_delivery_status(2, {{ $customerInfo->customer_id }})">
                                                Delivering
                                            </option>
                                            <option value="3"
                                                    <?php if ($customerInfo->delivery_status == 3) echo 'selected';?> onchange="change_delivery_status(3, {{ $customerInfo->customer_id }})">
                                                Delivered
                                            </option>
                                        </select>
                                     </div>
                                    </td>
                                    <td>
                                		<textarea id="shipping_updated_{{$customerInfo->customer_id}}" rows="3" style="width: 100%"
                                            placeholder="Add Shipping Address" onfocusout="shipping_update({{ $customerInfo->customer_id }})">
                                            {{ $customerInfo->shipping_address }}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No card delivery found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#deliveryList').DataTable({
            //"paging": false
            "order": []
        });
    });

    //JAVASCRIPT to update shipping address
    function shipping_update(customer_id) {
        var updated_shipping = document.getElementById("shipping_updated_" + customer_id).value;
        var url = "{{ url('/client/b2b2c_shipping_address') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'updated_shipping': updated_shipping,
                'customer_id': customer_id
            },
            success: function (data) {
                console.log(data);
            }
        });
    }

    //JAVASCRIPT to change delivery status
    function change_delivery_status(customer_id) {
        var current_status = document.getElementById("status_block_" + customer_id).value;
        var url = "{{ url('/client/b2b2c_delivery_status') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'current_status': current_status,
                'customer_id': customer_id
            },
            success: function (data) {
                console.log(data);
            }
        });
    }
</script>

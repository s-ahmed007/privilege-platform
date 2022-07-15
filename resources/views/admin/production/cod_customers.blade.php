@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

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
            @elseif(session('info updated'))
                <div class="alert alert-success">
                    {{ session('info updated') }}
                </div>
            @elseif(session('codPaymentFailed'))
                <div class="alert alert-danger">
                    {{ session('codPaymentFailed') }}
                </div>
            @elseif(session('codPaymentCancelled'))
                <div class="alert alert-warning">
                    {{ session('codPaymentCancelled') }}
                </div>
            @elseif(session('cod_approval_error'))
                <div class="alert alert-warning">
                    {{ session('cod_approval_error') }}
                </div>
            @endif
            <h3>Cash On Delivery Members</h3>
        </div>

        <div class="title_right">
            <div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">
                <form action="{{ url('cod-customer') }}" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="customerSearchKey">Search COD Customer</label><br>
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
                    @if($profileInfo)
                        <table class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th style="width: 10%">Image</th>
                                <th style="width: 10%">Customer ID</th>
                                <th style="width: 10%">Customer Info</th>
                                <th style="width: 10%">Card Ordered</th>
                                <th style="width: 18%">Shipping Address</th>
                                <th style="width: 10%">Order Details</th>
                                <th style="width: 22%"><p align="center" style="margin:unset">ACTION</p></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($profileInfo as $customerInfo)
                                <tr>
                                    <th>{{ $customerInfo->serial }}</th>
                                    <td><img src="{{ $customerInfo->customer_profile_image }}" width="100%"
                                             style="border-radius: 50%"></td>
                                    <td>{{ $customerInfo->customer_id }} <br>
                                        @if($customerInfo->delivery_type == 6 || $customerInfo->delivery_type == 7)
                                            <p class="center"><span class="label label-danger">Card Lost</span></p>
                                        @endif
                                    </td>
                                    <td>
                                        <b>{{ $customerInfo->customer_full_name }}</b>
                                        <br>{{ $customerInfo->customer_contact_number }}
                                        <br>{{ $customerInfo->customer_email }}
                                    </td>
                                    <td> @if($customerInfo->customer_type==1)
                                            <p class="card-type-gold">Gold</p>
                                        @elseif ($customerInfo->customer_type==2)
                                            <p class="card-type-premium">Premium Member</p>
                                        @else
                                            <p class="card-type-guest">Guest</p>
                                        @endif
                                    </td>
                                    <td>{{ $customerInfo->shipping_address }}</td>
                                    <td><?php echo $customerInfo->order_date != null ? $customerInfo->order_date : $customerInfo->member_since; ?>
                                        <h4>
                                            <span class="label btn-activate">৳{{ intval($customerInfo->total_payable) }}</span>
                                        </h4>
                                    </td>
                                    @if($customerInfo->moderator_status==1)
                                        <td align="middle">
                                            <a class="btn btn-done" href="{{url('/edit-cod-user/'.$customerInfo->customer_id)}}">Edited</a>
                                    @else
                                        <td align="middle">
                                        <a class="btn btn-primary" href="{{url('/edit-cod-user/'.$customerInfo->customer_id)}}">Edit</a>
                                    @endif

                                    @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                                        <a href="{{url('/decline-cod/'.$customerInfo->customer_id)}}">
                                            <button class="btn btn-delete" onclick="return confirm('Are you sure?');">Delete</button>
                                        </a>
                                        @if($customerInfo->moderator_status==1 && ($customerInfo->delivery_type == 4 || $customerInfo->delivery_type == 6 || $customerInfo->delivery_type == 7))
                                            <a href="{{url('/approve-cod/'.$customerInfo->customer_id)}}">
                                                <button class="btn btn-activate pull-right" onclick="return confirm('Are you sure?');">Approve</button>
                                            </a>
                                        @endif
                                    @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $profileInfo->links() }}
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No customers found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(function () {
        $("#customerSearchKey").autocomplete({
            source: '{{url('/customerByCod')}}',
            autoFocus: true,
            delay: 500
        });
    });
</script>
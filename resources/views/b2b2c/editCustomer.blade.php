@include('b2b2c.layout.header')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Edit Customer</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('client/update-customer/'.$customer_info->customer_id) }}"
                          enctype="multipart/form-data">

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Name</label>
                            <span style="color: red;">
                                 @if ($errors->getBag('default')->first('first_name'))
                                    {{ $errors->getBag('default')->first('first_name') }}
                                @endif
                            </span>
                            <div class="col-md-4 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="First Name" name="first_name"
                                       value="{{$customer_info->customerInfo->customer_first_name}}">
                            </div>
                            <span style="color: red;">
                                 @if ($errors->getBag('default')->first('last_name'))
                                    {{ $errors->getBag('default')->first('last_name') }}
                                @endif
                            </span>
                            <div class="col-md-5 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Last Name" name="last_name"
                                       value="{{$customer_info->customerInfo->customer_last_name}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                            <span style="color: red;">
                                 @if ($errors->getBag('default')->first('email'))
                                    {{ $errors->getBag('default')->first('email') }}
                                @endif
                            </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Email" name="email" value="{{$customer_info->customerInfo->customer_email}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone</label>
                            <span style="color: red;">
                                 @if ($errors->getBag('default')->first('phone'))
                                    {{ $errors->getBag('default')->first('phone') }}
                                @endif
                            </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Phone Number" name="phone"
                                       value="{{$customer_info->customerInfo->customer_contact_number}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Card</label>
                            <span style="color: red;">
                                 @if ($errors->getBag('default')->first('customer_id'))
                                    {{ $errors->getBag('default')->first('customer_id') }}
                                @endif
                            </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="16 Digit Card Number" name="customer_id"
                                       maxlength="16" value="{{$customer_info->customerInfo->customer_id}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Shipping Address</label>
                            <span style="color: red;">
                                 @if ($errors->getBag('default')->first('shipping_address'))
                                    {{ $errors->getBag('default')->first('shipping_address') }}
                                @endif
                            </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Shipping Address" name="shipping_address"
                                       value="{{$customer_info->customerInfo->cardDelivery->shipping_address}}">
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-activate pull-right">Submit</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
</div>
@include('b2b2c.layout.footer')
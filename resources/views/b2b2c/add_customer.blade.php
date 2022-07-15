@include('b2b2c.layout.header')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Add Customer</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('client/store-customer') }}"
                          enctype="multipart/form-data">

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Name</label>
                            <span style="color: red;">
                                 @if ($errors->getBag('default')->first('first_name'))
                                    {{ $errors->getBag('default')->first('first_name') }}
                                @endif
                            </span>
                            <div class="col-md-4 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="First Name" name="first_name" value="{{old('first_name')}}">
                            </div>
                            <span style="color: red;">
                                 @if ($errors->getBag('default')->first('last_name'))
                                    {{ $errors->getBag('default')->first('last_name') }}
                                @endif
                            </span>
                            <div class="col-md-5 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Last Name" name="last_name" value="{{old('last_name')}}">
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
                                <input type="text" class="form-control" placeholder="Email" name="email" value="{{old('email')}}">
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
                                       maxlength="14" minlength="14" value="+88">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Password</label>
                            <span style="color: red;">
                                 @if ($errors->getBag('default')->first('password'))
                                    {{ $errors->getBag('default')->first('password') }}
                                @endif
                            </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <div class="pass-req">
                                    <p>Must contain: &#x2022;&nbsp;A number &#x2022;&nbsp;An uppercase and lowercase letter
                                        &#x2022;&nbsp;Min 8 characters
                                    </p>
                                </div>
                                <div class="col-md-11" style="padding: unset">
                                <input type="password" class="form-control" placeholder="Password"
                                       name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" id="reg-pass">
                                </div>
                                <div class="col-md-1">
                                <span toggle="#reg-pass" class="eye-close field-icon toggle-password"></span>
                                </div>
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
                                       maxlength="16" value="{{old('customer_id')}}">
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
                                       value="{{old('shipping_address')}}">
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
<script type="text/javascript">
    $(".toggle-password").click(function () {

        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
</script>
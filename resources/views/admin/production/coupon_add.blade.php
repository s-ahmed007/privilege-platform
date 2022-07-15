@include('admin.production.header')

<div class="right_col" role="main">
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add Coupon Information</h2>
                <div class="clearfix"></div>
                @if(Session::has('coupon added'))
                    <div class="alert alert-success">{{Session::get('coupon added')}}</div>
                @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
                @endif
            </div>
            <div class="x_content">
                <br/>
                <form class="form-horizontal form-label-left" method="post" action="{{ url('addCoupon') }}"
                      enctype="multipart/form-data">

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Partner Name</label>
                        <span style="color: red;">
                         @if ($errors->getBag('default')->first('partner_id'))
                                {{ $errors->getBag('default')->first('partner_id') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <select name="partner_id" class="form-control" id="select_id" onchange="partner_exists()">
                                <option selected disabled="">Select partner</option>
                                @foreach($allPartners as $partner)
                                    <option value="{{$partner['partner_account_id']}}">{{$partner['partner_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Coupon Type</label>
                        <span style="color: red;">
                         @if ($errors->getBag('default')->first('coupon_type'))
                                {{ $errors->getBag('default')->first('coupon_type') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" placeholder="1/3" name="coupon_type" value="{{old('coupon_type')}}">
                            <p>1=coupon, 3=birthday coupon</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Reward text</label>
                        <span style="color: red;">
                         @if ($errors->getBag('default')->first('reward_text'))
                                {{ $errors->getBag('default')->first('reward_text') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" placeholder="Do not put more than 5 words" name="reward_text" value="{{old('reward_text')}}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">No. of coupons</label>
                        <span style="color: red;">
                         @if ($errors->getBag('default')->first('coupon_count'))
                                {{ $errors->getBag('default')->first('coupon_count') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control"
                                   placeholder="Enter the amounts fo coupon you want to add" name="coupon_count" value="{{old('coupon_count')}}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Coupon Details</label>
                        <span style="color: red;">
                         @if ($errors->getBag('default')->first('coupon_details'))
                                {{ $errors->getBag('default')->first('coupon_details') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" placeholder="Do not put more than 5 words" name="coupon_details" value="{{old('coupon_details')}}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Coupon T&C</label>
                        <span style="color: red;">
                         @if ($errors->getBag('default')->first('coupon_tnc'))
                                {{ $errors->getBag('default')->first('coupon_tnc') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <textarea name="coupon_tnc" class="form-control" placeholder="Use less than 150 characters">{{old('coupon_tnc')}}</textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Expiry</label>
                        <span style="color: red;">
                         @if ($errors->getBag('default')->first('exp_date'))
                                {{ $errors->getBag('default')->first('exp_date') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="date" class="form-control"
                                   placeholder="yyyy-mm-dd" name="exp_date">
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
<!-- /page content -->
@include('admin.production.footer')


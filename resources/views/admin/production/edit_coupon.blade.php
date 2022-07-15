@include('admin.production.header')


<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Edit Coupon</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body col-sm-10">
        @if (isset($coupon_info))

            <form class="form-horizontal form-label-left" method="post" action="{{ url('edit_coupon_done/') }}"
                  enctype="multipart/form-data">

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Partner Name</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" class="form-control" value="{{ $partner_name[0]['partner_name'] }}" readonly>
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
                        <input type="text" class="form-control" value="{{ $coupon_info[0]['coupon_type'] }}"
                               placeholder="1/3" name="coupon_type">
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
                        <input type="text" class="form-control" value="{{ $coupon_info[0]['reward_text'] }}"
                               placeholder="Do not put more than 5 words" name="reward_text">
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
                        <input type="text" class="form-control" value="{{ $coupon_info[0]['stock'] }}"
                               placeholder="Enter the amounts fo coupon you want to add" name="coupon_count">
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
                        <input type="text" class="form-control" value="{{ $coupon_info[0]['coupon_details'] }}"
                               placeholder="Do not put more than 5 words" name="coupon_details">
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
                        <textarea name="coupon_tnc" class="form-control"
                                  placeholder="Use less than 150 characters">{{ $coupon_info[0]['coupon_tnc'] }}</textarea>
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
                        <input type="date" class="form-control" value="{{ $coupon_info[0]['expiry_date'] }}"
                               placeholder="yyyy-mm-dd" name="exp_date">
                    </div>
                </div>

                <input type="hidden" value="{{ $coupon_info[0]['id'] }}" name="coupon_id">

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="ln_solid"></div>
                <div class="form-group">
                    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </div>
            </form>

        @endif
    </div>
</div>


@include('admin.production.footer')
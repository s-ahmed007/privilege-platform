@include('admin.production.header')

<div class="right_col" role="main">
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Refer Bonus</h2>
                <div class="clearfix"></div>
                @if (Session::has('updated'))
                    <div class="alert alert-success title_right"
                         style="text-align: center">{{ Session::get('updated') }}</div>
                @elseif(session('try_again'))
                    <div class="alert alert-warning"> {{ session('try_again') }} </div>
                @endif
            </div>
            <div class="x_content">
                <br/>
                @if (isset($coupon_info))

                    <form class="form-horizontal form-label-left" method="post" action="{{ url('edit_refer_bonus') }}"
                          enctype="multipart/form-data">

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Reward text</label>
                            <span style="color: red;">
                         @if ($errors->getBag('default')->first('reward_text'))
                                    {{ $errors->getBag('default')->first('reward_text') }}
                                @endif
                        </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" value="{{ $coupon_info->reward_text }}"
                                       placeholder="Do not put more than 5 words" name="reward_text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Refer Bonus</label>
                            <span style="color: red;">
                         @if ($errors->getBag('default')->first('bonus_amount'))
                                    {{ $errors->getBag('default')->first('bonus_amount') }}
                                @endif
                        </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" value="{{ $bonus_info->price }}"
                                       placeholder="Refer bonus amount" name="bonus_amount">
                            </div>
                        </div>
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
    </div>
</div>

@include('admin.production.footer')
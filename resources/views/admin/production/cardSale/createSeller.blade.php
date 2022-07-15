@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <h3>Create A New Seller</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/store-seller') }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Name:</label>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <input type="text" class="form-control" placeholder="First Name" value="{{old('first_name')}}" name="first_name"/>
                                <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('first_name'))
                                        {{ $errors->getBag('default')->first('first_name') }}
                                @endif
                            </span>
                            </div>
                            <div class="col-md-5 col-sm-5 col-xs-12">
                                <input type="text" class="form-control" placeholder="Last Name" value="{{old('last_name')}}" name="last_name"/>
                                <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('last_name'))
                                    {{ $errors->getBag('default')->first('last_name') }}
                                @endif
                            </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Username:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Username" value="{{old('username')}}" name="username"/>
                            </div>
                            <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('username'))
                                   {{ $errors->getBag('default')->first('username') }}
                                @endif
                             </span>
                        </div>
{{--                        <div class="form-group">--}}
{{--                             <span style="color: #E74430;">--}}
{{--                                @if ($errors->getBag('default')->first('password'))--}}
{{--                                     {{ $errors->getBag('default')->first('password') }}--}}
{{--                                @endif--}}
{{--                             </span>--}}
{{--                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Password:</label>--}}
{{--                            <div class="col-md-9 col-sm-9 col-xs-12">--}}
{{--                                <input type="text" class="form-control" placeholder="Password" name="password"/>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone Number:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" name="phone_number" maxlength="14" minlength="14" value="+88"
                                       placeholder="Phone Number with country code">
                            </div>
                            <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('phone_number'))
                                     {{ $errors->getBag('default')->first('phone_number') }}
                                @endif
                             </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">PIN:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Numeric" name="pin"/>
                            </div>
                            <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('pin'))
                                     {{ $errors->getBag('default')->first('pin') }}
                                @endif
                             </span>
                        </div>
                        <div class="form-group">
                             <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('commission'))
                                     {{ $errors->getBag('default')->first('commission') }}
                                @endif
                             </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Commission Percentage:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Ex: 10" name="commission"/>
                            </div>
                        </div>
                        <div class="form-group">
                             <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('trial_commission'))
                                     {{ $errors->getBag('default')->first('trial_commission') }}
                                @endif
                             </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Trial Commission:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Ex: 10" name="trial_commission"/>
                            </div>
                        </div>
                        @if(count($promo_codes) > 0)
                            <div class="form-group">
                                 <span style="color: #E74430;">
                                    @if ($errors->getBag('default')->first('promo_ids'))
                                         {{ $errors->getBag('default')->first('promo_ids') }}
                                    @endif
                                 </span>
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Promo Code:</label>
                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <?php
                                        $promo_count = count($promo_codes);
                                        $half_count = floor($promo_count / 2)+1;
                                    ?>
                                    @for($i=0; $i < $half_count ; $i++)
                                        <input type="checkbox" name="promo_code[]" value="{{$promo_codes[$i]['id']}}"> {{$promo_codes[$i]['code']}}<br>
                                    @endfor
                                </div>
                                <div class="col-md-5 col-sm-5 col-xs-12">
                                    @for($i; $i< $promo_count ; $i++)
                                        <input type="checkbox" name="promo_code[]" value="{{$promo_codes[$i]['id']}}"> {{$promo_codes[$i]['code']}}<br>
                                    @endfor
                                </div>
                            </div>
                        @endif
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

@include('admin.production.footer')

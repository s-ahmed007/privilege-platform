@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')

<!-- page content -->
<div class="right_col" role="main">
    <div class="heading">
        <h3>Edit Basic Information</h3>
    </div>
    <div class="bar"></div>
    <div>
        @if (Session::has('updated'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
        @elseif(session('try_again'))
            <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
    </div>
    <?php $validation_page = 0;?>
    @if ($errors->getBag('default')->first('type') || $errors->getBag('default')->first('admin_code') || $errors->getBag('default')->first('about') || $errors->getBag('default')->first('password')
    || $errors->getBag('default')->first('confirm_pass'))
        <?php $validation_page = 1;?>
    @endif
    <div class="clearfix"></div>
    <div class="panel-body">
        @if (isset($partnerInfo))
            <form class="form-horizontal form-label-left" method="post" action="{{ url('storeBasicInfo') }}">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Select Category:</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <select class="form-control" name="category">
                            {{--Partner Category--}}
                            @if($partnerInfo->info->partner_category == '3')
                                <option value="3">Food & Drinks</option>
                            @elseif($partnerInfo->info->partner_category == '1')
                                <option value="1">Beauty & Spa</option>
                            @elseif($partnerInfo->info->partner_category == '2')
                                <option value="2">Entertainment</option>
                            @elseif($partnerInfo->info->partner_category == '4')
                                <option value="4">Travel & Getaways</option>
                            @elseif($partnerInfo->info->partner_category  == '5')
                                <option value="5">Health & Fitness</option>
                            @else
                                <option value="6">Lifestyle</option>
                            @endif
                            {{--Partner Category Ends--}}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Representation Type:</label>
                    <span style="color: red;">
             @if ($errors->getBag('default')->first('type'))
                            {{ $errors->getBag('default')->first('type') }}
                        @endif
            </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        @if ($validation_page==1)
                            <input type="text" class="form-control" name="type" value="{{old('type')}}">
                        @else
                            <input type="text" class="form-control" value="{{$partnerInfo->info->partner_type}}"
                                   name="type">
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Name:</label>
                    <span style="color: red;">
             @if ($errors->getBag('default')->first('name'))
                            {{ $errors->getBag('default')->first('name') }}
                        @endif
            </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" class="form-control" value="{{$partnerInfo->info->partner_name}}" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Owner Name:</label>
                    <span style="color: red;">
             @if ($errors->getBag('default')->first('owner'))
                            {{ $errors->getBag('default')->first('owner') }}
                        @endif
            </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        @if ($validation_page==1)
                            <input type="text" class="form-control" name="owner" value="{{old('owner')}}">
                        @else
                            <input type="text" class="form-control" value="{{$partnerInfo->info->owner_name}}"
                                   name="owner">
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Admin Code:</label>
                    <span style="color: red;">
             @if ($errors->getBag('default')->first('admin_code'))
                            {{ $errors->getBag('default')->first('admin_code') }}
                        @endif
            </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        @if ($validation_page==1)
                            <input type="text" class="form-control" name="admin_code" value="{{old('admin_code')}}">
                        @else
                            <input type="text" class="form-control" value="{{$partnerInfo->admin_code}}"
                                   name="admin_code">
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Facebook:</label>
                    <span style="color: red;">
             @if ($errors->getBag('default')->first('facebook'))
                            {{ $errors->getBag('default')->first('facebook') }}
                        @endif
            </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        @if ($validation_page==1)
                            <input type="text" class="form-control" name="facebook" value="{{old('facebook')}}">
                        @else
                            <input type="text" class="form-control" value="{{$partnerInfo->info->facebook_link}}"
                                   name="facebook">
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Instagram:</label>
                    <span style="color: red;">
             @if ($errors->getBag('default')->first('instagram'))
                            {{ $errors->getBag('default')->first('instagram') }}
                        @endif
            </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        @if ($validation_page==1)
                            <input type="text" class="form-control" name="instagram" value="{{old('instagram')}}">
                        @else
                            <input type="text" class="form-control" value="{{$partnerInfo->info->instagram_link}}"
                                   name="instagram">
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Website:</label>
                    <span style="color: red;">
             @if ($errors->getBag('default')->first('website'))
                            {{ $errors->getBag('default')->first('website') }}
                        @endif
            </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        @if ($validation_page==1)
                            <input type="text" class="form-control" name="website" value="{{old('website')}}">
                        @else
                            <input type="text" class="form-control" value="{{$partnerInfo->info->website_link}}"
                                   name="website">
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">About:</label>
                    <span style="color: red;">
             @if ($errors->getBag('default')->first('about'))
                            {{ $errors->getBag('default')->first('about') }}
                        @endif
            </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        {{--<input type="text" class="form-control" value="{{$partnerInfo['about']}}" name="about">--}}
                        @if ($validation_page==1)
                            <textarea rows="5" style="width:100%" name="about">{{old('about')}} </textarea>
                        @else
                            <textarea rows="5" style="width:100%" name="about">{{$partnerInfo->info->about}} </textarea>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Password:</label>
                    <span style="color: red;">
             @if ($errors->getBag('default')->first('password'))
                            {{ $errors->getBag('default')->first('password') }}
                        @endif
            </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="password" class="form-control" placeholder="Password (0-9, A-Z, a-z)"
                               name="password"
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" id="log-pass1"
                               title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
                        <span toggle="#log-pass1" class="eye-close field-icon toggle-password"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Confirm password:</label>
                    <span style="color: red;">
             @if ($errors->getBag('default')->first('confirm_pass'))
                            {{ $errors->getBag('default')->first('confirm_pass') }}
                        @endif
            </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="password" class="form-control" placeholder="Password (0-9, A-Z, a-z)"
                               name="confirm_pass" id="log-pass2">
                        <span toggle="#log-pass2" class="eye-close field-icon toggle-password"></span>
                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="ln_solid"></div>
                <div class="form-group">
                    <p class="center">
                        <button type="submit" class="btn btn-activate pull-right">Submit</button>
                    </p>
                </div>
            </form>
    </div>
    @endif
</div>
@include('partner-admin.production.footer')
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
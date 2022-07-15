@include('admin.production.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>

<script>tinymce.init({selector: '#textarea1', plugins: "lists, advlist"});</script>
<script>tinymce.init({selector: '#textarea2', plugins: "lists, advlist"});</script>
<script>tinymce.init({selector: '#textarea3', plugins: "lists, advlist"});</script>
<style>
    .field-icon {
        float: right;
        margin-right: 15px;
        margin-top: -25px;
        position: relative;
        z-index: 2;
    }
</style>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Edit Partner</h3>
        </div>
        @if(session('try_again'))
            <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        @if (isset($profileInfo))
            <form action="{{ url('partnerEditDone/'.$profileInfo->info->partner_account_id.'/'. $profileInfo->id) }}"
                  class="form-horizontal" method="post">
                <div>
                    <h3 style="text-align: center;">Basic Info</h3>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="name">Category:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" readonly
                                   value="{{ $profileInfo->info->category->type ?? 'Not found' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="name">Name:</label>
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('partner_name'))
                                {{ $errors->getBag('default')->first('partner_name') }}
                            @endif
                     </span>
                        <div class="col-sm-9">
                            <input type="text" name="partner_name" class="form-control" id="email"
                                   value="{{ $profileInfo->info->partner_name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Representing type(45 char):</label>
                        <span style="color: #E74430;">
                         @if ($errors->getBag('default')->first('type'))
                                {{ $errors->getBag('default')->first('type') }}
                            @endif
                     </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" placeholder="Representing type" name="type"
                                   value="{{$profileInfo->info->partner_type}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="name">Facebook:</label>
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('facebook'))
                                {{ $errors->getBag('default')->first('facebook') }}
                            @endif
                     </span>
                        <div class="col-sm-9">
                            <input type="text" name="facebook" class="form-control" id="email"
                                   value="{{ $profileInfo->info->facebook_link }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="name">Website:</label>
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('website'))
                                {{ $errors->getBag('default')->first('website') }}
                            @endif
                     </span>
                        <div class="col-sm-9">
                            <input type="text" name="website" class="form-control" id="email"
                                   value="{{ $profileInfo->info->website_link }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Instagram:</label>
                        <span style="color: #E74430;">
                         @if ($errors->getBag('default')->first('instagram'))
                                {{ $errors->getBag('default')->first('instagram') }}
                            @endif
                     </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" name="instagram"
                                   value="{{ $profileInfo->info->instagram_link }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="name">About:</label>
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('about'))
                                {{ $errors->getBag('default')->first('about') }}
                            @endif
                     </span>
                        <div class="col-sm-9">
                            <input type="text" name="about" class="form-control" id="email"
                                   value="{{ $profileInfo->info->about }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Contract Expiry Date:</label>
                        <span style="color: red;">
                         @if ($errors->getBag('default')->first('contract_expiry_date'))
                                {{ $errors->getBag('default')->first('contract_expiry_date') }}
                            @endif
                     </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="date" class="form-control" name="contract_expiry_date"
                                   value="{{date('Y-m-d', strtotime($profileInfo->info->expiry_date))}}">
                        </div>
                    </div>
                </div>
                <div>
                    <h3 style="text-align: center;">Branch Info</h3>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="name">Email:</label>
                        <span style="color: red;">
                           @if ($errors->getBag('default')->first('partner_email'))
                                {{ $errors->getBag('default')->first('partner_email') }}
                            @endif
                        </span>
                        <div class="col-sm-9">
                            <input type="text" name="partner_email" class="form-control" id="email"
                                   value="{{ $profileInfo->partner_email }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="name">Contact:</label>
                        <span style="color: red;">
                           @if ($errors->getBag('default')->first('partner_mobile'))
                                {{ $errors->getBag('default')->first('partner_mobile') }}
                            @endif
                        </span>
                        <div class="col-sm-9">
                            <input type="text" name="partner_mobile" class="form-control" placeholder="phone number"
                                   value="{{ $profileInfo->partner_mobile }}" id="phone_number" maxlength="15">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="name">Address:</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('partner_address'))
                                {{ $errors->getBag('default')->first('partner_address') }}
                            @endif
                        </span>
                        <div class="col-sm-9">
                            <input type="text" name="partner_address" class="form-control" id="email"
                                   value="{{ $profileInfo->partner_address }}">
                        </div>
                    </div>
                    <div class="form-group">
                       <span style="color: #E74430;">
                           @if ($errors->getBag('default')->first('division'))
                               {{ $errors->getBag('default')->first('division') }}
                           @endif
                        </span>
                        <label class="control-label col-sm-3">Select Division:</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="division">
                                <option <?php if ($profileInfo->partner_division == '') echo 'selected disabled';?>>
                                    -----
                                </option>
                                @foreach($all_divs as $division)
                                    <option
                                        <?php if ($profileInfo->partner_division == $division->name) echo 'selected';?>
                                        value="{{$division->name}}">{{$division->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                       <span style="color: #E74430;">
                           @if ($errors->getBag('default')->first('area'))
                               {{ $errors->getBag('default')->first('area') }}
                           @endif
                        </span>
                        <label class="control-label col-sm-3">Select Area:</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="area">
                                <?php if ($profileInfo->partner_area == '') echo ' <option selected disabled>-----</option>';?>
                                @foreach($all_areas as $area)
                                    <option
                                        <?php if ($profileInfo->partner_area == $area['area_name']) echo 'selected';?>
                                        value="{{$area['area_name']}}">{{$area['area_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="name">Location:</label>
                        <span style="color: red;">
                           @if ($errors->getBag('default')->first('partner_location'))
                                {{ $errors->getBag('default')->first('partner_location') }}
                            @endif
                       </span>
                        <div class="col-sm-9">
                            <input type="text" name="partner_location" class="form-control" id="email"
                                   value="{{ $profileInfo->partner_location }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Longitude:</label>
                        <span style="color: red;">
                           @if ($errors->getBag('default')->first('longitude'))
                                {{ $errors->getBag('default')->first('longitude') }}
                            @endif
                       </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" value="{{$profileInfo->longitude}}"
                                   name="longitude">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Latitude:</label>
                        <span style="color: red;">
                           @if ($errors->getBag('default')->first('latitude'))
                                {{ $errors->getBag('default')->first('latitude') }}
                            @endif
                       </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" value="{{$profileInfo->latitude}}" name="latitude">
                        </div>
                    </div>
{{--                    <div class="form-group">--}}
{{--                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Password:</label>--}}
{{--                        <span style="color: red;">--}}
{{--                           @if ($errors->getBag('default')->first('password'))--}}
{{--                                {{ $errors->getBag('default')->first('password') }}--}}
{{--                            @endif--}}
{{--                       </span>--}}
{{--                        <div class="col-md-9 col-sm-9 col-xs-12">--}}
{{--                            <?php--}}
{{--                            $password = (new \App\Http\Controllers\functionController)->encrypt_decrypt('decrypt', $profileInfo->password);--}}
{{--                            ?>--}}
{{--                            <input type="password" class="form-control" placeholder="Password (0-9, A-Z, a-z)"--}}
{{--                                   name="password" value="{{$password}}"--}}
{{--                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" id="log-pass1"--}}
{{--                                   title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">--}}
{{--                            <span toggle="#log-pass1" class="eye-close field-icon toggle-password"></span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Confirm password:</label>--}}
{{--                        <span style="color: red;">--}}
{{--                           @if ($errors->getBag('default')->first('confirm_pass'))--}}
{{--                                {{ $errors->getBag('default')->first('confirm_pass') }}--}}
{{--                            @endif--}}
{{--                       </span>--}}
{{--                        <div class="col-md-9 col-sm-9 col-xs-12">--}}
{{--                            <input type="password" class="form-control" placeholder="Password (0-9, A-Z, a-z)"--}}
{{--                                   name="confirm_pass" value="{{$password}}" id="log-pass2">--}}
{{--                            <span toggle="#log-pass2" class="eye-close field-icon toggle-password"></span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="form-group">
                        <label class="col-md-3 col-sm-3 col-xs-12 control-label">Select Facilities:
                            <br>
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <div id="attributeArea">
                                @foreach($facilities as $facility)
                                  <div class="checkbox">
                                      <label>
                                          <input type="checkbox" class="flat" name="{{str_replace(' ', '_', $facility->name)}}" {{ $profileInfo->facilities && in_array($facility->id, $profileInfo->facilities) ? 'checked' : ''}}>{{$facility->name}}
                                      </label>
                                  </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" style="text-align: unset;">Opening Hours:
                      Format to follow - hh:mm aa - hh:mm aa (ex. 06:00 AM - 06:00 PM) / Closed / Always Open
                              </label>
                        <label class="col-md-1">SAT</label>
                        <div class="col-md-8 col-sm-3 col-xs-6">
                            @if ($errors->getBag('default')->first('sat'))
                                <div style="color: red">Field is required</div>
                            @endif
                            <input type="text" class="form-control" value="{{$profileInfo->openingHours['sat']}}" name="sat">
                        </div>
                        <label class="col-md-1">SUN</label>
                        <div class="col-md-8 col-sm-3 col-xs-6">
                            @if ($errors->getBag('default')->first('sun'))
                                <div style="color: red">Field is required</div>
                            @endif
                            <input type="text" class="form-control" value="{{$profileInfo->openingHours['sun']}}" name="sun" value="{{old('sun')}}">
                        </div>
                        <div class="col-md-3"></div>
                        <label class="col-md-1">MON</label>
                        <div class="col-md-8 col-sm-3 col-xs-6">
                            @if ($errors->getBag('default')->first('mon'))
                                <div style="color: red">Field is required</div>
                            @endif
                            <input type="text" class="form-control" value="{{$profileInfo->openingHours['mon']}}" name="mon" value="{{old('mon')}}">
                        </div>
                        <div class="col-md-3"></div>
                        <label class="col-md-1">TUES</label>
                        <div class="col-md-8 col-sm-3 col-xs-6">
                            @if ($errors->getBag('default')->first('tues'))
                                <div style="color: red">Field is required</div>
                            @endif
                            <input type="text" class="form-control" value="{{$profileInfo->openingHours['tue']}}" name="tues" value="{{old('tues')}}">
                        </div>
                        <div class="col-md-3"></div>
                        <label class="col-md-1">WED</label>
                        <div class="col-md-8 col-sm-3 col-xs-6">
                            @if ($errors->getBag('default')->first('wed'))
                                <div style="color: red">Field is required</div>
                            @endif
                            <input type="text" class="form-control" value="{{$profileInfo->openingHours['wed']}}" name="wed" value="{{old('wed')}}">
                        </div>
                        <div class="col-md-3"></div>
                        <label class="col-md-1">THU</label>
                        <div class="col-md-8 col-sm-3 col-xs-6">
                            @if ($errors->getBag('default')->first('thu'))
                                <div style="color: red">Field is required</div>
                            @endif
                            <input type="text" class="form-control" value="{{$profileInfo->openingHours['thurs']}}" name="thu" value="{{old('thu')}}">
                        </div>
                        <div class="col-md-3"></div>
                        <label class="col-md-1">FRI</label>
                        <div class="col-md-8 col-sm-3 col-xs-6">
                            @if ($errors->getBag('default')->first('fri'))
                                <div style="color: red">Field is required</div>
                            @endif
                            <input type="text" class="form-control" value="{{$profileInfo->openingHours['fri']}}" name="fri" value="{{old('fri')}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Main Branch:</label>
                        <span style="color: red;">
                       @if ($errors->getBag('default')->first('main_branch'))
                                {{ $errors->getBag('default')->first('main_branch') }}
                            @endif
                       </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            @if($profileInfo->main_branch == 1)
                                <i class="check-icon" style="font-size: 1.5em; color: forestgreen;"></i>
                            @else
                                <input type="checkbox" class="form-control" name="main_branch" onchange="doalert(this)">
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Terms & Conditions and Discounts -->
                <hr>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <div style="float: right;">
                        <button type="submit" class="btn btn-activate pull-right">Submit</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@include('admin.production.footer')
<script>
    function doalert(checkboxElem) {
        if (checkboxElem.checked) {
            alert("You are going to change the main branch of this partner.");
        }
    }
</script>

<script type="text/javascript">
    $(document).ready(function () {
        var discount_count = $("#discount_count").val();
        var i = discount_count - 1;
        //if there is no previously added extra discounts
        if (isNaN(i)) {
            var i = 0;
        }
        $('#add').click(function () {
            i++;
            $('#special_discounts').append(
                '<div id="row' + i + '"><br><br>'
                + '<div class="col-md-4 col-sm-4 col-xs-12">'
                + '<input type="text" class="form-control" placeholder="Discount Title" name="special_discount_title[]" value="">'
                + '</div>'
                + '<div class="col-md-3 col-sm-3 col-xs-12">'
                + '<input type="text" class="form-control" placeholder="Gold Discount (digit)" name="special_discount_gold[]" value="">'
                + '</div>'
                + '<div class="col-md-3 col-sm-3 col-xs-12">'
                + '<input type="text" class="form-control" placeholder="Royalty Premium Membership Discount (digit)" name="special_discount_platinum[]" value="">'
                + '</div>'
                + '<div class="col-md-2 col-sm-2 col-xs-12">'
                + '<button name="remove" id="' + i + '" class="btn btn-danger btn_remove">Remove</button>'
                + '</div>'
                + '</div>'
            );
            //Following script to remove newly add discount blocks
            $(document).on('click', '.btn_remove', function () {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });
        });
        //Following script to remove previous discount blocks
        $(document).on('click', '.btn_remove', function () {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
        });
    });
</script>
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